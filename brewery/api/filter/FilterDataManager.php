<?php

class FilterDataManager extends BaseDataManager
{

    function createFilteringItem(
        $filterDate,
        $beerID,
        $filterTankID,
        $comment,
        $modifyUserID,
        $amount = 0
    ): array
    {
        $sql = "INSERT INTO `filtration`(
            `filterDate`,
            `beerID`,
            `filterTankID`,
            `comment`,
            `modifyUserID`
        )
        VALUES(
               '$filterDate', $beerID, $filterTankID, '', $modifyUserID
            )";

        return $this->baseInsert($sql);
    }

    function addPourToFilterMap(
        $transferDate,
        $amount,
        $fermentationID,
        $filtrationID,
        $comment,
        $modifyUserID
    ): array
    {
        $receivedAmount = $amount > 105 ? $amount - ($amount * 0.02) - 100 : 0;

        $sql = "INSERT INTO `pour_in_filtration_map`(
            `transferDate`,
            `amount`,
            `receivedAmount`,
            `fermentationID`,
            `filtrationID`,
            `comment`,
            `modifyUserID`
        )
        VALUES(
               '$transferDate',
               $amount,
               $receivedAmount,
               $fermentationID,
               $filtrationID,
               '$comment', 
               $modifyUserID
            )";

        return $this->baseInsert($sql);
    }

    function getCurrentFilteringDataByTankId($tankID): array
    {
        $sql = "SELECT `ID`, `filterDate`, `beerID`, `filterTankID`, `status`, `comment`, `modifyDate`, `modifyUserID` FROM `filtration`
                WHERE
                    `filterTankID` = $tankID AND `status` = 1
                ORDER BY
                    `modifyDate`";
        $result = $this->getDataAsArray($sql);
        if (count($result) > 0)
            return $result[0];
        else
            return [];
    }

    public function getFiltrationTanks(): array
    {
        $sql = "SELECT t.* FROM `tanks` t LEFT JOIN dictionary_items di ON di.id = t.`tankType` " .
            "WHERE di.code = 'ttFiltration' AND t.status > 0 ORDER BY t.sortValue";
        return $this->getDataAsArray($sql);
    }

    public function getAllActiveFiltration(): array
    {
        $sql = "SELECT f.*, (SELECT SUM(receivedAmount) FROM `pour_in_filtration_map` WHERE `filtrationID` = f.ID GROUP BY `filtrationID` LIMIT 1) AS amount 
                FROM filtration f
                WHERE f.status = 1";
        return $this->getDataAsArray($sql);
    }
}