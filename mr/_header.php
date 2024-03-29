<?php
//namespace Apeni\JWT;
session_start();
$VERSION = "4.2.0";
include_once '_webLoad.php';
//$sessionData = checkToken();
if (!isset($_SESSION['username'])) {
    $url = "http" . ((!empty($_SERVER['HTTPS'])) ? "s" : "") . "://" . $_SERVER['SERVER_NAME'] . $folder . "/login.php";
//    $url = str_replace('administrator/page1.php', 'login.php', $url);
    header("Location: $url");
}
//print_r($_SESSION);
$userType = $_SESSION['usertype'] ?? 0;

?>

<!DOCTYPE html>
<html>
<head>
    <title>Apeni</title>
    <!-- Bootstrap CSS -->
    <!-- Latest compiled and minified CSS -->
    <!--    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"
          integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <!-- Our Custom CSS -->
    <link rel="stylesheet" href="../commonWeb/css/sidebar-style.css">
    <link rel="stylesheet" href="../commonWeb/css/main.css">
    <!-- Scrollbar Custom CSS -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">

    <link rel="stylesheet" href="../commonWeb/css/alk-sanet.min.css"/>
    <link rel="stylesheet" href="../commonWeb/css/bpg-arial.min.css"/>
    <!--    <link rel="stylesheet" href="style/local.css">-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css"
          integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://code.highcharts.com/highcharts.js"></script>
</head>
<body onpageshow="f_show()" onpagehide="f_hide()">
<?php

$pos = strpos($_SERVER['PHP_SELF'], "index.php");
if ($pos !== false) {
    $thisPage = 'index';
}

$pos = strpos($_SERVER['PHP_SELF'], "byClient.php");
if ($pos !== false) {
    $thisPage = 'byClient';
}

$pos = strpos($_SERVER['PHP_SELF'], "currentOrders.php");
if ($pos !== false) {
    $thisPage = 'currentOrders';
}

$pos = strpos($_SERVER['PHP_SELF'], "finances.php");
if ($pos !== false) {
    $thisPage = 'finances';
}

$pos = strpos($_SERVER['PHP_SELF'], "cleaning.php");
if ($pos !== false) {
    $thisPage = 'cleaning';
}

$pos = strpos($_SERVER['PHP_SELF'], "storeHouse.php");
if ($pos !== false) {
    $thisPage = 'storeHouse';
}

if ($userType != USERTYPE_ADMIN && $thisPage != 'currentOrders')
    header("Location: logout.php");

?>
<input type="hidden" id="currUserdata"
       data-ut="<?php echo $userType ?>"
       data-page="<?= $thisPage ?>"
       data-tkn="<?= $_SESSION['tkn'] ?>"
       data-regionID="0"
       data-userID="<?= $_SESSION['userID'] ?>"/>

<div class="wrapper">
    <!-- Sidebar Holder -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3 id="loged_username"><?= $_SESSION['username'] ?></h3>
            <h5><?= $userType == USERTYPE_ADMIN ? "admin" : "user" ?></h5>
        </div>

        <ul class="list-unstyled components">
            <?php if ($userType == USERTYPE_ADMIN) : ?>
                <li class="index">
                    <a href="index.php">თვის შედეგები</a>
                </li>
                <li class="byClient">
                    <a href="byClient.php">ობიექტები</a>
                </li>
            <?php endif; ?>
            <li class="currentOrders">
                <a href="currentOrders.php">შეკვეთები</a>
            </li>
            <?php if ($userType == USERTYPE_ADMIN) : ?>
                <li class="finances">
                    <a href="finances.php">ფინანსები</a>
                </li>
            <?php endif; ?>
            <?php if ($userType == USERTYPE_ADMIN || $userType == USERTYPE_MANAGER) : ?>
                <li class="storeHouse">
                    <a href="storeHouse.php">საწყობი</a>
                </li>
            <?php endif; ?>
            <li class="cleaning">
                <a href="cleaning.php">სისტ. წმენდა</a>
            </li>
        </ul>

        <div class="region-container">
            <label for="selRegion">რეგიონი</label>
            <select id="selRegion" class="form-control"></select>
        </div>

        <ul class="list-unstyled CTAs">
            <!-- <li><a href="https://bootstrapious.com/tutorial/files/sidebar.zip" class="download">Download source</a></li> -->
            <li><a href="logout.php" class="article">გასვლა</a></li>
        </ul>
        <div class="onbuttom">v <?= $VERSION ?></div>
    </nav>
    <!--sidebar-->

    <!-- Page Content Holder -->
    <div id="content">

        <button type="button" id="sidebarCollapse" class="btn btn-info navbar-btn">
            <i class="glyphicon glyphicon-menu-hamburger"></i>
            <span></span>
        </button>