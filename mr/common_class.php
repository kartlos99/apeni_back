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


        $sql = "SELECT oi.*, l.dasaxeleba FROM apenige2_mr3.order_items oi " .
            "LEFT JOIN apenige2_mr3.ludi l ON l.id = oi.beerID " .
            "WHERE `orderID` IN ($orderIDs) ";

        $orderItems = [];
        $result = mysqli_query($this->con, $sql);
        while ($rs = mysqli_fetch_assoc($result)) {
            $orderItems[] = $rs;
        }

        $sqlBottleOrderItems = "SELECT `id`, `orderID`, `bottleID`, `count` FROM `order_items_bottle` WHERE `orderID` IN ($orderIDs)";
        $bottleOrderItems = [];
        $result = mysqli_query($this->con, $sqlBottleOrderItems);
        while ($rs = mysqli_fetch_assoc($result)) {
            $bottleOrderItems[] = $rs;
        }

        $sql =
            "SELECT `orderID`, `beerID`, `chek`,`canTypeID`, sum(`count`) AS `count`, u.username AS distributor, l.dasaxeleba FROM apenige2_mr3.sales s 
            LEFT JOIN apenige2_mr3.users u 
            ON u.id = s.`modifyUserID` 
            LEFT JOIN apenige2_mr3.ludi l ON l.id = s.beerID
            WHERE `orderID` IN ($orderIDs)
            GROUP BY `orderID`, `beerID`, `canTypeID`";

        $sales = [];
        $result = mysqli_query($this->con, $sql);
        if (mysqli_num_rows($result) > 0)
            while ($rs = mysqli_fetch_assoc($result)) {
                $sales[] = $rs;
            }

        $sqlBottleSales = "SELECT orderID, bottleID, SUM(`count`) AS `count` FROM bottle_sales 
                WHERE `orderID` IN ($orderIDs)
                GROUP BY `orderID`, bottleID";
        $bottleSales = [];
        $result = mysqli_query($this->con, $sqlBottleSales);
        while ($rs = mysqli_fetch_assoc($result)) {
            $bottleSales[] = $rs;
        }

        foreach ($orders as $index => $order) {
            $oItems = [];
            foreach ($orderItems as $item) {
                if ($order['ID'] == $item['orderID']) {
                    $oItems[] = $item;
                }
            }
            $oItemsBottle = [];
            foreach ($bottleOrderItems as $item) {
                if ($order['ID'] == $item['orderID']) {
                    $oItemsBottle[] = $item;
                }
            }
            $oSales = [];
            foreach ($sales as $item) {
                if ($order['ID'] == $item['orderID']) {
                    $oSales[] = $item;
                }
            }
            $oBottleSales = [];
            foreach ($bottleSales as $item) {
                if ($order['ID'] == $item['orderID']) {
                    $oBottleSales[] = $item;
                }
            }

            $orders[$index]['items'] = $oItems;
            $orders[$index]['bottleItems'] = $oItemsBottle;
            $orders[$index]['sales'] = $oSales;
            $orders[$index]['bottleSales'] = $oBottleSales;
        }

        return $orders;
    }

    function attachTakenMoney($orders, $date)
    {
        $clientIDs = "";
        foreach ($orders as $order) {
            $clientIDs .= $order['clientID'] . ',';
        }
        $clientIDs = trim($clientIDs, ',');

        $sqlMoneyPerClient =
            "SELECT `obieqtis_id`, `distributor_id`, SUM(`tanxa`) AS money, paymentType FROM apenige2_mr3.moneyoutput 
                WHERE date(`tarigi`) = '$date' AND `obieqtis_id` IN ($clientIDs)
                GROUP BY `obieqtis_id`, paymentType ";

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

    function attachEmptyBarrels($orders, $date)
    {
        $clientIDs = "";
        foreach ($orders as $order) {
            $clientIDs .= $order['clientID'] . ',';
        }
        $clientIDs = trim($clientIDs, ',');

        $sqlBarrelsPerClient =
            "SELECT `clientID`, `distributorID`, `canTypeID`, SUM(`count`) AS `count` FROM apenige2_mr3.barrel_output 
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

    function attachRegions($orders)
    {

        $sqlQuery = "SELECT `customerID`, `regionID` FROM apenige2_mr3.customer_to_region_map";
        $rMap = [];
        $result = mysqli_query($this->con, $sqlQuery);
        while ($rs = mysqli_fetch_assoc($result)) {
            $rMap[$rs['customerID']][] = $rs['regionID'];
        }

        foreach ($orders as $index => $order) {
//            20200612 ze asxabs
            $orders[$index]['availableRegions'] = $rMap[$order['clientID']];
        }

        return $orders;
    }

    function checkOrderCompletion($orderID): bool
    {
        $isCompletedForBarrels = true;
        $isCompletedForBottles = true;

        $sqlGetDifference =
            "SELECT o.`beerID`, o.`canTypeID`, o.`count`, (o.count - ifnull(s.saleCount, 0)) AS difference FROM apenige2_mr3.order_items o
                LEFT JOIN (
                    SELECT beerID, canTypeID, SUM(count) AS saleCount FROM apenige2_mr3.sales
                    WHERE orderID = $orderID
                    GROUP BY beerID, canTypeID
                ) s
                ON o.beerID = s.beerID AND o.canTypeID = s.canTypeID
                WHERE o.`orderID`= $orderID";

        $result = mysqli_query($this->con, $sqlGetDifference);
        while ($rs = mysqli_fetch_assoc($result)) {
            if ($rs['difference'] > 0)
                $isCompletedForBarrels = false;
        }

        $checkBottleDiffSql = "SELECT oib.bottleID, (oib.count - ifnull(bs1.saleCount , 0)) AS difference FROM order_items_bottle oib
            LEFT JOIN (
                SELECT bs.bottleID, SUM(bs.count) AS saleCount FROM bottle_sales bs
                WHERE orderID = $orderID
                GROUP BY bs.bottleID
                ) bs1
            ON oib.bottleID = bs1.bottleID
            WHERE oib.orderID = $orderID";
        $bottleCheckResult = mysqli_query($this->con, $checkBottleDiffSql);
        while ($rs = mysqli_fetch_assoc($bottleCheckResult)) {
            if ($rs['difference'] > 0)
                $isCompletedForBottles = false;
        }

        if ($isCompletedForBarrels && $isCompletedForBottles) {
            $updateOrderSql =
                "UPDATE `orders` SET `orderStatusID` = " . ORDER_STATUS_COMPLETED .
                " WHERE ID = " . $orderID;

            mysqli_query($this->con, $updateOrderSql);
        }

        return $isCompletedForBarrels && $isCompletedForBottles;
    }

    function getActiveOrderIDForClient($clientID, $regionID): int {
        $getOrderSql = "
            SELECT ifnull(max(o.ID), 0) AS orderID FROM `orders` o
            LEFT JOIN dictionary_items di ON di.id = o.orderStatusID
            WHERE di.code = 'order_active' AND o.`regionID` = $regionID AND o.`clientID` = $clientID";

        $result = mysqli_query($this->con, $getOrderSql);

        return mysqli_fetch_assoc($result)['orderID'];
    }
}

class VersionControl
{
    public $con;

    function __construct($db_con)
    {
        $this->con = $db_con;
    }

    function updateVersionFor($field)
    {
        $sql = "UPDATE `versionflow` SET $field = $field + 1 ";
        mysqli_query($this->con, $sql);
    }
}

class DataProvider
{
    public $dbConn;

    function __construct($dbConn)
    {
        $this->dbConn = $dbConn;
    }

    function sqlToArray($sqlQuery)
    {
        $resultArr = [];
        $result = mysqli_query($this->dbConn, $sqlQuery);
        if ($result) {
            while ($rs = mysqli_fetch_assoc($result)) {
                $resultArr[] = $rs;
            }
            return $resultArr;
        } else {
            $response[SUCCESS] = false;
            $response[ERROR_TEXT] = "sqlToArray: can't execute the sql query! " . $sqlQuery;
            $response[ERROR_CODE] = 132;
            die(json_encode($response));
        }
    }

    function getAvailableRegionsForCustomer($customerID)
    {
        $sqlQuery = "SELECT rm.`regionID`, r.name, r.ownStorage FROM `customer_to_region_map` rm
            LEFT JOIN regions r ON r.ID = rm.`regionID`
            WHERE rm.`active` = 1 AND `customerID` = $customerID
            ORDER BY r.name ";

        $result = mysqli_query($this->dbConn, $sqlQuery);
        $arr = [];
        while ($rs = mysqli_fetch_assoc($result)) {
            $arr[] = $rs;
        }
        return $arr;
    }

    function getBarrels()
    {
        $bData = [];
        $sql = "SELECT * FROM `kasri` ORDER BY `sortValue` desc";
        $result = mysqli_query($this->dbConn, $sql);
        if ($result) {
            $arr = [];
            while ($rs = mysqli_fetch_assoc($result)) {
                $arr[] = $rs;
            }
            foreach ($arr as $item) {
                $bData[$item['id']] = $item;
            }
        }
        return $bData;
    }

    function getClients()
    {
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
}

class DbKey
{
    public static $CUSTOMER_MAP_TB = "`customer_to_region_map`";
    public static $USER_MAP_TB = "`user_to_region_map`";
}

class QueryHelper
{

    function queryGlobalStoreBalance($date, $saleExceptionID = '', $outputExceptionID = '')
    {
        $additionalSaleFilter = $saleExceptionID == '' ? '' : " AND s.ID <> $saleExceptionID ";
        $additionalOutputFilter = $outputExceptionID == '' ? '' : " AND s.ID <> $outputExceptionID ";

        return "
            SELECT k.id, k.dasaxeleba AS barrelName, k.initialAmount,
                ifnull(bout.val, 0) +
                ifnull(sbout.val, 0) AS globalIncome,
                ifnull(sal.val, 0) +
                ifnull(shinput.val, 0) AS globalOutput
            FROM `kasri` k
            LEFT JOIN (
                SELECT `canTypeID`, SUM(`count`) AS val FROM `barrel_output` brl
                LEFT JOIN regions r ON r.ID = brl.regionID
                WHERE r.ownStorage = 0 AND DATE(`outputDate`) <= '$date' $additionalOutputFilter
                GROUP BY `canTypeID`
            ) bout ON k.id = bout.canTypeID
            
            LEFT JOIN (
                SELECT `barrelID`, SUM(`count`) AS val FROM `storehousebarreloutput` s
                LEFT JOIN regions r ON r.ID = s.regionID
                WHERE r.ownStorage = 1 AND DATE(`outputDate`) <= '$date'
                GROUP BY `barrelID`
            ) sbout ON k.id = sbout.barrelID
            
            LEFT JOIN (
                SELECT `canTypeID`, SUM(`count`) AS val FROM `sales` s
                LEFT JOIN regions r ON r.ID = s.regionID
                WHERE r.ownStorage = 0 AND DATE(`saleDate`) <= '$date' $additionalSaleFilter
                GROUP BY `canTypeID`
            ) sal ON k.id = sal.canTypeID
            
            LEFT JOIN (
                SELECT `barrelID`, SUM(`count`) AS val FROM `storehousebeerinpit` s
                LEFT JOIN regions r ON r.ID = s.regionID
                WHERE r.ownStorage = 1 AND `chek`=0 AND DATE(`inputDate`) <= '$date'
                GROUP BY `barrelID`
            ) shinput ON k.id = shinput.barrelID";
    }
}