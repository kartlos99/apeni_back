<?php

// ----------  xarjis damateba  ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

$currTime = date("Y-m-d H:i:s", time()+4*3600);

$distrID = $_POST["distrid"];
$amount	    = $_POST["amount"];
$comment   	= $_POST["comment"];

    $sql = "INSERT INTO `xarjebi`(
            `distributor_id`,
            `tanxa`,
            `comment`
        )
        VALUES(
            $distrID,
            $amount,
            '$comment'
            )";
            

    if(mysqli_query($con, $sql)){
        $responce[RESULT] = SUCCESS;
        $responce[DATA] = mysqli_insert_id($con);
    }else{
        $responce[RESULT] = ERROR;
        $responce[ERROR] = "Could not able to execute $sql " . mysqli_error($con);
    }

echo json_encode($responce);

mysqli_close($con);

?>