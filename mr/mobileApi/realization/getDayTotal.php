<?php
namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
// $sessionData = checkToken();
$sessionData = (object)[
    "regionID" => 1,
    "userID" => 1
    ];

$receivedDate = $_GET["date"];
$distrId = $_GET["distributorId"];

$barrelFilterByDistr = $distrId == 0 ? "" : " AND distributorID = '$distrId' ";

$sqlMoney = "SELECT `paymentType`, round(IFNULL(sum(tanxa),0),2) AS amount FROM `moneyoutput` WHERE DATE(tarigi) = '$receivedDate' AND `regionID` = {$sessionData->regionID}";
$sqlBarrelOutput = "
SELECT canTypeID, SUM(backCount) AS backCount, SUM(saleCount) as saleCount from ( SELECT
    `canTypeID`,
    `count` AS backCount,
    0 as saleCount
FROM
    `barrel_output`
WHERE
    DATE(`outputDate`) = '$receivedDate' $barrelFilterByDistr AND `regionID` = {$sessionData->regionID}
UNION ALL
SELECT
    `canTypeID`,
    0 AS backCount,
    `count` AS saleCount
FROM
    `sales`
WHERE
    DATE(`saleDate`) = '$receivedDate' $barrelFilterByDistr AND `regionID` = {$sessionData->regionID}
    ) a
GROUP by `canTypeID`
";

$sqlXarji = "SELECT `id`, `regionID`, `tarigi` AS `date`, `distributor_id` AS `distributorID`, `tanxa` AS `amount`, `category`, `comment`
    FROM `xarjebi` WHERE DATE(tarigi) = '$receivedDate' AND `regionID` = {$sessionData->regionID}";


$sqlSale = "
SELECT
    b.name AS beerName,
    ROUND( SUM( s.count * s.unitPrice * k.litraji ),  2 ) AS price,
    SUM( s.count * k.litraji) AS litraji
FROM
    `sales` AS s
LEFT JOIN beer AS b ON  s.beerID = b.id
LEFT JOIN kasri AS k ON k.id = s.canTypeID
WHERE
    DATE(s.saleDate) = '$receivedDate' AND `regionID` = {$sessionData->regionID}
";

$sqlBottleSale = "SELECT 
	bs.`bottleID`,
    b.name,
    round(SUM(bs.`price` * bs.count), 2) AS `price`,
    SUM(bs.`count`) AS `count` 
FROM `bottle_sales` bs
LEFT JOIN bottles b ON b.id = bs.bottleID

WHERE 
    DATE(bs.saleDate) = '$receivedDate' AND `regionID` = {$sessionData->regionID}
";

$grouping = " GROUP BY s.beerID ";
$groupMoney = " GROUP BY `paymentType` ";
$groupBottleSale = "GROUP BY bs.bottleID ";

if ($distrId > 0) {
    // konkretuli distributori .....
    $sqlSale .= " AND s.distributorID = '$distrId' ";

    $sqlMoney .= " AND distributor_id = '$distrId' ";

    $sqlXarji .= " AND `distributor_id` = $distrId";
    
    $sqlBottleSale .= " AND bs.distributorID = $distrId ";
}

$sqlSale .= $grouping;
$sqlMoney .= $groupMoney;
$sqlBottleSale .= $groupBottleSale;

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

$expenseArr = [];
$xarjResult = mysqli_query($con, $sqlXarji);
while ($rs = mysqli_fetch_assoc($xarjResult)) {
    $expenseArr[] = $rs;
}

$bottleSaleArr = [];
$bottleResult = mysqli_query($con, $sqlBottleSale);
while ($rs = mysqli_fetch_assoc($bottleResult)) {
    $bottleSaleArr[] = $rs;
}

$resultArr = [];
$resultArr['sale'] = $saleArr;
$resultArr['takenMoney'] = $moneyArr;
$resultArr['barrels'] = $barrelArr;
$resultArr["expenses"] = $expenseArr;
$resultArr["bottleSale"] = $bottleSaleArr;

$response[DATA] = $resultArr;

// die( json_encode($sqlXarji));
echo json_encode($resultArr);

mysqli_close($con);