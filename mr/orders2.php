<?php
session_start();
require_once('../phpcode/load.php');
$logged = $myop->checklogin();

if ($logged == false) {
    $url = "http" . ((!empty($_SERVER['HTTPS'])) ? "s" : "") . "://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    $redirect = str_replace('orders.php', 'login.php', $url);
    header("Location: $redirect?action=login");
    exit;
} else {
    $username = $_COOKIE['k_user'];
    $authID = $_COOKIE['k_authID'];

    $table = 'users';
    $sql = "SELECT * FROM $table WHERE username = '" . $username . "'";

    $results = $con->query($sql);

    if (!$results) {
        die('aseti momxmarebeli ar arsebobs!');
    }

    $logedUser = mysqli_fetch_assoc($results);
}

function headerRow($items = [], $pos = 0, $margeN = 1)
{
    $h_row = "";
    for ($i = 0; $i < count($items); $i++) {
        $colspan = ($pos == $i ? " colspan=\"$margeN\"" : "");
        $h_row .= "<th$colspan>$items[$i]</th>";
    }
    return $h_row;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>შეკვეთები</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"
          integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <!-- Our Custom CSS -->
    <!--    <link rel="stylesheet" href="../style/sidebar-style.css">-->
    <!-- Scrollbar Custom CSS -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">

    <!--    <link rel="stylesheet" href="css/alk-sanet.min.css"/>-->
    <!--    <link rel="stylesheet" href="css/bpg-arial.min.css"/>-->
    <link rel="stylesheet" href="../styles/main.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css"
          integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
</head>
<body>
<div id="dateDiv">
    <label for="orderDate">აირჩიეთ თარიღი: </label>
    <input id="orderDate" type="date">
    <button id="btnLoadOrders" class="btn">ჩატვირთვა</button>
</div>
<table id="tbOrderList" class="table-section table">
    <thead>
    <tr>
        <?= headerRow(["chk", "ობიექტი", "ლუდი", "შეკვ.30", "შეკვ.50", "მიტანა.30", "მიტანა.50", "კომენტარი", "დისტრიბუტორი", "თანხა"], 0, 1) ?>
    </tr>
    </thead>
    <tbody></tbody>
</table>


<script src="https://code.jquery.com/jquery-3.2.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>
<!-- jQuery Custom Scroller CDN -->
<script
        src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.concat.min.js"></script>

<!--<script type="text/javascript" src="../js/sha256.js"></script>-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/twbs-pagination/1.3/jquery.twbsPagination.min.js"></script>

<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>-->
<!--<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">-->
<!--<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">

<script type="text/javascript">
    $(document).ready(function () {
        $("#sidebar").mCustomScrollbar({
            theme: "minimal"
        });

        $('#sidebarCollapse').on('click', function () {
            $('#sidebar, #content').toggleClass('active');
            $('.collapse.in').toggleClass('in');
            $('a[aria-expanded=true]').attr('aria-expanded', 'false');
        });
    });

</script>

<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script type="text/javascript" src="js/order.js"></script>
</body>

</html>