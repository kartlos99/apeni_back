<?php

// ---------- get davalianeba ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

//$dges = $_GET["tarigi"];


$sql = "
SELECT
	s.obj_id,
    o.dasaxeleba,
    round(s.pr * 100)/100 AS pr,
    s.pay,
    s.`k30in`,
    s.`k50in`,
    ifnull(k.`k30_out`, 0) AS `k30out`,
    ifnull(k.`k50_out`, 0) AS `k50out`
FROM
    sumof_prpaykin AS s
LEFT JOIN kasri_back AS k
ON
    s.obj_id = k.obieqtis_id
LEFT JOIN $CUSTOMER_TB AS o
ON s.obj_id = o.id
    
";


$result = $con->query($sql);

$arr = array();    
while($rs = mysqli_fetch_assoc($result)) {
    $arr[] = $rs;
}

echo json_encode($arr);


mysqli_close($con);

?>