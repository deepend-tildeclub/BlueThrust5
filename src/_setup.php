<?php

/*
 * Bluethrust Clan Scripts v4
 * Copyright 2014
 *
 * Author: Bluethrust Web Development
 * E-mail: support@bluethrust.com
 * Website: http://www.bluethrust.com
 *
 * License: http://www.bluethrust.com/license.php
 *
 */



// Error reporting default = off.
$debug = false;
mysqli_report(MYSQLI_REPORT_OFF);
error_reporting(0);
ini_set('display_errors', '0');

function debug() {
	global $debug;
	$debug = true;
	mysqli_report(MYSQLI_REPORT_STRICT);
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
}



// DECLARE GLOBAL VARIABLES
$SQL_PROFILER = [];



// Check PHP Version

if(version_compare(phpversion(), "7.0") < 0) {
	die("You must be using at least PHP version 5.3 in order to run Bluethrust Clan Scripts v4.  Your current PHP Version: ".phpversion());	
}


// This setup page should not be changed.  Edit _config.php to configure your website.

ini_set('session.use_only_cookies', 1);
ini_set('session.gc_maxlifetime', 60*60*24*3);

if(!isset($prevFolder)) {
	$prevFolder = "";	
}


if(isset($_COOKIE['btUsername']) && isset($_COOKIE['btPassword'])) {
	session_start();
	$_SESSION['btUsername'] = $_COOKIE['btUsername'];
	$_SESSION['btPassword'] = $_COOKIE['btPassword'];
}
else {
	session_start();
}

if(!isset($_SESSION['csrfKey'])) {
	$_SESSION['csrfKey'] = md5(uniqid());
}

require_once($prevFolder."_config.php");
define("BASE_DIRECTORY", $BASE_DIRECTORY);
//define("BASE_DIRECTORY", str_replace("//", "/", $_SERVER['DOCUMENT_ROOT'].$MAIN_ROOT));
define("MAIN_ROOT", $MAIN_ROOT);


$PAGE_NAME = "";
require_once(BASE_DIRECTORY."_functions.php");

define("FULL_SITE_URL", getHTTP().$_SERVER['SERVER_NAME'].MAIN_ROOT);


$mysqli = new btmysql($dbhost, $dbuser, $dbpass, $dbname);


$mysqli->set_tablePrefix($dbprefix);
$mysqli->set_testingMode(true);

$logObj = new Basic($mysqli, "logs", "log_id");

// Get Clan Info
$webInfoObj = new WebsiteInfo($mysqli);

$webInfoObj->select(1);

$websiteInfo = $webInfoObj->get_info_filtered();
$CLAN_NAME = $websiteInfo['clanname'];
$THEME = $websiteInfo['theme'];
define("THEME", $THEME);

$arrWebsiteLogoURL = parse_url($websiteInfo['logourl']);
if(!isset($arrWebsiteLogoURL['scheme']) || $arrWebsiteLogoURL['scheme'] == "") {
	$websiteInfo['logourl'] = $MAIN_ROOT."themes/".$THEME."/".$websiteInfo['logourl'];
}

// Default websiteinfo values
require_once(BASE_DIRECTORY."include/websiteinfo_defaults.php");


if(!isset($_SESSION['appendIP'])) {
	$_SESSION['appendIP'] = substr(md5(uniqid().time()),0,10);
}

$IP_ADDRESS = $_SERVER['REMOTE_ADDR'];

// Check Debug Mode
if($websiteInfo['debugmode'] == 1) {
	debug();
}

// Check for Ban
$ipbanObj = new IPBan($mysqli);
if($ipbanObj->isBanned($IP_ADDRESS)) {
	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."banned.php';</script>");
}

date_default_timezone_set($websiteInfo['default_timezone']);


$hooksObj = new btHooks();
$btThemeObj = new btTheme();
$clockObj = new Clock($mysqli);
$btThemeObj->setThemeDir($THEME);
$btThemeObj->setClanName($CLAN_NAME);
$btThemeObj->initHead();
$breadcrumbObj = new BreadCrumb();

require_once(BASE_DIRECTORY."plugins/mods.php");