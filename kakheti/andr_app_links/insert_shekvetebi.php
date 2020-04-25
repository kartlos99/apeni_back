<?php

// ---------- shekvetis registracia/რედაქტირება ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

$currTime = date("Y-m-d H:i:s", time()+4*3600);
$currDate = date("Y-m-d", time()+4*3600);
// die(print_r( $_POST["k30"] ));

// *************************************************************************
// tu pirveli chaweraa, mashin gadmogvaqvs daumtavrebeli shekvetebi

$dges_server = date("Y-m-d", time()+4*3600);  

$sql_chekdate="SELECT MAX(date_format(`tarigi`,'%Y-%m-%d')) AS tarigi FROM `shekvetebi`";

$rs = mysqli_query($con, $sql_chekdate);
$rt = mysqli_fetch_assoc($rs);

if ($rt['tarigi'] != $dges_server){
    $sql = "SELECT * FROM `last_orders_group_view` WHERE tarigi1 < '$dges_server' AND in_30+in_50 < wont_30+wont_50";
    $result_2 = $con->query($sql);
    while($row = mysqli_fetch_assoc($result_2)) {
        // tu gvaqvs daumtavrebeli shekvetebi am dristvis, vainsetebt shesabamis chanawers
        $k30 = $row['wont_30']-$row['in_30'];
        $k50 = $row['wont_50']-$row['in_50'];
        $rcomment = $row['comment'].' gushindeli';
        $rdistributor = $row['distributor_id'];
        $robjid = $row['obieqtis_id'];
        $rlid = $row['l_id'];
        $rchek = $row['chk'];
        
        $sql = "INSERT INTO 
            `shekvetebi` 
            (`tarigi`, `obieqtis_id`, `ludis_id`, `kasri30`, `kasri50`, `comment`, `chek`, `distributor_id`) 
            VALUES 
            ('$dges_server', $robjid, $rlid, $k30, $k50, '$rcomment', $rchek, $rdistributor) ";

        if(!mysqli_query($con, $sql)){
            die("error_writing");
        }
    }
}
// ************************************************************************


$obieqtis_id 	= $_POST["obieqtis_id"];
$comment 	    = $_POST["comment"];
$chek    	    = $_POST["chek"];
$distributor_id = $_POST["distributor_id"];
    $ludis_id   = $_POST["beer_id"];
    $k30        = $_POST["k30"];
    $k50        = $_POST["k50"];

$moqmedeba      = $_POST["moqmedeba"];

if($moqmedeba == "შეკვეთის დამატება"){
    if (count($ludis_id) < 1){
        die("ERROR: Empty data to insert!");
    }
    $multiValue = "";
    for ($i = 0; $i < count($ludis_id); $i++){
        $_id = $ludis_id[$i];
        $_k30 = $k30[$i];
        $_k50 = $k50[$i];
        if ($i > 0) {
            $multiValue .= ",";
        }
        $multiValue .= "('$currTime', '$obieqtis_id', '$_id', '$_k30', '$_k50', '$comment', '$chek', '$distributor_id')";
    }
    
    $sql = "INSERT INTO 
        `shekvetebi` 
        (`tarigi`, `obieqtis_id`, `ludis_id`, `kasri30`, `kasri50`, `comment`, `chek`, `distributor_id`) 
        VALUES " . $multiValue;
    
    if(mysqli_query($con, $sql)){	
    	$last_id = mysqli_insert_id($con);
    	echo "ჩაწერილია!";
    } else {
    	echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
    }

}

if($moqmedeba == "რედაქტირება"){
    $order_id   = $_POST["order_id"];
    $user_type =  $_POST["user_type"];
    
    $sql_d="SELECT date_format(`tarigi`,'%Y-%m-%d') AS tarigi FROM `shekvetebi` WHERE id = " . $order_id;

    $rs_d = mysqli_query($con, $sql_d);
    $rt_d = mysqli_fetch_assoc($rs_d);
    
    if ($user_type == "1" && $rt_d['tarigi'] != $currDate){
        die("can't perform this operation!");
    }

    $sql = "UPDATE `shekvetebi` 
    SET
    `ludis_id`='$ludis_id', `kasri30`='$k30', `kasri50`='$k50', `comment`='$comment', `chek`='$chek', `distributor_id`='$distributor_id'
    WHERE 
    id = $order_id ";

    if(mysqli_query($con, $sql)){	
    	echo "შეკვეთა დაკორექტირდა!" ;
    } else {
    	echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
    }
    
}


mysqli_close($con);

?>