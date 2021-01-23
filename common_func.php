<?php
namespace Apeni\JWT;

use DomainException;
use Exception;
use ExpiredException;
use UnexpectedValueException;

function dieWithError($code, $text) {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = $text;
    $response[ERROR_CODE] = $code;
    die(json_encode($response));
}

function checkToken() {
    // temporary, while testing api
    return [];
    $token = getBearerToken();

    try {

        $dataPayload = JWT::decode($token, SECRET_KEY, ['HS256']);

        return json_encode($dataPayload);

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