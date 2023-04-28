<?php

class DistributionDataManager extends BaseDataManager
{

    private function getSaleVolumeByBeerKind($date): array
    {
        $sql = "SELECT `producedBeerID`, SUM(`count` * b.volume) AS amount FROM `sales` s
                LEFT JOIN barrel b ON b.id = s.`barrelID`
                WHERE date(`saleDate`) = '$date'   
                GROUP BY producedBeerID";

        return $this->getDataAsArray($sql);
    }

    private function getRawSaleInfo($date): array {
        $sql = "SELECT `clientID`, `operatorID`, `producedBeerID`, `barrelID`, `count`, `modifyUserID` FROM `sales`
                WHERE date(`saleDate`) = '$date'";
        return $this->getDataAsArray($sql);
    }

    private function getProducedBeers(): array
    {
        $sql = "SELECT * FROM `produced_beer` WHERE 1";
        return $this->getDataAsArray($sql);
    }

    public function getTotalInfo($date): array
    {
        return [
            "distributionVolume" => $this->getSaleVolumeByBeerKind($date),
            "distributionData" => $this->getRawSaleInfo($date),
            "producedBeers" => $this->getProducedBeers()
        ];
    }
}