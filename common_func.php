<?php


class OrderHelper {

    public $con;

    function __construct($db_con)
    {
        $this->con = $db_con;
    }

    function attachItemsToOrder($orders){
        $orderIDs = "";
        foreach ($orders as $order) {
            $orderIDs .= $order['ID'] . ',';
        }
        $orderIDs = trim($orderIDs, ',');


        $sql = " SELECT * FROM `order_items` WHERE `orderID` IN ($orderIDs) ";

        $orderItems = [];
        $result = mysqli_query( $this->con, $sql);
        while ($rs = mysqli_fetch_assoc($result)) {
            $orderItems[] = $rs;
        }


        $sql = "
        SELECT `orderID`, `beerID`, `chek`,`canTypeID`, sum(`count`) AS `count` FROM `sales` 
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

}