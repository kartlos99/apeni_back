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
    // return [];
    $token = getBearerToken();
    $regionID = getRegion();
    if (!is_numeric($regionID) || $regionID == "0")
        dieWithError(409, "no region set!");

    try {

        $dataPayload = JWT::decode($token, SECRET_KEY, ['HS256']);
        $dataPayload->{'regionID'} = $regionID;
        return $dataPayload;

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

function hasOwnStorage($dbConn, $regionID)
{
    $rg = mysqli_fetch_assoc(mysqli_query($dbConn, "SELECT `ownStorage` FROM `regions` WHERE `ID`=$regionID"));
    return $rg['ownStorage'] == 1;
}