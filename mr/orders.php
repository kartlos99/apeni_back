<?php
session_start();
require_once('../phpcode/load.php');
$logged = $myop->checklogin();

if ($logged == false){
	$url = "http". ((!empty($_SERVER['HTTPS'])) ? "s" : "") . "://" . $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	$redirect = str_replace('orders.php', 'login.php', $url);
	header("Location: $redirect?action=login");
	exit;
} else {
	$username = $_COOKIE['k_user'];
	$authID = $_COOKIE['k_authID'];
	
	$table = 'users';
	$sql = "SELECT * FROM $table WHERE username = '".$username."'";

	$results = $con->query($sql);

	if(!$results){
		die('aseti momxmarebeli ar arsebobs!');
	}

	$logedUser = mysqli_fetch_assoc($results);
}



// ---------- get shekvetebi ----------

// header("Access-Control-Allow-Origin: *");
// header("Content-Type: application/json; charset=UTF-8");

require_once('../imports.php');
require_once('/xampp/htdocs/app_config/mobile_tb.php');

// time() funqcia gvibrnebs serveris mimdinare dros, chven vart +4 saati
//$dges_server = date("Y-m-d", time()+4*3600);
$dges_server = "2020-04-14";
// $dges = $_GET["tarigi"];

function reNewOrders($dbcon, $date){
    // *************************************************************************
    // tu pirveli motxovnaa, mashin gadmogvaqvs daumtavrebeli shekvetebi
    
    $sql_chekdate="SELECT MAX(date_format(`tarigi`,'%Y-%m-%d')) AS tarigi FROM `shekvetebi`";
    
    $rs = mysqli_query($dbcon, $sql_chekdate);
    $rt = mysqli_fetch_assoc($rs);
    
    if ($rt['tarigi'] != $date){
        $sql = "SELECT * FROM `last_orders_group_view` WHERE tarigi1 < '$date' AND in_30+in_50 < wont_30+wont_50";
        $result_2 = $dbcon->query($sql);
        while($row = mysqli_fetch_assoc($result_2)) {
            // tu gvaqvs daumtavrebeli shekvetebi am dristvis, vainsetebt shesabamis chanawers
            $k30 = $row['wont_30']-$row['in_30'];
            $k50 = $row['wont_50']-$row['in_50'];
            $rcomment = $row['comment'];
            $rdistributor = $row['distributor_id'];
            $robjid = $row['obieqtis_id'];
            $rlid = $row['l_id'];
            $rchek = $row['chk'];
            
            $sql = "INSERT INTO 
                `shekvetebi` 
                (`tarigi`, `obieqtis_id`, `ludis_id`, `kasri30`, `kasri50`, `comment`, `chek`, `distributor_id`) 
                VALUES 
                ('$date', $robjid, $rlid, $k30, $k50, '$rcomment', $rchek, $rdistributor) ";
    
            if(!mysqli_query($dbcon, $sql)){
                die("error_writing");
            }
        }
    }
    // ************************************************************************
}

// wina requestis mere 5 wami unda gavides rom shevamowmo shekvetebi gadmosatania tu ara!
if (isset($_SESSION['last_chek_time'])){
    if (time() - $_SESSION['last_chek_time'] > 5){
        $_SESSION['last_chek_time'] = time();
        reNewOrders($con, $dges_server);
    }
}else{
    $_SESSION['last_chek_time'] = time();
    reNewOrders($con, $dges_server);
}


$sql="SELECT
    tarigi1,
    DATE_FORMAT(tarigi, '%Y-%m-%d %H:%i') AS tarigi_hhmm,
    $CUSTOMER_TB.dasaxeleba AS obieqti,
    ludi.dasaxeleba,
    (k30in) AS in_30,
    (k50in) AS in_50,
    (k30wont) AS wont_30,
    (k50wont) AS wont_50,
    chk,
    distributor_id,
    users.name,
    order_id,
    a.comment,
    ludi.color
FROM
    (
    SELECT
        DATE_FORMAT(tarigi, '%Y-%m-%d') AS tarigi1,
        tarigi,
        obieqtis_id,
        ludis_id,
        kasri30 AS k30in,
        kasri50 AS k50in,
        0 AS k30wont,
        0 AS k50wont,
        0 AS chk,
        distributor_id,
        id AS order_id,
        comment
    FROM
        `beerinput`
    UNION
SELECT
    DATE_FORMAT(tarigi, '%Y-%m-%d') AS tarigi1,
    tarigi,
    obieqtis_id,
    ludis_id,
    0 AS k30in,
    0 AS k50in,
    kasri30 AS k30wont,
    kasri50 AS k50wont,
    chek AS chk,
    distributor_id,
    id AS order_id,
    comment
FROM
    `shekvetebi`
) AS a
LEFT JOIN $CUSTOMER_TB ON obieqtis_id = $CUSTOMER_TB.id
LEFT JOIN ludi ON ludis_id = ludi.id
LEFT JOIN users ON distributor_id = users.id
WHERE
    tarigi1 = '$dges_server'
ORDER BY
    name,
    obieqti,
    dasaxeleba";
    
    
$arr = array();
$result = $con->query($sql);
    
while($rs = mysqli_fetch_assoc($result)) {
    $arr[] = $rs;
}

// echo json_encode($arr);

mysqli_close($con);

function makeHeadRow($columns){
    $hRow = "<thead><tr>";
    foreach($columns as $item){
        $hRow .= "<th>" . $item . "</th>";
    }
    return $hRow . "</tr></thead>";    
}

function makerow($columns){
    $newRow = "<tr>";
    if ($columns["chk"] == "1"){
        $newRow .= "<td class='ck'>" . "</td>";
    }else{
        $newRow .= "<td>" . "</td>";    
    }
    $newRow .= "<td>" . $columns["obieqti"] . "</td>";
    $newRow .= "<td>" . $columns["dasaxeleba"] . "</td>";
    $newRow .= "<td>" . $columns["wont_30"] . "</td>";
    $newRow .= "<td>" . $columns["wont_50"] . "</td>";
    $newRow .= "<td>" . $columns["in_30"] . "</td>";
    $newRow .= "<td>" . $columns["in_50"] . "</td>";
    $newRow .= "<td width='300px'>" . $columns["comment"] . "</td>";
    $newRow .= "<td>" . $columns["name"] . "</td>";

    return $newRow . "</tr>";
}

$headarray = ["chk", "ობიექტი", "ლუდი", "შეკვ.30", "შეკვ.50", "მიტანა_30", "მიტანა_50", "კომენტარი", "დისტრიბუტორი"];

$output ="";
if (count($arr) > 0) {
    $output .= makeHeadRow($headarray);
    $output .= "<tbody>";
    foreach($arr as $row){        
        $output .= makerow($row);
    }
    $output .= "</tbody>";
}


?>

<!DOCTYPE html>
<html>
    <head>
        <title>შეკვეთები</title>
        <script
      src="https://code.jquery.com/jquery-3.3.1.min.js"
      integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
      crossorigin="anonymous"></script>
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="/styles/main.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    </head>
    
    <body>
    <table class="table">
        <?php echo $output ?>    
    </table>
    

    </body>

</html>