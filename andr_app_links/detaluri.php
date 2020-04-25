<?php

// ---------- get obieqtis amonaweri  ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

// time() funqcia gvibrnebs serveris mimdinare dros, chven vart +4 saati
// $dges = date("Y-m-d", time()+4*3600);  
$dro = $_GET["tarigi"];
$objID = $_GET["objID"];


$sql = "

SELECT 
    DATE_FORMAT(tarigi, '%Y-%m-%d %H:%i') AS dt, 
    k_in,
    pr, 
    pay, 
    k_out,
    (SELECT sum(pr-pay) FROM `amonaweri_obj` a 
    WHERE 
        a.tarigi <= b.tarigi 
        AND
        obieqtis_id = $objID) AS `bal`,
    id,
    comment
    FROM `amonaweri_obj` b
WHERE 
    obieqtis_id = $objID
ORDER by b.tarigi DESC
LIMIT 0, 50
    
";
    
$arr = array();
$result = $con->query($sql);
    
while($rs = mysqli_fetch_assoc($result)) {
    $arr[] = $rs;
}

echo json_encode($arr);
//echo json_encode($dges);

mysqli_close($con);

?>