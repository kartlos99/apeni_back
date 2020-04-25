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

$sql_money = "SELECT round(IFNULL(sum(tanxa),0),2) AS money FROM `moneyoutput` WHERE DATE(tarigi) = '$dro' ";
$sql_kasri = "SELECT round(IFNULL(SUM(kasri30),0),2) AS k30, round(IFNULL(SUM(kasri50),0),2) AS k50 FROM `kasrioutput` WHERE DATE(tarigi) = '$dro'";

$xarj_sql = "SELECT * FROM `xarjebi` WHERE DATE(tarigi) = '$dro'";

$sql = "SELECT
            l.dasaxeleba,
            round(SUM(
                (
                    b.kasri30 * 30 + b.kasri50 * 50
                ) * b.ert_fasi
            ),2) AS pr,
            round(SUM(
                b.kasri30 * 30 + b.kasri50 * 50
            ),2) AS lt,
            round(SUM(kasri30),2) AS k30,
            round(SUM(kasri50),2) AS k50
        FROM
            `beerinput` AS b
        LEFT JOIN ludi AS l
        ON
            b.ludis_id = l.id
        WHERE 
            DATE(b.tarigi) = '$dro' ";
        
$grouping = " GROUP BY dasaxeleba";


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
        $sql .= $grouping;
    }

}else{ 
    // konkretuli distributori .....
    $sql .= " AND b.distributor_id = '$distrId' " . $grouping;
    
    $sql_money .= " AND distributor_id = '$distrId' ";

    $sql_kasri .= " AND distributor_id = '$distrId' ";
    
    $xarj_sql .= " AND `distributor_id` = $distrId";
}
    
$arr = array();
$result = $con->query($sql);
    
while($rs = mysqli_fetch_assoc($result)) {
    $arr[] = $rs;
}

$result_m = $con->query($sql_money);
$arr[] = mysqli_fetch_assoc($result_m);

$result_k = $con->query($sql_kasri);
$arr[] = mysqli_fetch_assoc($result_k);

$xarj_arr = array();
$xarj_rs = mysqli_query($con, $xarj_sql);
while($rs = mysqli_fetch_assoc($xarj_rs)) {
    $xarj_arr[] = $rs;
}

$arr[] = $xarj_arr;

echo json_encode($arr);

mysqli_close($con);
?>