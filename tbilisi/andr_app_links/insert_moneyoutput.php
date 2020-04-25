<?php

// ---------- tanxis ageba ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

$currTime = date("Y-m-d H:i:s", time()+4*3600+2);

$obieqtis_id 	    = $_POST["obieqtis_id"];
$distributor_id 	= $_POST["distributor_id"];
$tanxa 	            = $_POST["tanxa"];
$comment 	        = $_POST["comment"];


$sql = "INSERT INTO 
    `moneyoutput` 
    (`tarigi`, `obieqtis_id`, `distributor_id`, `tanxa`, `comment`) 
    VALUES 
    ('$currTime', '$obieqtis_id', '$distributor_id', '$tanxa', '$comment')";

if(mysqli_query($con, $sql)){	
	$last_id = mysqli_insert_id($con);
	echo "shekveta dafiqsirda,  N:" + $last_id ;
} else {
	echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
}


mysqli_close($con);

?>