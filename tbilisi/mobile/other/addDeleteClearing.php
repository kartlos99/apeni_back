<?php
namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkToken();

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$postData = json_decode($json);

$response[DATA] = "";

if ($postData->recordID == "0") { // axali chanaweri

    $sql =
        "INSERT INTO `gawmenda` (`regionID`, `obieqtis_id`, `distributor_id`, `tarigi`) " .
        "VALUES ('$sessionData->regionID', '$postData->clientID', '$postData->distributorID', '$timeOnServer')";
    $doneText = "ჩანაწერი დაემატა!";

} else { // delete

    $sql = "DELETE from gawmenda WHERE id = $postData->recordID ";
    $doneText = "ჩანაწერი წაიშალა!";
}

if (mysqli_query($con, $sql)) {
    $response[DATA] = $doneText;
} else {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = mysqli_error($con);
    $response[ERROR_CODE] = mysqli_errno($con);
}

echo json_encode($response);

//$response[DATA] = $sql;
// die json_encode($response);