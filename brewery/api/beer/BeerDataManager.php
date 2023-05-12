<?php

class BeerDataManager extends BaseDataManager
{

    public function insertBeer(
        $name, $price, $status, $color, $sortValue
    ): array
    {
        if (empty($sortValue))
            $sortValue = "(SELECT AUTO_INCREMENT FROM information_schema.tables WHERE TABLE_NAME = 'beers' AND table_schema = 'brewery')";
        else
            $sortValue = "'" . $sortValue . "'";

        $sql = "
            INSERT INTO `beers`(
                `name`,
                `price`,
                `status`,
                `color`,
                `sortValue`
            )
            VALUES(
            '$name', '$price', '$status', '$color', $sortValue
        )";

        return $this->baseInsert($sql);
    }

    public function getBeers(): array
    {
        $sql = "SELECT
                    `ID`,
                    `name`,
                    `price`,
                    `status`,
                    `color`,
                    `sortValue`
                FROM
                    `beers`
                WHERE
                    `status` > 0";
        return $this->getDataAsArray($sql);
    }

    public function updateBeer($beerID, $name, $price, $status, $color, $sortValue): array
    {
        $sql = "UPDATE
                    `beers`
                SET
                    `name` = '$name',
                    `price` = '$price',
                    `status` = '$status',
                    `color` = '$color',
                    `sortValue` = '$sortValue'
                WHERE
                    `ID` = $beerID";
        return $this->getDataAsArray($sql);
    }

}