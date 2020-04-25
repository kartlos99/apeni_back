<?php

// ---------- get sawyobis detaluri sia + kakhetis monacemebi avtomaturad ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

if (isset($_GET["tarigi"])){
    $tarigi = $_GET["tarigi"];
}else{
    $tarigi = date("Y-m-d", time()+4*3600);
}

// ----- tbilisis obieqtis ID = 185  kakhetis app-shi ----

$arr = array();
$aka = "
    SELECT
    a.id,
    a.tarigi,
    IFNULL(u.name, 'კახეთიდან') AS name,
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
        apenige2_apeni2.`sawyobi_in`
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
        apenige2_apeni2.`sawyobi_out`
    UNION ALL
    SELECT
        0 AS id,
        tarigi,
        0 AS distrib_id,
        0 AS ludis_id,
        kk.kasri30,
        kk.kasri50,
        0 AS chek,
        COMMENT
    FROM
        apenige2_kakheti.kasrioutput kk
    WHERE
        kk.obieqtis_id = 185
    UNION ALL
    SELECT
        0 AS id,
        tarigi,
        0 AS distrib_id,
        ludis_id,
        bk.kasri30,
        bk.kasri50,
        chek,
        COMMENT
    FROM
        apenige2_kakheti.beerinput bk
    WHERE
        bk.obieqtis_id = 185
	) a
LEFT JOIN users u ON
    a.distrib_id = u.id
LEFT JOIN ludi l ON
    a.ludis_id = l.id
WHERE
    tarigi <= '$tarigi'
ORDER BY
    tarigi DESC
LIMIT 100
    ";

    $result = $con->query($aka);
    
     while($rs = mysqli_fetch_assoc($result)) {
         $arr[] = $rs;
     }

echo json_encode($arr);
//echo $aka;

mysqli_close($con);
?>