<?php

namespace Apeni\JWT;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkToken();
require_once('../../BaseDbManager.php');
$dbManager = new \BaseDbManager();

const PAGE_SIZE = 20;

$groupID = $_GET['groupID'] ?? null;
$groupsToShow = [];

if (empty($groupID)) {

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

$combinedIoSql =
    "SELECT `id`, `groupID`, `inputDate` AS ioDate, `distributorID`, `beerID`, `barrelID`, `count`, `chek`, `comment` 
    FROM `storehousebeerinpit` $filterInput
    UNION 
    SELECT `id`, `groupID`, `outputDate` AS ioDate, `distributorID`, 0 AS `beerID`, `barrelID`, `count`, `chek`, `comment` 
    FROM `storehousebarreloutput` $filterInput
    UNION ALL
    SELECT `id`, `groupID`, `inputDate` AS ioDate, `distributorID`, `bottleID` AS beerID, 0 AS barrelID, `count`, `chek`, `comment`
    FROM `storehouse_bottle_input` $filterInput
    ORDER BY ioDate DESC, beerID";

$dataArr = $dbManager->getDataAsArray($combinedIoSql);

$arr = [];
foreach ($dataArr as $item) {
    $arr[$item['groupID']][] = $item;
}

$finalMap = [];

foreach ($arr as $item) {

    $mItem = [];
    $mItem['groupID'] = $item[0]['groupID'];
    $mItem['ioDate'] = $item[0]['ioDate'];
    $mItem['distributorID'] = $item[0]['distributorID'];
    $mItem['chek'] = $item[0]['chek'];
    $mItem['comment'] = $item[0]['comment'];

    foreach ($item as $row) {
        if ($row["barrelID"] == 0) {
            $mItem['bottleInput'][] = [
                "id" => $row["id"],
                "bottleID" => $row["beerID"],
                "count" => $row["count"],
            ];
        } else if ($row["beerID"] == 0) {
            $mItem['barrelOutput'][] = [
                "id" => $row["id"],
                "barrelID" => $row["barrelID"],
                "count" => $row["count"],
            ];
        } else {
            $mItem['barrelInput'][] = [
                "id" => $row["id"],
                "beerID" => $row["beerID"],
                "barrelID" => $row["barrelID"],
                "count" => $row["count"],
            ];
        }
    }

    $finalMap[] = $mItem;
}

echo json_encode($finalMap);

$dbManager->closeConnection();