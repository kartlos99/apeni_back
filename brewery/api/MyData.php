<?php

use function Apeni\JWT\dieWithError;

class MyData
{
    private $dbConn;

    function __construct($dbConn)
    {
        $this->dbConn = $dbConn;
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


}