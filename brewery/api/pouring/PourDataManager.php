<?php

class PourDataManager extends BaseDataManager
{

    function addSale(
        $saleDate,
        $clientID,
        $producedDeerID,
        $unitPrice,
        $barrelID,
        $count,
        $tankID,
        $beerOriginID,
        $comment,
        $modifyUserID
    ): array
    {
        $insertSql = "INSERT INTO `sales`(
                        `saleDate`,
                        `clientID`,
                        `operatorID`,
                        `producedBeerID`,
                        `unitPrice`,
                        `barrelID`,
                        `count`,
                        `tankID`,
                        `beerOriginID`,
                        `comment`,
                        `modifyUserID`
                    )
                    VALUES(
                        '$saleDate',
                        $clientID,
                        $modifyUserID,
                        $producedDeerID,
                        $unitPrice,
                        $barrelID,
                        $count,
                        $tankID,
                        $beerOriginID,
                        $comment,
                        $modifyUserID)";

        return $this->baseInsert($insertSql);
    }
}