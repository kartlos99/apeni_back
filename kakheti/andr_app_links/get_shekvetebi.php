
<?php

// ---------- get shekvetebi ----------  DELETE FROM `shekvetebi` WHERE date(`tarigi`) = '2019-05-22'

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

// time() funqcia gvibrnebs serveris mimdinare dros, chven vart +4 saati
$dges_server = date("Y-m-d", time()+4*3600);  
$dges = $_GET["tarigi"];

// session_start();

// $_SESSION['dt'] = $dges;

// *************************************************************************
// tu pirveli motxovnaa, mashin gadmogvaqvs daumtavrebeli shekvetebi

$sql_chekdate="SELECT MAX(date_format(`tarigi`,'%Y-%m-%d')) AS tarigi FROM `shekvetebi`";

$rs = mysqli_query($con, $sql_chekdate);
$rt = mysqli_fetch_assoc($rs);

if ($rt['tarigi'] != $dges_server){
    $sql = "SELECT * FROM `last_orders_group_view` WHERE tarigi1 < '$dges_server' AND in_30+in_50 < wont_30+wont_50";
    $result_2 = $con->query($sql);
    while($row = mysqli_fetch_assoc($result_2)) {
        // tu gvaqvs daumtavrebeli shekvetebi am droistvis, vainsetebt shesabamis chanawers
        $k30 = $row['wont_30']-$row['in_30'];
        $k50 = $row['wont_50']-$row['in_50'];
        $rcomment = $row['comment'];
        $rdistributor = $row['distributor_id'];
        $robjid = $row['obieqtis_id'];
        $rlid = $row['l_id'];
        $rchek = $row['chk'];
        
        $sql = "INSERT INTO 
            `shekvetebi` 
            (`tarigi`, `obieqtis_id`, `ludis_id`, `kasri30`, `kasri50`, `comment`, `chek`, `distributor_id`) 
            VALUES 
            ('$dges_server', $robjid, $rlid, $k30, $k50, '$rcomment', $rchek, $rdistributor) ";
// echo $sql . "<br>";
        if(!mysqli_query($con, $sql)){
            die("error_writing");
        }
    }
}
// die("end");
// ************************************************************************

$sql="SELECT
    tarigi1,
    DATE_FORMAT(tarigi, '%Y-%m-%d %H:%i') AS tarigi_hhmm,
    obieqtebi.dasaxeleba AS obieqti,
    ludi.dasaxeleba,
    (k30in) AS in_30,
    (k50in) AS in_50,
    (k30wont) AS wont_30,
    (k50wont) AS wont_50,
    chk,
    distributor_id,
    users.name,
    order_id,
    a.comment,
    ludi.color
FROM
    (
    SELECT
        DATE_FORMAT(tarigi, '%Y-%m-%d') AS tarigi1,
        tarigi,
        obieqtis_id,
        ludis_id,
        kasri30 AS k30in,
        kasri50 AS k50in,
        0 AS k30wont,
        0 AS k50wont,
        0 AS chk,
        distributor_id,
        id AS order_id,
        comment
    FROM
        `beerinput`
    UNION
SELECT
    DATE_FORMAT(tarigi, '%Y-%m-%d') AS tarigi1,
    tarigi,
    obieqtis_id,
    ludis_id,
    0 AS k30in,
    0 AS k50in,
    kasri30 AS k30wont,
    kasri50 AS k50wont,
    chek AS chk,
    distributor_id,
    id AS order_id,
    comment
FROM
    `shekvetebi`
) AS a
LEFT JOIN obieqtebi ON obieqtis_id = obieqtebi.id
LEFT JOIN ludi ON ludis_id = ludi.id
LEFT JOIN users ON distributor_id = users.id
WHERE
    tarigi1 = '$dges'
ORDER BY
    obieqti,
    dasaxeleba,
    order_id";
    
    
$arr = array();
$result = $con->query($sql);
    
while($rs = mysqli_fetch_assoc($result)) {
    $arr[] = $rs;
}

echo json_encode($arr);

mysqli_close($con);

?>