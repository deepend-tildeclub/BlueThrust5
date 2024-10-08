<?php

if (!defined("MAIN_ROOT")) {
	exit();
}

$usernameMessage = "Not Logged In! - <a href='".MAIN_ROOT."login.php'>Log In</a> to have your account connected to this donation!";
$extraNameTooltip = "If left blank, the donation will be from Anonymous.";
if (LOGGED_IN) {
	$member->select($_SESSION['btUsername']);
	$usernameMessage = $member->getMemberLink();
	$extraNameTooltip = "";
}





$i = 0;
$arrComponents = [
		"username" => [
			"type" => "custom",
			"sortorder" => $i++,
			"html" => "<div class='formInput'>".$usernameMessage."</div>",
			"display_name" => "Account Name"
		]
	];


if ($campaignInfo['allowname'] == 1) {
	$arrComponents['name'] = [
		"type" => "text",
		"sortorder" => $i++,
		"attributes" => ["class" => "formInput textBox bigTextBox"],
		"display_name" => "Your Name",
		"tooltip" => "This field is optional.".$extraNameTooltip
	];
}



if ($campaignInfo['allowmessage'] == 1) {
	$arrComponents['message'] = [
		"type" => "textarea",
		"sortorder" => $i++,
		"attributes" => ["class" => "formInput textBox bigTextBox", "rows" => "5"],
		"display_name" => "Message",
		"tooltip" => "Max 140 characters."
	];
}


$arrComponents['amount'] = [
		"type" => "text",
		"sortorder" => $i++,
		"attributes" => ["class" => "formInput textBox smallTextBox"],
		"display_name" => "Donation Amount",
		"value" => $campaignInfo['minimumamount'],
		"html" => "<div class='formInput formInputSideText'>".$campaignInfo['currency']."</div>"
	];


if ($campaignInfo['allowhiddenamount'] == 1) {
	$arrComponents['hideamount'] = [
		"type" => "checkbox",
		"sortorder" => $i++,
		"attributes" => ["class" => "formInput"],
		"display_name" => "Hide Amount",
		"tooltip" => "If you check this box, your donation amount will be hidden on the donation profile page.",
		"value" => 1
	];
}

$arrComponents['submit'] = [
		"type" => "submit",
		"value" => "Continue",
		"attributes" => ["class" => "submitButton formSubmitButton"],
		"sortorder" => $i++
	];


if (isset($_GET['fail']) && $_GET['fail'] == "amount") {
	$arrComponents['show_fail'] = [

		"type" => "custom",
		"sortorder" => $i++,
		"html" => "<p align='center' class='main failedFont'><b>The minimum donation amount is ".$campaignObj->formatAmount($campaignInfo['minimumamount'])."</b></p>"
	];
}


$setupFormArgs = [
		"name" => "donate_form-".$_GET['campaign_id'],
		"components" => $arrComponents,
		"description" => "Use the form below to make a donation!",
		"attributes" => ["action" => MAIN_ROOT."plugins/donations/paypal-redirect.php?campaign_id=".$_GET['campaign_id'], "method" => "post"]
	];

$hooksObj->run("donate_form-".$_GET['campaign_id']);

$donationForm->buildForm($setupFormArgs);
