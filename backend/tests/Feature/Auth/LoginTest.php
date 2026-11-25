<?php

use App\Models\User;

use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\withHeader;

it('logs in with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@gmail.com',
        'password' => 'testpassword',
    ]);

    // Sanctum only starts a session for a stateful Origin header.
    withHeader('Origin', 'http://localhost:8080');

    $response = postJson('api/v1/auth/login', [
        'email' => 'test@gmail.com',
        'password' => 'testpassword',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.id', $user->id)
        ->assertJsonPath('data.email', 'test@gmail.com')
        ->assertJsonPath('data.is_demo', false);

    $response->assertJsonStructure([
        'data' => [
            'id',
            'name',
            'email',
            'email_verified_at',
            'is_demo',
            'created_at',
        ],
    ])->assertJsonCount(6, 'data');

    assertAuthenticated();
});

it('rejects log in with invalid credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@gmail.com',
        'password' => 'testpassword',
    ]);

    withHeader('Origin', 'http://localhost:8080');

    $unknownEmailResponse = postJson('api/v1/auth/login', [
        'email' => 'wrongmail@gmail.com',
        'password' => 'password',
    ]);

    $wrongPasswordResponse = postJson('api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'wrongpassword',
    ]);

    $unknownEmailResponse->assertStatus(422)->assertJsonPath('error.code', 'validation_failed');
    $wrongPasswordResponse->assertStatus(422)->assertJsonPath('error.code', 'validation_failed');

    $unknownEmailResponse->assertJsonValidationErrorFor('email', 'error.details.fields');
    $wrongPasswordResponse->assertJsonValidationErrorFor('email', 'error.details.fields');

    expect($unknownEmailResponse->json('error.details.fields'))
        ->toEqual($wrongPasswordResponse->json('error.details.fields'));

    assertGuest();
});

it('rate limits the sixth login attempt within a minute', function () {
    User::factory()->create([
        'email' => 'rate-limit@gmail.com',
        'password' => 'testpassword',
    ]);

    withHeader('Origin', 'http://localhost:8080');

    // Same email + IP keeps all six attempts in one limiter bucket.
    for ($attempt = 1; $attempt <= 5; $attempt++) {
        postJson('api/v1/auth/login', [
            'email' => 'rate-limit@gmail.com',
            'password' => 'wrongpassword',
        ])->assertStatus(422)
            ->assertJsonValidationErrorFor('email', 'error.details.fields')
            ->assertJsonPath('error.code', 'validation_failed');
    }

    $response = postJson('api/v1/auth/login', [
        'email' => 'rate-limit@gmail.com',
        'password' => 'wrongpassword2',
    ]);

    $response->assertStatus(429)
        ->assertJsonPath('error.code', 'rate_limited')
        ->assertJsonPath('error.message', 'Too many requests. Try again later.')
        ->assertHeader('X-RateLimit-Limit', '5')
        ->assertHeader('X-RateLimit-Remaining', '0')
        ->assertHeader('Retry-After');

    $retryAfter = (int) $response->baseResponse->headers->get('Retry-After');

    expect($retryAfter)->toBeGreaterThan(0);

    assertGuest();
});

it('rejects a demo user with the right password like a wrong password', function () {
    $demoUser = User::factory()->create([
        'email' => 'demo@gmail.com',
        'password' => 'testpassword',
        'is_demo' => true,
    ]);

    withHeader('Origin', 'http://localhost:8080');

    // A demo user with the right password must not reveal that the account exists.
    $demoResponse = postJson('api/v1/auth/login', [
        'email' => $demoUser->email,
        'password' => 'testpassword',
    ]);

    $wrongPasswordResponse = postJson('api/v1/auth/login', [
        'email' => $demoUser->email,
        'password' => 'wrongpassword',
    ]);

    $demoResponse->assertStatus(422)->assertJsonPath('error.code', 'validation_failed');
    $wrongPasswordResponse->assertStatus(422)->assertJsonPath('error.code', 'validation_failed');

    $demoResponse->assertJsonValidationErrorFor('email', 'error.details.fields');

    expect($demoResponse->json('error.details.fields'))
        ->toEqual($wrongPasswordResponse->json('error.details.fields'));

    assertGuest();
});

it('rejects me without a session', function () {
    withHeader('Origin', 'http://localhost:8080');

    getJson('api/v1/auth/me')
        ->assertStatus(401)
        ->assertJsonPath('error.code', 'unauthenticated')
        ->assertJsonPath('error.message', 'Please sign in first.');

    assertGuest();
});

it('returns the current user with exactly the expected fields', function () {
    $user = User::factory()->create([
        'email' => 'me@gmail.com',
        'password' => 'testpassword',
    ]);

    withHeader('Origin', 'http://localhost:8080');

    postJson('api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'testpassword',
    ])->assertStatus(200);

    $response = getJson('api/v1/auth/me');

    $response->assertStatus(200)
        ->assertJsonPath('data.id', $user->id)
        ->assertJsonPath('data.email', $user->email);

    // Explicit allowlist: no password hash or remember token may leak.
    $response->assertExactJsonStructure([
        'data' => [
            'id',
            'name',
            'email',
            'email_verified_at',
            'is_demo',
            'created_at',
        ],
        'meta' => [
            'request_id',
        ],
    ])->assertJsonMissingPath('data.password')
        ->assertJsonMissingPath('data.remember_token');

    assertAuthenticated();
});

it('logs out and invalidates the session', function () {
    $user = User::factory()->create([
        'email' => 'logout@gmail.com',
        'password' => 'testpassword',
    ]);

    withHeader('Origin', 'http://localhost:8080');

    postJson('api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'testpassword',
    ])->assertStatus(200);

    // A 204 has no body; the follow-up /me proves the session is dead.
    postJson('api/v1/auth/logout')->assertNoContent();

    assertGuest('web');

    // The test container reuses guard instances, so clear Sanctum's cached user.
    app('auth')->forgetGuards();

    getJson('api/v1/auth/me')
        ->assertStatus(401)
        ->assertJsonPath('error.code', 'unauthenticated');

    assertGuest();
});
