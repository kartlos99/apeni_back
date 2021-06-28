<?php

// ---------- money output ----------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once('connection.php');

$sql = "SELECT * FROM moneyoutput join users join $CUSTOMER_TB on (moneyoutput.distributor_id = users.id) and (moneyoutput.obieqtis_id = $CUSTOMER_TB.id) where obieqtis_id = '2'" ;
$arr = array();
$result = $con->query($sql);
    
while($rs = mysqli_fetch_assoc($result)) {
    $arr[] = $rs;
}

echo json_encode($arr);


mysqli_close($con);

?>