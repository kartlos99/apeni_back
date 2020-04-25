<?php

// ---------- sistemis gawmwndis dafiqsireba ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

$currTime = date("Y-m-d H:i:s", time()+4*3600);

$distrib_id     = $_POST["distrib_id"];
$comment 	    = $_POST["comment"];
$objID   	    = $_POST["objID"];
$id        	    = $_POST["id"];

if($id == "0"){ // axali chanaweri

    $sql = "INSERT INTO `gawmenda` (`obieqtis_id`, `distributor_id`, `tarigi`, `comment`) 
                    VALUES ( '$objID', '$distrib_id', '$currTime', '$comment')";

    if(mysqli_query($con, $sql)){	
	    $ins_id = mysqli_insert_id($con);
	    echo "ჩაწერილია!".$ins_id ;
    } else {
    	echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
    }
} else{ // redaqtireba

    $sql = "DELETE from gawmenda WHERE id = $id ";
            
    if(mysqli_query($con, $sql)){
        echo "წაშლილია!" ; 
    }else{
        echo "ERROR: Could not able to execute $sql " . mysqli_error($con);
    }       
}

mysqli_close($con);

?>