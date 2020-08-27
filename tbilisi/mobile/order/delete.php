<?php
namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
checkToken();

$json = file_get_contents('php://input');
$postData = json_decode($json);

$deleteSql = "UPDATE `orders` SET `orderStatusID` = " . ORDER_STATUS_DELETED . "
WHERE ID = " . $postData->orderID;


if (!mysqli_query($con, $deleteSql)) {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = mysqli_error($con);
    $response[ERROR_CODE] = mysqli_errno($con);
}


echo json_encode($response);

// $response[DATA] = $sql;
// die(json_encode($response));