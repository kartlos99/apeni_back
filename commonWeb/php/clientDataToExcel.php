<?php
session_start();
require_once "../../mr/_webLoad.php";

if (!isset($_SESSION['username'])) {
    $url = "http" . ((!empty($_SERVER['HTTPS'])) ? "s" : "") . "://" . $_SERVER['SERVER_NAME'] . $folder . "/login.php";
//    $url = str_replace('administrator/page1.php', 'login.php', $url);
    header("Location: $url");
}


function makeRow($columns, $teg)
{
    $hRow = "<tr>";
    foreach ($columns as $item) {
        $hRow .= "<" . $teg . ">" . $item . "</" . $teg . ">";
    }
    return $hRow . "</tr>";
}

$output = '<table class="table" bordered="1">';

if (isset($_GET["clientID"])) {

    $regionID = $_GET["regionID"];
    $clientID = $_GET["clientID"];
    $startDate = $_GET["startDate"];
    $endDate = $_GET["endDate"];

    if ($clientID > 0) {
        $myobj = "a";
        $sql = "select dasaxeleba from customer where id= $clientID";
        $res1 = mysqli_query($con, $sql);
        while ($r = mysqli_fetch_assoc($res1)) {
            $myobj = $r["dasaxeleba"];
        }
        $fileName = $myobj . "_" . $dateOnServer;

        $sql = "
        SELECT 
            DATE_FORMAT(c.tarigi, '%Y-%m-%d %H:%i') AS dt, 
            canCount,
            c.canTypeID, 
            c.canName AS kasri,
            c.dasaxeleba,
            lt,
            pr,
            c.pay, 
            k_out,
            (
                SELECT round(sum(pr-pay), 2) FROM `client_actions` sc
                WHERE sc.tarigi <= c.tarigi AND clientID = $clientID
            ) AS `bal`,
            c.id,
            c.comment,
            c.username
            FROM `client_actions` AS c
        WHERE 
            clientID = $clientID AND Date(c.tarigi) >= '$startDate' AND Date(c.tarigi) <= '$endDate'
            AND c.regionID = $regionID
        ORDER by c.tarigi DESC ";

        $result = mysqli_query($con, $sql);
        if (mysqli_num_rows($result) > 0) {
            $tHead = ["თარიღი", "რაოდენობა(კასრი)", "კასრის ტიპი", "ლუდი", "ლიტრი", "ღირებ.", "გადახდა", "კასრი დაბრუნ.", "ბალანსი", "კომენტარი", "დისტრიბუტორი"];
            $output .= makeRow($tHead, "th");
            while ($row = mysqli_fetch_array($result)) {
                $output .= makeRow([
                    $row["dt"],
                    $row["canCount"] == 0 ? '' : $row["canCount"],
                    $row["kasri"] == null ? '' : $row["kasri"],
                    $row["dasaxeleba"],
                    $row["lt"] == 0 ? '' : $row["lt"],
                    round($row["pr"], 2) == 0 ? '-' : round($row["pr"], 2),
                    round($row["pay"], 2) == 0 ? '-' : round($row["pay"], 2),
                    $row["k_out"],
                    $row["bal"],
                    $row["comment"] == null ? '' : $row["comment"],
                    $row["username"]
                ], "td");
            }


        }

    } else {

        $sql = "
        SELECT 
            DATE_FORMAT(c.tarigi, '%Y-%m-%d %H:%i') AS dt, 
            o.dasaxeleba AS obieqti,
            canCount,
            c.canTypeID, 
            c.canName AS kasri,
            c.dasaxeleba,
            lt,
            pr,
            c.pay, 
            k_out,
            c.id,
            c.comment,
            c.username
            FROM `client_actions` AS c, customer AS o
        WHERE 
            c.clientID = o.id AND Date(c.tarigi) >= '$startDate' AND Date(c.tarigi) <= '$endDate'
            AND c.regionID = $regionID
        ORDER by c.tarigi DESC";

        $result = mysqli_query($con, $sql);
        if (mysqli_num_rows($result) > 0) {
            $tHead = ["თარიღი", "ობიექტი", "რაოდენობა(კასრი)", "კასრის ტიპი", "ლუდი", "ლიტრი", "ღირებ.", "გადახდა", "კასრი დაბრუნ.", "კომენტარი", "დისტრიბუტორი"];
            $output .= makeRow($tHead, "th");
            while ($row = mysqli_fetch_array($result)) {
                $output .= makeRow([
                    $row["dt"],
                    $row["obieqti"],
                    $row["canCount"] == 0 ? '' : $row["canCount"],
                    $row["kasri"] == null ? '' : $row["kasri"],
                    $row["dasaxeleba"],
                    $row["lt"] == 0 ? '' : $row["lt"],
                    round($row["pr"], 2) == 0 ? '-' : round($row["pr"], 2),
                    round($row["pay"], 2) == 0 ? '-' : round($row["pay"], 2),
                    $row["k_out"],
                    $row["comment"],
                    $row["username"]
                ], "td");
            }
            $output .= '</table>';
        }

        $fileName = "saerto_" . $dateOnServer;
    }

    $output .= '</table>';

    header("Content-Type: application/xls");
    header("Content-Disposition: attachment; filename=$fileName.xls");
    echo $output;
}

mysqli_close($con);