<?php

use function Apeni\JWT\dieWithError;

class BaseDataManager
{

    private $dbConn;

    function __construct()
    {
        $this->dbConn = mysqli_connect(HOST, DB_user, DB_pass, DB_name) or die('db_connection_error!..');
        mysqli_set_charset($this->dbConn, "utf8");
    }

    public function closeConnection()
    {
        mysqli_close($this->dbConn);
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
            dieWithError(CUSTOM_HTTP_ERROR_CODE, mysqli_error($this->dbConn));
        }
        return $arr;
    }

    /**
     * @param $insertSql
     * @return array
     * base function for data inserting
     */
    protected function baseInsert($insertSql): array
    {
        $result = mysqli_query($this->dbConn, $insertSql);
        if (!$result)
            dieWithError(CUSTOM_HTTP_ERROR_CODE, mysqli_error($this->dbConn));
        return [RECORD_ID_KEY => mysqli_insert_id($this->dbConn)];
    }

    protected function baseDelete($deleteSql)
    {
        return mysqli_query($this->dbConn, $deleteSql);
    }

    protected function dieWithDataError($text = "un known error", $errorCode = null) {
        dieWithError(CUSTOM_HTTP_ERROR_CODE, $text, $errorCode);
    }
}