<?php

namespace Apeni\JWT;
require_once "../../BaseDbManager.php";
require_once "../../common_data.php";


class HistoryManager extends \BaseDbManager
{


    public function __construct()
    {
        parent::__construct();
    }

    function getHistory($recordID, $table): array
    {
        global $CUSTOMER_TB;
        global $BARREL_OUTPUT_TB;
        global $MONEY_OUTPUT_TB;
        global $ORDERS_TB;

        switch ($table) {
            case $CUSTOMER_TB:
                return $this->getCustomerHistory($recordID);
            case $BARREL_OUTPUT_TB:
                return $this->getBarrelOutputHistory($recordID);
            case $MONEY_OUTPUT_TB:
                return $this->getMoneyOutputHistory($recordID);
            case $ORDERS_TB:
                return $this->getOrderHistory($recordID);
            default:
                return [];
        }
    }

    private function formHistorySql($recordID, $tableName, $tableNameH, $fields): string
    {
        return "SELECT hid, " . implode(", ", $fields) . "
                FROM `$tableNameH`
                WHERE id = $recordID
                UNION ALL
                SELECT 0, " . implode(", ", $fields) . "
                FROM `$tableName`
                WHERE id = $recordID ";
    }

    private function getCustomerHistory($recordID): array
    {
        $fields = [
            "`id`", "dasaxeleba", "adress", "tel", "comment", "sk", "sakpiri", "active", "reg_date",
            "if(chek = 0, '-', 'დიახ') as isChecked",
            "modifyDate", "modifyUserID"
        ];
        return [
            HISTORY_KEY => $this->getDataAsArray($this->formHistorySql($recordID, "customer", "customer_history", $fields)),
            USERS_MAP_KEY => $this->getDistributorsNames()
        ];
    }

    private function getBarrelOutputHistory($recordID): array
    {
        $fields = [
            "id", "regionID", "outputDate", "clientID", "distributorID", "canTypeID", "count", "comment", "modifyDate", "modifyUserID"
        ];
        return [
            HISTORY_KEY => $this->getDataAsArray($this->formHistorySql($recordID, "barrel_output", "barrel_history", $fields)),
            USERS_MAP_KEY => $this->getDistributorsNames(),
            CUSTOMERS_MAP_KEY => $this->getCustomerNames(),
            BARRELS_MAP_KEY => $this->getBarrelNames()
        ] ;
    }

    private function getMoneyOutputHistory($recordID): array
    {
        $fields = [
            "id",
            "regionID",
            "tarigi",
            "obieqtis_id",
            "distributor_id",
            "tanxa",
            "paymentType",
            "comment",
            "modifyDate",
            "modifyUserID"
        ];
        return $this->getDataAsArray($this->formHistorySql($recordID, "moneyoutput", "money_history", $fields));
    }

    private function getOrderHistory($recordID): array
    {
        $fields = [
            "id",
            "regionID",
            "orderDate",
            "orderStatusID",
            "distributorID",
            "clientID",
            "comment",
            "sortValue",
            "modifyDate",
            "modifyUserID"
        ];
        return $this->getDataAsArray($this->formHistorySql($recordID, "orders", "orders_history", $fields));
    }

    private function getCustomerNames(): array {
        return $this->getDataAsIdNameMap("SELECT id, dasaxeleba AS name FROM `customer`");
    }
    private function getBarrelNames(): array {
        return $this->getDataAsIdNameMap("SELECT id, dasaxeleba AS name FROM `kasri`");
    }
    private function getBeerNames(): array {
        return $this->getDataAsIdNameMap("SELECT id, dasaxeleba AS name FROM `ludi`");
    }
    private function getDistributorsNames(): array {
        return $this->getDataAsIdNameMap("SELECT id, username as name FROM `users`");
    }
}