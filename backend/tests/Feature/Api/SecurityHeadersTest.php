<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;

use function Pest\Laravel\get;
use function Pest\Laravel\getJson;

/**
 * Laravel may append ", private" to Cache-Control, so only no-store is required.
 *
 * @param  TestResponse<Response>  $response
 */
function assertSecurityHeaders(TestResponse $response): void
{
    $response->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('Referrer-Policy', 'same-origin');

    expect($response->headers->get('Cache-Control'))->toContain('no-store');
}

beforeEach(function () {
    Route::middleware('api')->prefix('api/v1/_test')->group(function () {
        Route::get('ok', fn () => ['ok' => true]);
        Route::get('boom', fn () => throw new RuntimeException('boom'));
    });
});

it('adds the security headers to a successful api response', function () {
    $response = getJson('/api/v1/_test/ok')->assertOk();

    assertSecurityHeaders($response);
});

it('adds the security headers to an api error response', function () {
    $response = getJson('/api/v1/_test/boom')->assertStatus(500);

    assertSecurityHeaders($response);
});

it('adds the security headers to an unknown api route', function () {
    $response = getJson('/api/v1/does-not-exist')->assertNotFound();

    assertSecurityHeaders($response);
});

it('leaves non-api responses alone', function () {
    get('/up')->assertOk()->assertHeaderMissing('X-Content-Type-Options');
});
