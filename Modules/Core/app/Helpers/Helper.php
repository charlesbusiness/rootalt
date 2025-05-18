<?php

namespace Modules\Core\Helpers;

use Carbon\Carbon;


class Helper
{
    /**
     * Determine if the app is running on the test environment or not.
     *
     * @return boolean
     */
    public static function isTest()
    {
        return config('core.app_env') === 'test';
    }
    /**
     * Determine if the app is running on the test environment or not.
     *
     * @return boolean
     */
    public static function isDev()
    {
        return config('core.app_env') === 'dev';
    }

    /**
     * Determine if the incoming request is from middleware.
     *
     * @return bool
     */
    public static function requestIsFromMiddleware()
    {
        return request()->ip() === '';
    }

    /**
     * Generate random unique reference.
     *
     * @return string
     */
    public static function reference()
    {
        return 'ITEX-' . Carbon::now()->format('YmdHisu') . mt_rand(10000, 99999);
    }

    /**
     * Generate random unique reference.
     *
     * @return string
     */
    public static function shortRef()
    {
        $string = 'ITEX-' . mt_rand(1, 100) . Carbon::now()->format('YmdHisu');
        if (strlen($string) > 16) {
            $string = substr($string, 0, 16);
        }
        return $string;
    }

    /**
     * Cast boolean literal to tinyint.
     *
     * @param string $value
     * @return bool
     */
    public static function toBool($value)
    {
        return $value == 'true' ? 1 : 0;
    }

    /**
     * Get all available products.
     *
     * @return array
     */
    public static function products()
    {
        return [
            'transfer',
            'vtu',
        ];
    }

    /**
     * Get all passwords allowed to make administrative actions on the system.
     *
     * @return array
     */
    public static function adminPasswords()
    {
        return explode(',', config('app.admin_passwords'));
    }

    /**
     * Convert the given xml string to an array.
     *
     * @param string $xml
     * @return array
     */
    public static function xmlToArray($xml)
    {
        $fileContents = str_replace(array("\n", "\r", "\t"), '', $xml);

        $fileContents = trim(str_replace('"', "'", $fileContents));

        $simpleXml = simplexml_load_string($fileContents);

        $json = json_encode($simpleXml);

        return json_decode($json, true);
    }

    /**
     * Convert the given xml string to an array.
     *
     * @param array $data
     * @param string $root
     * @return string
     */
    public static function arrayToXml($data): string
    {
        $string = '';

        foreach ($data as $key => $value) {
            $string .= "<{$key}>";

            if (is_array($value)) {
                $string .= static::arrayToXml($value);
            } else {
                $string .= $value;
            }

            $string .= "</{$key}>";
        }

        return $string;
    }



    /**
     * Convert the given  timestamp to date instance.
     *
     * @param string $date
     * @return string
     */
    public static function timeStampToDate(string $time = null): string
    {
        return $time ? date('Y-m-d H:i:s', $time / 1000) : date('Y-m-d H:i:s', time());
    }

    /**
     * Convert the given  timestamp to date instance.
     *
     * @param string $date
     * @return mixed
     */
    public static function dateTotimeStamp($date)
    {

        $date = Carbon::parse($date);

        return $date->timestamp * 1000;
    }

    public static function extractNumbericValue(string $str)
    {
        // Define the regex pattern to match numbers (both integers and decimals)
        $pattern = '/\d+(\.\d+)?/';

        // Use preg_match_all to find all matches of the pattern in the string
        preg_match($pattern, $str, $matches);
        return $matches[0];
       
    }
}
