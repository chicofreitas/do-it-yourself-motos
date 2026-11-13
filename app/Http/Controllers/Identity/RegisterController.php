<?php

namespace App\Http\Controllers\Identity;

use App\Http\Controllers\Controller;
use App\Actions\RegisterUser;
use App\Http\Requests\RegiserUserRequest;

class RegisterController extends Controller
{
    /**
     * Register a new user.
     * @unauthenticated
     */
    public function register(RegiserUserRequest $request)
    {
        $user = RegisterUser::run($request->all());
        return response()->json($user, 201);
    }
}
