<?php

namespace Apeni\JWT;
use DataProvider;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkToken();
$clientID = $_GET["clientID"];
$regionId = $sessionData->regionID;
$dataProvider = new DataProvider($con);

const PRICE_REGION_SCOPED_KEY = "regionScopePrice";
const PAYED_REGION_SCOPED_KEY = "regionScopePay";

// region doesn't matter for debt
$sql = "SELECT dbt.*, ifnull(cr.needCleaning, 0) AS needCleaning, ifnull(cr.passDays, 0) AS passDays FROM `debt_by_customer` dbt
LEFT JOIN cleaningreport cr
ON dbt.`clientID` = cr.clientID 
WHERE dbt.`clientID` = $clientID";

$result = mysqli_query($con, $sql);

$regionScopedDebtSql = "
SELECT 
ROUND((
    SELECT
		 ifnull( SUM(sales.count * barrels.volume * sales.unitPrice), 0)
	FROM `sales` 
	LEFT JOIN barrels ON barrels.id = sales.canTypeID
    WHERE sales.clientID = $clientID AND sales.regionID = $regionId
) 
+
(
	SELECT 
    	ifnull(SUM(bottle_sales.price * bottle_sales.count), 0)
    FROM bottle_sales
    WHERE bottle_sales.clientID = $clientID AND bottle_sales.regionID = $regionId
), 2) AS `price`,
(
    SELECT round(ifnull(SUM(tanxa),0),2) FROM moneyoutput
	WHERE moneyoutput.obieqtis_id = $clientID AND moneyoutput.regionID = $regionId
) AS `payed` ";

$regionScopedResult = mysqli_query($con, $regionScopedDebtSql);

if ($result) {
    $dataArr = mysqli_fetch_assoc($result);
    $dataArr['barrels'] = getBarrelsBalanceList($con, $clientID);
    $dataArr['availableRegions'] = $dataProvider->getAvailableRegionsForCustomer($clientID);
    if ($regionScopedResult) {
        $rData = mysqli_fetch_assoc($regionScopedResult);
        $dataArr[PRICE_REGION_SCOPED_KEY] = $rData['price'];
        $dataArr[PAYED_REGION_SCOPED_KEY] = $rData['payed'];
    }
    $response[DATA] = $dataArr;
} else {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = "can't get debt!";
    $response[ERROR_CODE] = ER_CODE_NOT_FOUNT;
}

echo json_encode($response);

mysqli_close($con);


function getBarrelsBalanceList($dbConn, $clientID = 0): array
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