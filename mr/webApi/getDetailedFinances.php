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
    $filterByCustomer = " AND m.`obieqtis_id` = " . $_GET['customerID'];

$sql = "SELECT 
    c.id, 
    c.dasaxeleba, 
    `distributor_id`, 
    u.username AS distributor, 
    `paymentType`, 
    round( `tanxa`, 2) AS amount, 
    m.`tarigi` 
FROM `moneyoutput` m
LEFT JOIN customer c ON c.id = m.`obieqtis_id`
LEFT JOIN users u ON u.id = m.`distributor_id`

WHERE date(`tarigi`) BETWEEN '$date1' AND '$date2' 
AND m.`regionID` = {$sessionData->regionID}
$filterByCustomer
ORDER by m.`tarigi` DESC, `distributor_id`, `paymentType`";

$customers = [];
$result = mysqli_query($con, $sql);
if (mysqli_num_rows($result) > 0)
    while ($rs = mysqli_fetch_assoc($result)) {
        $customers[] = $rs;
    }

$response[DATA] = $customers;

echo json_encode($response);