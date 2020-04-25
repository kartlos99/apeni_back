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
        
        <script
          src="https://code.jquery.com/jquery-3.3.1.min.js"
          integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
          crossorigin="anonymous"></script>
        <script src="https://code.highcharts.com/highcharts.js"></script>
    </head>
<body>
    
    
    <p class="righttext"> momxmarebeli: <?php echo $logedUser['name']?> <a href="login.php?action=logout">logout</a></p>
    <a href="/all_info.php">ობიექტები</a>
    
    <div id='to_in_one'>
        <div id=l_div>
    
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

</div>
        <div id=r_div></div>
        <select id='sel_year'></select>
    </div> <!--2 in 1-->

<div><a href="/all_info.php">ობიექტები</a></div>


<!--<form method="get" action="excel.php">
    შეიყვანეთ ობიექტის ID :  <input type="text" name="objID" value=""/>
        <input type="submit" name="data" class="btn btn-success" value="Export to Excel" />
    </form>-->


<script >
    var myChart;
    var option = {
        chart: {
            type: 'column'
        },
        title: {
            text: 'რეალიზაცია თვეების მიხედვით'
        },
        xAxis: {
            categories: ['ვაშლი', 'ბანანი', 'პური']
        },
        yAxis: {
            title: {
                text: 'ლიტრაჟი'
            }
        },
        series: [{
            name: 'Jane',
            data: [1, 3, 4]
        }, {
            name: 'John',
            data: [5, 7, 3]
        }]
    };
    
    var chemiSeriisData = [2,12,9];
    var mydata = [];
    var tveebi = ['იანვ', 'თებ', 'მარ', 'აპრ', 'მაისი', 'ივნ', 'ივლ', 'აგვ', 'სექტ', 'ოქტ', 'ნოემ', 'დეკ'];
    var years = [];
        
    $(function () { 
        //myChart = Highcharts.chart('container1', option);
        getDataforChart(0);
    });
    
    function getDataforChart(yeartoshow){
        var url = 'http://apeni.ge/monthly_results.php';
        if (yeartoshow != 0){
            url = url + '?year='+yeartoshow;
        }
        
        $.ajax({
        url: url,
        method: 'get',
        dataType: 'json',
        success: function (response) {   
            
            mydata = [];

            response.forEach(function (item) {
                if (!years.includes(item.weli)){
                    years.push(item.weli);
                }
                //mydata.ludi = item.dasaxeleba;
                var exist = false;
                for (var i = 0; i<mydata.length; i++){
                    if (item.dasaxeleba == mydata[i].name){
                        mydata[i].data.push({                           
                            name : tveebi[item.tve-1], year : item.weli, x : parseInt(item.tve)-1, y: parseInt(item.lt)
                        });
                        exist = true;
                    }
                }
                
                if (!exist){
                    mydata.push({
                        name: item.dasaxeleba,
                        data: [{name : tveebi[item.tve-1], year : item.weli, x : parseInt(item.tve)-1, y: parseInt(item.lt)}],
                        color : item.color
                    });     
                }
                
                console.log(item);
                //$('<option />').text(item.EmEmail).attr('value', item.id).appendTo('#sel_rmail');

            });
            
            console.log(mydata);
            if (yeartoshow == 0){
                for (var i = years.length-1; i >= 0; i--){
                    $('<option />').text(years[i]).attr('value', years[i]).appendTo('#sel_year');
                }
                var yy = $('#sel_year').val();
                
                for (var i = mydata.length-1; i >= 0; i--){
                    for (var j = mydata[i].data.length-1; j >= 0; j--){
                        if (mydata[i].data[j].year != yy){
                            mydata[i].data.splice(j, 1);
                        }
                    }
                }
                
            }
            
            option.series = mydata;
            option.xAxis.categories = tveebi;
            console.log(option);
            myChart = Highcharts.chart('r_div', option);
                                
            console.log(mydata);
        }
    });
    }
    
    
    
    $('#sel_year').on('change',function(){
        var yy = $('#sel_year').val();
        
        getDataforChart(yy);
            
    })
        
    $('#btn1').on('click',function(){
        
        option.series = mydata;
        option.xAxis.categories = tveebi;
        console.log(option);
        myChart = Highcharts.chart('container1', option);
    });
</script> 
</body>
</html>