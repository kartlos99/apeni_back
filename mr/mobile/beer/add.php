<?php

namespace Apeni\JWT;

// use DataProvider;
use QueryHelper;
use DbKey;
use VersionControl;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('../connection.php');
$sessionData = checkToken();

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$postData = json_decode($json);

$dasaxeleba = $postData->dasaxeleba;
$price 	    = $postData->fasi;
$beerId   	= $postData->id;
$color   	= $postData->color;


if($beerId == "0"){ // axali ludis chawera

    $sql = "INSERT INTO `ludi` (`dasaxeleba`, `fasi`, `active`, `color`, `sortValue`) 
                    VALUES ( '$dasaxeleba', '$price', '1', '$color', UNIX_TIMESTAMP())";

    if(mysqli_query($con, $sql)){	
	    $newBeer_id = mysqli_insert_id($con);
	    
	    $id_arr = array();
	    $sql_obj_ids = "SELECT id FROM $CUSTOMER_TB ";
	    
	    $result = $con->query($sql_obj_ids);
    
        while($rs = mysqli_fetch_assoc($result)) {
            // $arr[] = $rs;
            $id_arr[] = $rs['id'];
        }
	
	    $values_to_insert = "(";
	
	    for ($i = 0; $i < count($id_arr); $i++) {
        
            $values_to_insert = $values_to_insert."'$id_arr[$i]', '$newBeer_id', '$price', '$timeOnServer')";
        
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
	
        $response[DATA] = "$newBeer_id";

    } else {
        dieWithError(mysqli_errno($con), mysqli_error($con));
    }
} else{ // redaqtireba

    $sql = "UPDATE ludi
            SET 
            `dasaxeleba` = '$dasaxeleba', `fasi`= '$price', `color` = '$color'
            WHERE
            id = $beerId ";
            
    if(!mysqli_query($con, $sql)){
        dieWithError(mysqli_errno($con), mysqli_error($con));
    }else{
        $response[DATA] = "";
    }       
}

$vc = new VersionControl($con);
$vc->updateVersionFor(BEER_VCS);
$vc->updateVersionFor(PRICE_VCS);

echo json_encode($response);
