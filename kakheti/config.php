<?php

define('HOST', 'Localhost');
define('DB_name', 'apenige2_kakheti');
define('DB_user', 'apenige2_kakheti_user');
define('DB_pass', 'KlasikA01');

define('NONCE', '384729873492473947293472938573498574395438');
define('KAY', 'skdlfjli9euteirfusdes9oeupt54t94ruwpwe9w35r90438r0r234905sfuuzHHKw45');

$link = mysqli_connect(HOST, DB_user, DB_pass, DB_name) or die('db_connection_error!..');
mysqli_set_charset($link, "utf8");

?>