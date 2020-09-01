<?php

namespace Apeni\JWT;
use OrderHelper;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('_load.php');

checkToken();

$receivedDate = $_GET["date"];

$orders = [];
$orderHelper = new OrderHelper($con);

$sql = "
SELECT o.*, di.code AS orderStatus, di.valueText AS statusName, cl.dasaxeleba AS client, u.username AS distr FROM `orders` o
LEFT JOIN dictionary_items di ON di.id = o.orderStatusID
LEFT JOIN (
    SELECT date(saleDate) AS dt, orderID FROM `sales` 
	GROUP BY orderID
) s ON s.orderID = o.ID
LEFT JOIN obieqtebi cl ON cl.id = o.clientID
LEFT JOIN users u ON u.id = o.distributorID
WHERE 
    (( date(`orderDate`) = '$receivedDate' OR di.code = 'order_active' ) 
    AND di.code <> 'order_deleted' 
    AND date(`orderDate`) <= '$receivedDate') 
    OR date(s.dt) = '$receivedDate'";

$result = mysqli_query($con, $sql);
while ($rs = mysqli_fetch_assoc($result)) {
    $orders[] = $rs;
}

if (count($orders) > 0) {
    $response[DATA] = $orderHelper->attachItemsToOrder($orders);
} else {
    $response[DATA] = $orders;
}


echo json_encode($response);

mysqli_close($con);