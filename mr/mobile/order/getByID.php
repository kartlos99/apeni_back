<?php
namespace Apeni\JWT;
use OrderHelper;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
checkToken();

$receivedOrderID = $_GET["orderID"];

$orders = [];
$orderHelper = new OrderHelper($con);

$sql = "
SELECT o.`ID`, date(o.`orderDate`) AS orderDate, o.`orderStatusID`, o.`distributorID`, o.`clientID`, 
       o.`comment`, o.`sortValue`, o.`modifyDate`, o.`modifyUserID`, di.code AS orderStatus, 
  ifnull(cr.needCleaning, 0) AS needCleaning, ifnull(cr.passDays, 0) AS passDays, (SELECT COUNT(ID) FROM `orders_history` WHERE `ID` = o.id) AS isEdited 
FROM `orders` o
LEFT JOIN dictionary_items di ON di.id = o.orderStatusID
LEFT JOIN cleaningreport cr ON o.clientID = cr.clientID
WHERE o.ID = " . $receivedOrderID;

$result = mysqli_query($con, $sql);
while ($rs = mysqli_fetch_assoc($result)) {
    $orders[] = $rs;
}

if (count($orders) == 1) {
    $response[DATA] = $orderHelper->attachRegions($orderHelper->attachItemsToOrder($orders));
} else {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = "can't find order!";
    $response[ERROR_CODE] = ER_CODE_NOT_FOUNT;
}

echo json_encode($response);

mysqli_close($con);