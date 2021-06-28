<?php

// ---------- obieqtis washla (siidan amogeba) ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

//$currTime = date("Y-m-d H:i:s", time()+4*3600);

$obj_id = $_POST["obj_id"];

if($obj_id > 0){
    
    $sql = "
        SELECT
        	s.obj_id,
            o.dasaxeleba,
            s.pr,
            s.pay,
            s.`k30in`,
            s.`k50in`,
            ifnull(k.`k30_out`, 0) AS `k30out`,
            ifnull(k.`k50_out`, 0) AS `k50out`
        FROM
            sumof_prpaykin AS s
        LEFT JOIN kasri_back AS k
        ON
            s.obj_id = k.obieqtis_id
        LEFT JOIN $CUSTOMER_TB AS o
        ON s.obj_id = o.id
        WHERE o.id = $obj_id
    ";

    $result = $con->query($sql);
    
    $rs = mysqli_fetch_assoc($result);
    
    if (($rs['pr']-$rs['pay'] > 0.5) || ($rs['k30in']+$rs['k50in']-$rs['k30out']-$rs['k50out'] <> 0)){
        echo "დავალიანების განულებამდე ობიექტი არ წაიშლება!";
    }else{
        
        $sql = "UPDATE $CUSTOMER_TB
                SET 
                `active` = 0
                WHERE
                id = $obj_id ";
        
        if(mysqli_query($con, $sql)){
            echo "Removed!";
        } else {
            echo "ERROR: Could not able to execute $sql " . mysqli_error($con);
        }
    }
     
}

mysqli_close($con);

?>