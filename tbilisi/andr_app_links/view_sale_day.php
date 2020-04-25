<?php

// ---------- view dgis realizacia ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

// time() funqcia gvibrnebs serveris mimdinare dros, chven vart +4 saati
// $dges = date("Y-m-d", time()+4*3600);  
$dro = $_GET["tarigi"];
$distrId = $_GET["distrid"];
$periodi = "dge";//$_GET["periodi"];

if($distrId == 0){
    if($periodi == "tve"){
$sql = "        

SELECT
YEAR(b.tarigi) as weli,
MONTH(b.tarigi) as tve,
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
    SUM(kasri50) AS k50
FROM
    `beerinput` AS b
LEFT JOIN ludi AS l
ON
    b.ludis_id = l.id

GROUP BY
	YEAR(b.tarigi), 
    MONTH(b.tarigi),    
    dasaxeleba 
    
    ";
    }
    
if($periodi == "dge"){
$sql = "

SELECT
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
    SUM(kasri50) AS k50
FROM
    `beerinput` AS b
LEFT JOIN ludi AS l
ON
    b.ludis_id = l.id
where DATE(b.tarigi) = '$dro' 
GROUP BY
    dasaxeleba
";
}

}else{
    $sql = "

SELECT
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
    SUM(kasri50) AS k50
FROM
    `beerinput` AS b
LEFT JOIN ludi AS l
ON
    b.ludis_id = l.id
where DATE(b.tarigi) = '$dro' AND b.distributor_id = '$distrId'
GROUP BY
    dasaxeleba
    
";
}
    
$arr = array();
$result = $con->query($sql);
    
while($rs = mysqli_fetch_assoc($result)) {
    $arr[] = $rs;
}

$sql = "SELECT IFNULL(sum(tanxa),0) as money FROM `moneyoutput` WHERE DATE(tarigi) = '$dro'  ";
    
    $result1 = $con->query($sql);
    $arr[] = mysqli_fetch_assoc($result1);


echo json_encode($arr);

mysqli_close($con);

?>