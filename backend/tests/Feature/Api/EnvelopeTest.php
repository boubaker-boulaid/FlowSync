<?php

use App\Exceptions\DefinitionInvalid;
use App\Exceptions\DemoLimitReached;
use App\Exceptions\InvalidState;
use App\Exceptions\RevisionConflict;
use App\Http\Resources\ApiResource;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\get;
use function Pest\Laravel\getJson;

beforeEach(function () {
    Route::middleware('api')->prefix('api/v1/_test')->group(function () {
        Route::get('ok', fn () => new ApiResource(['pong' => true]));
        Route::get('boom', fn () => throw new RuntimeException('secret detail'));
        Route::get('validate', fn (Request $request) => $request->validate([
            'name' => 'required',
        ]));
        Route::get('revision-conflict', fn () => throw new RevisionConflict(['current_revision' => 15]));
        Route::get('invalid-state', fn () => throw new InvalidState('archived', ['draft', 'active']));
        Route::get('definition-invalid', fn () => throw new DefinitionInvalid(['nodes.0.type' => ['Unknown node type.']]));
        Route::get('demo-limit', fn () => throw new DemoLimitReached(60));
        Route::get('throttled', fn () => throw new ThrottleRequestsException('Too Many Attempts.', null, ['Retry-After' => 30]));
        Route::get('wants-json', fn (Request $request) => ['wants_json' => $request->wantsJson()]);
    });
});

it('renders an unknown api route as a not_found envelope', function () {
    $response = get('/api/v1/does-not-exist');

    $response->assertStatus(404)->assertJsonPath('error.code', 'not_found');

    expect($response->json('meta.request_id'))->toMatch('/^req_[0-9A-Z]{26}$/');
    expect($response->getContent())->toContain('"details":{}');
});

it('hides the exception message behind a generic server_error', function () {
    getJson('/api/v1/_test/boom')
        ->assertStatus(500)
        ->assertJsonPath('error.code', 'server_error')
        ->assertDontSee('secret detail');
});

it('wraps an api resource in data and meta.request_id', function () {
    $response = getJson('/api/v1/_test/ok');

    $response->assertOk()->assertJsonPath('data.pong', true);

    expect($response->json('meta.request_id'))->toMatch('/^req_[0-9A-Z]{26}$/');
});

it('renders a validation error as validation_failed with field messages', function () {
    $response = getJson('/api/v1/_test/validate');

    $response->assertStatus(422)->assertJsonPath('error.code', 'validation_failed');

    expect($response->json('error.details.fields.name'))->not->toBeEmpty();
});

it('renders a revision conflict with its details', function () {
    getJson('/api/v1/_test/revision-conflict')
        ->assertStatus(409)
        ->assertJsonPath('error.code', 'revision_conflict')
        ->assertJsonPath('error.details.current_revision', 15);
});

it('renders an invalid state with the current status and allowed_from', function () {
    getJson('/api/v1/_test/invalid-state')
        ->assertStatus(409)
        ->assertJsonPath('error.code', 'invalid_state')
        ->assertJsonPath('error.details.status', 'archived')
        ->assertJsonPath('error.details.allowed_from', ['draft', 'active']);
});

it('renders an invalid definition with its errors', function () {
    getJson('/api/v1/_test/definition-invalid')
        ->assertStatus(422)
        ->assertJsonPath('error.code', 'definition_invalid')
        ->assertJsonPath('error.details.errors', ['nodes.0.type' => ['Unknown node type.']]);
});

it('renders a demo limit with a Retry-After header', function () {
    getJson('/api/v1/_test/demo-limit')
        ->assertStatus(429)
        ->assertJsonPath('error.code', 'demo_limit_reached')
        ->assertHeader('Retry-After', '60');
});

it('keeps the Retry-After header when rate limited', function () {
    getJson('/api/v1/_test/throttled')
        ->assertStatus(429)
        ->assertJsonPath('error.code', 'rate_limited')
        ->assertHeader('Retry-After', '30');
});

it('makes api requests want json even without an Accept header', function () {
    // Plain get(), not getJson(): no Accept header is sent, so only
    // ForceJsonResponse can make wantsJson() true.
    get('/api/v1/_test/wants-json')
        ->assertOk()
        ->assertJsonPath('wants_json', true);
});
