<?php

define('RESULT', 'result');
define('SUCCESS', 'success');
define('ERROR_TEXT', 'errorText');
define('ERROR_CODE', 'errorCode');
define('DATA', 'data');
define('XARJI', 'xarji');
define('ID', 'ID');

const USERTYPE_ADMIN = 9;
const USERTYPE_MANAGER = 10;
const USERTYPE_DISTRIBUTOR = 11;

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

const COMMON_SQL_ERROR_CODE = 2001;
const COMMON_ERROR_CODE = 1001;


const ER_CODE_EXTRA_BARREL_OUTPUT = 2201;
const ER_TEXT_EXTRA_BARREL_OUTPUT = "შეყვანილი რაოდენობის კასრი არ არის ობიექტზე: %s -> %d";

const ER_CODE_EXTRA_BARREL_OUTPUT_STORE = 2202;
const ER_TEXT_EXTRA_BARREL_OUTPUT_STORE = "შეყვანილი რაოდენობის ცარელი კასრი არ არის საწყობში, მითითებული დროისთვის.\nდრო: %s, \n%s -> %d";

const ER_TEXT_EXTRA_BARREL_SALE = "საწყობში არაგვაქვს ამდენი რაოდენობა!";

const ER_CODE_ADD_SALES = 2203;
const ER_CODE_BARREL_OUTPUT = 2204;
const ER_CODE_MONEY_OUTPUT = 2205;

const ER_CODE_NO_PERMISSION = 1401;
const ER_TEXT_NO_PERMISSION = "არაგაქვთ ოპერაციის განხორციელების უფლება!";

const ER_CODE_CANT_CHECK_DEBT = 3001;
const ER_TEXT_CANT_CHECK_DEBT = "ვერ მოხერხდა დავალიანების გამოთვლა!";

const ER_CODE_DEBT_ON_CLIENT = 3002;
const ER_TEXT_DEBT_ON_CLIENT = "ობიექტზე არსებობს დავალიანება!";


//  ****************  DB const values  *******************

$CUSTOMER_TB = "customer";
//$CUSTOMER_MAP_TB = "`customer_to_region_map`";
$BARREL_OUTPUT_TB = "barrel_output";
$MONEY_OUTPUT_TB = "moneyoutput";
$SALES_TB = "sales";
$ORDERS_TB = "orders";

const HISTORY_KEY = "history";
const USERS_MAP_KEY = "users";
const CUSTOMERS_MAP_KEY = "customers";
const BARRELS_MAP_KEY = "barrels";
const BEERS_MAP_KEY = "beers";
