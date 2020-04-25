<?php

// ---------- axali/redaqtireba ludi ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

$currTime = date("Y-m-d H:i:s", time()+4*3600);

$dasaxeleba = $_POST["dasaxeleba"];
$price 	    = $_POST["price"];
$beerId   	= $_POST["beerId"];
$color   	= '#FFFFFF';
if (isset($_POST["color"])){
    $color   	= $_POST["color"];
}


if($beerId == "0"){ // axali ludis chawera

    $sql = "INSERT INTO `ludi` (`dasaxeleba`, `fasi`, `active`, `color`) 
                    VALUES ( '$dasaxeleba', '$price', '1', '$color')";

    if(mysqli_query($con, $sql)){	
	    $newBeer_id = mysqli_insert_id($con);
	    
	    $id_arr = array();
	    $sql_obj_ids = "SELECT id FROM `obieqtebi` ";
	    
	    $result = $con->query($sql_obj_ids);
    
        while($rs = mysqli_fetch_assoc($result)) {
            // $arr[] = $rs;
            $id_arr[] = $rs['id'];
        }
	
	    $values_to_insert = "(";
	
	    for ($i = 0; $i < count($id_arr); $i++) {
        
            $values_to_insert = $values_to_insert."'$id_arr[$i]', '$newBeer_id', '$price', '$currTime')";
        
            if($i < (count($id_arr)-1)) {
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
} else{ // redaqtireba

    $sql = "UPDATE ludi
            SET 
            `dasaxeleba` = '$dasaxeleba', `fasi`= '$price', `color` = '$color'
            WHERE
            id = $beerId ";
            
    if(!mysqli_query($con, $sql)){
        echo "ERROR: Could not able to execute $sql " . mysqli_error($con);
    }else{
        echo "განახლებულია!" ;       
    }       
}


mysqli_close($con);

?>