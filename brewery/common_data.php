<?php

const HOUR_DIFF_ON_SERVER = 2; // home 2 hour, on server 4 hour

// time() funqcia gvibrnebs serveris mimdinare dros, chven vart +4 saati
$timeOnServer = date("Y-m-d H:i:s", time() + HOUR_DIFF_ON_SERVER * 3600);
$dateOnServer = date("Y-m-d", time() + HOUR_DIFF_ON_SERVER * 3600);


const RECORD_ID_KEY = "recordId";