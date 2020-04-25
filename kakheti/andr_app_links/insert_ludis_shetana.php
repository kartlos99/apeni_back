<?php

// ---------- ludis shetana / kasri out / money out ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

// $currTime = date("Y-m-d H:i:s", time()+4*3600);
// $currTime1 = date("Y-m-d H:i:s", time()+4*3600+1);
// $currTime2 = date("Y-m-d H:i:s", time()+4*3600+2);
$currTime   = $_POST["set_tarigi"];
$currTime1  = $currTime.":01";
$currTime2  = $currTime.":02";

$mitana 	= $_POST["mitana"];
$kout 	    = $_POST["kout"];
$mout 	    = $_POST["mout"];
$id         = $_POST["id"]; // romelic unda ganvaaxlot

$obieqtis_id 	    = $_POST["obieqtis_id"];
$distributor_id 	= $_POST["distributor_id"];
$ludis_id   	= $_POST["beer_type"];
//$ludis_raod 	= $_POST["litraji"];
$ert_fasi 	    = $_POST["ert_fasi"];
$kasri30 	    = $_POST["k30"];
$kasri50        = $_POST["k50"];
$comment 	    = $_POST["comment"];

$kasri30out     = $_POST["k30out"];
$kasri50out     = $_POST["k50out"];

$tanxa 	        = $_POST["tanxa"];


if($mitana == '1'){
    if($id != "0"){
    $sql = "UPDATE `beerinput` 
       SET `tarigi` = '$currTime', `distributor_id` = '$distributor_id', `ludis_id` = '$ludis_id', `ert_fasi` = '$ert_fasi', `kasri30` = '$kasri30', `kasri50` = '$kasri50', `comment` = '$comment'
       WHERE
        `beerinput`.`id` = $id";
    }else{
    $sql = "INSERT INTO 
        `beerinput` 
        (`tarigi`, `obieqtis_id`, `distributor_id`, `ludis_id`, `ert_fasi`, `kasri30`, `kasri50`, `comment`) 
        VALUES 
        ('$currTime', '$obieqtis_id', '$distributor_id', '$ludis_id', '$ert_fasi', '$kasri30', '$kasri50', '$comment')";
    }

    if(mysqli_query($con, $sql)){	
	   // $last_id = mysqli_insert_id($con);
	    echo '1' ;
    } else {
	    echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
    }
}

if($kout == '1'){
    if($id != "0"){
        $sql1 = "UPDATE `kasrioutput`
            SET `tarigi` = '$currTime1', `distributor_id` = '$distributor_id', `kasri30` = '$kasri30out', `kasri50` = '$kasri50out', `comment` = '$comment'
            WHERE
                `id` = $id ";
    }else{
        $sql1 = "INSERT INTO 
        `kasrioutput` (`tarigi`, `obieqtis_id`, `distributor_id`, `kasri30`, `kasri50`, `comment`) 
        VALUES ('$currTime1', '$obieqtis_id', '$distributor_id', '$kasri30out', '$kasri50out', '$comment')";
    }
    

    if(mysqli_query($con, $sql1)){	
	   // $last_id1 = mysqli_insert_id($con);
	    echo '1' ;
    } else {
	    echo "ERROR: Could not able to execute $sql1. " . mysqli_error($con);
}

}

if($mout == '1'){
    if($id != "0"){
        $sql2 = "UPDATE `moneyoutput`
            SET `tarigi` = '$currTime2', `distributor_id` = '$distributor_id', `tanxa` = '$tanxa', `comment` = '$comment'
            WHERE
                `id` = $id ";
    }else{
        $sql2 = "INSERT INTO 
        `moneyoutput` 
           (`tarigi`, `obieqtis_id`, `distributor_id`, `tanxa`, `comment`) 
        VALUES 
            ('$currTime2', '$obieqtis_id', '$distributor_id', '$tanxa', '$comment')";
    }

    if(mysqli_query($con, $sql2)){	
	   // $last_id2 = mysqli_insert_id($con);
	    echo '1';
    } else {
	    echo "ERROR: Could not able to execute $sql2. " . mysqli_error($con);
    }
}

mysqli_close($con);

?>