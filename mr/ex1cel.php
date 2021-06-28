<?php
 //header("Content-Type: text/plain");
 
require_once('andr_app_links/connection.php');
$output = '';
$dges = date("Y-m-d", time()+4*3600);  
$myobj = "a";

if(isset($_GET["objID"]))
{
    $objID = $_GET["objID"];
    $sql = "";
    
    if($objID > 0){
        $myobj = "a";
        $sql = "select dasaxeleba from $CUSTOMER_TB where id= $objID";
        $res1 = $con->query($sql);
        while($r = mysqli_fetch_assoc($res1)){
            $myobj = $r["dasaxeleba"];
        }

        $sql = "
        SELECT 
            DATE_FORMAT(b.tarigi, '%Y-%m-%d %H:%i') AS dt, 
            k_in,
            lt,
            pr, 
            pay, 
            k_out,
            (SELECT sum(pr-pay) FROM `amonaweri_obj` a 
            WHERE 
                a.tarigi <= b.tarigi 
                AND
                obieqtis_id = $objID) AS `bal`,
            b.id,
            b.comment,
            c.name
            FROM `amonaweri_obj` b, users c
        WHERE 
            obieqtis_id = $objID AND b.distributor_id = c.id
        ORDER by b.tarigi DESC
        ";
    
        $result = $con->query($sql);
    
        if(mysqli_num_rows($result) > 0){
            $output .= '
                <table class="table" bordered="1">
                <tr>
                    <th>თარიღი</th>
                    <th>კასრი რეალიზ.</th>
                    <th>ლიტრი</th>
                    <th>ღირებ.</th>
                    <th>გადახდა</th>
                    <th>კასრი დაბრუნ.</th>
                    <th>ბალანსი</th>
                    <th>კომენტარი</th>
                    <th>დისტრიბუტორი</th>
                </tr>
            ';
            while($row = mysqli_fetch_array($result)) {
                $output .= '
                <tr>
                    <td>'.$row["dt"].'</td>
                    <td>'.$row["k_in"].'</td>
                    <td>'.$row["lt"].'</td>
                    <td>'.round($row["pr"],2).'</td>
                    <td>'.round($row["pay"],2).'</td>
                    <td>'.$row["k_out"].'</td>
                    <td>'.$row["bal"].'</td>
                    <td>'.$row["comment"].'</td>
                    <td>'.$row["name"].'</td>
                </tr>
                ';  
            }
            $output .= '</table>';
            $fileName = $myobj._.$dges;
        }
        
    }    
        
    if($objID == 0){
        $sql = "
        SELECT 
            DATE_FORMAT(b.tarigi, '%Y-%m-%d %H:%i') AS dt, 
            o.dasaxeleba,
            k_in,
            lt,
            pr, 
            pay, 
            k_out,
            
            b.comment,
            c.name
            FROM `amonaweri_obj` b, users c, $CUSTOMER_TB o
        WHERE 
            b.distributor_id = c.id AND b.obieqtis_id = o.id
        ORDER by b.tarigi DESC
            ";
        $result = $con->query($sql);
    
        if(mysqli_num_rows($result) > 0){
            $output .= '
                <table class="table" bordered="1">
                <tr>
                    <th>თარიღი</th>
                    <th>ობიექტი</th>
                    <th>კასრი რეალიზ.</th>
                    <th>ლიტრი</th>
                    <th>ღირებ.</th>
                    <th>გადახდა</th>
                    <th>კასრი დაბრუნ.</th>
                    <th>კომენტარი</th>
                    <th>დისტრიბუტორი</th>
                </tr>
            ';
            while($row = mysqli_fetch_array($result)) {
                $output .= '
                <tr>
                    <td>'.$row["dt"].'</td>
                    <td>'.$row["dasaxeleba"].'</td>
                    <td>'.$row["k_in"].'</td>
                    <td>'.$row["lt"].'</td>
                    <td>'.round($row["pr"],2).'</td>
                    <td>'.round($row["pay"],2).'</td>
                    <td>'.$row["k_out"].'</td>
                    <td>'.$row["comment"].'</td>
                    <td>'.$row["name"].'</td>
                </tr>
            ';  
            }
            $output .= '</table>';
            $fileName = "saerto_".$dges;
        }
        
    }
    header("Content-Type: application/xls");
    header("Content-Disposition: attachment; filename=$fileName.xls");
    echo $output;
        
    // $arr = array();
    // while($rs = mysqli_fetch_assoc($result)) {
        // $arr[] = $rs;
    // }    
}

//echo json_encode($arr);
mysqli_close($con);
?>
