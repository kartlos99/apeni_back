<?php
require_once('load.php');
$myop->register('login.php');
?>

<!DOCTYPE html>
<html>
<head>
	<title>registration</title>
	<link rel="stylesheet" type="text/css" href="main.css">
</head>
<body>

	<div style="width: 960px; background: #fff; border: 1px solid #e4e4e4; padding: 20px; margin: 10px auto;">
		<h3>რეგისტრაცია</h3>

		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<table>
				<tr>
					<td>Name</td>
					<td><input type="text" name="name"></td>
				</tr>
				<tr>
					<td>Username</td>
					<td><input type="text" name="username"></td>
				</tr>
				<tr>
					<td>Password</td>
					<td><input type="password" name="password"></td>
				</tr>
				<tr>
					<td>Email</td>
					<td><input type="text" name="email"></td>
				</tr>
				<input type="hidden" name="regdate" value="<?php echo time(); ?>">
				<tr>
					<td></td>
					<td><input type="submit" value="register"/></td>
				</tr>
			</table>
			
		</form>
		<p>arsebuli momxmareblistvis <a href="login.php?action=fromreg">login</a></p>
	</div>
</body>
</html>