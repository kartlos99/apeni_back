<?php

// ---------- get sawyobis detaluri sia ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

if (isset($_GET["tarigi"])){
    $tarigi = $_GET["tarigi"];
}else{
    $tarigi = date("Y-m-d", time()+4*3600);
}

$arr = array();
$aka = "
    SELECT
	a.id,
    a.tarigi,
    u.name,
    IFNULL(l.dasaxeleba, '-') AS ludi,
    a.ludis_id,
    kasri30,
    kasri50,
    chek,
    a.comment
FROM
    (
    SELECT
        id,
        tarigi,
        shemomtani_id AS distrib_id,
        ludis_id,
        kasri30,
        kasri50,
        chek,
        COMMENT
    FROM
        `sawyobi_in`
    UNION ALL
    SELECT
        id,
        tarigi,
        wamgebi_id AS distrib_id,
        0 AS ludis_id,
        kasri30,
        kasri50,
        chek,
        COMMENT
    FROM
    `sawyobi_out`
) a
LEFT JOIN users u ON
    a.distrib_id = u.id
LEFT JOIN ludi l ON
    a.ludis_id = l.id
WHERE
    tarigi <= '$tarigi'
ORDER BY
    tarigi
DESC
    
    ";

    $result = $con->query($aka);
    
     while($rs = mysqli_fetch_assoc($result)) {
         $arr[] = $rs;
     }

echo json_encode($arr);
//echo $aka;

mysqli_close($con);
?>