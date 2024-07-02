<?php

namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('_load.php');

const YEAR = "year";
const MONTH = "month";
const BOTTLE_INPUT = "BOTTLE_INPUT";


$sessionData = checkToken();

$year = $_GET['year'] ?? 2020;

$sql = [
    BOTTLE_INPUT => "SELECT 
concat(YEAR(s.`inputDate`), MONTH(s.`inputDate`), s.`bottleID`) AS ID,
YEAR(s.`inputDate`) AS `year`,
MONTH(s.`inputDate`) AS `month`,
`bottleID`,
b.name AS bottle,
SUM(`count`) AS amount,
SUM(`count` * b.actualVolume) AS liter
FROM `storehouse_bottle_input` s

LEFT JOIN bottles AS b
ON  s.`bottleID` = b.id

WHERE year(s.`inputDate`) = $year
AND regionID = {$sessionData->regionID}

GROUP BY
	YEAR(s.`inputDate`), 
    MONTH(s.`inputDate`),    
    s.`bottleID`
    
ORDER BY YEAR(s.`inputDate`), MONTH(s.`inputDate`), b.sortValue"
];

$queryResult = [];
$result = mysqli_query($con, $sql[BOTTLE_INPUT]);
while ($rs = mysqli_fetch_assoc($result)) {
    $queryResult[] = $rs;
}

$arr = [];
foreach ($queryResult as $item) {
    $arr[$item[MONTH]][] = $item;
}

$response[DATA] = $arr;

echo json_encode($response);