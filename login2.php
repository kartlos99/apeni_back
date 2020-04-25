<?php
require_once('phpcode/load.php');

if( $_GET["action"] == 'logout'){
	$loggedout = $myop->logout();
}

$logged = $myop->login('total_sale.php');
?>

<!DOCTYPE html>
<html>
<head>
	<title>login</title>		
	<link rel="stylesheet" type="text/css" href="/styles/main.css">
</head>
<body>

<div style="width: 960px; background: #fff; border: 1px solid #e4e4e4; padding: 20px; margin: 10px auto;">
	<h3>login page</h3>
	<p>
		<?php 
			if ($logged == 'invalid') { echo "araswori paroli!";}
			
			if ($_GET["action"] == 'login') {echo "gaiaret avtorizacia!";} else {
				if ($logged == 'empty') { echo "sheavseT velebi!";}	
			}
		?>
	</p>

		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<table>
				
				<tr>
					<td>Username</td>
					<td><input type="text" name="username"></td>
				</tr>
				<tr>
					<td>Password</td>
					<td><input type="password" name="password"></td>
				</tr>
				<input type="hidden" name="regdate" value="54545454">
				<tr>
					<td></td>
					<td><input type="submit" value="login"/></td>
				</tr>
			</table>
			
		</form>
		<p>რეგისტრაცია გათიშულია </p>

</body>
</html>