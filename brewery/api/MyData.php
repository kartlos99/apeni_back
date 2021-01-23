<?php


class MyData
{
    private $dbConn;

    function __construct($dbConn)
    {
        $this->dbConn = $dbConn;
    }

    function getTanks()
    {
        $bData = [];
        $sql = "SELECT * FROM `tanks` ORDER BY `sortValue` desc";
        $arr = $this->getDataAsArray($sql);
        foreach ($arr as $key => $item) {
            $bData[$item['id']] = $item;
        }
        return $bData;
    }

    function getClients()
    {
        $sql = "SELECT id, dasaxeleba FROM `obieqtebi` WHERE `active`=1 ORDER BY dasaxeleba";
        return $this->getDataAsArray($sql);
    }

    function getDataAsArray($query)
    {
        $result = mysqli_query($this->dbConn, $query);
        $arr = [];
        if ($result) {
            while ($rs = mysqli_fetch_assoc($result)) {
                $arr[] = $rs;
            }
        }
        return $arr;
    }
}