<?php

class BoilerDataManager extends BaseDataManager
{
    public function addBoilingToFermentationMap($bID, $fID, $amount): array
    {
        $sql = "INSERT INTO `b_to_f_map`(`bID`, `fID`, `amount`)
                VALUES($bID, $fID, $amount)";
        return $this->baseInsert($sql);
    }

    public function removeYeastFromFermentation($yeastID)
    {
        $sql = "UPDATE
            `fermentation`
        SET
            `yeastRemoveDate` = CURRENT_TIMESTAMP
        WHERE
            `yeastID` = $yeastID AND `active` = 1";

        $this->baseInsert($sql);
    }

    function inputYeastIntoFermentation($yeastID, $fermentationID)
    {
        $sql = "UPDATE
            `fermentation`
        SET
            `yeastAddDate` = CURRENT_TIMESTAMP,
            `yeastRemoveDate` = null,
            `yeastID` = $yeastID
        WHERE
            `ID` = $fermentationID";

        $this->baseInsert($sql);
    }

    public function insertBoiling(
        $code, $startDate, $density, $amount, $tankID, $beerID, $boilingTime, $amountToVirlpool, $yeast, $comment, $modifyUserID
    ): array
    {
        $sql = "INSERT INTO `boiling`(
            `code`,
            `startDate`,
            `density`,
            `amount`,
            `tankID`,
            `beerID`,
            `boilingTime`,
            `amountToVirlpool`,
            `yeast`,
            `comment`,
            `modifyUserID`
        )
        VALUES(
            '$code', '$startDate', '$density', $amount, $tankID, $beerID, $boilingTime, $amountToVirlpool, $yeast, '$comment', $modifyUserID
        )";

