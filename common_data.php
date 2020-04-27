<?php

define('RESULT','result');
define('SUCCESS','success');
define('ERROR_TEXT','errorText');
define('ERROR_CODE','errorCode');
define('DATA','data');
define('XARJI','xarji');

// time() funqcia gvibrnebs serveris mimdinare dros, chven vart +4 saati
$timeOnServer = date("Y-m-d H:i:s", time()+4*3600);
$dateOnServer = date("Y-m-d", time()+4*3600);

$response = [];
$response[SUCCESS] = true;
$response[ERROR_CODE] = 0;
$response[ERROR_TEXT] = '';