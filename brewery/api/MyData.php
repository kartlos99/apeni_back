<?php

use function Apeni\JWT\dieWithError;

class MyData
{
    private $dbConn;

    function __construct($dbConn)
    {
        $this->dbConn = $dbConn;
    }

    function getFermentationDataByTankID($tankID) {
        $sql = "SELECT * FROM `f_data`
            WHERE `fID`= (SELECT ID FROM `fermentation`
                        WHERE `tankID`=$tankID AND `active` = 1)
            ORDER by `modifyDate` ";
        return $this->getDataAsArray($sql);
    }
    function getFermentationDataByID($fID) {
        $sql = "SELECT * FROM `f_data`
            WHERE `fID`= $fID
            ORDER by `modifyDate` ";
        return $this->getDataAsArray($sql);
    }

    function getTanks()
    {
        $bData = [];
        $sql = "
            SELECT t.*, di.code FROM `tanks` t
            LEFT JOIN dictionary_items di ON tankType = di.id
            WHERE active = 1
            ORDER BY tankType, sortValue";
        $arr = $this->getDataAsArray($sql);
        foreach ($arr as $key => $item) {
            // $bData[$item[ID]] = $item;
            $bData[] = $item;
        }
        return $bData;
    }

    function getClients()
    {
        $sql = "SELECT id, dasaxeleba FROM `obieqtebi` WHERE `active`=1 ORDER BY dasaxeleba";
        return $this->getDataAsArray($sql);
    }

    /**
     * @param $query
     * @return array
     * base data retrieving function
     * returns data as Array from DB according sql query
     */
    function getDataAsArray($query)
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
    private function baseInsert($insertSql)
    {
        $result = mysqli_query($this->dbConn, $insertSql);
        if (!$result)
            dieWithError(422, mysqli_error($this->dbConn));
        return [RECORD_ID_KEY => mysqli_insert_id($this->dbConn)];
    }

    public function insertBoiling($code, $startDate, $density, $amount, $tankID, $beerID, $comment, $modifyUserID)
    {
        $sql = "
            insert into boiling (code, startDate, density, amount, tankID, beerID, comment, modifyUserID) 
            VALUE ('$code', '$startDate', '$density', $amount, $tankID, $beerID, '$comment', $modifyUserID)";
        return $this->baseInsert($sql);
    }

    function insertFermentation($code, $density, $comment, $startDate, $userID)
    {
        $sql = "INSERT INTO `fermentation`(
            `code`,
            `density`,
            `tankID`,
            `beerID`,
            `active`,
            `comment`,
            `startDate`,
            `modifyUserID`
        )
        VALUES('$code', '$density', 1, 1, 1, '$comment', '$startDate', $userID)";
        return $this->baseInsert($sql);
    }

    function mapBoilingToFermentation($bID, $fID, $amount) {
        $sql = "INSERT INTO `b_to_f_map`(`bID`, `fID`, `amount`) VALUES ($bID, $fID, $amount)";
        return $this->baseInsert($sql);
    }

    function insertFermentationData($sql) {
        return $this->baseInsert($sql);
    }

    /**
     * update
     */
    function updateFermentationSealingDate($fID, $dateStr) {

        if (is_null($dateStr)){
            $sql = "UPDATE `fermentation` SET `sealingDate` = null WHERE ID = $fID";
        } else {
            $sql = "UPDATE `fermentation` SET `sealingDate` = '$dateStr' WHERE ID = $fID";
        }
        return $this->baseInsert($sql);
    }
}