<?php

require_once "BaseDbManager.php";

class ChangesReporter extends BaseDbManager
{

    private $userID;

    public function __construct($userID)
    {
        parent::__construct();
        $this->userID = $userID;
    }

    function checkForReport($tableName, $recordID): int
    {
        $FIELD_NAME = "passedDays";
        $sqlGetPassedDays = "SELECT DATEDIFF( CURRENT_DATE, date(`modifyDate`)) AS $FIELD_NAME " .
            "FROM $tableName " .
            "WHERE ID = $recordID";

        $result = $this->getDataAsArray($sqlGetPassedDays);
        $passedDays = $this->getSingleValue($result, $FIELD_NAME);
        if (is_null($passedDays) && $passedDays > 0) {
            return -1;
        } else {
            return $this->logChange(trim($tableName, '`'), $recordID);
        }
    }

    function logChange($tableName, $recordID): int
    {
        $sql = "INSERT INTO `changeslog` (`tableName`, `editedRecordID`, `modifyUsedID`)" .
            "VALUES ('$tableName', '$recordID', '$this->userID')";

        return $this->baseInsert($sql)[RECORD_ID_KEY];
    }
}