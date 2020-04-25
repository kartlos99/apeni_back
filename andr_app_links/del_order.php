<?php

// ---------- shekveTis washla ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

//$currTime = date("Y-m-d H:i:s", time()+4*3600);

$id = $_POST["order_id"];

if($id > 0){
    
    $sql = "DELETE FROM shekvetebi WHERE id = $id ";
    
    if(mysqli_query($con, $sql)){
        echo "Removed!" ;       
    } else {
        echo "ERROR: Could not able to execute $sql " . mysqli_error($con);
    }
     
}

mysqli_close($con);

?>