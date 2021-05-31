<?php
namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');
checkToken();
//$versionControl = new VersionControl($con);

$sql = "SELECT * FROM `versionflow` WHERE 1";

$result = mysqli_query($con, $sql);

if ($result) {
    $response[DATA] = mysqli_fetch_assoc($result);
} else {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = "can't get table versions!";
    $response[ERROR_CODE] = ER_CODE_VCS;
}

echo json_encode($response);

mysqli_close($con);