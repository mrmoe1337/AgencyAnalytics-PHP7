<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

spl_autoload_register(function($class) {
    include str_replace('\\', '/', $class) . '.php';
});

$crawler = new src\Crawler('https://agencyanalytics.com',5);
$crawler->crawlPage()->createReport();