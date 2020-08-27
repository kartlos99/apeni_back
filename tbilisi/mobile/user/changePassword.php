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

$sqlFindUser =
    "SELECT id, username, pass, active FROM `users` " .
    "WHERE `id` = '$postData->userID' AND active = 1";

$sqlUpdatePass =
    "UPDATE users SET `pass` = '$postData->newPass' " .
    "WHERE `id` = '$postData->userID'";

$findUserResult = mysqli_query($con, $sqlFindUser);

if ($findUserResult) {
    if (mysqli_num_rows($findUserResult) == 1) {
        $dbUserPass = mysqli_fetch_assoc($findUserResult)['pass'];
        if ($dbUserPass == $postData->oldPass) {
            mysqli_query($con, $sqlUpdatePass);
            $response[DATA] = "done";
        } else {
            $response[SUCCESS] = false;
            $response[ERROR_TEXT] = "პაროლი არასწორია!";
            $response[ERROR_CODE] = 1235;
        }

    } else {
        $response[SUCCESS] = false;
        $response[ERROR_TEXT] = "მომხმარებლის იდენტიფიკაცია ვერ მოხერხდა!";
        $response[ERROR_CODE] = 1234;
    }

} else  {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = mysqli_error($con);
    $response[ERROR_CODE] = mysqli_errno($con);
}

echo json_encode($response);

//$response[DATA] = $sql;
// die json_encode($response);