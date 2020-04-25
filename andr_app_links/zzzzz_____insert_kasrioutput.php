<?php

// ---------- kasrebis ageba ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

$currTime = date("Y-m-d H:i:s", time()+4*3600+1);

$obieqtis_id 	    = $_POST["obieqtis_id"];
$distributor_id 	= $_POST["distributor_id"];
$k30 	        = $_POST["k30"];
$k50 	        = $_POST["k50"];
$comment 	    = $_POST["comment"];


$sql = "INSERT INTO 
    `kasrioutput` (`tarigi`, `obieqtis_id`, `distributor_id`, `kasri30`, `kasri50`, `comment`) 
    VALUES ('$currTime', '$obieqtis_id', '$distributor_id', '$k30', '$k50', '$comment')";

if(mysqli_query($con, $sql)){	
	$last_id = mysqli_insert_id($con);
	echo 'shekveta dafiqsirda,  N:' + $last_id ;
} else {
	echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
}


mysqli_close($con);

?>