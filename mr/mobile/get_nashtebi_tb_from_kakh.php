<?php

// ---------- get sawyobis nashtebi V2 (kaxetidan) ----------

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
$transferDate = '2052-12-12';
// kakhetis app-is tbilisis obieqtidan infos avtomaturad wamogebaze gadasvlis tarigi
$gadasvlis_tarigi = '2019-06-01';

if($chek == '0'){
// carieli kasrebi (obieqtebidan amogebuli da sawobidan gagzavnili sawarmoshi)
    $sql = "
    SELECT
        0 AS ludis_id,
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
            apenige2_apeni2.`sawyobi_out` AS s
        UNION ALL
        SELECT
            tarigi,
            0 AS k30s,
            0 AS k50s,
            r.kasri30 AS k30r,
            r.kasri50 AS k50r
        FROM
            apenige2_apeni2.`kasrioutput` AS r
        UNION ALL
        SELECT
            tarigi,
            kk.kasri30 AS k30s,
            kk.kasri50 AS k50s,
            0 AS k30r,
            0 AS k50r
        FROM
            apenige2_kakheti.kasrioutput kk 
        WHERE
            obieqtis_id = 185 AND tarigi > '$gadasvlis_tarigi'
    ) AS a
    WHERE
        tarigi <= '$tarigi'
    ";

    $result = $con->query($sql);
    
    while($rs = mysqli_fetch_assoc($result)) {
        $arr[] = $rs;
    }

// savse kasrebi (sawyobshi shemosuli da obieqtebze darigebuli)
// tbilisis app-shi sawyobSi shetanas agar vafiqsirebT. amis magivrad
// katetis app-shi 'Tbilisi'-s obieqtze gacemul raodenbas vangarishobt
// ---- tbilisis obieqtis ID - 185 ---------

    $sql = "
    SELECT
	ludi.dasaxeleba AS ludis_id,
    SUM(k30s) AS k30s,
    SUM(k50s) AS k50s,
    SUM(k30r) AS k30r,
    SUM(k50r) AS k50r
FROM (    
    SELECT
        ludis_id,
        0 AS k30s,
        0 AS k50s,
        kasri30 AS k30r,
        kasri50 AS k50r
    FROM
        apenige2_apeni2.`beerinput` b
    WHERE
        tarigi <= '$tarigi'
    UNION ALL    
    SELECT
        ludis_id,
        s.kasri30 AS k30s,
        s.kasri50 AS k50s,
        0 AS k30r,
        0 AS k50r
    FROM
        apenige2_apeni2.`sawyobi_in` s  
    WHERE
        tarigi <= '$tarigi' AND chek = '0'
    UNION ALL
    SELECT     
        ludis_id,
        bk.kasri30 AS k30s,
        bk.kasri50 AS k50s,
        0 AS k30r,
        0 AS k50r
    FROM
        apenige2_kakheti.beerinput bk
    WHERE 
        bk.obieqtis_id = 185 AND tarigi <= '$tarigi' AND chek = '0' AND tarigi > '$gadasvlis_tarigi'
) AS f
LEFT JOIN apenige2_apeni2.ludi ON f.ludis_id = ludi.id
GROUP BY
    f.ludis_id
    ";

    $result = mysqli_query($con, $sql);
    
    while($rs = mysqli_fetch_assoc($result)) {
        $arr[] = $rs;
    }
}

if($chek == '1'){
    // savse kasrebi (sawyobshi shetanili da shekvetili)
    $sql =  "
    SELECT
    	ludi.dasaxeleba AS ludis_id,
        SUM(k30s) AS k30s,
        SUM(k50s) AS k50s,
        SUM(k30r) AS k30r,
        SUM(k50r) AS k50r
    FROM ( 
    
        SELECT
        	tarigi,
            ludis_id,
            s.kasri30 AS k30s,
            s.kasri50 AS k50s, 
            0 AS `k30r`,
            0 AS `k50r`
        FROM
            apenige2_apeni2.`sawyobi_in` AS s
        WHERE
            chek = '1'
        
        UNION ALL
        
        SELECT     
        	tarigi,
            ludis_id,
            bk.kasri30 AS k30s,
            bk.kasri50 AS k50s,
            0 AS k30r,
            0 AS k50r
        FROM
            apenige2_kakheti.beerinput bk
        WHERE 
            bk.obieqtis_id = 185 AND chek = '1' AND tarigi > '$gadasvlis_tarigi'
        
        UNION ALL
        
        SELECT
            tarigi,
            ludis_id,
            0 AS `k30s`,
            0 AS `k50s`,
            kasri30 AS k30r,
            kasri50 AS k50r
        FROM
            apenige2_apeni2.`shekvetebi`
        WHERE
            chek = '1'
        
        ) AS f
    LEFT JOIN apenige2_apeni2.ludi ON f.ludis_id = ludi.id
    WHERE
        tarigi <= '$tarigi'
    GROUP BY
        f.ludis_id
    ";
    
        $result = $con->query($sql);
    
    while($rs = mysqli_fetch_assoc($result)) {
        $arr[] = $rs;
    }
}

echo json_encode($arr);

mysqli_close($con);
?>