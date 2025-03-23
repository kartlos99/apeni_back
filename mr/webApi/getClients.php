<?php
namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('_load.php');

$sessionData = checkToken();

$date1 = $_GET['date1'];
$date2 = $_GET['date2'];

$filterByCustomer = "";

if (isset($_GET['customerID']) && $_GET['customerID'] > 0)
    $filterByCustomer = " AND o.id = " . $_GET['customerID'];

$sql = "SELECT
       s.clientID, 
       o.dasaxeleba AS clientName,
       s.beerID,
       b.name AS beerName,
SUM(s.count * k.volume) AS liter,
round(SUM(s.count * k.volume * s.unitPrice), 2) AS price,
b.color
from sales s 
LEFT JOIN $CUSTOMER_TB o ON o.id = s.clientID
LEFT JOIN beer b ON b.id = s.beerID
LEFT JOIN barrels k ON k.id = s.canTypeID
WHERE 
      date(s.saleDate) >= '$date1'
  AND date(s.saleDate) <= '$date2'
  AND o.active = 1
  AND regionID = {$sessionData->regionID}
$filterByCustomer
GROUP BY
s.clientID, s.beerID
ORDER BY liter DESC";

$sales = [];
$result = mysqli_query($con, $sql);
while ($rs = mysqli_fetch_assoc($result)) {
    $sales[] = $rs;
}

$response[DATA] = $sales;

echo json_encode($response);