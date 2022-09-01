<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once "app/Crawler.php";
require_once "app/Reporting.php";

use app\Crawler\Crawler;
use app\Crawler\Reporting;

$crawler = new Crawler('https://agencyanalytics.com',5);
$reporting = new Reporting('https://agencyanalytics.com',5);
$reporting->createReport();

