<?php

namespace App\Http\Responses;

use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Http\Responses\LoginResponse as LoginResponseContract;
use Override;

class LoginResponse extends LoginResponseContract
{
    #[Override]
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  mixed  $request
     * @return JsonResponse
     */
    public function toResponse($request)
    {
        return (new UserResource($request->user()))->toResponse($request)->setStatusCode(200);
    }
}
