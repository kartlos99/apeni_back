<?php
require_once('phpcode/load.php');
$logged = $myop->checklogin();

if ($logged == false){
	$url = "http". ((!empty($_SERVER['HTTPS'])) ? "s" : "") . "://" . $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	$redirect = str_replace('all_info.php', 'login.php', $url);
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

?>

<!DOCTYPE html>
<html>
    <head>
        <title>ობიექტების ამონაწერი</title>
        <script
      src="https://code.jquery.com/jquery-3.3.1.min.js"
      integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
      crossorigin="anonymous"></script>
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="/styles/main.css">
        <style>
            table {
                border 2px solid black;
                border-collapse: collapse;
                background-color: #eee;
            }
            th {
                padding:8px;
                font-size: 20px;
                background-color: #aaa;
            }
            td {
                padding:4px;
            }
            p {
                padding:8px;
                background-color: #089;
                font-size: 20px;
                color: #fff;
                text-align: center;
            }
            form {
                padding: 4px;
                margin: 8px;
                color: #fff;
                background-color: #055;
            }
            strong {
                margin: 8px;
            }
        </style>
    </head>
    
<body>
    <table width="100%">
        <tr>
            <td><a href="/tbilisi/orders2.php">შეკვეთები</a></td>
            <td><a href="/total_sale.php">თვის შედეგები</a></td>
            <td><p>ამონაწერის ჩამოტვირთვა</p></td>
            <td><p class="righttext"> (თბილისი) მომხმარებელი: <?php echo $logedUser['name']?> <a href="login.php?action=logout">გასვლა</a></p></td>
        </tr>
    </table>
    <div class="righttext" ><a href="https://www.apeni.ge/kakheti/all_info.php">კახეთის გვერდზე გადასვლა</a>  
    </div>
    
    <table width="100%">
        <tr>
            <td width='25%'>
                
<?php
//  header("Content-Type: text/plain");
 
require_once('imports.php');

$sql = "SELECT * FROM obieqtebi where `active`=1 order by dasaxeleba" ;
//$arr = array();
$result = $con->query($sql);

$output = '
            <table class="table" bordered="1">
            <tr>
                <th>ობ. დასახელება</th>
                <th>გადმოწერა</th>
            </tr>
        ';
        
        while($row = mysqli_fetch_array($result))
        {
          $output .= '
            <tr>
                <td>'.$row["dasaxeleba"].'</td>
                <td><Button onclick="fdown('.$row["id"].')">ამონაწ. <i class="material-icons" style="font-size:18px">&#xe258;</i></Button></td>
            </tr>
        ';  
        }
        
        $output .= '</table>';

    echo $output;
mysqli_close($con);
?>

<Button onclick="fdown(0)">საერთო ამონაწერი  <i class="material-icons" style="font-size:18px">&#xe258;</i></Button>

            </td>
            <td class='chart3' valign="top" width='74%'>
                <span>აირჩიეთ პერიოდი </span><input id="date1" type="date">-დან   <input id="date2" type="date">-მდე  <button id='btnR' style="font-size:13px">განახლება<i class="material-icons"  style="font-size:18px">refresh</i></button>
                <h4 id="suminfo"></h4>
                <div id='container3'></div>
            </td>
        </tr>
    </table>            

<form id="form1" method="get" action="excel.php" display="">
    შეიყვანეთ ობიექტის ID :  
    <input id="inp1" type="text" name="objID" value=""/>
    <input type="submit" name="data" class="btn btn-success" value="Export to Excel" />
</form>

<script>

    $('#form1').hide();
    function fdown(objid){
        $('#inp1').val(objid);
        $('#form1').trigger('submit');
    
    }
    
    var ludi1 = 'ლაგერი';
    var ludi2 = 'გაუფილტრავი';
    var optionO = {
        chart: {
            type: 'bar'
        },
        tooltip: {
        },
        title: {
            text: "რეალიზაცია ობიექტების მიხედვით"
        },

        xAxis: {
            categories: []
        },
        yAxis: {
            min: 0,
            title: {
                text: 'ლიტრი'
            },
            opposite: true
        },
        legend: {
            reversed: true
        },
        plotOptions: {
            series: {
                stacking: 'normal'
            }
        },
        series: []
    };
    
    var currDate = new Date();

    var strDate1 = dateformat(currDate);
    $('#date1').val(strDate1).attr('max', strDate1);
    currDate.setDate(currDate.getDate() + 1);
    
    var strDate2 = dateformat(currDate);
    $('#date2').val(strDate2).attr('max', strDate2);
    currDate.setDate(1);
    strDate1 = dateformat(currDate);
    $('#date1').val(strDate1);
    
    var ludi1_sum = 0;
    var ludi2_sum = 0;
    
    function getDataforChart(){
        var dt1 = $('#date1').val();
        var dt2 = $('#date2').val();
        var ur = location.protocol + '//apeni.ge/results_by_obj.php?date1='+dt1+'&date2='+dt2;
        
        // ******************** obieqtebi **************************************
        $.ajax({
            url: ur,
            method: 'get',
            dataType: 'json',
            success: function (response) {
                
                ludi1_sum = 0;
                ludi2_sum = 0;
                
                var lag = [];
                var gauf = [];
                var obieqtebi = Object.keys(response);
                var serObj = [];
                var beersum = [];
                
                for (var i = 0; i<obieqtebi.length; i++){
                    beersum[i] = response[obieqtebi[i]][0].sum;
                }
                var xa = []
                for (var i = 0; i<obieqtebi.length; i++){
                    xa.push({n: obieqtebi[i], s: beersum[i]})
                }
                
                function compare(a,b) {
                  if (a.s < b.s)
                    return 1;
                  if (a.s > b.s)
                    return -1;
                  return 0;
                }

                xa.sort(compare);
                
                for (var i = 0; i<xa.length; i++){
                    obieqtebi[i] = xa[i].n;
                    var currobj = response[obieqtebi[i]];
                    
                    for (var j = 1; j<currobj.length; j++){
                        
                        if (currobj[j].ludi == ludi1){
                            ludi1_sum += parseInt(currobj[j].lt);
                            lag.push({x:i, y: parseInt(currobj[j].lt), name: currobj[j].obname+' : <b>'+xa[i].s+'</b> (ლტ.)', color: currobj[j].color})
                        } else{
                            ludi2_sum += parseInt(currobj[j].lt);
                            gauf.push({x:i, y: parseInt(currobj[j].lt), name: currobj[j].obname+' : <b>'+xa[i].s+'</b> (ლტ.)', color: currobj[j].color})                                
                        }
                    }
                }
                
                if (gauf.length > 0 && lag.length > 0){
                    serObj.push({
                        name : ludi1,
                        data : lag,
                        color: lag[0].color
                    }, {
                        name : ludi2,
                        data : gauf,
                        color: gauf[0].color
                    });
                }else{
                    if (gauf.length > 0){
                        serObj.push({
                            name : ludi2,
                            data : gauf,
                            color: gauf[0].color
                        });    
                    }
                    if (lag.length > 0){
                        serObj.push({
                            name : ludi1,
                            data : lag,
                            color: lag[0].color
                        });    
                    }
                }

                optionO.series = serObj;
                optionO.chart.height = obieqtebi.length * 30 +160 + 'px';
                optionO.xAxis.categories = obieqtebi;
                Highcharts.chart('container3', optionO);
                console.log(optionO);
                $('#container3').css("height", optionO.chart.height );
                
                $('#suminfo').html("ლაგერი " + ludi1_sum+ " <br>გაუფილრავი " + ludi2_sum);
                console.log(ludi1_sum);
            }
        });
    }
    
    $('#btnR').on('click',function(){
        getDataforChart();
    });
    
    function dateformat(d) {
        var mm, dd;
        if (d.getMonth() < 9) {
            mm = "0" + (d.getMonth() + 1);
        } else {
            mm = d.getMonth() + 1;
        }
        if (d.getDate() < 10) {
            dd = "0" + d.getDate();
        } else {
            dd = d.getDate();
        }
        return d.getFullYear() + "-" + mm + "-" + dd;
    }
    
    getDataforChart();
    
</script>

</body>
</html>