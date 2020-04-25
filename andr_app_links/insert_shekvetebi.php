<?php

// ---------- shekvetis registracia/რედაქტირება ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

$currTime = date("Y-m-d H:i:s", time()+4*3600);

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
$ludis_id   	= $_POST["beer_type"];
$k30 	        = $_POST["k30"];
$k50 	        = $_POST["k50"];
$comment 	    = $_POST["comment"];
$chek    	    = $_POST["chek"];
$distributor_id = $_POST["distributor_id"];

$moqmedeba      = $_POST["moqmedeba"];

if($moqmedeba == "შეკვეთის დამატება"){
$sql = "INSERT INTO 
    `shekvetebi` 
    (`tarigi`, `obieqtis_id`, `ludis_id`, `kasri30`, `kasri50`, `comment`, `chek`, `distributor_id`) 
    VALUES 
    ('$currTime', '$obieqtis_id', '$ludis_id', '$k30', '$k50', '$comment', '$chek', '$distributor_id')";

if(mysqli_query($con, $sql)){	
	$last_id = mysqli_insert_id($con);
	echo "ჩაწერილია!";
} else {
	echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
}
    
}

if($moqmedeba == "რედაქტირება"){
    $order_id      = $_POST["order_id"];
    $sql = "UPDATE `shekvetebi` 
    SET
    `ludis_id`='$ludis_id', `kasri30`='$k30', `kasri50`='$k50', `comment`='$comment edited:$currTime', `chek`='$chek', `distributor_id`='$distributor_id'
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