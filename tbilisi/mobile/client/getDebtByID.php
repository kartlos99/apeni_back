<?php
namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
checkToken();
$clientID = $_GET["clientID"];

$sql = "SELECT dbt.*, ifnull(cr.needCleaning, 0) AS needCleaning, ifnull(cr.passDays, 0) AS passDays FROM `clients_debt` dbt
LEFT JOIN cleaningreport cr
ON dbt.`clientID` = cr.clientID 
WHERE dbt.`clientID` = $clientID";

$result = mysqli_query($con, $sql);

if ($result) {
    $dataArr = mysqli_fetch_assoc($result);
    $dataArr['barrels'] = getBarrelsBalanceList($con, $clientID);
    $response[DATA] = $dataArr;
} else {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = "can't get debt!";
    $response[ERROR_CODE] = ER_CODE_NOT_FOUNT;
}

echo json_encode($response);

mysqli_close($con);


function getBarrelsBalanceList($dbConn, $clientID = 0)
{
    $sqlQuery = "CALL getBarrelBalanceByID($clientID, 0);";
    $arr = [];
    $result = mysqli_query($dbConn, $sqlQuery);
    while ($rs = mysqli_fetch_assoc($result)) {
        if ($rs['balance'] != 0)
            $arr[] = $rs;
    }
    $result->close();
    $dbConn->next_result();
    return $arr;
}