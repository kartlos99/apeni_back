<?php

// ---------- get sysCleaning list ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

$sql = "
SELECT
    MAX(g.id) AS id,
    distributor_id,
    DATEDIFF(CURRENT_DATE, MAX(tarigi)) AS dge,
    DATE(MAX(tarigi)) AS tarigi,
    o.dasaxeleba,
    g.comment
FROM
    `gawmenda` AS g
LEFT JOIN obieqtebi AS o
ON
    g.obieqtis_id = o.id
WHERE
	o.active = 1
GROUP BY dasaxeleba    
ORDER BY dge DESC
";
	
$arr = array();
$result = $con->query($sql);
    
while($rs = mysqli_fetch_assoc($result)) {
    $arr[] = $rs;
}

echo json_encode($arr);

mysqli_close($con);
?>	