<?php

use function Apeni\JWT\dieWithError;

class MyData
{
    private $dbConn;

    function __construct($dbConn)
    {
        $this->dbConn = $dbConn;
    }

    function getFermentationDataByTankID($tankID): array
    {
        $sql = "SELECT * FROM `f_data`
            WHERE `fID`= (SELECT ID FROM `fermentation`
                        WHERE `tankID`=$tankID AND `active` = 1)
            ORDER by `modifyDate` ";
        return $this->getDataAsArray($sql);
    }

    public function getCurrentFermentationDataOnTank($tankID): array
    {
        $sql = "SELECT * FROM `fermentation`
                WHERE `tankID`=$tankID AND `active` = 1
            ORDER by `modifyDate` ";
        return $this->getDataAsArray($sql);
    }

    function getFermentationDataByID($fID): array
    {
        $sql = "SELECT * FROM `f_data`
            WHERE `fID`= $fID
            ORDER by `measurementDate` ";
        return $this->getDataAsArray($sql);
    }

    public function getBrewsInFermentation($fermentationID): array
    {
        $sql = "SELECT b.ID, b.code, b.startDate, b.density, b.beerID, b.amountToVirlpool AS boiledAmount, map.amount AS transferAmount FROM `b_to_f_map` map
            LEFT JOIN boiling b
            ON b.ID = map.`bID`
            WHERE `fID` = $fermentationID ";
        return $this->getDataAsArray($sql);
    }

    public function getAmountToFilter($fermentationID): int
    {
        $sql = "SELECT SUM(`amount`) AS amount FROM `pour_in_filtration_map` 
                WHERE fermentationID = $fermentationID
                GROUP BY `fermentationID`";
        $result = $this->getDataAsArray($sql);
        if (empty($result))
            return 0;
        else
            return $result[0]["amount"];
    }

    function pouredFromFermentationToBarrels($fermentationID): int
    {
        return $this->getAmountToBarrel($fermentationID, 0);
    }

    function pouredFromFiltrationToBarrels($filtrationID): int
    {
        return $this->getAmountToBarrel($filtrationID, 1);
    }

    function getAmountToBarrel($beerOriginID, $beerType): int
    {
        $sql = "SELECT sum(b.volume * s.count) AS amount FROM sales s
                LEFT JOIN barrel b ON s.barrelID = b.ID
                WHERE `producedBeerID` MOD 2 = $beerType AND s.beerOriginID = $beerOriginID
                GROUP BY s.beerOriginID";
        $result = $this->getDataAsArray($sql);
        if (empty($result))
            return 0;
        else
            return $result[0]["amount"];
    }

    function getTanks(): array
    {
        $bData = [];
        $sql = "
            SELECT t.*, di.code FROM `tanks` t
            LEFT JOIN dictionary_items di ON tankType = di.id
            WHERE status > 0
            ORDER BY tankType, sortValue";
        $arr = $this->getDataAsArray($sql);
        foreach ($arr as $key => $item) {
            // $bData[$item[ID]] = $item;
            $bData[] = $item;
        }
        return $bData;
    }

    function getClients(): array
    {
        $sql = "SELECT * FROM `client` WHERE `status` > 0";
        return $this->getDataAsArray($sql);
    }

    function getBarrels(): array
    {
        $sql = "SELECT * FROM `barrel` ORDER BY `sortValue`";
        return $this->getDataAsArray($sql);
    }

    /**
     * @param $query
     * @return array
     * base data retrieving function
     * returns data as Array from DB according sql query
     */
    function getDataAsArray($query): array
    {
        $result = mysqli_query($this->dbConn, $query);
        $arr = [];
        if ($result) {
            while ($rs = mysqli_fetch_assoc($result)) {
                $arr[] = $rs;
            }
        } else {
            dieWithError(422, mysqli_error($this->dbConn));
        }
        return $arr;
    }

    /**
     * @param $insertSql
     * @return array
     * base function for data inserting
     */
    private function baseInsert($insertSql): array
    {
        $result = mysqli_query($this->dbConn, $insertSql);
        if (!$result)
            dieWithError(422, mysqli_error($this->dbConn));
        return [RECORD_ID_KEY => mysqli_insert_id($this->dbConn)];
    }

    public function insertBoiling($code, $startDate, $density, $amount, $tankID, $beerID, $comment, $modifyUserID): array
    {
        $sql = "
            insert into boiling (code, startDate, density, amount, tankID, beerID, comment, modifyUserID) 
            VALUE ('$code', '$startDate', '$density', $amount, $tankID, $beerID, '$comment', $modifyUserID)";
        return $this->baseInsert($sql);
    }

