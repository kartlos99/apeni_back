<?php

namespace Apeni\JWT;

use DomainException;
use Exception;
use ExpiredException;
use UnexpectedValueException;

function dieWithError($code, $text)
{
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = $text;
    $response[ERROR_CODE] = $code;
    die(json_encode($response));
}

function checkToken()
{
    // temporary, while testing api
//     return (object)["userID" => 15, "regionID" => 1, "userType" => 9];
    $token = getBearerToken();
    $regionID = getRegion();
    if (!is_numeric($regionID) || $regionID == "0")
        dieWithError(409, "no region set!");

    $dataPayload = checkGivenToken($token);
    $dataPayload->{'regionID'} = $regionID;
    return $dataPayload;
}

function checkGivenToken($token) {

    try {

        return JWT::decode($token, SECRET_KEY, ['HS256']);

    } catch (ExpiredException $e) {
        $errorText = $e->getMessage();
    } catch (UnexpectedValueException $exception) {
        $errorText = $exception->getMessage();
    } catch (DomainException $exception) {
        $errorText = $exception->getMessage();
    } catch (Exception $exception) {
        $errorText = $exception->getMessage();
    }

    if (!empty($errorText))
        dieWithError(401, $errorText);

    return [];
}

function hasOwnStorage($dbConn, $regionID): bool
{
    $rg = mysqli_fetch_assoc(mysqli_query($dbConn, "SELECT `ownStorage` FROM `regions` WHERE `ID`=$regionID"));
    return $rg['ownStorage'] == 1;
}

const errorMessage = "errorMessage";
const errorCode = "errorCode";

function throwHttpError($errorCode = 0, $errorText = "Unknown error.", $httpStatusCode = 422)
{
    // die( $errorText . $errorCode . $httpStatusCode11);
    http_response_code($httpStatusCode);
    die(json_encode([
        errorMessage => $errorText,
        errorCode => $errorCode
    ]));
}