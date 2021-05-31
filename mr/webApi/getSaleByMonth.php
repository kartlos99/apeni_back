<?php

namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('_load.php');

checkToken();

$year = isset($_GET['year']) ? $_GET['year'] : 2020;

$sqlSales = "SELECT 
concat(YEAR(s.saleDate), MONTH(s.saleDate), s.beerID) AS ID,
YEAR(s.saleDate) AS weli,
MONTH(s.saleDate) AS tve,
l.dasaxeleba AS beerName,
l.color,
l.id AS beerID,
round(sum(s.count * k.litraji * s.unitPrice), 2) AS price,
sum(s.count * k.litraji) AS liter
FROM `sales` s

LEFT JOIN ludi AS l
ON  s.beerID = l.id

LEFT JOIN kasri AS k
ON  s.canTypeID = k.id

WHERE YEAR(s.saleDate) = $year    

GROUP BY
	YEAR(s.saleDate), 
    MONTH(s.saleDate),    
    s.beerID
    
ORDER BY YEAR(s.saleDate), MONTH(s.saleDate), l.sortValue";

$sql = "
SELECT 
concat(YEAR(s.saleDate), MONTH(s.saleDate), s.beerID) AS sID,
YEAR(s.saleDate) AS weli,
MONTH(s.saleDate) AS tve,
l.dasaxeleba AS beerName,
sum(s.count * k.litraji) AS liter,
sum(s.count) AS canCount, 
k.dasaxeleba AS canType
FROM `sales` s

LEFT JOIN ludi AS l
ON  s.beerID = l.id

LEFT JOIN kasri AS k
ON  s.canTypeID = k.id

WHERE YEAR(s.saleDate) = $year    

GROUP BY
	YEAR(s.saleDate), 
    MONTH(s.saleDate),    
    l.dasaxeleba,
    s.canTypeID
    
ORDER BY YEAR(s.saleDate), MONTH(s.saleDate), l.sortValue, k.sortValue;
";

$sqlMoneyOutput = "
SELECT
    YEAR(tarigi) AS weli,
    MONTH(tarigi) AS tve,
    SUM(tanxa) AS money
FROM
    `moneyoutput`
WHERE YEAR(tarigi) = $year    
GROUP BY
    YEAR(tarigi),
    MONTH(tarigi)";

$sales = [];
$result = mysqli_query($con, $sqlSales);
while ($rs = mysqli_fetch_assoc($result)) {
    $sales[] = $rs;
}
$detailSales = [];
$result = mysqli_query($con, $sql);
while ($rs = mysqli_fetch_assoc($result)) {
    $detailSales[] = $rs;
}
$moneyArr = [];
$result = mysqli_query($con, $sqlMoneyOutput);
while ($rs = mysqli_fetch_assoc($result)) {
    $moneyArr[] = $rs;
}

$arr = [];

foreach ($sales as $key => $item) {
    $arr[$item['tve']]['sales'][] = $item;
//    $arr["$key"] = $key;
}
//$response[DATA] = $arr;
//die (json_encode($response));

foreach ($arr as $mKey => $monthItem) {
    foreach ($monthItem['sales'] as $bKey => $beerItem) {
        foreach ($detailSales as $detail) {
            if ($beerItem['ID'] == $detail['sID'])
                $arr[$mKey]['sales'][$bKey]['barrels'][] = $detail;
        }

    }
}

foreach ($moneyArr as $moneyItem) {
    $arr[$moneyItem['tve']]['money'] = $moneyItem['money'];
}

ksort($arr, SORT_NUMERIC);

$response[DATA] = $arr;

echo json_encode($response);