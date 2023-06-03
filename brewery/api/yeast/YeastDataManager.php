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
        $sql = "SELECT `ID`, `code`, `parentID`, `useCount`, `itemCreateDate`, `status`, `comment`, `modifyDate`, `modifyUserID`, 
                ifnull((SELECT`value`
                FROM `yeast_data`
                WHERE
                    `measurementDate` = (
                    SELECT MAX(`measurementDate`)
                    FROM yeast_data
                    WHERE yeastID = `yeast`.`ID`
                )
                ), 0) AS ph
                FROM `yeast` 
                WHERE `status` > 0";
        return $this->getDataAsArray($sql);
    }

}