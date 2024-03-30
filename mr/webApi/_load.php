<?php

const HOME_SERVER_NAME = "localhost";
const REAL_SERVER_NAME = "apeni.ge";
const TEST_SERVER_NAME = "test.apeni.ge";

$mainDIR = "";
$appConfigFile = "";

switch ($_SERVER['SERVER_NAME']) {
    case REAL_SERVER_NAME:
        $mainDIR = "/home/apenige2/public_html";
        $appConfigFile = "/home/apenige2/app_config/mobile_mr.php";
        break;
    case TEST_SERVER_NAME:
        $mainDIR = "/home/apenige2/public_html/test.apeni.ge";
        $appConfigFile = "/home/apenige2/app_config/test_mr.php";
        break;
    default:
        $mainDIR = "/xampp/htdocs/apeni.localhost.com";
        $appConfigFile = "/xampp/htdocs/app_config/mobile_tb.php";
        break;
}

require_once($appConfigFile);

require_once($mainDIR . '/mr/common_data.php');
require_once($mainDIR . '/mr/common_func.php');
require_once($mainDIR . '/mr/common_class.php');

include_once('../../jwt/JWT.php');
include_once('../../jwt/extension.php');

