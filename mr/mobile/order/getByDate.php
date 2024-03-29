<?php
namespace Apeni\JWT;
// ---------- gadascem dRes, gibrunebs shekveTebs ----------

use OrderHelper;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkToken();

$receivedDate = $_GET["date"];

$orders = [];
$orderHelper = new OrderHelper($con);

$sql = "
SELECT o.*, di.code AS orderStatus , ifnull(cr.needCleaning, 0) AS needCleaning, ifnull(cr.passDays, 0) AS passDays,
       (SELECT COUNT(ID) FROM `orders_history` WHERE `ID` = o.id) AS isEdited
FROM `orders` o
LEFT JOIN dictionary_items di ON di.id = o.orderStatusID
LEFT JOIN (
    SELECT date(saleDate) AS dt, orderID FROM `sales` 
	GROUP BY orderID
) s ON s.orderID = o.ID
LEFT JOIN cleaningreport cr ON o.clientID = cr.clientID
WHERE 
    ((( date(`orderDate`) = '$receivedDate' OR di.code = 'order_active' ) 
--    AND di.code <> 'order_deleted' 
    AND date(`orderDate`) <= '$receivedDate') 
    OR date(s.dt) = '$receivedDate' OR o.ID IN (SELECT DISTINCT orderID FROM sales WHERE date(`saleDate`) = '$receivedDate'))
 and o.`regionID` = {$sessionData->regionID}
    ";

$result = mysqli_query($con, $sql);
while ($rs = mysqli_fetch_assoc($result)) {
    $orders[] = $rs;
}

if (count($orders) > 0) {
    $response[DATA] = $orderHelper->attachRegions($orderHelper->attachItemsToOrder($orders));
} else {
    $response[DATA] = $orders;
}


echo json_encode($response);

mysqli_close($con);