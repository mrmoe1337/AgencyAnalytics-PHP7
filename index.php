<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

use app\Crawler\Crawler;

require_once "app/Crawler.php";

$crawler = new Crawler('https://agencyanalytics.com',5);
$crawler->crawlPage()->createReport();