<?php

namespace App\Http\Controllers\Identity;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class EmailVerificationController extends Controller
{
  /**
   * Verify email address.
   */
  public function verify(EmailVerificationRequest $request) 
  {
    $request->fulfill();
 
    return response()->json(['message' => 'Email verified successfully']);
  }
}