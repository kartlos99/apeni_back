<?php

// ---------- shedegebi tviurad ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('andr_app_links/connection.php');

$yy = 0;


$sql = "SELECT
YEAR(b.tarigi) AS weli,
MONTH(b.tarigi) AS tve,
    l.dasaxeleba,
    SUM(
        (
            b.kasri30 * 30 + b.kasri50 * 50
        ) * b.ert_fasi
    ) AS pr,
    SUM(
        b.kasri30 * 30 + b.kasri50 * 50
    ) AS lt,
    SUM(kasri30) AS k30,
    SUM(kasri50) AS k50,
    l.color
FROM
    `beerinput` AS b
LEFT JOIN ludi AS l
ON
    b.ludis_id = l.id ";
    
if (isset($_GET['year'])){
    $yy = $_GET['year'];
    $sql = $sql."WHERE 
    YEAR(b.tarigi) = '$yy' ";    
}

$sql = $sql."GROUP BY
	YEAR(b.tarigi), 
    MONTH(b.tarigi),    
    dasaxeleba
    order by weli, tve
    " ;
    
$arr = array();
$result = $con->query($sql);
    
while($rs = mysqli_fetch_assoc($result)) {
    $arr[] = $rs;
}
//echo $sql;
echo json_encode($arr);

mysqli_close($con);
?>