        return $this->baseInsert($sql);
    }

    public function insertWaterDataItems($data, $boilingID, $userID): array
    {
        $deleteSql = "DELETE FROM `boiling_data_water` WHERE `boilingID` = $boilingID";
        $this->baseDelete($deleteSql);

        if (count($data) < 1)
            return [];

        $sql = "INSERT INTO `boiling_data_water`(
            `boilingID`,
            `type`,
            `amount`,
            `modifyUserID`
        )
        VALUES ";

        $multiValue = "";
        foreach ($data as $item) {
            $dataType = $item->type;
            $amount = $item->amount;
            $multiValue .= "($boilingID, $dataType, $amount, $userID),";
        }
        $values = trim($multiValue, ',');
        return $this->baseInsert($sql . $values);
    }

    public function insertSaltDataItems($data, $boilingID, $userID): array
    {
        $deleteSql = "DELETE FROM `boiling_data_salt` WHERE `boilingID` = $boilingID";
        $this->baseDelete($deleteSql);

        if (count($data) < 1)
            return [];

        $sql = "INSERT INTO `boiling_data_salt`(
            `boilingID`,
            `type`,
            `amount`,
            `modifyUserID`
        )
        VALUES ";

        $multiValue = "";
        foreach ($data as $item) {
            $dataType = $item->type;
            $amount = $item->amount;
            $multiValue .= "($boilingID, $dataType, $amount, $userID),";
        }
        $values = trim($multiValue, ',');
        return $this->baseInsert($sql . $values);
    }

    public function insertMaltDataItems($data, $boilingID, $userID): array
    {
        $deleteSql = "DELETE FROM `boiling_data_malt` WHERE `boilingID` = $boilingID";
        $this->baseDelete($deleteSql);

        if (count($data) < 1)
            return [];

        $sql = "INSERT INTO `boiling_data_malt`(
            `boilingID`,
            `name`,
            `amount`,
            `modifyUserID`
        )
        VALUES ";

        $multiValue = "";
        foreach ($data as $item) {
            $name = $item->name;
            $amount = $item->amount;
            $multiValue .= "($boilingID, '$name', $amount, $userID),";
        }
        $values = trim($multiValue, ',');
        return $this->baseInsert($sql . $values);
    }

    public function insertHopsDataItems($data, $boilingID, $userID): array
    {
        $deleteSql = "DELETE FROM `boiling_data_hops` WHERE `boilingID` = $boilingID";
        $this->baseDelete($deleteSql);

        if (count($data) < 1)
            return [];

        $sql = "INSERT INTO `boiling_data_hops`(
            `boilingID`,
            `name`,
            `amount`,
            `modifyUserID`
        )
        VALUES ";

        $multiValue = "";
        foreach ($data as $item) {
            $name = $item->name;
            $amount = $item->amount;
            $multiValue .= "($boilingID, '$name', $amount, $userID),";
        }
        $values = trim($multiValue, ',');
        return $this->baseInsert($sql . $values);
    }

    public function insertDelayDataItems($data, $boilingID, $userID): array
    {
        $deleteSql = "DELETE FROM `boiling_data_delay` WHERE `boilingID` = $boilingID";
        $this->baseDelete($deleteSql);

        if (count($data) < 1)
            return [];

        $sql = "INSERT INTO `boiling_data_delay`(
            `boilingID`,
            `type`,
            `temperature`,
            `delay`,
            `modifyUserID`
        )
        VALUES ";

        $multiValue = "";
        foreach ($data as $item) {
            $dataType = $item->type;
            $temperature = $item->temperature;
            $delay = $item->delay;
            $multiValue .= "($boilingID, $dataType, $temperature, $delay, $userID),";
        }
        $values = trim($multiValue, ',');
        return $this->baseInsert($sql . $values);
    }

    public function insertFilteringDataItems($data, $boilingID, $userID): array
    {
        $deleteSql = "DELETE FROM `boiling_data_filtring` WHERE `boilingID` = $boilingID";
        $this->baseDelete($deleteSql);

        if (count($data) < 1)
            return [];

        $sql = "INSERT INTO `boiling_data_filtring`(
            `boilingID`,
            `type`,
            `density`,
            `amount`,
            `modifyUserID`
        )
        VALUES ";

        $multiValue = "";
        foreach ($data as $item) {
            $dataType = $item->type;
            $density = $item->density;
            $amount = $item->amount;
            $multiValue .= "($boilingID, $dataType, $density, $amount, $userID),";
        }
        $values = trim($multiValue, ',');
        return $this->baseInsert($sql . $values);
    }

    public function nextBoilingID()
    {
        $sql = "SELECT MAX(`id`)+1 as nextID FROM `boiling` ";
        $data = $this->getDataAsArray($sql);
        return $data[0]["nextID"];
    }

    public function getLastBoilingData($beerID): array
    {
        $sql = "SELECT * FROM `boiling`
            WHERE `beerID` = $beerID
            ORDER BY startDate DESC
            LIMIT 1";
        $resultData = $this->getDataAsArray($sql);
        if (count($resultData) > 0)
            return $resultData[0];
        else
            return [];
    }

    public function getWaterData($boilingID): array
    {
        $sql = "SELECT
                `type`,
                `amount`
            FROM
                `boiling_data_water`
            WHERE
                `boilingID` = $boilingID
            order by `type`";
        return $this->getDataAsArray($sql);
    }

    public function getSaltData($boilingID): array
    {
        $sql = "SELECT
                `type`,
                `amount`
            FROM
                `boiling_data_salt`
            WHERE
                `boilingID` = $boilingID 
                order by `type`";
        return $this->getDataAsArray($sql);
    }

    public function getMaltData($boilingID): array
    {
        $sql = "SELECT
                `name`,
                `amount`
            FROM
                `boiling_data_malt`
            WHERE
                `boilingID` = $boilingID 
                order by `name`";
        return $this->getDataAsArray($sql);
    }

    public function getHopsData($boilingID): array
    {
        $sql = "SELECT
                `name`,
                `amount`
            FROM
                `boiling_data_hops`
            WHERE
                `boilingID` = $boilingID 
                order by `name`";
        return $this->getDataAsArray($sql);
    }

    public function getDelayData($boilingID): array
    {
        $sql = "SELECT
                `type`,
                `temperature`,
                `delay`
            FROM
                `boiling_data_delay`
            WHERE
                `boilingID` = $boilingID 
                order by `type`";
        return $this->getDataAsArray($sql);
    }

    public function getFilteringData($boilingID): array
    {
        $sql = "SELECT
                `type`,
                `density`,
                `amount`
            FROM
                `boiling_data_filtring`
            WHERE
                `boilingID` = $boilingID 
                order by `type`";
        return $this->getDataAsArray($sql);
    }

    public function getIncompleteBoilings(): array
    {
        $query = "SELECT * FROM `boiling` b
                    LEFT JOIN v_boiling_distribution_sum dist_sum
                    ON b.id = dist_sum.boilingID
                    WHERE abs(b.`amountToVirlpool` - dist_sum.distributedAmount) > 10";
        return parent::getDataAsArray($query);
    }
}