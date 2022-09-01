<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once "app/Crawler.php";

use app\Crawler\Crawler;
use app\Crawler\Reporting;

$crawler = new Crawler('https://agencyanalytics.com',5);
$reporting = new Reporting();
$reporting->createReport();

