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
    k_in, 
    k_out, 
    (SELECT
        sum(k_in-k_out) 
    FROM 
        `amonaweri_barrel` a 
    WHERE 
        a.tarigi <= b.tarigi
        AND
        obieqtis_id = $clientID
        ) AS `bal`,
    id,
    comment
    FROM `amonaweri_barrel` b
WHERE 
    obieqtis_id = $clientID 
ORDER by b.tarigi DESC
LIMIT $offset, $pageSize ";

$totalPagesSql = "SELECT count(*) FROM `amonaweri_barrel` b WHERE obieqtis_id = $clientID";
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