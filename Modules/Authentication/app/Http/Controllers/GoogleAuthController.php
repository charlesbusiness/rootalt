<?php

namespace Modules\Authentication\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Authentication\Services\GoogleAuthenticationService;

class GoogleAuthController extends Controller
{
   protected $authService;
   public function __construct(GoogleAuthenticationService $authService)
   {
      $this->authService = $authService;
   }
   /**
    * Display a listing of the resource.
    */
   public function index()
   {
      return view('authentication::index');
   }

   /**
    * Show the form for creating a new resource.
    */
   public function create()
   {
      return view('authentication::create');
   }

   /**
    * Redirect to google page for authentication.
    */
   public function redirectToGoogle(Request $request)
   {
      return $this->authService->redirectToGoogle($request);
   }

   /**
    * Redirect to google page for authentication.
    */
   public function googleLogin(Request $request)
   {
      return $this->authService->googleLogin($request);
   }

   /**
    * Get the authentication user data.
    */
   public function handleGoogleCallback(Request $request)
   {
      return $this->authService->handleGoogleCallback($request);
   }

   /**
    * Get the authentication user data.
    */
   public function mimickFrontend(Request $request)
   {
      return view('auth:google');
   }
}
