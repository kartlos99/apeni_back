<?php
namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
checkToken();
$clientID = $_GET["clientID"];

$sql = "SELECT * FROM `clients_debt` WHERE `clientID` = $clientID";

$result = mysqli_query($con, $sql);

if ($result) {
    $response[DATA] = mysqli_fetch_assoc($result);
} else {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = "can't get debt!";
    $response[ERROR_CODE] = ER_CODE_NOT_FOUNT;
}

echo json_encode($response);

mysqli_close($con);