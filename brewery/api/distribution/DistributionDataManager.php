<?php

class DistributionDataManager extends BaseDataManager
{

    private function getSaleVolumeByBeerKind(): array
    {
        $sql = "SELECT `producedBeerID`, SUM(`count` * b.volume) AS amount FROM `sales` s
                LEFT JOIN barrel b ON b.id = s.`barrelID`
                WHERE 1
                GROUP BY producedBeerID";

        return $this->getDataAsArray($sql);
    }

    private function getRawSaleInfo(): array {
        $sql = "SELECT `clientID`, `operatorID`, `producedBeerID`, `barrelID`, `count`, `modifyUserID` FROM `sales`
                -- WHERE date(`saleDate`) = '2023-04-24'";
        return $this->getDataAsArray($sql);
    }

    private function getProducedBeers(): array
    {
        $sql = "SELECT * FROM `produced_beer` WHERE 1";
        return $this->getDataAsArray($sql);
    }

    public function getTotalInfo(): array
    {
        return [
            "producedBeers" => $this->getProducedBeers(),
            "distributionVolume" => $this->getSaleVolumeByBeerKind(),
            "distributionData" => $this->getRawSaleInfo()
        ];
    }
}