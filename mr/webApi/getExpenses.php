<?php
namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('_load.php');

$sessionData = checkToken();

$date1 = $_GET['date1'];
$date2 = $_GET['date2'];


$expensesSql = "
SELECT ex.`id`, date(`tarigi`) AS expenseDate, u.username AS operator, `tanxa`, ex.`comment` 

FROM `xarjebi` ex
LEFT JOIN users u ON ex.`distributor_id` = u.id
WHERE date(`tarigi`) >= '$date1' AND date(`tarigi`) <= '$date2' AND `regionID` = {$sessionData->regionID}

ORDER BY ex.`tarigi`
";


$data = [];
$result = mysqli_query($con, $expensesSql);
if ($result) {
    while ($rs = mysqli_fetch_assoc($result)) {
        $data[] = $rs;
    }
} else {
    $response[ERROR_CODE] = 428;
    $response[ERROR_TEXT] = "server error";
}

$response[DATA] = $data;

echo json_encode($response);