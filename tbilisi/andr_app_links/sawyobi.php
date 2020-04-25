<?php

// ---------- kasrebi chamotana / redaqtirebit ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

// $currTime = date("Y-m-d H:i:s", time()+4*3600);
$currTime   = $_POST["set_tarigi"];
$currTime1  = $currTime.":01";

$chamotana      = $_POST["chamotana"];
$wageba         = $_POST["wageba"];
$id             = $_POST["id"]; // romelic unda ganvaaxlot

$distributor_id = $_POST["distributor_id"];
$chek           = $_POST["chek"];
$comment 	    = $_POST["comment"];

$kasri30out     = $_POST["k30out"];
$kasri50out     = $_POST["k50out"];


if($chamotana == '1'){
    $ludis_id   	= $_POST["beer_id"];
    $kasri30 	    = $_POST["k30"];
    $kasri50        = $_POST["k50"];
    
    if($id != "0"){
        $sql = "UPDATE `sawyobi_in` 
           SET tarigi = '$currTime', `shemomtani_id` = '$distributor_id', `ludis_id` = '$ludis_id', `kasri30` = '$kasri30', `kasri50` = '$kasri50', `chek`='$chek', `comment` = '$comment'
           WHERE
            `sawyobi_in`.`id` = $id";
    }else{
        
        $multiValue = "";
        for ($i = 0; $i < count($ludis_id); $i++){
            $_id = $ludis_id[$i];
            $_k30 = $kasri30[$i];
            $_k50 = $kasri50[$i];
            $_efasi = $ert_fasi[$i];
            if ($i > 0) {
                $multiValue .= ",";
            }
            $multiValue .= "('$currTime', '$distributor_id', '$_id', '$_k30', '$_k50', '$chek', '$comment')";
        }
        
    $sql = "INSERT INTO `sawyobi_in` 
        (`tarigi`, `shemomtani_id`, `ludis_id`, `kasri30`, `kasri50`, `chek`, `comment`)
        VALUES " . $multiValue;
    }
    
    if(mysqli_query($con, $sql)){	
	   // $last_id = mysqli_insert_id($con);
	    echo '1' ;
    } else {
	    echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
    }
}

if($wageba == '1'){
    if($id != "0"){
    $sql = "UPDATE `sawyobi_out` 
       SET tarigi = '$currTime1', `wamgebi_id` = '$distributor_id', `kasri30` = '$kasri30out', `kasri50` = '$kasri50out', `comment` = '$comment'
       WHERE
        `sawyobi_out`.`id` = $id";
    }else{
    $sql = "INSERT INTO 
        `sawyobi_out` 
        (`tarigi`, `wamgebi_id`, `kasri30`, `kasri50`, `comment`)
        VALUES 
        ('$currTime1', '$distributor_id', '$kasri30out', '$kasri50out', '$comment')";
    }
    
    if(mysqli_query($con, $sql)){	
	   // $last_id = mysqli_insert_id($con);
	    echo '1' ;
    } else {
	    echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
    }
}

mysqli_close($con);
?>