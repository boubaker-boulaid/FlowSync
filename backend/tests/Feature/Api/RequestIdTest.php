<?php

use Illuminate\Support\Str;

use function Pest\Laravel\get;
use function Pest\Laravel\withHeader;

it('generates a request id when none is sent', function () {
    $response = get('/up');

    $response->assertOk();

    $requestId = $response->headers->get('X-Request-Id');

    expect($requestId)->toMatch('/^req_[0-9A-Z]{26}$/');
});

it('keeps a well-formed request id sent by the client', function () {
    $requestId = 'req_'. Str::ulid();

    $response = withHeader('X-Request-Id', $requestId)->get('/up');

    $response->assertOk();

    expect($response->headers->get('X-Request-Id'))->toBe($requestId);
});

it('replaces a malformed request id with a new one', function () {
    $requestId = 'hello world';

    $response = withHeader('X-Request-Id', $requestId)->get('/up');

    $response->assertOk();

    expect($response->headers->get('X-Request-Id'))->not()->toBe('hello world')
        ->toMatch('/^req_[0-9A-Z]{26}$/');
});
