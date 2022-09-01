<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

spl_autoload_register(function($class) {
    require 'app/' . str_replace('\\', '/', $class) . '.php';
});

use app\Crawler\Crawler;

$crawler = new Crawler('https://agencyanalytics.com',5);
$crawler->crawlPage()->createReport();