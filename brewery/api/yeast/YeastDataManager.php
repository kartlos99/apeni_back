<?php

class YeastDataManager extends BaseDataManager
{

    public function createYeast(
        $name, $userID
    ): array
    {
        $sql = "
            INSERT INTO `yeast`(
                `code`,
                `modifyUserID`
            )
            VALUES('$name', '$userID')";

        return $this->baseInsert($sql);
    }

    public function getYeasts(): array
    {
        $sql = "SELECT `ID`, `code`, `parentID`, `ph`, `useCount`, `itemCreateDate`, `status`, `comment`, `modifyDate`, `modifyUserID` FROM `yeast` WHERE `status` > 0";
        return $this->getDataAsArray($sql);
    }

}