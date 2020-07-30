<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');

$sql = "
SELECT
    MAX(g.id) AS id,
    distributor_id AS distributorID,
    DATEDIFF(CURRENT_DATE, MAX(tarigi)) AS passDays,
    DATE(MAX(tarigi)) AS clearDate,
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
ORDER BY passDays DESC
";

$result = mysqli_query($con, $sql);

$arr = [];
if ($result) {
    while ($rs = mysqli_fetch_assoc($result)) {
        $arr[] = $rs;
    }
} else {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = mysqli_error($con);
    $response[ERROR_CODE] = mysqli_errno($con);
}

$response[DATA] = $arr;

echo json_encode($response);

mysqli_close($con);