    function insertFermentation($code, $density, $yeastID, $tankID, $beerID, $comment, $startDate, $userID): array
    {
        $sql = "INSERT INTO `fermentation`(
            `code`,
            `density`,
            `yeastAddDate`,
            `yeastID`,
            `tankID`,
            `beerID`,
            `active`,
            `comment`,
            `startDate`,
            `modifyUserID`
        )
        VALUES('$code', '$density', '$startDate', $yeastID, $tankID, $beerID, 1, '$comment', '$startDate', $userID)";
        return $this->baseInsert($sql);
    }

    function mapBoilingToFermentation($bID, $fID, $amount): array
    {
        $sql = "INSERT INTO `b_to_f_map`(`bID`, `fID`, `amount`) VALUES ($bID, $fID, $amount)";
        return $this->baseInsert($sql);
    }

    function insertFermentationData($sql): array
    {
        return $this->baseInsert($sql);
    }

    function updateFermentationItem($fID): array
    {
        $sql = "UPDATE `fermentation` SET 
                `density` = (SELECT `value` FROM `f_data` WHERE `fID` = $fID AND dataType = 8 ORDER BY `measurementDate` DESC, `ID` DESC LIMIT 1),
                `ph` = (SELECT `value` FROM `f_data` WHERE `fID` = $fID AND dataType = 6 ORDER BY `measurementDate` DESC, `ID` DESC LIMIT 1),
                `pressure` = (SELECT `value` FROM `f_data` WHERE `fID` = $fID AND dataType = 7 ORDER BY `measurementDate` DESC, `ID` DESC LIMIT 1)
                WHERE `ID` = $fID";
        return $this->baseInsert($sql);
    }

    /**
     * update
     */
    function updateFermentationSealingDate($fID, $dateStr): array
    {
        if (is_null($dateStr)) {
            $sql = "UPDATE `fermentation` SET `sealingDate` = null WHERE ID = $fID";
        } else {
            $sql = "UPDATE `fermentation` SET `sealingDate` = '$dateStr' WHERE ID = $fID";
        }
        return $this->baseInsert($sql);
    }

    /**
     * tanks
     */
    public function insertTank($number, $title, $volume, $tankType, $comment, $status, $sortValue, $modifyUserID): array
    {
        $sql = "
            INSERT INTO `tanks`(
                `number`,
                `title`,
                `volume`,
                `tankType`,
                `comment`,
                `status`,
                `sortValue`,
                `modifyUserID`
            ) VALUE ('$number', '$title', '$volume', $tankType, '$comment', $status, '$sortValue', $modifyUserID)";
        return $this->baseInsert($sql);
    }

    public function updateTank($tankID, $number, $title, $volume, $tankType, $comment, $status, $sortValue, $modifyUserID): array
    {
        $sql = "
            UPDATE
                `tanks`
            SET
                `number` = '$number',
                `title` = '$title',
                `volume` = '$volume',
                `tankType` = $tankType,
                `comment` = '$comment',
                `status` = $status,
                `sortValue` = '$sortValue',
                `modifyUserID` = $modifyUserID
            WHERE
                `tanks`.ID = $tankID ";
        return $this->baseInsert($sql);
    }

    public function deactivateTank($tankID, $modifyUserID): array
    {
        $sql = "
            UPDATE
                `tanks`
            SET
                `status` = 2,
                `modifyUserID` = $modifyUserID
            WHERE
                `tanks`.ID = $tankID ";
        return $this->baseInsert($sql);
    }

    public function getFermentationTanks(): array
    {
        $sql = "SELECT t.* FROM `tanks` t LEFT JOIN dictionary_items di ON di.id = t.`tankType` " .
            "WHERE di.code = 'ttFermentation' AND t.status > 0 ORDER BY t.sortValue";
        return $this->getDataAsArray($sql);
    }

    public function getAllActiveFermentation(): array
    {
        $sql = "SELECT
                    f.*,
                    ifnull((SELECT SUM(`amount`) FROM `b_to_f_map` WHERE fID = f.ID GROUP BY `fID` LIMIT 1), 0) -
                    ifnull((SELECT SUM(`amount`) FROM `pour_in_filtration_map` WHERE fermentationID = f.ID GROUP BY `fermentationID` LIMIT 1), 0) -
                    ifnull((SELECT sum(b.volume * s.count) AS amount FROM sales s
                        LEFT JOIN barrel b ON s.barrelID = b.ID
                        WHERE `producedBeerID` MOD 2 = 0 AND s.beerOriginID = f.ID
                        GROUP BY s.beerOriginID
                        limit 1), 0)
                       AS amount
                FROM fermentation f
                WHERE f.active = 1";
        return $this->getDataAsArray($sql);
    }

    function emptyingFermentation(
        $tankID,
        $fermentationID,
        $emptyingDate,
        $amount,
        $comment,
        $modifyUserID
    ): array
    {
        $sql = "
            INSERT INTO `emptying`(
                `tankID`,
                `fermentationID`,
                `emptyingDate`,
                `amount`,
                `comment`,
                `modifyUserID`
            )
            VALUES(
                $tankID,
                $fermentationID,
                '$emptyingDate',
                $amount,
                $comment,
                $modifyUserID) ";

        $result = $this->baseInsert($sql);

        if ($result) {
            // set fermentation status to INACTIVE
            $sqlCloseFermentation = "UPDATE `fermentation` SET `active` = 2 WHERE ID = $fermentationID";
            $this->baseInsert($sqlCloseFermentation);
        }

        return $result;
    }

    function terminateYeast($yeastID, $userID)
    {
        $sql = "UPDATE `yeast` SET 
                `status` = 2,
                `modifyDate` = CURRENT_TIMESTAMP,
                `modifyUserID` = $userID
                WHERE ID = $yeastID";
        $this->baseInsert($sql);
    }

    public function getProducedBeer(): array
    {
        $sql = "SELECT (b.ID * 2) + valueInt - 1 AS producedBeerID, b.id AS beerID, name, valueText AS beerType, b.`status`, b.color, valueInt AS unfiltered FROM beers b
                JOIN
                (
                    SELECT valueInt, valueText, di.id FROM `dictionary_items` di
                    LEFT JOIN dictionary d ON d.id = di.`dictionaryID`
                    WHERE d.code = 'beerType'
                    ORDER by sortID
                ) bt
                WHERE b.status > 0
                ORDER BY outBeerID";

        return $this->getDataAsArray($sql);
    }
}