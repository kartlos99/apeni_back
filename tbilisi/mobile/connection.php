<?php

require_once('/xampp/htdocs/app_config/mobile_tb.php');

require_once('/xampp/htdocs/apeni.localhost.com/common_data.php');

require_once('/xampp/htdocs/apeni.localhost.com/common_func.php');

function dieWithError($code, $text) {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = $text;
    $response[ERROR_CODE] = $code;
    die(json_encode($response));
}
