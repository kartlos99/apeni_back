<?php

class BaseDbManager
{

    private $dbConn;

    public function __construct()
    {
        $this->dbConn = mysqli_connect(HOST, DB_user, DB_pass, DB_name) or die('db_connection_error!..');
        mysqli_set_charset($this->dbConn, "utf8");
    }

    public function closeConnection()
    {
        mysqli_close($this->dbConn);
    }

    function getDataAsArray($sqlQuery): array
    {
        $result = mysqli_query($this->dbConn, $sqlQuery);
        $resultArray = [];
        if ($result) {
            while ($rs = mysqli_fetch_assoc($result)) {
                $resultArray[] = $rs;
            }
        } else {
            $this->dieWithError(BASE_ERROR_CODE, mysqli_error($this->dbConn));
        }
        return $resultArray;
    }

    protected function baseInsert($insertSql): array
    {
        $result = mysqli_query($this->dbConn, $insertSql);
        if (!$result)
            $this->dieWithError(BASE_ERROR_CODE, mysqli_error($this->dbConn));
        return [RECORD_ID_KEY => mysqli_insert_id($this->dbConn)];
    }

    protected function baseDelete($deleteSql)
    {
        return mysqli_query($this->dbConn, $deleteSql);
    }

    function dieWithError($code, $text)
    {
        $response[SUCCESS] = false;
        $response[ERROR_TEXT] = $text;
        $response[ERROR_CODE] = $code;
        die(json_encode($response));
    }

    function getSingleValue($resultArray, $fieldName)
    {
        if (empty($resultArray)) {
            return null;
        } else {
            return $resultArray[0][$fieldName];
        }
    }
}