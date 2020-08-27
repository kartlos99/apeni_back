<?php
namespace Apeni\JWT;
use VersionControl;
// ---------- chanaweris washla (3 in 1) ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
checkToken();
//$currTime = date("Y-m-d H:i:s", time()+4*3600);

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$data = json_decode($json);

$id = $data->recordID;
$table = $data->table;
$userID = $data->userID;

$vc = new VersionControl($con);


if ($id > 0) {
    if ($table == "mitana") {
        $sql = "SELECT remove_sale($id, $userID)";
    }

    if ($table == "kout") {
        $sql = "SELECT remove_barrel($id, $userID) ";
    }

    if ($table == "mout") {
        $sql = "SELECT remove_money($id, $userID) ";
    }

//    if ($table == "order") {
//        $sql = "SELECT remove_order($id, $userID) ";
//    }

    if ($table == "users") {
        $sql = "UPDATE users SET active = 0 WHERE id = $id ";
        $vc->updateVersionFor(USER_VCS);
    }

    if ($table == "sawyobi_in") {
        $sql = "DELETE FROM sawyobi_in WHERE id = $id ";
    }

    if ($table == "sawyobi_out") {
        $sql = "DELETE FROM sawyobi_out WHERE id = $id ";
    }

    if ($table == "xarjebi") {
        $sql = "DELETE FROM xarjebi WHERE id = $id ";
    }

    if (!mysqli_query($con, $sql)) {
        $response[SUCCESS] = false;
        $response[ERROR_TEXT] = "Could not able to execute $sql " . mysqli_error($con);
    }
} else {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = "ID error: id = $id";
}

echo json_encode($response);

mysqli_close($con);
