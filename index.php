<?php


error_reporting(E_ALL);
ini_set('display_errors', '1');

require 'vendor/autoload.php';

$crawler = new AgencyAnalytics\App\Crawler(
    url: 'https://agencyanalytics.com',
    depth: 5
);

$crawler->crawlPages()->createReport();