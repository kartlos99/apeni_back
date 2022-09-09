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

$sql = "SELECT id, dasaxeleba FROM `customer` c
WHERE
    c.`active`=1 AND c.id IN (
        SELECT DISTINCT clientID FROM `sales` s 
        WHERE s.saleDate BETWEEN '$date1' AND '$date2'
            AND s.regionID = {$sessionData->regionID}
    )
ORDER BY dasaxeleba";

$customers = [];
$result = mysqli_query($con, $sql);
while ($rs = mysqli_fetch_assoc($result)) {
    $customers[] = $rs;
}

$response[DATA] = $customers;

echo json_encode($response);