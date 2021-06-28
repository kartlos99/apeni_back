<?php

// ---------- axali/redaqtireba obieqti ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

$currTime = date("Y-m-d H:i:s", time()+4*3600);

$name 	    = $_POST["name"];
$adress 	= $_POST["adress"];
$tel   	    = $_POST["tel"];
$comment 	= $_POST["comment"];
$sk         = $_POST["sk"];
$sakpiri    = $_POST["sakpiri"];
$fasebi     = $_POST["fasebi"];
$id_ebi     = $_POST["id_ebi"];
$chek     = $_POST["chek"];

$moqmedeba  = $_POST["moqmedeba"];

$arr_pr = array();
$arr_id = array();

$arr_pr =  explode('|',$fasebi);
$arr_id =  explode('|',$id_ebi);
	
if($moqmedeba == "ახალი ობიექტი"){

    $sql = "INSERT INTO 
        $CUSTOMER_TB 
        (`dasaxeleba`, `adress`, `tel`, `comment`, `sk`, `sakpiri`, `reg_date`, `chek`) 
        VALUES 
        ('$name', '$adress', '$tel', '$comment', '$sk', '$sakpiri', '$currTime', '$chek')";

    if(mysqli_query($con, $sql)){	
	    $last_id = mysqli_insert_id($con);
	
	    $values_to_insert = "(";
	
	    for ($i = 0; $i < count($arr_pr); $i++) {
        
            $values_to_insert = $values_to_insert."'$last_id', '$arr_id[$i]', '$arr_pr[$i]', '$currTime')";
        
            if($i < (count($arr_pr)-1)) {
                $values_to_insert = $values_to_insert.", (";
            }
	    } 
	
	    $sql_fasebi = "INSERT INTO `fasebi` 
	                    (`obj_id`, `beer_id`, `fasi`, `tarigi`) 
	                VALUES
	                    $values_to_insert";
	        
        if(!mysqli_query($con, $sql_fasebi)){
            echo "ERROR: Could not able to execute $sql_fasebi. " . mysqli_error($con);
        }
	
	    echo "ჩაწერილია!" ;
    } else {
    	echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
    }
} 

if($moqmedeba == "რედაქტირება"){
    $obj_id = $_POST["obj_id"];
    $sql = "UPDATE $CUSTOMER_TB
            SET 
            `dasaxeleba` = '$name', `adress`= '$adress', `tel`='$tel', `comment`='$comment', `sk` = '$sk', `sakpiri` = '$sakpiri', `chek` = '$chek'
            WHERE
            id = $obj_id ";
    
    if(!mysqli_query($con, $sql)){
        echo "ERROR: Could not able to execute $sql " . mysqli_error($con);
    }
        
    for ($i = 0; $i < count($arr_pr); $i++) {
        $sql_fasebi = " UPDATE fasebi
            SET `fasi` = $arr_pr[$i], `tarigi` = '$currTime'
            WHERE `obj_id`=$obj_id AND `beer_id` = $arr_id[$i] ";
        
        if(!mysqli_query($con, $sql_fasebi)){
            echo "ERROR: Could not able to execute $sql " . mysqli_error($con);
        }
    }
	echo "განახლებულია!" ;   
}


mysqli_close($con);

?>