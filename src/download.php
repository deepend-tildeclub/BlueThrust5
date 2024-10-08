<?php

/*
 * BlueThrust Clan Scripts
 * Copyright 2014
 *
 * Author: Bluethrust Web Development
 * E-mail: support@bluethrust.com
 * Website: http://www.bluethrust.com
 *
 * License: http://www.bluethrust.com/license.php
 *
 */


require_once("_setup.php");

require_once("classes/member.php");
require_once("classes/downloadcategory.php");
require_once("classes/download.php");


$ipbanObj = new Basic($mysqli, "ipban", "ipaddress");

if ($ipbanObj->select($IP_ADDRESS, false)) {
	$ipbanInfo = $ipbanObj->get_info();

	if (time() < $ipbanInfo['exptime'] or $ipbanInfo['exptime'] == 0) {
		die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."banned.php';</script>");
	} else {
		$ipbanObj->delete();
	}
}


$LOGGED_IN = false;
if (isset($_SESSION['btUsername']) and isset($_SESSION['btPassword'])) {
	$memberObj = new Member($mysqli);
	if ($memberObj->select($_SESSION['btUsername'])) {
		if ($memberObj->authorizeLogin($_SESSION['btPassword'])) {
			$LOGGED_IN = true;
		}
	}
}

$downloadCatObj = new DownloadCategory($mysqli);
$downloadObj = new Download($mysqli);
$blnShowDownload = false;

if ($downloadObj->select($_GET['dID'])) {
	$downloadInfo = $downloadObj->get_info_filtered();
	$downloadCatObj->select($downloadInfo['downloadcategory_id']);

	$accessType = $downloadCatObj->get_info("accesstype");


	if ($accessType == 1 && $LOGGED_IN) {
		$blnShowDownload = true;
	} elseif ($accessType == 0) {
		$blnShowDownload = true;
	}

	$fileContents1 = file_get_contents($downloadInfo['splitfile1']);
	$fileContents2 = file_get_contents($downloadInfo['splitfile2']);

	if ($blnShowDownload && $fileContents1 !== false && $fileContents2 !== false) {
		header("Content-Description: File Transfer");
		header("Content-Length: ".$downloadInfo['filesize'].";");
		header("Content-disposition: attachment; filename=".$downloadInfo['filename']);
		header("Content-type: ".$downloadInfo['mimetype']);

		echo $fileContents1.$fileContents2;
	}
}


if (!$blnShowDownload) {
	// Start Page
	$PAGE_NAME = "Download - ";
	$dispBreadCrumb = "";
	require_once($prevFolder."themes/".$THEME."/_header.php");

	echo "
		<div class='breadCrumbTitle'>Download</div>
		<div class='breadCrumb' style='padding-top: 0px; margin-top: 0px; margin-bottom: 20px'>
		<a href='".$MAIN_ROOT."'>Home</a> > Download
		</div>
		
		<div class='shadedBox main' style='text-align: center; margin: 20px auto; width: 50%'>
			<p>
			Unable to download file!
			</p>
		</div>
	";

	require_once($prevFolder."themes/".$THEME."/_footer.php");
}
