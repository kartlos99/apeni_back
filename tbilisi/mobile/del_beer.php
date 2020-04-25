<?php

// ---------- ludis washla (siidan amogeba) ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

//$currTime = date("Y-m-d H:i:s", time()+4*3600);

$id = $_POST["beerId"];

if($id > 0){
    
    $sql = "UPDATE `ludi` SET `active` = 0 WHERE `ludi`.`id` = $id ";
    
    if(mysqli_query($con, $sql)){
        echo "Removed!" ;       
    } else {
        echo "ERROR: Could not able to execute $sql " . mysqli_error($con);
    }

}else{
    echo "ID error ".$id;
}

mysqli_close($con);
?>