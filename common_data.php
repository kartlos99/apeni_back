<?php

define('RESULT', 'result');
define('SUCCESS', 'success');
define('ERROR_TEXT', 'errorText');
define('ERROR_CODE', 'errorCode');
define('DATA', 'data');
define('XARJI', 'xarji');

const ORDER_STATUS_COMPLETED = 2;
const ORDER_STATUS_AUTO_CREATED = 5;
const ORDER_STATUS_DELETED = 4;

const ER_CODE_NOT_FOUNT = 1001;

// time() funqcia gvibrnebs serveris mimdinare dros, chven vart +4 saati
$timeOnServer = date("Y-m-d H:i:s", time() + 2 * 3600);
$dateOnServer = date("Y-m-d", time() + 2 * 3600);

$response = [];
$response[SUCCESS] = true;
$response[ERROR_CODE] = 0;
$response[ERROR_TEXT] = '';