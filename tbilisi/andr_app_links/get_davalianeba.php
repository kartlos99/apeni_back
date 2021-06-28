<?php

// ---------- get davalianeba ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

//$dges = $_GET["tarigi"];


$sql = "

SELECT
	o.id AS `obj_id`,
    o.dasaxeleba,
    round(ifnull(s.pr, 0) * 100)/100 AS pr,
    ifnull(s.pay, 0) AS `pay`,
    ifnull(s.`k30in`, 0) AS `k30in`,
    ifnull(s.`k50in`, 0) AS `k50in`,
    ifnull(k.`k30_out`, 0) AS `k30out`,
    ifnull(k.`k50_out`, 0) AS `k50out`
FROM
	$CUSTOMER_TB AS o
LEFT JOIN  
	sumof_prpaykin AS s
ON s.obj_id = o.id
LEFT JOIN
	kasri_back AS k
ON
    k.obieqtis_id = o.id
WHERE o.active = 1
    
";


$result = $con->query($sql);

$arr = array();    
while($rs = mysqli_fetch_assoc($result)) {
    $arr[] = $rs;
}

echo json_encode($arr);


mysqli_close($con);

    // SELECT
    // 	s.obj_id,
    //     o.dasaxeleba,
    //     s.pr,
    //     s.pay,
    //     s.`k30in`,
    //     s.`k50in`,
    //     ifnull(k.`k30_out`, 0) AS `k30out`,
    //     ifnull(k.`k50_out`, 0) AS `k50out`
    // FROM
    //     sumof_prpaykin AS s
    // LEFT JOIN kasri_back AS k
    // ON
    //     s.obj_id = k.obieqtis_id
    // LEFT JOIN $CUSTOMER_TB AS o
    // ON s.obj_id = o.id


?>