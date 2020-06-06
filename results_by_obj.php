<?php

// ---------- shedegebi obieqtebis mixedvit ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('imports.php');

$d1 = $_GET['date1'];
$d2 = $_GET['date2'];

$sql = "SELECT
	o.dasaxeleba as obname,
    l.dasaxeleba as ludi,
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
    b.ludis_id = l.id 
left join obieqtebi o
ON
	o.id = b.`obieqtis_id`
WHERE b.tarigi BETWEEN '$d1' AND '$d2'     
GROUP BY
	o.dasaxeleba, l.dasaxeleba
    order by lt
    " ;
    
$arr = array();
$arrRows = array();
$result = $con->query($sql);
    
while($rs = mysqli_fetch_assoc($result)) {
    $arrRows[] = $rs;
    $arr[$rs['obname']] = array(['sum' => 0]);
    
    
}

for ($i =0; $i < count($arrRows); $i++){
    
    array_push($arr[$arrRows[$i]['obname']], $arrRows[$i]);
    $arr[$arrRows[$i]['obname']][0]['sum'] += $arrRows[$i]['lt'];
    //var_dump($arr[$arrRows[$i]['obname']]);
    //echo $arrRows[$i];
}

while($rs = mysqli_fetch_assoc($result)) {

    // array_push($arr[$rs['obname']], 'sdsd');

}
//echo $sql;
echo json_encode($arr);

mysqli_close($con);
?>