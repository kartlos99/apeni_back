<?php

// ---------- shedegebi tviurad M ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('andr_app_links/connection.php');

$yy = 2000;
if (isset($_GET['year'])){
    $yy = $_GET['year'];
}

$sql = "SELECT

MONTH(b.tarigi) AS tve,    
    SUM(
        (
            b.kasri30 * 30 + b.kasri50 * 50
        ) * b.ert_fasi
    ) AS pr,
    m.money
FROM
    `beerinput` AS b
LEFT JOIN (
    SELECT
    YEAR(tarigi) AS weli,
    MONTH(tarigi) AS tve,
    SUM(tanxa) AS money
FROM
    `moneyoutput`
WHERE
    YEAR(tarigi) = '$yy' AND obieqtis_id NOT IN(185, 187, 192)
GROUP BY
    YEAR(tarigi),
    MONTH(tarigi)
          ) AS m
ON
    MONTH(b.tarigi) = m.tve
WHERE
    YEAR(b.tarigi) = '$yy' AND obieqtis_id NOT IN(185, 187, 192)
GROUP BY
	YEAR(b.tarigi), 
    MONTH(b.tarigi) ";


$arr = array();
$result = $con->query($sql);
    
while($rs = mysqli_fetch_assoc($result)) {
    $arr[] = $rs;
}
//echo $sql;
echo json_encode($arr);

mysqli_close($con);
?>