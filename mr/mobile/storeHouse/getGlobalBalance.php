<?php

namespace Apeni\JWT;

use DataProvider;
use QueryHelper;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkToken();
$dataProvider = new DataProvider($con);
$queryHelper = new QueryHelper();

if (isset($_GET["date"])) {
    $date = $_GET["date"];
} else {
    $date = $dateOnServer;
}

$response[DATA] = $dataProvider->sqlToArray($queryHelper->queryGlobalStoreBalance($date));

echo json_encode($response);

mysqli_close($con);