<?php

use Illuminate\Support\Facades\Route;

use function Pest\Laravel\get;
use function Pest\Laravel\getJson;

beforeEach(function () {
    Route::middleware('api')->prefix('api/v1/_test')->group(function () {
        Route::get('boom', fn () => throw new RuntimeException('secret details.'));
    });
});

it('renders an unknown api route as a not_found envelope', function () {
    $response = get('api/v1/does-not-exists');

    $response->assertStatus(404)->assertJsonPath('error.code', 'not_found');

    expect($response->json('meta.request_id'))->toMatch('/^req_[0-9A-Z]{26}$/');
    expect($response->getContent())->toContain('"details":{}');
});

it('hides the exception message behind a generic server_error', function () {
    getJson('api/v1/_test/boom')->assertStatus(500)
        ->assertJsonPath('error.code', 'server_error')
        ->assertDontSee('secret details.');
});
