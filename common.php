<?php
/**
 * User: loveyu
 * Date: 2016/8/31
 * Time: 0:18
 */
require_once __DIR__."/function.php";
ini_set('display_errors', 'on');
error_reporting(E_ALL | E_STRICT);

if(isset($_GET['m']) && !empty($_GET['m'])) {
	$month = intval($_GET['m']);
} else {
	$month = intval(date("n"));
}
if($month < 1 || $month > 12) {
	$month = 1;
}

if(isset($_GET['y']) && !empty($_GET['y'])) {
	$year = intval($_GET['y']);
} else {
	$year = intval(date("Y"));
}

if($year < 2000 || $year > date("Y") + 2) {
	include("404.php");
	exit;
}

if(isset($_GET['update']) && $_GET['update'] === "force") {
	get_source($year, $month);
}
