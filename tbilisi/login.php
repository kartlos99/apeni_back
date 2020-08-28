<?php
include_once '_webLoad.php';
include_once('../commonWeb/php/loginScript.php'); // Includes Login Script

// print_r($_SESSION);
// print_r($_POST);

if (isset($_SESSION['username'])) {

    header('location: index.php');

}

?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"
          integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <!--      <link rel="stylesheet" href="style/bootstrap.min.css" >    -->
    <!--      <link rel="stylesheet" href="style/bootstrap-theme.min.css" >-->

    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link href="../commonWeb/css/main.css" rel="stylesheet">

    <title>Lima iCloud - Login</title>

</head>
<!--<body background="../commonWeb/img/beerBkg.jpg">-->


<div style="width: 560px; background: #fff; border: 1px solid #e4e4e4; padding: 20px; margin: 10px auto; border-radius: 5px;">
    <h3 style="margin : 16px">login</h3>

    <form id="loginform" action="" method="post">

        <div class="input-group">
            <span class="input-group-addon" id="sizing-addon2"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></span>
            <input id="username" type="text" class="form-control" placeholder="Username" aria-describedby="sizing-addon2"
                   name="username">
        </div>

        <div class="input-group">
            <span class="input-group-addon" id="sizing-addon2"><span class="glyphicon glyphicon-asterisk" aria-hidden="true"></span></span>
            <input id="pass" type="password" class="form-control" placeholder="Password" aria-describedby="sizing-addon2">
            <input id="passHiden" type="hidden" name="password" value="">
        </div>

        <input type="hidden" name="regdate" value="54545454">

        <div>
            <input type="submit" name="submit" class="btn btn-default centered" value="login"/>
        </div>

    </form>

    <p class="error-msg"><?php        echo $error;        ?></p>

</div>



<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>
<!-- jQuery Custom Scroller CDN -->
<!--
      <script src="js/jquery-3.2.1.slim.min.js"></script>
      <script src="js/bootstrap.min.js"></script>
-->
<script type="text/javascript" src="../commonWeb/js/sha256.js"></script>
<!--<script type="text/javascript" src="js/form1.js"></script>-->

<script>
    $('#pass').on('keyup',function (value) {
        var ps = $(this).val();
        var pass = ps;//sha256_digest(ps);
        $('#passHiden').val(pass);
    });

</script>

</body>
</html>