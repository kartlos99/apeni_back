<?php
namespace Apeni\JWT;
// ---------- get kasri list ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');
checkToken();

$sql = "SELECT `id`, `dasaxeleba` AS name, `litraji` AS volume, `sortValue` FROM `kasri` ORDER BY sortValue";
$arr = [];
$result = mysqli_query($con, $sql);

while ($rs = mysqli_fetch_assoc($result)) {
    $arr[] = $rs;
}

$response[DATA] = $arr;

echo json_encode($response);

mysqli_close($con);
