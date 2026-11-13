<?php

namespace App\Http\Controllers\Identity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
  /**
   * Login user
   * 
   * @unauthenticated
   */
  public function login(Request $request)
  {
    $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
    if (Auth::attempt($credentials)) {
        //$request->session()->regenerate();

        $token = $request->user()->createToken('do_it_token');
 
        return ['token' => $token->plainTextToken];
    }

    return response()->json(['message' => 'Unauthorized'], 401);
  }
}