<?php
 //header("Content-Type: text/plain");
 
require_once('andr_app_links/connection.php');

$output = '<table class="table" bordered="1">';
$dges = date("Y-m-d", time()+4*3600);  
$myobj = "a";
$year = 2018;

function makeRow($columns, $teg){
    $hRow = "<tr>";
    foreach($columns as $item){
        $hRow .= "<".$teg.">" . $item . "</".$teg.">";
    }
    return $hRow . "</tr>";    
}

if(isset($_GET["objID"]))
{
    $objID = $_GET["objID"];
    $sql = "";
    
    if($objID > 0){
        $myobj = "a";
        $sql = "select dasaxeleba from obieqtebi where id= $objID";
        $res1 = $con->query($sql);
        while($r = mysqli_fetch_assoc($res1)){
            $myobj = $r["dasaxeleba"];
        }

        $sql = "
        SELECT 
            DATE_FORMAT(b.tarigi, '%Y-%m-%d %H:%i') AS dt, 
            kasri30,
            kasri50,
            dasaxeleba,
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
            obieqtis_id = $objID AND b.distributor_id = c.id AND YEAR(tarigi) > $year
        ORDER by b.tarigi DESC
        ";
    
        $result = $con->query($sql);
    
        if(mysqli_num_rows($result) > 0){
            $tHead = [ "თარიღი2","კასრი რეალიზ.30","კასრი რეალიზ.50","დასახელება","ლიტრი","ღირებ.","გადახდა","კასრი დაბრუნ.","ბალანსი","კომენტარი","დისტრიბუტორი" ];
            $output .= makeRow($tHead, "th");
            while($row = mysqli_fetch_array($result)) {
                $output .= makeRow([
                    $row["dt"],
                    $row["kasri30"] == 0 ? '' : $row["kasri30"],
                    $row["kasri50"] == 0 ? '' : $row["kasri50"],
                    $row["dasaxeleba"],
                    $row["lt"],
                    round($row["pr"],2),
                    round($row["pay"],2),
                    $row["k_out"],
                    $row["bal"],
                    $row["comment"],
                    $row["name"]
                ], "td");
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
            kasri30,
            kasri50,
            b.dasaxeleba as beerName,
            lt,
            pr, 
            pay, 
            k_out,
            
            b.comment,
            c.name
        FROM 
            `amonaweri_obj` b, users c, obieqtebi o
        WHERE 
            b.distributor_id = c.id AND b.obieqtis_id = o.id AND YEAR(tarigi) > $year AND o.id NOT IN(185, 187, 192)
        ORDER by b.tarigi DESC
            ";
        $result = $con->query($sql);
    
        if(mysqli_num_rows($result) > 0){
            $tHead = [ "თარიღი", "ობიექტი", "რეალიზ. კ30", "კ50" , "ლუდი", "ლიტრი","ღირებ.","გადახდა","კასრი დაბრუნ.","კომენტარი","დისტრიბუტორი" ];
            $output .= makeRow($tHead, "th");
            while($row = mysqli_fetch_array($result)) {
                $output .= makeRow([
                    $row["dt"],
                    $row["dasaxeleba"],
                    $row["kasri30"] == 0 ? '' : $row["kasri30"],
                    $row["kasri50"] == 0 ? '' : $row["kasri50"],
                    $row["beerName"],
                    $row["lt"],
                    round($row["pr"],2),
                    round($row["pay"],2),
                    $row["k_out"],
                    $row["comment"],
                    $row["name"]
                ], "td");
            }
            $output .= '</table>';
            $fileName = "saerto_".$dges;
        }
        
    }
    header("Content-Type: application/xls");
    header("Content-Disposition: attachment; filename=$fileName.xls");
    echo $output;

}

//echo json_encode($arr);
mysqli_close($con);
?>