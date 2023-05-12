<?php

require_once('/xampp/htdocs/app_config/brewery_db.php');
//require_once('/home/apenige2/app_config/brewery_db.php');

$rootPath = "/xampp/htdocs/apeni.localhost.com";
//$rootPath = "/home/apenige2/public_html";

require_once($rootPath . '/brewery/common_data.php');
require_once($rootPath . '/brewery/common_functions.php');
require_once($rootPath . '/brewery/api/MyData.php');
require_once($rootPath . '/brewery/api/BaseDataManager.php');
require_once($rootPath . '/brewery/api/boiler/BoilerDataManager.php');
require_once($rootPath . '/brewery/api/filter/FilterDataManager.php');
require_once($rootPath . '/brewery/api/beer/BeerDataManager.php');
require_once($rootPath . '/brewery/api/yeast/YeastDataManager.php');
require_once($rootPath . '/brewery/api/pouring/PourDataManager.php');
require_once($rootPath . '/brewery/api/distribution/DistributionDataManager.php');
require_once($rootPath . '/brewery/api/user/UserDataManager.php');

require_once($rootPath . '/jwt/JWT.php');
require_once($rootPath . '/jwt/extension.php');
