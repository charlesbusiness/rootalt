<?php

namespace Modules\Authentication\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyEmailMiddleware
{
  /**
   * Handle an incoming request.
   */
  public function handle(Request $request, Closure $next)
  {

    $user =  Auth::user();

    if (!$user) {
      $data = $request->email ?? $request->username;
      $user = user($data);
    }

    if (!$user) {
      return failedResponse(null, "Your account creation was not completed. Please finish the setup");
    }

    if ($user && is_null($user->email_verified_at)) {
      $user->email_isVerified = false;
      return failedResponse($user, "Please verify your email address to continue");
    }

    return $next($request);
  }
}
