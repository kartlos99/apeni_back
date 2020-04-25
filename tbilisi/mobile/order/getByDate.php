<?php

// ---------- gadascem dRes, gibrunebs shekveTebs ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');

//$currTime = date("Y-m-d H:i:s", time()+4*3600);


// time() funqcia gvibrnebs serveris mimdinare dros, chven vart +4 saati
$dges_server = date("Y-m-d H:i:s", time()+4*3600);  
$dges = $_GET["date"];


$sql=" SELECT * FROM `order_items` WHERE date(`modifyDate`) = '$dges' ";

$orderItems = [];
$result = mysqli_query($con, $sql);
    
while($rs = mysqli_fetch_assoc($result)) {
    $orderItems[] = $rs;
}

$sql=" SELECT * FROM `orders` WHERE date(`orderDate`) = '$dges' ";

$orders = [];
$result = mysqli_query($con, $sql);
    
while($rs = mysqli_fetch_assoc($result)) {
    $orders[] = $rs;
}


foreach($orders as $index => $order){
    $oItems = [];
    foreach($orderItems as $item){
        if($order['ID'] == $item['orderID']){
            $oItems[] = $item;
        }
    }
    $orders[$index]['items'] = $oItems;
}

$response[DATA] = $orders;

echo json_encode($response);

mysqli_close($con);