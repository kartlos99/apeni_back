<?php

namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkToken();
require_once('../../BaseDbManager.php');
$dbManager = new \BaseDbManager();

const PAGE_SIZE = 10;

$groupID = $_GET['groupID'] ?? null;
$groupsToShow = [];

if (is_null($groupID)) {

    $pageIndex = $_GET['pageIndex'] ?? 0;
    $offset = $pageIndex * PAGE_SIZE;

    $sqlGroupsToShow = "SELECT bb.groupID FROM
    (SELECT groupID, inputDate FROM storehousebeerinpit
     WHERE regionID = {$sessionData->regionID}
    UNION ALL
     SELECT groupID, inputDate FROM storehouse_bottle_input
     WHERE regionID = {$sessionData->regionID}
    ) bb
    GROUP BY bb.`groupID`
    ORDER BY bb.`inputDate` DESC
    LIMIT $offset, " . PAGE_SIZE;

    $resp = $dbManager->getDataAsArray($sqlGroupsToShow);
    foreach ($resp as $item) {
        $groupsToShow[] = $item['groupID'];
    }
} else {
    $groupsToShow[] = $groupID;
}

$filterInput = "WHERE `regionID` = {$sessionData->regionID} "
    . " AND groupID IN ( '" . implode("', '", $groupsToShow) . "' )";

$sqlBarrelsIO =
    "SELECT `ID`, `groupID`, `inputDate` AS ioDate, `distributorID`, `beerID`, `barrelID`, `count`, `chek`, `comment` " .
    "FROM `storehousebeerinpit` " . $filterInput .
    "UNION " .
    "SELECT `ID`, `groupID`, `outputDate` AS ioDate, `distributorID`, 0 AS `beerID`, `barrelID`, `count`, `chek`, `comment` " .
    "FROM `storehousebarreloutput` " . $filterInput .
    "ORDER BY ioDate DESC, beerID
    ";

$sqlBottleInputs = "SELECT
                        `id`,
                        `regionID`,
                        `groupID`,
                        `inputDate`,
                        `distributorID`,
                        `bottleID`,
                        `count`,
                        `chek`,
                        `comment`
                    FROM
                        `storehouse_bottle_input` " . $filterInput;

$response[DATA] = [
    'barrels' => $dbManager->getDataAsArray($sqlBarrelsIO),
    'bottles' => $dbManager->getDataAsArray($sqlBottleInputs)
];

echo json_encode($response);

mysqli_close($con);
