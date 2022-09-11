<?php
namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('_load.php');

$sessionData = checkToken();

$date1 = $_GET['date1'];
$date2 = $_GET['date2'];

$sql = "SELECT id, dasaxeleba FROM `customer` c
WHERE
    c.`active`=1 AND c.id IN (
        SELECT DISTINCT obieqtis_id FROM `moneyoutput` m 
        WHERE m.tarigi BETWEEN '$date1' AND '$date2'
            AND m.regionID = {$sessionData->regionID}
    )
ORDER BY dasaxeleba";

$customers = [];
$result = mysqli_query($con, $sql);
while ($rs = mysqli_fetch_assoc($result)) {
    $customers[] = $rs;
}

$response[DATA] = $customers;

echo json_encode($response);