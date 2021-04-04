<?php
namespace Apeni\JWT;
use VersionControl;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkToken();

// Takes raw data from the request
$json = file_get_contents('php://input');
$postData = json_decode($json);

if ($sessionData->userType != USERTYPE_ADMIN)
    dieWithError(ER_CODE_NO_PERMISSION, ER_TEXT_NO_PERMISSION);

$sql = "SELECT dbt.*, ifnull(cr.needCleaning, 0) AS needCleaning FROM `clients_debt` dbt
LEFT JOIN cleaningreport cr
ON dbt.`clientID` = cr.clientID 
WHERE dbt.`clientID` = " . $postData->clientID;

$result = mysqli_query($con, $sql);

if ($result) {
    $debt = mysqli_fetch_assoc($result);
    if ($debt['barrel'] - $debt['barrelTakenBack'] > 0 || $debt['price'] - $debt['payed'] > 0.5) {
        dieWithError(ER_CODE_DEBT_ON_CLIENT, ER_TEXT_DEBT_ON_CLIENT);
    }
} else
    dieWithError(ER_CODE_CANT_CHECK_DEBT, ER_TEXT_CANT_CHECK_DEBT);

$sql = "UPDATE `obieqtebi` SET `active` = 0 WHERE `id` = " . $postData->clientID;

if (mysqli_query($con, $sql)) {
    $response[DATA] = SUCCESS;
    $vc = new VersionControl($con);
    $vc->updateVersionFor(CLIENT_VCS);
    $vc->updateVersionFor(PRICE_VCS);

} else {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = mysqli_error($con);
    $response[ERROR_CODE] = mysqli_errno($con);
}

echo json_encode($response);