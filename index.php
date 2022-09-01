<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once "app/Crawler.php";

use app\Crawler\Crawler;

$crawler = new Crawler('https://agencyanalytics.com',5);

