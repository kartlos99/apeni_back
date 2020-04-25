<?php

// ---------- axali/redaqtireba momxmarebeli ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

$currTime = date("Y-m-d H:i:s", time()+4*3600);

$username 	= $_POST["username"];
$name 	    = $_POST["name"];
$pass       = $_POST["pass"];
$tel   	    = $_POST["tel"];
$adress 	= $_POST["adress"];
$comment 	= $_POST["comment"];
$type 	    = $_POST["type"];
$maker 	    = $_POST["maker"];

$moqmedeba  = $_POST["moqmedeba"];


if($moqmedeba == "ახალი მომხმარებელი"){

    $sql = "INSERT INTO `users` 
        (`username`, `pass`, `name`, `type`, `maker`, `tel`, `adress`, `comment`, `reg_date`) 
    VALUES
        ('$username', '$pass', '$name', '$type', '$maker', '$tel', '$adress', '$comment', '$currTime')";

    if(mysqli_query($con, $sql)){	
	    $last_id = mysqli_insert_id($con);
	    
	    echo "ჩაწერილია!";
    } else {
    	echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
    }
} 

if($moqmedeba == "რედაქტირება"){
    $obj_id = $_POST["obj_id"];
    $passCH = $_POST["passCH"];
    
    if($passCH == "1"){
    $sql = "UPDATE `users` 
            SET `username`= '$username', `pass`='$pass', `type`=$type, `maker`=$maker, `name` = '$name', `adress`= '$adress', `tel`='$tel', `comment`='$comment'
            WHERE
            `users`.`id` = $obj_id ";
    }else{
        $sql = "UPDATE `users` 
            SET `username`= '$username', `type`=$type, `maker`=$maker, `name` = '$name', `adress`= '$adress', `tel`='$tel', `comment`='$comment'
            WHERE
            `users`.`id` = $obj_id ";
    }
    
    if(mysqli_query($con, $sql)){
        echo "განახლებულია!" ; 
    }else{
        echo "ERROR: Could not able to execute $sql " . mysqli_error($con);
    }
}

mysqli_close($con);
?>