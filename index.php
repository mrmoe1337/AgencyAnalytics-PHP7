<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require 'vendor/autoload.php';

use Agencyanalytics\App\Crawler;

$crawler = new Crawler(
    url: 'https://agencyanalytics.com',
    depth: 5
);

$crawler->crawlPages()->createReport();