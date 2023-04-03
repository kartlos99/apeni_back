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
        if (isset($distributionItem->fermentationTankInfo->fermentation)) {
            $fermentationID = $distributionItem->fermentationTankInfo->fermentation->ID;
            if (
                isset($distributionItem->fermentationTankInfo->fermentation->yeastID)
                && $distributionItem->fermentationTankInfo->fermentation->yeastID != $distributionItem->yeastID
            ) {
                $dbManager->removeYeastFromFermentation($distributionItem->yeastID);
                $dbManager->inputYeastIntoFermentation($distributionItem->yeastID, $fermentationID);
            }
        } else {
            $dbManager->removeYeastFromFermentation($distributionItem->yeastID);
            $insertResult = $myData->insertFermentation(
                "ferm-" . $postData->code,
                $postData->density,
                $distributionItem->yeastID,
                $distributionItem->fermentationTankInfo->ID,
                $postData->beerID,
                null,
                $timeOnServer,
                $userID
            );
            $fermentationID = $insertResult[RECORD_ID_KEY];
        }
        $dbManager->addBoilingToFermentationMap($postData->ID, $fermentationID, $distributionItem->amount);
    }
}


echo json_encode([RECORD_ID_KEY => 0]);

$dbManager->closeConnection();