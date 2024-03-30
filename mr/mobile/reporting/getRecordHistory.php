<?php

namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

use Apeni\JWT\HistoryManager;

require_once('../connection.php');
require_once "HistoryManager.php";
$sessionData = checkToken();

//$sessionData = checkToken();

$historyManager = new HistoryManager();

$recordID = $_GET["recordID"] ?? 0;
$table = $_GET["table"] ?? "";

$history = $historyManager->getHistory($recordID, $table);

$response[DATA] = $history;

echo json_encode($response);

//mysqli_close($con);