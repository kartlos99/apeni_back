<?php

// ---------- change pass ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

//$currTime = date("Y-m-d H:i:s", time()+4*3600);

$user_id = $_POST["user_id"];
$new_pass = $_POST["new_pass"];
$old_pass = $_POST["old_pass"];

if($user_id > 0){
    
    $sql = "UPDATE users
            SET 
            `pass` = '$new_pass'
            WHERE
            id = $user_id";
    
    if(mysqli_query($con, $sql)){
        echo "sheicvala!" ;       
    } else {
        echo "ERROR: Could not able to execute $sql " . mysqli_error($con);
    }
}

mysqli_close($con);

?>