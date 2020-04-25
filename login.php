<?php
require_once('phpcode/load.php');

if( isset($_GET["action"]) && $_GET["action"] == 'logout'){
	$loggedout = $myop->logout();
}

$logged = $myop->login('total_sale.php');




?>

<!DOCTYPE html>
<html>
<head>
	<title>login</title>		
	<link rel="stylesheet" type="text/css" href="/styles/main.css">
    	<!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    
    <!--<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">-->
    <style>
        body {
            background: #dcd;
        }
        div {
            margin : 6px;
        }
        .centered {
            text-align: center;
        }
    </style>
</head>
<body>

<div style="width: 360px; background: #fff; border: 1px solid #e4e4e4; padding: 20px; margin: 10px auto;">
	<h3>login თბილისი</h3>
	<p>
	    <?php 
			if ($logged == 'invalid') { echo "არასწორი მომხმარებელი ან პაროლი!";}
			
			if (isset($_GET["action"]) && $_GET["action"] == 'login') {
			    echo "გაიარეთ ავტორიზაცია!";
			} else {
				if ($logged == 'empty' && $_SERVER['REQUEST_METHOD'] === 'POST') { 
				    echo "შეავსეთ ველები!";
				}	
			}
		?>
	</p>

    <form action="" method="post">
      
      <div class="input-group">
        <span class="input-group-addon" id="sizing-addon2"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></span>
        <input type="text" class="form-control" placeholder="Username" aria-describedby="sizing-addon2" name="username">
      </div>

      <div class="input-group">
        <span class="input-group-addon" id="sizing-addon2"><span class="glyphicon glyphicon-asterisk" aria-hidden="true"></span></span>
        <input type="password" class="form-control" placeholder="Password" aria-describedby="sizing-addon2" name="password">
      </div>
  
      <input type="hidden" name="regdate" value="54545454">
  
      <div>
        <input type="submit" class="btn btn-default centered" value="login"/>
      </div>

    </form>

<div class="righttext" ><a href="https://www.apeni.ge/kakheti/login.php">კახეთის გვერდზე გადასვლა</a> </div>
</div>



<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</body>
</html>