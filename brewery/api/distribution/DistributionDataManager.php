<?php

class DistributionDataManager extends BaseDataManager
{

    private function getRawSaleInfo($date): array {
        $sql = "SELECT `clientID`, `operatorID`, `producedBeerID`, `barrelID`, `count`, `modifyUserID`, b.volume AS unitVolume FROM `sales` s 
                LEFT JOIN barrel b ON s.barrelID = b.id
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
            "distributionData" => $this->getRawSaleInfo($date),
            "producedBeers" => $this->getProducedBeers()
        ];
    }
}