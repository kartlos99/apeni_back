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

$sql =
    "INSERT INTO `xarjebi`(`tarigi`, `distributor_id`, `tanxa`, `comment`) VALUES ( " .
    "'$postData->date', '$postData->distributorID', $postData->amount, '$postData->comment' )";

if(mysqli_query($con, $sql)){
    $response[DATA] = mysqli_insert_id($con);
}else{
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = mysqli_error($con);
    $response[ERROR_CODE] = mysqli_errno($con);
}

echo json_encode($response);

mysqli_close($con);