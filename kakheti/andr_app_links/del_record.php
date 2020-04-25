<?php

// ---------- chanaweris washla (3 in 1) ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

//$currTime = date("Y-m-d H:i:s", time()+4*3600);

$id = $_POST["id"];
$table = $_POST["table"];
$userid = $_POST["userid"];

if($id > 0){
    if($table == "mitana"){
        $sql = "DELETE FROM beerinput WHERE id = $id";
    }
    
    if($table == "kout"){
        $sql = "DELETE FROM kasrioutput WHERE id = $id ";
    }
    
    if($table == "mout"){
        $sql = "DELETE FROM moneyoutput WHERE id = $id ";
    }
    
    if($table == "order"){
        $sql = "DELETE FROM shekvetebi WHERE id = $id ";
    }
    
    if($table == "users"){
        $sql = "UPDATE users SET active = 0 WHERE id = $id ";
    }
    
    if($table == "sawyobi_in"){
        $sql = "DELETE FROM sawyobi_in WHERE id = $id ";
    }
    
    if($table == "sawyobi_out"){
        $sql = "DELETE FROM sawyobi_out WHERE id = $id ";
    }

    if(mysqli_query($con, $sql)){
            echo "Removed!" ;       
        } else {
            echo "ERROR: Could not able to execute $sql " . mysqli_error($con);
        }
}else{
        echo "ID error: id = $id";
}

mysqli_close($con);

?>