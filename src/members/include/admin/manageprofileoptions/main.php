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

if (!isset($member)) {
	exit();
} else {
	$memberInfo = $member->get_info_filtered();
	$consoleObj->select($_GET['cID']);
	if (!$member->hasAccess($consoleObj)) {
		exit();
	}
}


echo "
<script type='text/javascript'>

$(document).ready(function() {
$('#breadCrumb').html(\"<a href='".$MAIN_ROOT."'>Home</a> > <a href='".$MAIN_ROOT."members'>My Account</a> > Manage Profile Options\");
});

</script>
";


if ($cID == "") {
	$cID = $consoleObj->findConsoleIDByName("Manage Profile Options");
}


$intAddProfileOptionsCID = $consoleObj->findConsoleIDByName("Add Profile Option");

$intManageProfileCatCID = $consoleObj->findConsoleIDByName("Manage Profile Categories");



$arrProfileCatIDs = [];
$result = $mysqli->query("SELECT * FROM ".$dbprefix."profilecategory ORDER BY ordernum DESC");
while ($row = $result->fetch_assoc()) {
	$arrProfileCatIDs[] = $row['profilecategory_id'];
}

foreach ($arrProfileCatIDs as $profileCatID) {
	$profileCatObj->select($profileCatID);
	$profileCatInfo = $profileCatObj->get_info_filtered();
	$catAssoc = $profileCatObj->getAssociateIDs("ORDER BY sortnum");

	$dispOptions .= "<tr><td class='dottedLine main' style='text-decoration: underline; padding-top: 5px; padding-bottom: 5px'><b>".$profileCatInfo['name']."</b></td><td colspan='4' class='dottedLine' align='center'><a href='".$MAIN_ROOT."members/console.php?cID=".$intManageProfileCatCID."&catID=".$profileCatInfo['profilecategory_id']."&action=edit'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/edit.png' width='24' height='24' title='Edit Profile Category'></a></tr>";
	$intHighestOrder = count($catAssoc);
	$counter = 0;
	$x = 1;
	foreach ($catAssoc as $profileID) {
		$profileOptionObj->select($profileID);
		$profileInfo = $profileOptionObj->get_info_filtered();

		if ($counter == 1) {
			$addCSS = " alternateBGColor";
			$counter = 0;
		} else {
			$addCSS = "";
			$counter = 1;
		}

		if ($x == 1) {
			$dispUpArrow = "<img src='".$MAIN_ROOT."images/transparent.png' width='24' height'24'>";
		} else {
			$dispUpArrow = "<a href='javascript:void(0)' onclick=\"moveOption('up', '".$profileInfo['profileoption_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/uparrow.png' width='24' height='24' title='Move Up'></a>";
		}

		if ($x == $intHighestOrder) {
			$dispDownArrow = "<img src='".$MAIN_ROOT."images/transparent.png' width='24' height'24'>";
		} else {
			$dispDownArrow = "<a href='javascript:void(0)' onclick=\"moveOption('down', '".$profileInfo['profileoption_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/downarrow.png' width='24' height='24' title='Move Down'></a>";
		}


		$dispOptions .= "
		<tr>
		<td class='dottedLine".$addCSS."' width=\"76%\">&nbsp;&nbsp;<span class='main'><b><a href='console.php?cID=".$cID."&oID=".$profileInfo['profileoption_id']."&action=edit'>".$profileInfo['name']."</a></b></td>
		<td align='center' class='dottedLine".$addCSS."' width=\"6%\">".$dispUpArrow."</td>
		<td align='center' class='dottedLine".$addCSS."' width=\"6%\">".$dispDownArrow."</td>
		<td align='center' class='dottedLine".$addCSS."' width=\"6%\"><a href='console.php?cID=".$cID."&oID=".$profileInfo['profileoption_id']."&action=edit'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/edit.png' width='24' height='24' title='Edit Profile Option'></a></td>
		<td align='center' class='dottedLine".$addCSS."' width=\"6%\"><a href='javascript:void(0)' onclick=\"deleteOption('".$profileInfo['profileoption_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' width='24' height='24' title='Delete Profile Option'></a></td>
		</tr>
		";
		$x++;
	}

	$dispOptions .= "<tr><td colspan='5' style='padding-top: 3px'></td></tr>";
}

echo "


<table class='formTable' style='border-spacing: 1px; margin-left: auto; margin-right: auto'>
<tr>
<td class='main' colspan='2' align='right'>
&raquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$intAddProfileOptionsCID."'>Add New Profile Option</a> &laquo;<br><br>
</td>
</tr>
<tr>
<td class='formTitle' width=\"76%\">Profile Option Name:</td>
<td class='formTitle' width=\"24%\">Actions:</td>
</tr>
</table>
<table class='formTable' style='border-spacing: 0px; margin-top: 0px; margin-left: auto; margin-right: auto'>
<tr><td colspan='5' class='dottedLine'></td></tr>
".$dispOptions."
</table>


";
