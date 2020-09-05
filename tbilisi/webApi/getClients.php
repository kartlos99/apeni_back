<?php
namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('_load.php');

checkToken();

$date1 = $_GET['date1'];
$date2 = $_GET['date2'];

$sql = "SELECT 
s.clientID, o.dasaxeleba AS clientName,
s.beerID, l.dasaxeleba AS beerName,
SUM(s.count * k.litraji) AS liter,
round(SUM(s.count * k.litraji * s.unitPrice), 2) AS price,
l.color
from sales s 
LEFT JOIN obieqtebi o ON o.id = s.clientID
LEFT JOIN ludi l ON l.id = s.beerID
LEFT JOIN kasri k ON k.id = s.canTypeID
WHERE 
s.saleDate BETWEEN '$date1' AND '$date2' AND 
o.active = 1
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