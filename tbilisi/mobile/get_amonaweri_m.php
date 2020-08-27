<?php
namespace Apeni\JWT;
// ---------- get amonaweri M ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');
checkToken();

// time() funqcia gvibrnebs serveris mimdinare dros, chven vart +4 saati
// $dges = date("Y-m-d", time()+4*3600);  
$dro = $_GET["tarigi"];
$objID = $_GET["objID"];


$sql = "
SELECT
    DATE_FORMAT(tarigi, '%Y-%m-%d %H:%i') AS dt,
    pr,
    pay,
    (SELECT sum(pr-pay) FROM `amonaweri_money` a
    WHERE
        a.tarigi <= b.tarigi
        AND
        clientID = $objID) AS `bal`,
    id,
    comment
    FROM `amonaweri_money` b
WHERE
    clientID = $objID AND tarigi < '$dro'
ORDER by b.tarigi DESC
LIMIT 0, 100
    
";
    
$arr = array();
$result = $con->query($sql);
    
while($rs = mysqli_fetch_assoc($result)) {
    $arr[] = $rs;
}

$response[DATA] = $arr;

echo json_encode($response);

mysqli_close($con);
