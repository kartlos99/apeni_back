<?php

class YeastDataManager extends BaseDataManager
{

    public function insertYeast(
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

}