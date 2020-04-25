<?php

// ---------- get sawyobis nashtebi ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

if (isset($_GET["tarigi"])){
    $tarigi = $_GET["tarigi"];
}else{
    $tarigi = date("Y-m-d", time()+4*3600);
}
$chek = $_GET['chek'];
$arr = array();
// moptichkuli realizaciis angarishis gadasvlis tariRi -- 'shekvetebidan' -> realizaciashi 'beerinput'-shi
$transferDate = '2022-12-12';

if($chek == '0'){
// carieli kasrebi (obieqtebidan amogebuli da sawobidan gagzavnili sawarmoshi)
    $sql = "
    SELECT
        0 as ludis_id,
        SUM(a.k30s) AS k30s,
        SUM(a.k50s) AS k50s,
        SUM(a.k30r) AS k30r,
        SUM(a.k50r) AS k50r
    FROM
        (
        SELECT
            tarigi,
            s.kasri30 AS k30s,
            s.kasri50 AS k50s,
            0 AS k30r,
            0 AS k50r
        FROM
            `sawyobi_out` AS s
        UNION ALL
        SELECT
            tarigi,
            0 AS k30s,
            0 AS k50s,
            r.kasri30 AS k30r,
            r.kasri50 AS k50r
        FROM
            `kasrioutput` AS r
        ) AS a
    WHERE
        tarigi <= '$tarigi'
        
    ";

    $result = $con->query($sql);
    
    while($rs = mysqli_fetch_assoc($result)) {
        $arr[] = $rs;
    }

// savse kasrebi (sawyobshi shemosuli da obieqtebze darigebuli)
    $sql = "
    SELECT
        ludi.dasaxeleba AS ludis_id,
        SUM(s.kasri30) AS k30s,
        SUM(s.kasri50) AS k50s,
        IFNULL(r.k30r, 0) AS k30r,
        IFNULL(r.k50r, 0) AS k50r
    FROM
        `sawyobi_in` AS s
    LEFT JOIN(
        SELECT
            ludis_id,
            SUM(kasri30) AS k30r,
            SUM(kasri50) AS k50r
        FROM
            `beerinput`
        WHERE
            tarigi <= '$tarigi'
        GROUP BY ludis_id
    ) AS r
    ON
        s.ludis_id = r.ludis_id
    LEFT JOIN ludi ON s.ludis_id = ludi.id    
    WHERE
        tarigi <= '$tarigi' and chek = '0'
    GROUP BY
        s.ludis_id
    ";

    $result = $con->query($sql);
    
    while($rs = mysqli_fetch_assoc($result)) {
        $arr[] = $rs;
    }
}

if($chek == '1'){
    // savse kasrebi (sawyobshi shetanili da shekvetili)
    $sql =  "
SELECT
    ludi.dasaxeleba AS ludis_id,
    SUM(s.kasri30) AS k30s,
    SUM(s.kasri50) AS k50s, 
    IFNULL(r.k30r, 0) AS `k30r`,
    IFNULL(r.k50r, 0) AS `k50r`
FROM
    `sawyobi_in` AS s
LEFT JOIN(
        SELECT
            ludis_id,
            IFNULL(SUM(kasri30),    0) AS k30r,
            IFNULL(SUM(kasri50),    0) AS k50r
        FROM
            (
            SELECT
                tarigi,
                chek,
                ludis_id,
                kasri30,
                kasri50
            FROM
                `shekvetebi`
            WHERE
                tarigi < '$transferDate'
            UNION ALL
            SELECT
                tarigi,
                chek,
                ludis_id,
                kasri30,
                kasri50
            FROM
                `beerinput`
            WHERE
                tarigi > '$transferDate'
        ) AS ra
        WHERE
            ra.tarigi <= '$tarigi' AND ra.chek = '1'
        GROUP BY
            ludis_id
) AS r
ON
    s.ludis_id = r.ludis_id
LEFT JOIN ludi ON s.ludis_id = ludi.id
WHERE
    tarigi <= '$tarigi' AND chek = '1'
GROUP BY
    s.ludis_id
    ";
    
        $result = $con->query($sql);
    
    while($rs = mysqli_fetch_assoc($result)) {
        $arr[] = $rs;
    }
}

echo json_encode($arr);

mysqli_close($con);
?>