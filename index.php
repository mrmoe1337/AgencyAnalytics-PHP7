<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once "app/Crawler.php";

use AgencyAnalytics\App\Crawler;

$crawler = new Crawler(
    url: 'https://agencyanalytics.com',
    depth: 5
);

$crawler->crawlPages()->createReport();