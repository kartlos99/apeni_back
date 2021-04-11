<?php
namespace Apeni\JWT;
// ---------- gadascem dRes, gibrunebs shekveTebs ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
checkToken();

$orderID = $_GET["orderID"];


$sqlOrderHistory =
    "SELECT * FROM `orders_history` WHERE `ID` = $orderID
UNION
SELECT 0, o.* FROM `orders` o WHERE `ID` = $orderID;";

$sqlOrderItemHistory =
    "SELECT * FROM `order_items_history` WHERE orderID = $orderID
UNION
SELECT 0, oit.*, 0 FROM `order_items` oit WHERE orderID = $orderID;";

$orders = [];
$result = mysqli_query($con, $sqlOrderHistory);
while ($rs = mysqli_fetch_assoc($result)) {
    $orders[] = $rs;
}

$orderItems = [];
$result = mysqli_query($con, $sqlOrderItemHistory);
while ($rs = mysqli_fetch_assoc($result)) {
    $orderItems[] = $rs;
}

foreach ($orders as $i => $order) {
    $itmList = [];

    array_filter($orderItems, function ($orderItem) {
        global $itmList;
        global $order;
        if ($orderItem['modifyDate'] == $order['modifyDate']) {
            $itmList[] = $orderItem;
            return true;
        }
        return false;
    });

    $orders[$i]['items'] = $itmList;
}

$response[DATA] = $orders;

echo json_encode($response);

mysqli_close($con);