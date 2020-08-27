<?php
namespace Apeni\JWT;
use VersionControl;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
checkToken();

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$postData = json_decode($json);

$response[DATA] = "";

$user = $postData->user;

if ($user->id == "") {
    $sql =
        "INSERT INTO `users` " .
        "(`username`, `pass`, `name`, `type`, `maker`, `tel`, `adress`, `comment`, `reg_date`) " .
        "VALUES " .
        "('$user->username', '$postData->password', '$user->name', '$user->type', '$user->maker', '$user->tel', '$user->adress', '$user->comment', '$timeOnServer')";
} else {

    $setPass = $postData->changePass ? "`pass`='$postData->password', " : "";

    $sql =
        "UPDATE `users` SET " .
        "`username`= '$user->username', " .
        $setPass .
        "`type`=$user->type, " .
        "`maker`=$user->maker, " .
        "`name` = '$user->name', " .
        "`adress`= '$user->adress', " .
        "`tel`='$user->tel', " .
        "`comment`='$user->comment' " .
        "WHERE" .
        "  `users`.`id` = $user->id ";
}

if (mysqli_query($con, $sql)) {
    $response[DATA] = $user->id == "" ? mysqli_insert_id($con) : $user->id;
    $vc = new VersionControl($con);
    $vc->updateVersionFor(USER_VCS);
} else {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = mysqli_error($con);
    $response[ERROR_CODE] = mysqli_errno($con);
}

echo json_encode($response);

//$response[DATA] = $sql;
// die json_encode($response);