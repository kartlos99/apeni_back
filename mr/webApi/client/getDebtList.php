<?php

namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

session_start();
require_once "../../../mr/_webLoad.php";

if (!isset($_SESSION['username'])) {
    $url = "http" . ((!empty($_SERVER['HTTPS'])) ? "s" : "") . "://" . $_SERVER['SERVER_NAME'] . $folder . "/login.php";
    header("Location: $url");
}

require_once('../../../commonWeb/Exporter.php');

use Exporter;

// region doesn't matter for debt
$sql = "SELECT dbt.clientID, dbt.clientName, dbt.price - dbt.payed AS moneyBalance  
FROM `clients_debt` dbt
LEFT JOIN customer c
ON dbt.`clientID` = c.ID 
WHERE `C`.`active` = 1";

$moneyDebtResult = mysqli_query($con, $sql);

$arr = [];
if ($moneyDebtResult) {
    while ($rs = mysqli_fetch_assoc($moneyDebtResult)) {
        $barrelsResult = getBarrelsBalanceList($con, $rs['clientID']);
        foreach ($barrelsResult as $item) {
            $rs[$item['dasaxeleba']] = $item['balance'];
        }
        $arr[] = $rs;
    }
    $response[DATA] = $arr;
} else {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = "can't get debt!";
    $response[ERROR_CODE] = ER_CODE_NOT_FOUNT;
    echo json_encode($response);
}

$columns = ["clientID", "clientName", "moneyBalance", "50იანი", "30იანი", "20იანი", "10იანი"];
$exporter = new Exporter();
$exporter->exportData($columns, $arr, "clientsDebt_" . $dateOnServer);

function getBarrelsBalanceList($dbConn, $clientID = 0): array
{
    $sqlQuery = "CALL getBarrelBalanceByID($clientID, 0);";
    $barrels = [];
    $result = mysqli_query($dbConn, $sqlQuery);
    while ($rs = mysqli_fetch_assoc($result)) {
        $barrels[] = $rs;
    }
    $result->close();
    $dbConn->next_result();
    return $barrels;
}