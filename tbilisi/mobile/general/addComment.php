<?php
namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
checkToken();
// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$postData = json_decode($json);

$response[DATA] = "";

$sqlAddComment =
    "INSERT INTO `comments`(`comment`, `modifyDate`, `modifyUserID`) " .
    "VALUES ('$postData->comment', '$timeOnServer', '$postData->modifyUserID')";

if (mysqli_query($con, $sqlAddComment)) {
    $response[DATA] = "done";
} else {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = mysqli_error($con);
    $response[ERROR_CODE] = mysqli_errno($con);
}

echo json_encode($response);

//$response[DATA] = $sql;
// die json_encode($response);