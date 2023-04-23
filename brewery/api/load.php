<?php

require_once('/xampp/htdocs/app_config/brewery_db.php');

$rootPath = "/xampp/htdocs/apeni.localhost.com";

require_once($rootPath . '/brewery/common_data.php');
require_once($rootPath . '/brewery/common_functions.php');
require_once($rootPath . '/brewery/api/MyData.php');
require_once($rootPath . '/brewery/api/BaseDataManager.php');
require_once($rootPath . '/brewery/api/boiler/BoilerDataManager.php');
require_once($rootPath . '/brewery/api/filter/FilterDataManager.php');
require_once($rootPath . '/brewery/api/beer/BeerDataManager.php');
require_once($rootPath . '/brewery/api/yeast/YeastDataManager.php');
require_once($rootPath . '/brewery/api/pouring/PourDataManager.php');

require_once($rootPath . '/jwt/JWT.php');
require_once($rootPath . '/jwt/extension.php');
