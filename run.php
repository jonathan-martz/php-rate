<?php
include('Rate.php');

$rate = new Rate('Rate.php');
$report = $rate->report();

var_dump($report);
