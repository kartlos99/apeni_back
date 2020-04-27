<?php

// ---------- gadascem dRes, gibrunebs shekveTebs ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');

$receivedDate = $_GET["date"];

$orders = [];

$sql = " SELECT * FROM `orders` WHERE date(`orderDate`) = '$receivedDate' ";

$result = mysqli_query($con, $sql);
while ($rs = mysqli_fetch_assoc($result)) {
    $orders[] = $rs;
}

$orderIDs = "";
foreach ($orders as $order) {
    $orderIDs .= $order['ID'] . ',';
}
$orderIDs = trim($orderIDs, ',');


$sql = " SELECT * FROM `order_items` WHERE `orderID` IN ($orderIDs) ";

$orderItems = [];
$result = mysqli_query($con, $sql);
while ($rs = mysqli_fetch_assoc($result)) {
    $orderItems[] = $rs;
}


$sql = "
SELECT `orderID`, `beerID`, `chek`,`canTypeID`, sum(`count`) AS `count` FROM `sales` 
WHERE `orderID` IN ($orderIDs)
GROUP BY `orderID`, `beerID`, `canTypeID`";

$sales = [];
$result = mysqli_query($con, $sql);
while ($rs = mysqli_fetch_assoc($result)) {
    $sales[] = $rs;
}


foreach ($orders as $index => $order) {
    $oItems = [];
    foreach ($orderItems as $item) {
        if ($order['ID'] == $item['orderID']) {
            $oItems[] = $item;
        }
    }
    $oSales = [];
    foreach ($sales as $item) {
        if ($order['ID'] == $item['orderID']) {
            $oSales[] = $item;
        }
    }

    $orders[$index]['items'] = $oItems;
    $orders[$index]['sales'] = $oSales;
}

$response[DATA] = $orders;

echo json_encode($response);

mysqli_close($con);