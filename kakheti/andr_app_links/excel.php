<?php
 //header("Content-Type: text/plain");
 
require_once('connection.php');
$output = '';
$dges = date("Y-m-d", time()+4*3600);  
$myobj = "a";

$objID = $_GET["objID"];
//isset($_post["exp_data"])
if($objID > 0)
{
    $myobj = "a";
    $sql = "select dasaxeleba from customer where id= $objID";
    $res1 = $con->query($sql);
    while($r = mysqli_fetch_assoc($res1)){
    $myobj = $r["dasaxeleba"];
    }


$sql = "

SELECT 
    DATE_FORMAT(tarigi, '%Y-%m-%d %H:%i') AS dt, 
    k_in,
    pr, 
    pay, 
    k_out,
    (SELECT sum(pr-pay) FROM `amonaweri_obj` a 
    WHERE 
        a.tarigi <= b.tarigi 
        AND
        obieqtis_id = $objID) AS `bal`,
    id,
    comment
    FROM `amonaweri_obj` b
WHERE 
    obieqtis_id = $objID
ORDER by b.tarigi DESC
LIMIT 0, 50
    
";

    $result = $con->query($sql);
    
    if(mysqli_num_rows($result) > 0){
        $output .= '
            <table class="table" bordered="1">
            <tr>
                <th>date</th>
                <th>k_realiz</th>
                <th>price</th>
                <th>pay</th>
                <th>k_back</th>
                <th>balance</th>
                <th>comment</th>
            </tr>
        ';
        while($row = mysqli_fetch_array($result))
        {
          $output .= '
            <tr>
                <td>'.$row["dt"].'</td>
                <td>'.$row["k_in"].'</td>
                <td>'.$row["pr"].'</td>
                <td>'.$row["pay"].'</td>
                <td>'.$row["k_out"].'</td>
                <td>'.$row["bal"].'</td>
                <td>'.$row["comment"].'</td>
            </tr>
        ';  
        }
        $output .= '</table>';
        $fileName = $myobj._.$dges;
        header("Content-Type: application/xls");
        header("Content-Disposition: attachment; filename=$fileName.xls");
        echo $output;
    }
    
    // $arr = array();
    // while($rs = mysqli_fetch_assoc($result)) {
        // $arr[] = $rs;
    // }    
}

//echo json_encode($arr);
mysqli_close($con);
?>

