<?php

namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('_load.php');

const YEAR = "year";
const MONTH = "month";
const BARREL_INPUT_DETAIL = "detail";
const BARREL_INPUT = "BarrelInput";


$sessionData = checkToken();

$year = $_GET['year'] ?? 2020;

$sql1 = "
SELECT 
ifnull(c1.id, s1.id) AS r_id,
ifnull(c1.year, s1.year) AS year,
ifnull(c1.month, s1.month) AS month,
ifnull(c1.canTypeID, s1.barrelID) AS barrelId,
ifnull(c1.barrel, s1.barrel) AS barrel,
c1.countSum AS count_from_customer,
s1.countSum AS count_from_sh
FROM (SELECT * FROM `customer_empty_out` c WHERE c.year = $year) c1
LEFT JOIN (SELECT * FROM sh_empty_out s WHERE s.year = $year) s1 ON c1.ID = s1.ID
UNION
SELECT 
ifnull(c1.id, s1.id) AS r_id,
ifnull(c1.year, s1.year) AS year,
ifnull(c1.month, s1.month) AS month,
ifnull(c1.canTypeID, s1.barrelID) AS barrelId,
ifnull(c1.barrel, s1.barrel) AS barrel,
c1.countSum AS count_from_customer,
s1.countSum AS count_from_sh
FROM (SELECT * FROM `customer_empty_out` c WHERE c.year = $year) c1
RIGHT JOIN (SELECT * FROM sh_empty_out s WHERE s.year = $year) s1 ON c1.ID = s1.ID

ORDER BY r_id
";


$queryResult = [];
$result = mysqli_query($con, $sql1);
while ($rs = mysqli_fetch_assoc($result)) {
    $queryResult[] = $rs;
}

$arr = [];

foreach ($queryResult as $key => $item) {
    $arr[$item[MONTH]][] = $item;
}

$response[DATA] = $arr;

echo json_encode($response);