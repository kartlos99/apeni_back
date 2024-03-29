<?php

const HOUR_DIFF_ON_SERVER = 2; // home 2 hour, on server 4 hour

// time() funqcia gvibrnebs serveris mimdinare dros, chven vart +4 saati
$timeOnServer = date("Y-m-d H:i:s", time() + HOUR_DIFF_ON_SERVER * 3600);
$dateOnServer = date("Y-m-d", time() + HOUR_DIFF_ON_SERVER * 3600);


const RECORD_ID_KEY = "recordId";

const CUSTOM_HTTP_ERROR_CODE = 422;

const ERROR_CODE_EMPTY_RESULT = "EMPTY_RESULT";
const ERROR_CODE_MULTI_RESULT = "MULTI_RESULT";
const ERROR_CODE_MISSED_PARAM = 1505;

const ERROR_CODE_RECORD_NOT_FOUNDED = 1404;
const ERROR_TEXT_RECORD_NOT_FOUNDED = "record not founded!";

const ERROR_TEXT_CANT_IDENTIFY_USER = "can't identify user!";