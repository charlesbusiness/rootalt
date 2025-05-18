<?php

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Modules\AdminManager\Models\Admin;
use Modules\Authentication\Models\OtpManager;
use Modules\Authentication\Models\TempUser;
use Modules\Core\Models\UserType;

/**
 * This will return a standard json response when a request successful
 * @param mixed $response 
 * @param string $message 
 */
function successfulResponse($response, $message = 'Process successful', $statusCode = 200)
{

    $responseObject = [
        'data' => $response['data'] ?? $response,
        'meta_data' => $response['meta_data'] ?? null,
        'message' => $message,
        'success' => true
    ];

    return response()->json($responseObject, $statusCode);
}


/**
 * Transform the paginated data response to have a uniform response 
 */

function transformPaginatedData(LengthAwarePaginator $paginatedData)
{

    return [
        'data' => $paginatedData->items(),
        'meta_data' => [
            'total' => $paginatedData->total(),
            'per_page' => $paginatedData->perPage(),
            'current_page' => $paginatedData->currentPage(),
            'last_page' => $paginatedData->lastPage(),
            'next_page_url' => $paginatedData->nextPageUrl(),
            'prev_page_url' => $paginatedData->previousPageUrl(),
        ],
    ];
}

/**
 * This will return a standard json response when a request failed
 * @param mixed $response 
 * @param string $message 
 * @param string $error 
 */
function failedResponse($response = null, $message = 'Process faile', $statusCode = 400)
{
    $responseObject = [
        'data' => $response,
        'success' => false,
        'message' => $message,

    ];

    return response()->json($responseObject, $statusCode);
}

function logError($e)
{
    try {
        $exceptionDetails = [
            'message' => $e->getMessage(),
            'file' => basename($e->getFile()),
            'line' => $e->getLine(),
            'type' => class_basename($e)
        ];

        Log::error('Exception Details: ' . print_r($exceptionDetails, true));
        return $exceptionDetails['message'];
    } catch (Throwable $er) {
        Log::error(' logError crashed: ' . $er->getMessage());
    }
}


/* Log data to file for all the http requests */
function logData($response, $isRequest = false)
{
    if ($isRequest) Log::info('Request Details: ' . print_r($response, true));

    else Log::info('Response Details: ' . print_r($response, true));
}



function user($data)
{
    return User::query()->where('email', $data)
        ->orWhere('username', $data)
        ->first();
}






function generateVerificationCode(): int
{
    $verificationCode = mt_rand(100000, 999999);
    $existingCode = OtpManager::where('code', $verificationCode)->first();
    if ($existingCode) {
        $verificationCode = generateVerificationCode();
    }
    return $verificationCode;
}


/**
 * Determine if the app is running on the test environment or not.
 *
 * @return boolean
 */
function isTest()
{
    return config('config.app_env') == 'test';
}
/**
 * Determine if the app is running on the test environment or not.
 *
 * @return boolean
 */
function isDev()
{
    return config('config.app_env') == 'dev' || config('config.app_env') == 'development';
}


function removePunctuationAndWhitespace(string $input): string
{
    // Use preg_replace to remove punctuation and whitespace
    return preg_replace('/[^\w]/', '', $input);
}


function isBusinessUser(User $user)
{
    $isBusiness = 'business';
    $usertType = $user->with('userType');
    if ($usertType == $isBusiness) {
        return true;
    }
    return false;
}


/**
 * Dynamically update any model instance using a single or arrays of conditions
 * @param Model $model the model to be updated
 *@param string $column the field in the database to be used for the query
 *@param mixed $values the matching value to check for before carrying out the update
 *@param array $data The data/fields to be updated
 */
function updateModel(Model $model, string $column, mixed $values, array $data): mixed
{
    try {
        $query = is_array($values)
            ? $model::query()->whereIn($column, $values)
            : $model::query()->where($column, $values);
        $query->update($data);
        return $query->first();
    } catch (Exception $e) {
        logError($e);
        return null;
    }
}


function convertFileToAssociativeArray($filePath)
{
    $result = [];

    // Open the file for reading
    $file = fopen($filePath, 'r');
    if ($file) {
        // Loop through each line in the file
        while (($line = fgets($file)) !== false) {
            // Remove any leading or trailing whitespace
            $line = trim($line);

            // Ignore empty lines
            if (empty($line)) {
                continue;
            }

            $data = explode(",", $line);
            array_shift($data);

            // Clean up extra quotes
            $data = array_map(function ($item) {
                return trim($item, "'");
            }, $data);

            // Add the data as an associative array
            $currency = str_replace(")", '', $data[5]);
            $currency = str_replace("'", '', $currency);
            $result[] = [
                'name' => str_replace("'", '', $data[0]),
                'code' => str_replace("'", '', $data[1]),
                'alpha3' => str_replace("'", '', $data[2]),
                'phone_code' => str_replace("'", '', $data[3]),
                'currency_code' => str_replace("'", '', $data[4]),
                'currency_name' => str_replace(")", '', $currency),
            ];
        }
        fclose($file);
    } else {
        // Error opening file
        echo "Unable to open file.";
    }

    return $result;
}

function gender()
{
    return ['male', 'female', 'others'];
}

function breakByCase(string $functionName)
{
    if (strpos($functionName, '_') !== false) {
        $text = str_replace('_', ' ', $functionName);
    } else {
        $text = preg_replace('/(?<!^)([A-Z])/', ' $1', $functionName);
    }

    $upper = strtoupper(substr($text, 0, 1)) . substr($text, 1, strlen($text) - 1);
    return $upper;
}

function breakByAnyCharacter(string $string, string $delimiters): string
{
    // Create a regex pattern that matches any of the delimiter characters
    $pattern = '/[' . preg_quote($delimiters, '/') . ']/';

    // Split the string by any of those characters
    $parts = preg_split($pattern, $string);

    // Capitalize each part
    $parts = array_map(function ($part) {
        return ucfirst(strtolower($part));
    }, $parts);

    // Join with space
    return implode(' ', $parts);
}


// function whereHasSample()
// {
//   $registeredVehicles = $this->vhRegistration
//     ->where('user_id', $userId)
//     ->when($request->v_type, fn($query) => $query->where('v_type', 'like', '%' . $request->v_type . '%'))
//     ->when($request->v_make, fn($query) => $query->where('v_make', $request->v_make))
//     ->when($request->cost_min || $request->cost_max, function ($query) use ($request) {
//         $query->whereHas('registrationCosts', function ($subQuery) use ($request) {
//             if ($request->cost_min) {
//                 $subQuery->where('cost', '>=', $request->cost_min);
//             }
//             if ($request->cost_max) {
//                 $subQuery->where('cost', '<=', $request->cost_max);
//             }
//         });
//     })
//     ->with(['registrationCosts'])
//     ->orderBy('created_at', 'desc')
//     ->get();

// }
