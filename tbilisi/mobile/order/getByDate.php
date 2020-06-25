<?php

// ---------- gadascem dRes, gibrunebs shekveTebs ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');

$receivedDate = $_GET["date"];

$orders = [];
$orderHelper = new OrderHelper($con);

$sql = "
SELECT o.*, di.code AS orderStatus FROM `orders` o
LEFT JOIN dictionary_items di ON di.id = o.orderStatusID
WHERE 
    ( date(`orderDate`) = '$receivedDate' OR di.code = 'order_active' ) 
    AND di.code <> 'order_deleted' 
    AND date(`orderDate`) <= '$receivedDate'";

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