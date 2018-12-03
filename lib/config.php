<?php

$TEST_DOMAIN = 'localhost';
$DEV_DOMAIN = 'xxxxx';
$PD_DOMAIN = 'xxxxx'; //production domain name

// -----------------------------------------------------------------------------
session_start();

// -----------------------------------------------------------------------------

if ($_SERVER['HTTP_HOST']==$TEST_DOMAIN || $_SERVER['HTTP_HOST']=='www.'.$TEST_DOMAIN){
    //TEST 
    $_CONFIG['db'] = array(
        'dsn' => 'mysql:host=localhost',
        'database' => 'clock-in',
        'username' => 'xxxxx',
        'password' => 'xxxxx',
        'presistent' => false,
        'page_size' => 10
    );
    $PAGE_SIZE = 10;

} else if ($_SERVER['HTTP_HOST']==$DEV_DOMAIN || $_SERVER['HTTP_HOST']=='www.'.$DEV_DOMAIN){
    //DEV
    $_CONFIG['db'] = array(
        'dsn' => 'mysql:host=localhost',
        'database' => 'clock-in',
        'username' => 'xxxxx',
        'password' => 'xxxxx',
        'presistent'=> false,
        'page_size' => 10
    );
    $PAGE_SIZE = 10;

} else if ($_SERVER['HTTP_HOST']==$PD_DOMAIN || $_SERVER['HTTP_HOST']=='www.'.$PD_DOMAIN){
    //PD
    $_CONFIG['db'] = array(
        'dsn' => 'mysql:host=xxxxx',
        'database' => 'clock-in',
        'username' => 'xxxxx',
        'password' => 'xxxxx',
        'presistent'=> false,
        'page_size' => 10
    );
    $PAGE_SIZE = 10;

} else {
    die(json_encode(['code'=>0, 'msg'=>'domain_error'], JSON_UNESCAPED_UNICODE));
}

// -----------------------------------------------------------------------------
error_reporting(E_ALL ^ E_NOTICE);
mb_internal_encoding('UTF-8');
date_default_timezone_set('Asia/Taipei');


?>