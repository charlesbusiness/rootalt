<?php

namespace Modules\Authentication\Services;

use App\Models\User;
use Faker\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Modules\Core\Models\Country;
use Modules\Core\Models\Role;
use Modules\Core\Models\UserType;
use Modules\Core\Services\CoreService;
use Modules\Core\Services\MessageService;
use Throwable;

class GoogleAuthenticationService extends CoreService
{
    protected $userModel;
    protected $auth;
    protected $emailService;
    protected $clientId;
    protected $clientSecret;
    protected $redirectUrl;
    protected $roleModel;
    protected $country;

    public function __construct(User $user, Auth $auth, MessageService $emailService, Country $country, Role $roleModel)
    {
        $this->userModel = $user;

        $this->roleModel = $roleModel;
        $this->auth = $auth;
        $this->country = $country;
        $this->emailService = $emailService;
        $this->clientId = config('authentication.google.client_id');
        $this->redirectUrl = config('authentication.google.redirect_uri');
        $this->clientSecret = config('authentication.google.client_secret');
        parent::__construct();
    }



    /**
     * Redirect to ggogle for authentication
     ** */

    public function redirectToGoogleMain()
    {
        $queryParams = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'response_type' => 'code',
            'scope' => 'openid email profile ',
            'access_type' => 'offline',
            'prompt' => 'select_account',
        ]);
        // 'scope' => 'openid email profile https://www.googleapis.com/auth/user.phonenumbers.read',

        return redirect("https://accounts.google.com/o/oauth2/auth?$queryParams");
    }

    public function redirectToGoogle()
    {
        $queryParams = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'response_type' => 'code',
            'scope' => 'openid email profile https://www.googleapis.com/auth/user.phonenumbers.read',
            'access_type' => 'offline',
            'prompt' => 'select_account',
        ]);

        return redirect("https://accounts.google.com/o/oauth2/auth?$queryParams");
    }


    // Handle Google Callback
    public function handleGoogleCallbackMain()
    {
        // Check if code exists in the query parameters
        $code = request('code');
        $errorResponse = $this->frontendUrl . '/auth/google/callback?error=true&&code=null';
        try {
            //code...

            if (!$code) {
                return redirect()->to($errorResponse);
            }

            // Exchange authorization code for access token
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'redirect_uri' => $this->redirectUrl,
                'grant_type' => 'authorization_code',
            ]);

            if ($response->failed()) {
                return redirect()->to($errorResponse);
            }

            $data = $response->json();
            $accessToken = $data['access_token'];

            // Use access token to get user info
            $userInfoResponse = Http::withToken($accessToken)
                ->get('https://www.googleapis.com/oauth2/v2/userinfo');

            if ($userInfoResponse->failed()) {
                return redirect()->to($errorResponse);
            }
            $peopleResponse = Http::withToken($data['access_token'])
                ->get('https://people.googleapis.com/v1/people/me?personFields=phoneNumbers');
            $phoneNumber = null;

            if ($peopleResponse->successful()) {
                $phoneData = $peopleResponse->json();
                $phoneNumber = @$phoneData['phoneNumbers']['value'];
            }

            if (is_null($phoneNumber)) { //Just generate a fake phone number for the user to be updated by the user later
                $faker = Factory::create();
                $phoneNumber = $faker->phoneNumber;
            }

            $googleUser = $userInfoResponse->json();

            $user = $this->userModel->query()->whereEmail($googleUser['email'])->first();

            // User already exists, generate token
            if (!$user) {
                $user =  $this->userModel->create([
                    'phone' => $phoneNumber,
                    'email' => $googleUser['email'],
                    'name' => $googleUser['name'],
                    'profile' => @$googleUser['picture'],
                    'password' => Hash::make(Factory::create()->password),
                    'email_verified_at' => now(),
                ]);
            }

            // $code = $this->saveVerificationCode(
            //     request: request()->merge([
            //         'email' => $user->email,
            //     ]),
            //     type: $this->googleAuthCodeType
            // );
            $token = $this->generateToken(user: $user);
            return redirect()->to("$this->frontendUrl/auth/google/callback?error=false&&token=$token");
        } catch (Throwable $e) {
            logError($e);
            return failedResponse(null, $this->errorMessage, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function handleGoogleCallback()
    {
        $code = request('code');
        $errorResponse = $this->frontendUrl . '/auth/google/redirect?error=true&&data=null';

        if (!$code) {
            return redirect()->to($errorResponse);
        }

        try {
            // Exchange authorization code for access token
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'redirect_uri' => $this->redirectUrl,
                'grant_type' => 'authorization_code',
            ]);

            if ($response->failed()) {
                return redirect()->to($errorResponse);
            }

            $data = $response->json();
            $accessToken = $data['access_token'];

            // Use access token to get user info
            $userInfoResponse = Http::withToken($accessToken)
                ->get('https://www.googleapis.com/oauth2/v2/userinfo');

            if ($userInfoResponse->failed()) {
                return redirect()->to($errorResponse);
            }

            $googleUser = $userInfoResponse->json();
            // Retrieve phone numbers and gender via People API
            $peopleResponse = Http::withToken($accessToken)
                ->get('https://people.googleapis.com/v1/people/me?personFields=phoneNumbers,genders,addresses');

            $peopleResponse = Http::withToken($accessToken)
                ->get('https://people.googleapis.com/v1/people/me?personFields=phoneNumbers,genders,addresses');


            $phoneNumber = null;
            $gender = null;
            $country = null;

            if ($peopleResponse->successful()) {
                $peopleData = $peopleResponse->json();

                // Extract phone number
                if (isset($peopleData['phoneNumbers'][0]['value'])) {
                    $phoneNumber = $peopleData['phoneNumbers'][0]['value'];
                }

                // Extract gender
                if (isset($peopleData['genders'][0]['value'])) {
                    $gender = $peopleData['genders'][0]['value'];
                }

                // // Extract country
                // if (isset($peopleData['addresses'][0]['country'])) {
                //     $countryName = $peopleData['addresses'][0]['country'];
                // }
            }

            // Generate fake phone number if none provided
            if (is_null($phoneNumber)) {
                $faker = Factory::create();
                $phoneNumber = $faker->phoneNumber;
            }

            // Generate fake phone number if none provided
            if (is_null($gender)) {
                $gender = 'male';
            }
            // Generate fake phone number if none provided
            if (is_null($country)) {
                $country = $this->country->where('name', ' Liberia')->first();
            }

            // Check if user already exists or create a new one
            $user = $this->userModel->query()->whereEmail($googleUser['email'])->first();

            if (!$user) {

                $user = $this->userModel->create([
                    'phone' => $phoneNumber,
                    'username' => explode('@', $googleUser['email'])[0],
                    'email' => $googleUser['email'],
                    'firstName' => $googleUser['given_name'],
                    'lastName' => $googleUser['family_name'] ?? $googleUser['name'],
                    // 'profile' => @$googleUser['picture'],
                    'gender' => $gender,
                    'password' => Hash::make(Factory::create()->password),
                    'email_verified_at' => now(),
                ]);
            }

            $token = $this->generateToken(user: $user);
            unset($token['permissions'], $token['role'], $token['user_type']);
            return redirect()->to("$this->frontendUrl/auth/google/redirect?error=false&data=$token");
        } catch (Throwable $e) {
            logError($e);

            return redirect()->to("$this->frontendUrl/auth/google/redirect?error=true&data=null");
        }
    }

    public function googleLogin(Request $request)
    {
        $data = $request->all();

        try {
            $code = $this->getValidCode($data);

            if ($code) {
                $user = user($code->email);
                $message = $this->message = "Login successful";
                $token = $this->generateToken(user: $user);
                $response = successfulResponse($token, $message, Response::HTTP_OK);
            } else {
                $message = $this->message = "Invalid code provided";
                $response = failedResponse($code, $message, Response::HTTP_OK);
            }
        } catch (Throwable $e) {
            $response = failedResponse(null, $this->errorMessage, Response::HTTP_INTERNAL_SERVER_ERROR);
            logError($e);
        }
        return $response;
    }
}
