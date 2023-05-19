<?php

namespace App\Http\Middleware;

use App\Http\Controllers\ResponseController;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }

    protected function unauthenticated($request, array $guards)
    {
        $response = new ResponseController();
        throw new HttpResponseException($response->__invoke(false, "Please login!", null, 401));
    }
}
