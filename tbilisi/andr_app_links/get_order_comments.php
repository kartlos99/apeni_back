<?php

// ---------- get order_comments ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

// wina dgis chatvlit mogvaqvs info 00:00 dan
$timeForOrder = date("Y-m-d", time()+4*3600-24*3600);

$timeForMitana = date("Y-m-d", time()+4*3600-3*24*3600);

$sql = "
SELECT dasaxeleba, `comment`, op, MAX(let) from (
SELECT o.dasaxeleba, s.`comment`, 'O' AS op, (SELECT ifnull(MAX(modifyDate), s.tarigi) from shekvetebi_history h WHERE h.shekvetisID = s.id) AS let FROM `shekvetebi` s
LEFT JOIN $CUSTOMER_TB o 
ON o.id = s.`obieqtis_id`
WHERE date(s.`tarigi`) >= '$timeForOrder' AND s.`comment` <> ''

UNION 

SELECT o.dasaxeleba, b.`comment`, 'D' AS op, (SELECT ifnull(MAX(modifyDate), b.tarigi) from beerinput_history h WHERE h.beerinputID = b.id) AS let FROM `beerinput` b
LEFT JOIN $CUSTOMER_TB o 
ON o.id = b.`obieqtis_id`
WHERE date(b.`tarigi`) >= '$timeForMitana' AND b.`comment` <> ''

UNION

SELECT o.dasaxeleba, m.`comment`, 'M' AS op, (SELECT ifnull(MAX(h.modifyDate), m.tarigi) from moneyoutput_history h WHERE h.moneyoutputID = m.id) AS let FROM moneyoutput m
LEFT JOIN $CUSTOMER_TB o 
ON o.id = m.`obieqtis_id`
WHERE date(m.`tarigi`) >= '$timeForMitana' AND m.`comment` <> ''

UNION

SELECT o.dasaxeleba, k.`comment`, 'K' AS op, (SELECT ifnull(MAX(h.modifyDate), k.tarigi) from kasrioutput_history h WHERE h.kasrioutputID = k.id) AS let FROM kasrioutput k
LEFT JOIN $CUSTOMER_TB o 
ON o.id = k.`obieqtis_id`
WHERE date(k.`tarigi`) >= '$timeForMitana' AND k.`comment` <> ''
    
) gr 

GROUP BY dasaxeleba, `comment`

ORDER BY let DESC";
// die($sql);
$arr = array();
$result = $con->query($sql);

while($rs = mysqli_fetch_assoc($result)) {
    $arr[] = $rs;
}

echo json_encode($arr);

mysqli_close($con);
?>