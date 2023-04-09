<?php

namespace Apeni\JWT;

use DomainException;
use Exception;
use ExpiredException;
use stdClass;
use UnexpectedValueException;

const KEY_ERROR_MESSAGE = "errorMessage";
const KEY_ERROR_CODE = "errorCode";

function dieWithError($httpStatusCode = CUSTOM_HTTP_ERROR_CODE, $text = "un known error", $errorCode = null)
{
    http_response_code($httpStatusCode);
    die(json_encode([
        KEY_ERROR_MESSAGE => $text,
        KEY_ERROR_CODE => $errorCode
    ]));
}

function dieWithDefaultHttpError($text = "un known error", $errorCode = null) {
    dieWithError(CUSTOM_HTTP_ERROR_CODE, $text, $errorCode);
}

function checkToken()
{
    // temporary, while testing api
    $fakePayload = new stdClass();
    $fakePayload->{'username'} = "kartlos-test";
    $fakePayload->{'userID'} = 1;
    return $fakePayload;

    $token = getBearerToken();

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