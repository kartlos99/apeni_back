<?php
//namespace Apeni\JWT;

class BeforeValidException extends \UnexpectedValueException
{
}

class SignatureInvalidException extends \UnexpectedValueException
{
}

class ExpiredException extends \UnexpectedValueException
{
}

function getAuthorizationHeader()
{
    $headers = null;
    if (isset($_SERVER["Authorization"])) {
        $headers = trim($_SERVER["Authorization"]);
    } else if (isset($_SERVER["HTTP_AUTORIZATION"])) {
        $headers = trim($_SERVER["HTTP_AUTORIZATION"]);
    } else if (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        $requestHeaders = array_combine(array_map('ucwords',
            array_keys($requestHeaders)),
            array_values($requestHeaders));
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    return $headers;
}

function getBearerToken()
{
    $headers = getAuthorizationHeader();
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
        return "";
    }
    return "";
}

function getRegion() {
    $REGION_KEY = "Region";
    $region = null;
    if (isset($_SERVER[$REGION_KEY])) {
        $region = trim($_SERVER[$REGION_KEY]);
    } else if (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        $requestHeaders = array_combine(array_map('ucwords',
            array_keys($requestHeaders)),
            array_values($requestHeaders));
        if (isset($requestHeaders[$REGION_KEY])) {
            $region = trim($requestHeaders[$REGION_KEY]);
        }
    }
    return $region;
}