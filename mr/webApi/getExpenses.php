<?php

namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('_load.php');

require_once('../../commonWeb/Exporter.php');

use Exporter;

$forExport = isset($_GET['forExport']);
$date1 = $_GET['date1'];
$date2 = $_GET['date2'];

if ($forExport) {
    checkGivenToken($_GET["token"]);
    $regionID = $_GET['regionID'] ?? dieWithError(409, "no region set");
} else {
    $sessionData = checkToken();
    $regionID = $sessionData->regionID;
}

$expensesSql = "
SELECT ex.`id`, date(`tarigi`) AS expenseDate, u.username AS operator, ex.`comment`, `tanxa` 
FROM `xarjebi` ex
LEFT JOIN users u ON ex.`distributor_id` = u.id
WHERE date(`tarigi`) >= '$date1' AND date(`tarigi`) <= '$date2' AND `regionID` = $regionID
ORDER BY ex.`tarigi`
LIMIT 500
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

$dailyCashSql = "SELECT
    DATE(`tarigi`) AS 'payDay',
    ROUND(SUM(`tanxa`), 2) AS 'cash'
FROM
    `moneyoutput` m
WHERE
    date(`tarigi`) >= '$date1' AND date(`tarigi`) <= '$date2' AND `regionID` = $regionID AND `paymentType` = 1
GROUP BY
    DATE(`tarigi`)
ORDER BY
    DATE(`tarigi`)";

$cashArr = [];
$cashResult = mysqli_query($con, $dailyCashSql);
if ($cashResult) {
    while ($rs = mysqli_fetch_assoc($cashResult)) {
        $cashArr[] = $rs;
    }
} else {
    $response[ERROR_CODE] = 428;
    $response[ERROR_TEXT] = "server error";
}

$groupedExpanses = [];
foreach ($data as $item) {
    $groupedExpanses[$item['expenseDate']]['expenses'][] = $item;
}
$groupedCash = [];
foreach ($cashArr as $item) {
    $groupedCash[$item['payDay']] = $item['cash'];
}

foreach ($groupedExpanses as $key => $item) {
    $groupedExpanses[$key]['cash'] = $groupedCash[$key] ?? 0;
}

$response[DATA] = $groupedExpanses;

if ($forExport) {
    $columns = ["id", "თარიღი", "ოპერატორი", "კომენტარი", "თანხა ₾"];
    $exporter = new Exporter();
    $exporter->exportData($columns, $data, "expenses_$date1--$date2");
} else {
    echo json_encode($response);
}

mysqli_close($con);