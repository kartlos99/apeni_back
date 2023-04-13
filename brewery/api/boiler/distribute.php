<?php

namespace Apeni\JWT;

use BoilerDataManager;
use MyData;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../load.php');
$sessionData = checkToken();
$userID = $sessionData->userID;

$json = file_get_contents('php://input');
$postData = json_decode($json);

$dbManager = new BoilerDataManager();
$myData = new MyData($dbLink);

if (count($postData->distributionInTanks) > 0) {
    foreach ($postData->distributionInTanks as $distributionItem) {
        if ($distributionItem->distribution->fermentationID > 0) {
            $fermentationID = $distributionItem->distribution->fermentationID;
            if (
                $distributionItem->distribution->yeastID > 0 &&
                $distributionItem->distribution->yeastID != $distributionItem->yeastID
                || ($distributionItem->distribution->yeastID == $distributionItem->yeastID
                    && !$distributionItem->distribution->containsYeast)
            ) {
                $dbManager->removeYeastFromFermentation($distributionItem->yeastID);
                $dbManager->inputYeastIntoFermentation($distributionItem->yeastID, $fermentationID);
            }
        } else {
            $dbManager->removeYeastFromFermentation($distributionItem->yeastID);
            $insertResult = $myData->insertFermentation(
                "F." . $postData->code . ".T" . $distributionItem->distribution->tankID,
                $postData->density,
                $distributionItem->yeastID,
                $distributionItem->distribution->tankID,
                $postData->beerID,
                null,
                $timeOnServer,
                $userID
            );
            $fermentationID = $insertResult[RECORD_ID_KEY];
            $dbManager->updateYeastCode($distributionItem->yeastID, $fermentationID);
        }
        if ($distributionItem->distribution->mapID == 0)
            $dbManager->addBoilingToFermentationMap($postData->ID, $fermentationID, $distributionItem->distribution->amount);
    }
}


echo json_encode([RECORD_ID_KEY => 0]);

$dbManager->closeConnection();