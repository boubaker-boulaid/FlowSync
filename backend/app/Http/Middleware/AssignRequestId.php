<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AssignRequestId
{
    /**
     * Make sure every response carries X-Request-Id (req_01JDHDHDJWYEI483...)
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = $request->header('X-Request-Id');

        // check the request id exists and valid if not create a valid one
        if (! $requestId || ! str_starts_with($requestId, 'req_') || ! Str::isUlid(Str::after($requestId, 'req_'))) {
            $requestId = 'req_'.Str::ulid();
        }

        // add the request id to every log line automatically
        Context::add('request_id', $requestId);

        $response = $next($request);

        $response->headers->set('X-Request-Id', $requestId);

        return $response;
    }
}
