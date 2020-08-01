<?php

define('RESULT', 'result');
define('SUCCESS', 'success');
define('ERROR_TEXT', 'errorText');
define('ERROR_CODE', 'errorCode');
define('DATA', 'data');
define('XARJI', 'xarji');

const HOUR_DIFF_ON_SERVER = 2; // home 2 hour, on server 4 hour


const ORDER_STATUS_COMPLETED = 2;
const ORDER_STATUS_AUTO_CREATED = 5;
const ORDER_STATUS_DELETED = 4;

const ER_CODE_NOT_FOUNT = 1001;
const ER_CODE_VCS = 1010;

const BEER_VCS = 'beer';
const CLIENT_VCS = 'client';
const USER_VCS = 'user';
const BARREL_VCS = 'barrel';
const PRICE_VCS = 'price';

// time() funqcia gvibrnebs serveris mimdinare dros, chven vart +4 saati
$timeOnServer = date("Y-m-d H:i:s", time() + HOUR_DIFF_ON_SERVER * 3600);
$dateOnServer = date("Y-m-d", time() + HOUR_DIFF_ON_SERVER * 3600);

$response = [];
$response[SUCCESS] = true;
$response[ERROR_CODE] = 0;
$response[ERROR_TEXT] = '';



const ER_CODE_ORDER_SORTING = 1101;
const ER_CODE_ORDER_UPD_DISTRIBUTOR = 1102;