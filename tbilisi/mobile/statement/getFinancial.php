<?php
namespace Apeni\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkToken();

$clientID = $_GET["clientID"];
$offset = isset($_GET["offset"]) ? $_GET["offset"] : 0;
$pageSize = 100;

$sql = "
SELECT
    DATE_FORMAT(tarigi, '%Y-%m-%d %H:%i') AS dt,
    pr,
    pay,
    (SELECT sum(pr-pay) FROM `amonaweri_money` a
    WHERE
        a.tarigi <= b.tarigi
        AND
        clientID = $clientID) AS `bal`,
    id,
    comment
    FROM `amonaweri_money` b
WHERE
    clientID = $clientID 
ORDER by b.tarigi DESC 
LIMIT $offset, $pageSize";

$totalPagesSql = "SELECT count(*) FROM `amonaweri_money` b WHERE clientID = $clientID";
$resultTotalCount = mysqli_query($con, $totalPagesSql);
$totalRowCount = mysqli_fetch_array($resultTotalCount)[0];

$arr = [];
$result = mysqli_query($con, $sql);

while($rs = mysqli_fetch_assoc($result)) {
    $arr[] = $rs;
}

$response[DATA]['totalCount'] = $totalRowCount;
$response[DATA]['list'] = $arr;

echo json_encode($response);

mysqli_close($con);