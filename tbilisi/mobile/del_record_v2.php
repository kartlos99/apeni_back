<?php

// ---------- chanaweris washla (3 in 1) ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

//$currTime = date("Y-m-d H:i:s", time()+4*3600);

// Takes raw data from the request
$json = file_get_contents('php://input');

// Converts it into a PHP object
$data = json_decode($json);

$id = $data->recordID;
$table = $data->table;
$userid = $data->userid;


if($id > 0){
    if($table == "mitana"){
        $sql = "SELECT remove_input($id, $userid)";
    }
    
    if($table == "kout"){
        $sql = "SELECT remove_kasrioutput($id, $userid)";
    }
    
    if($table == "mout"){
        $sql = "SELECT remove_moneyoutput($id, $userid) ";
    }
    
    if($table == "order"){
        $sql = "SELECT remove_order($id, $userid) ";
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
    
    if($table == "xarjebi"){
        $sql = "DELETE FROM xarjebi WHERE id = $id ";
    }

    if(mysqli_query($con, $sql)){
            $response[RESULT] = SUCCESS;
        } else {
            $response[RESULT] = ERROR;
            $response[ERROR] = "Could not able to execute $sql " . mysqli_error($con);
        }
}else{
    $response[RESULT] = ERROR;
    $response[ERROR] = "ID error: id = $id";
    $response['post'] = 'json';//$data;//json_decode($_POST); //implode(" ",array_keys($_POST));
}

echo json_encode($response);

mysqli_close($con);

?>