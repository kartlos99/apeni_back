<!DOCTYPE html>
<html>
    <head>
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
                background-color: #044;
                font-size: 20px;
                color: #fff;
                text-align: center;
            }
            form {
                padding: 4px;
                color: #fff;
                background-color: #055;
            }
        </style>
    </head>
    
<body>
    
    <p>აირჩიეთ ობიექტი ამონაწერის ჩამოსატვირთად</p>
    
<?php
//  header("Content-Type: text/plain");
 
require_once('connection.php');

$sql = "SELECT * FROM $CUSTOMER_TB where `active`=1 order by dasaxeleba" ;
//$arr = array();
$result = $con->query($sql);

$output = '
            <table class="table" bordered="1">
            <tr>
                <th>ID</th>
                <th>ობ. დასახელება</th>
            </tr>
        ';
        
        while($row = mysqli_fetch_array($result))
        {
          $output .= '
            <tr>
                <td>'.$row["id"].'</td>
                <td>'.$row["dasaxeleba"].'</td>
            </tr>
        ';  
        }
        
        $output .= '</table>';
        
    echo $output;

mysqli_close($con);

?>

<form method="get" action="excel.php">
    შეიყვანეთ ობიექტის ID :  <input type="text" name="objID" value=""/>
        <input type="submit" name="data" class="btn btn-success" value="Export to Excel" />
    </form>
<h1></h1>
<div><a href="/total_sale.php">თვის შედეგები</a></div>

</body>
</html>