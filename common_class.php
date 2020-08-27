<?php

class OrderHelper
{

    public $con;

    function __construct($db_con)
    {
        $this->con = $db_con;
    }

    function attachItemsToOrder($orders)
    {
        $orderIDs = "";
        foreach ($orders as $order) {
            $orderIDs .= $order['ID'] . ',';
        }
        $orderIDs = trim($orderIDs, ',');


        $sql = " SELECT * FROM `order_items` WHERE `orderID` IN ($orderIDs) ";

        $orderItems = [];
        $result = mysqli_query($this->con, $sql);
        while ($rs = mysqli_fetch_assoc($result)) {
            $orderItems[] = $rs;
        }


        $sql =
            "SELECT `orderID`, `beerID`, `chek`,`canTypeID`, sum(`count`) AS `count` FROM `sales` 
            WHERE `orderID` IN ($orderIDs)
            GROUP BY `orderID`, `beerID`, `canTypeID`";

        $sales = [];
        $result = mysqli_query($this->con, $sql);
        if (mysqli_num_rows($result) > 0)
            while ($rs = mysqli_fetch_assoc($result)) {
                $sales[] = $rs;
            }


        foreach ($orders as $index => $order) {
            $oItems = [];
            foreach ($orderItems as $item) {
                if ($order['ID'] == $item['orderID']) {
                    $oItems[] = $item;
                }
            }
            $oSales = [];
            foreach ($sales as $item) {
                if ($order['ID'] == $item['orderID']) {
                    $oSales[] = $item;
                }
            }

            $orders[$index]['items'] = $oItems;
            $orders[$index]['sales'] = $oSales;
        }

        return $orders;
    }

    function checkOrderCompletion($orderID)
    {
        $isCompleted = true;

        $sqlGetDifference =
            "SELECT o.`beerID`, o.`canTypeID`, o.`count`, (o.count - ifnull(s.saleCount, 0)) AS difference FROM `order_items` o
                LEFT JOIN (
                    SELECT beerID, canTypeID, SUM(count) AS saleCount FROM sales
                    WHERE orderID = $orderID
                    GROUP BY beerID, canTypeID
                ) s
                ON o.beerID = s.beerID AND o.canTypeID = s.canTypeID
                WHERE o.`orderID`= $orderID";

        $result = mysqli_query($this->con, $sqlGetDifference);
        while ($rs = mysqli_fetch_assoc($result)) {
            if ($rs['difference'] > 0)
                $isCompleted = false;
        }

        if ($isCompleted) {
            $updateOrderSql =
                "UPDATE `orders` SET `orderStatusID` = " . ORDER_STATUS_COMPLETED .
                " WHERE ID = " . $orderID;

            mysqli_query($this->con, $updateOrderSql);
        }

        return $isCompleted ;
    }
}

class VersionControl {
    public $con;

    function __construct($db_con)
    {
        $this->con = $db_con;
    }

    function updateVersionFor($field) {
        $sql = "UPDATE `versionflow` SET $field = $field + 1 ";
        mysqli_query($this->con, $sql);
    }
}