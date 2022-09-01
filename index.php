<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

spl_autoload_register(function($class){
    require_once __DIR___.'/src/'.str_replace('\\','/', $class) . '.php';
});

use src\Crawler\Crawler;

$crawler = new Crawler('https://agencyanalytics.com',5);
$crawler->crawlPage()->createReport();