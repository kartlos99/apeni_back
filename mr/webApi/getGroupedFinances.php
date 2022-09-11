<?php
namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('_load.php');

$sessionData = checkToken();

$date1 = $_GET['date1'];
$date2 = $_GET['date2'];

$sql = "SELECT c.id, c.dasaxeleba, `distributor_id`, u.username AS distributor, `paymentType`, round(SUM(`tanxa`), 2) AS amount FROM `moneyoutput` m
LEFT JOIN customer c ON c.id = m.`obieqtis_id`
LEFT JOIN users u ON u.id = m.`distributor_id`
LEFT JOIN dictionary_items di ON di.id = m.`paymentType`
WHERE date(`tarigi`) BETWEEN '$date1' AND '$date2' AND m.`regionID` = {$sessionData->regionID}
GROUP BY `obieqtis_id`,`paymentType`,`distributor_id`
ORDER by `obieqtis_id`,`distributor_id`,`paymentType`";

$customers = [];
$result = mysqli_query($con, $sql);
while ($rs = mysqli_fetch_assoc($result)) {
    $customers[] = $rs;
}

$response[DATA] = $customers;

echo json_encode($response);