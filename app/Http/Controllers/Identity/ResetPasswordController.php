<?php

namespace App\Http\Controllers\Identity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    /**
     * Send reset password link.
	 * @unauthenticated
     */
    public function forgot(Request $request)
    {
        $request->validate(['email' => 'required|email']);
 
		$status = Password::sendResetLink(
			$request->only('email')
		);

		if ($status === Password::ResetLinkSent){
			return response()->json($status, 201);
		}
    }

	/**
     * Reset password.
	 * @unauthenticated
     */
	public function reset(Request $request, string $token)
	{
	    return response()->json($token, 201);
	}
}
