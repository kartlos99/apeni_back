<?php

// $x = 34;

// echo $x;

// php_info();
session_start();

// Echo session variables that were set on previous page
//echo 'stored session variables!   ';

$dt = time();



if (isset($_SESSION['tt'])){
    if (time() - $_SESSION['tt'] >= 1){
        $_SESSION['N3'] += 1;        
        $_SESSION['tt'] = time();
    }
}else{
    $_SESSION['N3'] += 1;
    $_SESSION['tt'] = time();
}

print_r($_SESSION);

echo '<br>';
echo $dt - $_SESSION['tt'];

echo "Home ENV!";
echo "<br>";
echo $_SERVER['SERVER_NAME'];
echo "<br>";
echo json_encode($_SERVER);

//echo session_destroy();
//printf("uniqid(): ".uniqid());
//printf("uniqid('php_'): %s\r\n", uniqid('php_',true));
//printf(str_replace(array('=','-','{','}','[',' ','+'), '', 'sdsdjius09d0f8d9s und==osfijfe9tfrs=kwhf vcvw+ehpdo--xf dg[df{ dfksdhjkgj}djksfh k'));

//date_default_timezone_set (Asia/Tbilisi);
//echo date('Y-m-d : g - h-i-s a', time());
// $arr = [ 'as', 7, 'be', '!:::6!2'];
// echo json_encode($arr);
