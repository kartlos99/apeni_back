<?php

require_once "BaseDbManager.php";

class ChangesReporter extends BaseDbManager
{

    private $userID;
    private $passedDays = 0;
    private $tableName;
    private $recordID;

    public function __construct($userID)
    {
        parent::__construct();
        $this->userID = $userID;
    }

    function checkRecord($tableName, $recordID)
    {
        $this->tableName = trim($tableName, '`');
        $this->recordID = $recordID;
        $FIELD_NAME = "passedDays";

        $sqlGetPassedDays = "SELECT DATEDIFF( CURRENT_DATE, date(`modifyDate`)) AS $FIELD_NAME " .
            "FROM $tableName " .
            "WHERE ID = $recordID";

        $result = $this->getDataAsArray($sqlGetPassedDays);
        $gap = $this->getSingleValue($result, $FIELD_NAME);
        if (!is_null($gap)) {
            $this->passedDays = $gap;
        }
    }

    function logAsNeed(): int
    {
        if ($this->passedDays > 0) {
            return $this->logChange();
        } else {
            return -1;
        }
    }

    private function logChange(): int
    {
        $sql = "INSERT INTO `changeslog` (`tableName`, `editedRecordID`, `modifyUsedID`)" .
            "VALUES ('$this->tableName', '$this->recordID', '$this->userID')";

        return $this->baseInsert($sql)[RECORD_ID_KEY];
    }
}