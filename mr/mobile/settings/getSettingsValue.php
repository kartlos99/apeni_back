<?php

namespace Apeni\JWT;
use DataProvider;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
checkToken();

$sqlSettings = "
SELECT `id`, `code`, `valueInt` FROM `dictionary_items`
WHERE 
`isActive` = 1 AND `dictionaryID` = 3 ";

$result = mysqli_query($con, $sqlSettings);

if ($result) {
    $params = [];
    while ($rs = mysqli_fetch_assoc($result)) {
        $params[] = $rs;
    }
    $response[DATA] = $params;
} else {
    $response[SUCCESS] = false;
    $response[ERROR_TEXT] = "can't get parameters for setting!";
    $response[ERROR_CODE] = ER_CODE_NOT_FOUNT;
}

echo json_encode($response);

mysqli_close($con);