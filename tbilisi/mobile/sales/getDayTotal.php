<?php
namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
checkToken();

$receivedDate = $_GET["tarigi"];
$distrId = $_GET["distrid"];

$barrelFilterByDistr = $distrId == 0 ? "" : " AND distributorID = '$distrId' ";

$sqlMoney = "SELECT `paymentType`, round(IFNULL(sum(tanxa),0),2) AS amount FROM `moneyoutput` WHERE DATE(tarigi) = '$receivedDate'
 GROUP BY `paymentType`";
$sqlBarrelOutput = "
SELECT canTypeID, SUM(backCount) AS backCount, SUM(saleCount) as saleCount from ( SELECT
    `canTypeID`,
    `count` AS backCount,
    0 as saleCount
FROM
    `barrel_output`
WHERE
    DATE(`outputDate`) = '$receivedDate' $barrelFilterByDistr
UNION
SELECT
    `canTypeID`,
    0 AS backCount,
    `count` AS saleCount
FROM
    `sales`
WHERE
    DATE(`saleDate`) = '$receivedDate' $barrelFilterByDistr
    ) a
GROUP by `canTypeID`
";

$sqlXarji = "SELECT * FROM `xarjebi` WHERE DATE(tarigi) = '$receivedDate'";


$sqlSale = "
SELECT
    l.dasaxeleba AS beerName,
    ROUND( SUM( s.count * s.unitPrice * k.litraji ),  2 ) AS price,
    SUM( s.count * k.litraji) AS litraji
FROM
    `sales` AS s
LEFT JOIN ludi AS l ON  s.beerID = l.id
LEFT JOIN kasri AS k ON k.id = s.canTypeID
WHERE
    DATE(s.saleDate) = '$receivedDate'
";

$grouping = " GROUP BY s.beerID ";

if ($distrId == 0) {
    $sqlSale .= $grouping;
} else {
    // konkretuli distributori .....
    $sqlSale .= " AND s.distributorID = '$distrId' " . $grouping;

    $sqlMoney .= " AND distributor_id = '$distrId' ";

    $sqlXarji .= " AND `distributor_id` = $distrId";
}

$saleArr = [];
$saleResult = mysqli_query($con, $sqlSale);
while ($rs = mysqli_fetch_assoc($saleResult)) {
    $saleArr[] = $rs;
}

$moneyArr = [];
$moneyResult = mysqli_query($con, $sqlMoney);
while ($rs = mysqli_fetch_assoc($moneyResult)) {
    $moneyArr[] = $rs;
}

$barrelArr = [];
$barrelResult = mysqli_query($con, $sqlBarrelOutput);
while ($rs = mysqli_fetch_assoc($barrelResult)) {
    $barrelArr[] = $rs;
}

$xarjArr = [];
$xarjResult = mysqli_query($con, $sqlXarji);
while ($rs = mysqli_fetch_assoc($xarjResult)) {
    $xarjArr[] = $rs;
}

$resultArr = [];
$resultArr['sale'] = $saleArr;
$resultArr['takenMoney'] = $moneyArr;
$resultArr['barrels'] = $barrelArr;
$resultArr[XARJI] = $xarjArr;

$response[DATA] = $resultArr;

echo json_encode($response);

mysqli_close($con);