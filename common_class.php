<?php
/*
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


        $sql = "SELECT oi.*, l.dasaxeleba FROM `order_items` oi " .
            "LEFT JOIN ludi l ON l.id = oi.beerID " .
            "WHERE `orderID` IN ($orderIDs) ";

        $orderItems = [];
        $result = mysqli_query($this->con, $sql);
        while ($rs = mysqli_fetch_assoc($result)) {
            $orderItems[] = $rs;
        }


        $sql =
            "SELECT `orderID`, `beerID`, `chek`,`canTypeID`, sum(`count`) AS `count`, u.username AS distributor FROM `sales` s 
            LEFT JOIN users u 
            ON u.id = s.`modifyUserID` 
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

    function attachTakenMoney($orders, $date) {
        $clientIDs = "";
        foreach ($orders as $order) {
            $clientIDs .= $order['clientID'] . ',';
        }
        $clientIDs = trim($clientIDs, ',');

        $sqlMoneyPerClient =
            "SELECT `obieqtis_id`, `distributor_id`, SUM(`tanxa`) AS money FROM `moneyoutput` 
                WHERE date(`tarigi`) = '$date' AND `obieqtis_id` IN ($clientIDs)
                GROUP BY `obieqtis_id` ";

        $moneyArr = [];
        $resultMoney = mysqli_query($this->con, $sqlMoneyPerClient);
        if (mysqli_num_rows($resultMoney) > 0)
            while ($rs = mysqli_fetch_assoc($resultMoney)) {
                $moneyArr[] = $rs;
            }

        foreach ($orders as $index => $order) {
            $amount = [];
            foreach ($moneyArr as $item) {
                if ($order['clientID'] == $item['obieqtis_id']) {
                    $amount[] = $item;
                }
            }
            $orders[$index]['amount'] = $amount;
        }
        return $orders;
    }

    function attachEmptyBarrels($orders, $date) {
        $clientIDs = "";
        foreach ($orders as $order) {
            $clientIDs .= $order['clientID'] . ',';
        }
        $clientIDs = trim($clientIDs, ',');

        $sqlBarrelsPerClient =
            "SELECT `clientID`, `distributorID`, `canTypeID`, SUM(`count`) AS `count` FROM `barrel_output` 
                WHERE date(`outputDate`) = '$date' AND `clientID` IN ($clientIDs)
                GROUP BY `clientID`, `canTypeID` ";

        $arr = [];
        $result = mysqli_query($this->con, $sqlBarrelsPerClient);
        if (mysqli_num_rows($result) > 0)
            while ($rs = mysqli_fetch_assoc($result)) {
                $arr[] = $rs;
            }

        foreach ($orders as $index => $order) {
            $barrels = [];
            foreach ($arr as $item) {
                if ($order['clientID'] == $item['clientID']) {
                    $barrels[] = $item;
                }
            }
            $orders[$index]['emptyBarrels'] = $barrels;
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
}*/

/*class VersionControl {
    public $con;

    function __construct($db_con)
    {
        $this->con = $db_con;
    }

    function updateVersionFor($field) {
        $sql = "UPDATE `versionflow` SET $field = $field + 1 ";
        mysqli_query($this->con, $sql);
    }
}*/

/*
class DataProvider {
    public $dbConn;

    function __construct($dbConn) {
        $this->dbConn = $dbConn;
    }

    function getBarrels() {
        $bData = [];
        $sql = "SELECT * FROM `kasri` ORDER BY `sortValue` desc";
        $result = mysqli_query($this->dbConn, $sql);
        if ($result) {
            $arr = [];
            while ($rs = mysqli_fetch_assoc($result)) {
                $arr[] = $rs;
            }
            foreach ($arr as $key => $item) {
                $bData[$item['id']] = $item;
            }
        }
        return $bData;
    }

    function getClients() {
        $sql = "SELECT id, dasaxeleba FROM `customer` WHERE `active`=1 ORDER BY dasaxeleba";
        $result = mysqli_query($this->dbConn, $sql);
        $arr = [];
        if ($result) {
            while ($rs = mysqli_fetch_assoc($result)) {
                $arr[] = $rs;
            }
        }
        return $arr;
    }
}*/

/*
class DbKey {
    public static $CUSTOMER_MAP_TB = "`customer_to_region_map`";
    public static $USER_MAP_TB = "`user_to_region_map`";
}*/