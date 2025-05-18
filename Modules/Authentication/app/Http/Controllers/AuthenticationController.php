<?php

namespace Modules\Authentication\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Authentication\Http\Requests\CreatePasswordRequest;
use Modules\Authentication\Http\Requests\LoginRequest;
use Modules\Authentication\Http\Requests\ResendEmailVeificationRequest;
use Modules\Authentication\Http\Requests\TwoFAVerificationRequest;
use Modules\Authentication\Http\Requests\VerifyEmailRequest;
use Modules\Authentication\Http\Requests\VerifyPhoneLoginRequest;
use Modules\Authentication\Services\AuthenticationService;

class AuthenticationController extends Controller
{
  protected $authService;
   public function __construct(AuthenticationService $authService)
   {
      $this->authService = $authService;
   }
   

    /**
     * Store a newly created resource in database.
     */
    public function store(Request $request)
    {
       return $this->authService->register($request);
    }


    /**
     * Login a user into their account.
     */
    public function login(LoginRequest $request)
    {
       return $this->authService->login($request);
    }


    /**
     * Login a user into their account.
     */
    public function verify2FA(TwoFAVerificationRequest $request)
    {
       return $this->authService->verify2FA($request);
    }

    


    /**
     * Store a newly created resource in database.
     */
    public function verify(VerifyEmailRequest $request)
    {
       return $this->authService->verifyAccount($request);
    }

    /**
     * Store a newly created resource in database.
     */
    public function resendEmailVerification(ResendEmailVeificationRequest $request)
    {
       return $this->authService->resendVerificationCode($request);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('authentication::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('authentication::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
