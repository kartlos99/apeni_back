<?php
namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

session_start();
require_once "../../../mr/_webLoad.php";
include_once('../../../jwt/JWT.php');
include_once('../../../jwt/extension.php');

$forExport = $_GET['forExport'] ?? false;

if ($forExport) {
    if (!isset($_SESSION['username'])) {
        $url = "http" . ((!empty($_SERVER['HTTPS'])) ? "s" : "") . "://" . $_SERVER['SERVER_NAME'] . $folder . "/login.php";
        header("Location: $url");
    }
    $regionID = $_GET['regionID'] ?? 0;
} else {
    $sessionData = checkToken();
    $regionID = $sessionData->regionID;
}

require_once('../../../commonWeb/Exporter.php');

use Exporter;

// region doesn't matter for debt
$sql = "SELECT dbt.clientID, dbt.clientName, round(dbt.price - dbt.payed, 2) AS moneyBalance  
FROM `debt_by_customer` dbt
LEFT JOIN customer c
ON dbt.`clientID` = c.ID 
WHERE `c`.`active` = 1
AND dbt.clientID IN (
    SELECT DISTINCT crm.customerID FROM customer_to_region_map crm
    WHERE crm.active = 1 and crm.regionID = $regionID
)
";

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

if ($forExport) {
    $columns = ["clientID", "clientName", "moneyBalance", "50იანი", "30იანი", "20იანი", "10იანი"];
    $exporter = new Exporter();
    $exporter->exportData($columns, $arr, "clientsDebt_" . $dateOnServer);
} else {
    echo json_encode($response);
}

mysqli_close($con);

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