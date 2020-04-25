<?php
$aiai = "<p>nonee</p>";
require_once('phpcode/load.php');
$logged = $myop->checklogin();

if ($logged == false){
	$url = "http". ((!empty($_SERVER['HTTPS'])) ? "s" : "") . "://" . $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	$redirect = str_replace('total_sale.php', 'login.php', $url);
	header("Location: $redirect?action=login");
	exit;
} else {
	$username = $_COOKIE['k_user'];
	$authID = $_COOKIE['k_authID'];
	
	$table = 'users';
	$sql = "SELECT * FROM $table WHERE username = '".$username."'";

	$results = $link->query($sql);

	if(!$results){
		die('aseti momxmarebeli ar arsebobs!');
	}

	$logedUser = mysqli_fetch_assoc($results);
}

?>

<!DOCTYPE html>
<html>
    <head>
        <title>realizacia tveebit</title>
        <link rel="stylesheet" type="text/css" href="/styles/main.css">
        <link rel="stylesheet" type="text/css" href="/styles/_main.css">
    </head>
<body>
    
    <p>რეალიზაცია თვეების მიხედვით </p>    
    <p class="righttext"> momxmarebeli: <?php echo $logedUser['name']?> <a href="login.php?action=logout">logout</a></p>
<?php
//  header("Content-Type: text/plain");
 
require_once('andr_app_links/connection.php');
$tanxa_word = "აღებული თანხა";
$arr_m = array();

    $sql_money = "
SELECT
    YEAR(tarigi) AS weli,
    MONTH(tarigi) AS tve,
    SUM(tanxa) AS money
FROM
    `moneyoutput`
GROUP BY
    YEAR(tarigi),
    MONTH(tarigi)
" ;
//$arr = array();
$result_money = $con->query($sql_money);

while($rs = mysqli_fetch_assoc($result_money)) {
    $arr_m[] = $rs;
}

function cxrilis_tavi($weli, $tve) {
    global $tanxa_word;
    global $arr_m;
    $tveText = date("F",mktime(0, 0, 0, $tve, 1, 2000));
    $tanxa = 0;
    for($i=0; $i<count($arr_m); $i++){
        if($arr_m[$i]["weli"] == $weli & $arr_m[$i]["tve"] == $tve){
            $tanxa = $arr_m[$i]["money"];
        }
    }
    echo "<h class='ricxvi'>$weli/$tve $tveText - $tanxa_word: ".$tanxa." ₾</h>";
}

$sql = "
SELECT
YEAR(b.tarigi) AS weli,
MONTH(b.tarigi) AS tve,
    l.dasaxeleba,
    SUM(
        (
            b.kasri30 * 30 + b.kasri50 * 50
        ) * b.ert_fasi
    ) AS pr,
    SUM(
        b.kasri30 * 30 + b.kasri50 * 50
    ) AS lt,
    SUM(kasri30) AS k30,
    SUM(kasri50) AS k50
FROM
    `beerinput` AS b
LEFT JOIN ludi AS l
ON
    b.ludis_id = l.id
GROUP BY
	YEAR(b.tarigi), 
    MONTH(b.tarigi),    
    dasaxeleba 
" ;
//$arr = array();
$result = $con->query($sql);

$tableHead = '<tr>
                <th>დასახელება</th>
                <th>ღირებულება</th>
                <th>ლიტრაჟი</th>
                <th>კასრი30</th>
                <th>კასრი50</th>
            </tr>';
            
$output = '<table class="table">'.$tableHead;
        
        $firstRow = true;
       
        while($row = mysqli_fetch_array($result))
        {
            if($firstRow){
                $weli = $row["weli"];
                $tve = $row["tve"];
                cxrilis_tavi($weli, $tve);
                $firstRow = false;
            }
            
            if($weli == $row["weli"] & $tve == $row["tve"]){
                $output .= '
                <tr>
                    <td>'.$row["dasaxeleba"].'</td>
                    <td align="right" class="ricxvi">'.$row["pr"].' ₾</td>
                    <td align="right" class="ricxvi">'.$row["lt"].'</td>
                    <td align="right" class="ricxvi">'.$row["k30"].'</td>
                    <td align="right" class="ricxvi">'.$row["k50"].'</td>
                </tr>
                ';  
            }else{
                $output .= '</table>';
                echo $output;
                
                $weli = $row["weli"];
                $tve = $row["tve"];
                cxrilis_tavi($weli, $tve);
                
                $output = '<table class="table">'.$tableHead;      
                
                $output .= '
                <tr>
                    <td>'.$row["dasaxeleba"].'</td>
                    <td align="right" class="ricxvi">'.$row["pr"].' ₾</td>
                    <td align="right" class="ricxvi">'.$row["lt"].'</td>
                    <td align="right" class="ricxvi">'.$row["k30"].'</td>
                    <td align="right" class="ricxvi">'.$row["k50"].'</td>
                </tr>
                ';  
            }
        }
        $output .= '</table>';
        
    echo $output;
    
    header("Location: ../andr_app_links/all_info.php");
    
mysqli_close($con);
?>

<div><a href="/all_info.php">ობიექტები</a></div>
<div><a href="/"><h4>მთავარი</h4></a></div>


<!--<form method="get" action="excel.php">
    შეიყვანეთ ობიექტის ID :  <input type="text" name="objID" value=""/>
        <input type="submit" name="data" class="btn btn-success" value="Export to Excel" />
    </form>-->


</body>
</html>