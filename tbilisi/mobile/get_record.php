<?php

// ---------- chanaweris amogeba (redaqtirebistvis) ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

$tb = $_GET["table"];
$id = $_GET["id"];

$arr = array();

if($id != 0){
    if($tb == "mitana"){
        $sql = "Select * from `beerinput` WHERE id = $id";
    }
    
    if($tb == "kout"){
        $sql = "Select * from `kasrioutput` WHERE id = $id";
    }
    
    $result = $con->query($sql);
    $arr[] = mysqli_fetch_assoc($result);
    
    echo json_encode($arr);    
}
echo $id;


mysqli_close($con);

?>