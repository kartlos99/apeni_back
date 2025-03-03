<?php

// const CUSTOM_HTTP_ERROR_CODE = 422;
// const KEY_ERROR_CODE = "errorCode";
// const KEY_ERROR_MESSAGE = "errorMessage";

class BaseDbManagerV2
{
    // const CUSTOM_HTTP_ERROR_CODE = 422;
    private $httpErrorCode = 422;

    private $dbConn;

    public function __construct()
    {
        $this->dbConn = mysqli_connect(HOST, DB_user, DB_pass, DB_name) or die('db_connection_error!.. class:BaseDbManager');
        mysqli_set_charset($this->dbConn, "utf8");
    }
    
    function dieWithHttpError($errorText = "un known error", $errorCode = 0, $httpStatusCode = 422)
    {
        // die( $errorText . $errorCode . $httpStatusCode11);
        http_response_code($httpStatusCode);
        die(json_encode([
            errorMessage => $errorText,
            errorCode => $errorCode
        ]));
    }
    
    function dieWithDbError() {
        // die(mysqli_errno($this->dbConn));
        // die(sqlErrorWithCode());
        $this->dieWithHttpError(
            mysqli_error($this->dbConn),
            json_encode(mysqli_errno($this->dbConn))
        );
    }

    public function closeConnection()
    {
        mysqli_close($this->dbConn);
    }

    function getDataAsIdNameMap($sqlQuery): array
    {
        $result = $this->getDataAsArray($sqlQuery);
        $resultArray = [];
        foreach ($result as $row) {
            $resultArray[$row['id']] = $row['name'];
        }
        return $resultArray;
    }

    function executeScript($sql): bool {
        return mysqli_query($this->dbConn, $sql);
    }

    function getDataAsArray($sqlQuery): array
    {
        $resultArray = [];
        try {
            $result = mysqli_query($this->dbConn, $sqlQuery);
            if ($result) {
                while ($rs = mysqli_fetch_assoc($result)) {
                    $resultArray[] = $rs;
                }
            } else {
                $this->dieWithDbError();
            }
        } catch (Exception $e) {
            $this->dieWithDbError();
        }
        return $resultArray;
    }

    function getDataAsArrayOfString($sqlQuery): array
    {
        $result = mysqli_query($this->dbConn, $sqlQuery);
        $resultArray = [];
        if ($result) {
            while ($rs = mysqli_fetch_assoc($result)) {
                $resultArray[] = json_encode($rs);
            }
        } else {
            $this->dieWithDbError();
        }
        return $resultArray;
    }

    function baseInsert($insertSql): array
    {
        try {
            $result = mysqli_query($this->dbConn, $insertSql);
            if (!$result)
                $this->dieWithDbError();
        } catch (Exception $e) {
            $this->dieWithDbError();
        }
        return [RECORD_ID_KEY => mysqli_insert_id($this->dbConn)];
    }

    protected function baseDelete($deleteSql)
    {
        return mysqli_query($this->dbConn, $deleteSql);
    }

    // function dieWithError($code, $text)
    // {
    //     $response[SUCCESS] = false;
    //     $response[ERROR_TEXT] = $text;
    //     $response[ERROR_CODE] = $code;
    //     die(json_encode($response));
    // }

    private function sqlErrorWithCode()
    {
        $fullError = [
            // "SQL_ERROR_CODE" => mysqli_errno($this->dbConn),
            "SQL_ERROR_TEXT" => mysqli_error($this->dbConn)
        ];
        return json_encode($fullError);
    }

    function getSingleValue($resultArray, $fieldName)
    {
        if (empty($resultArray)) {
            return null;
        } else {
            return $resultArray[0][$fieldName];
        }
    }

    function hasPermission($permissionCode, $userID): bool {
        $sql = "SELECT permissions.ID, permissions.code FROM `permission_mapping` 
            LEFT JOIN permissions ON permission_mapping.permissionID = permissions.ID
            WHERE permission_mapping.roleID = (SELECT users.type FROM users WHERE users.id = $userID)";

        $permissionList = $this->getDataAsArray($sql);
        foreach ($permissionList as $permission) {
            if ($permission['code'] == $permissionCode)
                return true;
        }
        return false;
    }
}