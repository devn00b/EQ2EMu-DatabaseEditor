<?php
/*  
    EQ2Editor:  Everquest II Database Editor v2.0
    Copyright (C) 2007 EQ2EMulator Development Team (http://www.eq2emulator.net or https://www.eq2emu.com)

    This file is part of EQ2Editor.

    EQ2Editor is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    EQ2Editor is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with EQ2Editor.  If not, see <http://www.gnu.org/licenses/>.
*/
if (!defined('IN_EDITOR'))
{
	die("Hack attempt recorded.");
}

require_once("common/config.php");
//$eq2->ForMe($_POST); // prints a foreach of the array passed


if (isset($_REQUEST['cmd']))
{
	switch($_REQUEST['cmd']) 
	{
		case "Login":
			if( !empty($_POST['lName']) && !empty($_POST['lPass']) )
				$eq2->LoginUser();
			// remember: when debugging logins, turn off this redirect!!!
			header("Location: index.php"); /* Redirect browser */
			break;

		case "Logout":
			unset($eq2->userdata);
			$eq2->DeleteCookie();
			// remember: when debugging logins, turn off this redirect!!!
			header("Location: index.php"); /* Redirect browser */
			break;
		
	}
}

if( isset($_COOKIE['eq2db']) )
{
	$eq2->userdata = $eq2->GetCookie();
	$eq2->user_role = intval($eq2->userdata['role']);
}

$eq2->PageTitle = $eq2->SetPageTitleData();
$eq2->PageLink = ( isset($_SERVER['QUERY_STRING']) ) ? $_SERVER['SCRIPT_NAME'] . "?" . $_SERVER['QUERY_STRING'] : $_SERVER['SCRIPT_NAME'];
$eq2->BackLink = preg_replace("/\&id=.*?$/", "", $eq2->PageLink);

$Page = ( isset($_GET['page']) ) ? $_GET['page'] : "home";

// Load a list of all known Roles for use in displaying memberships
$eq2->role_list = $eq2->eq2db->GetRoleList();

if( $GLOBALS['config']['debug'] )
{
	$eq2->AddDebugData("userdata", $eq2->userdata);
	$eq2->AddDebugData("config", $GLOBALS['config']);
	if( isset($eq2->user_role) && $eq2->user_role & 16 )
		$eq2->AddDebugData("database", $GLOBALS['database']);
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>
<?= $eq2->PageTitle ?>
</title>
<link type="text/css" rel="stylesheet" href="css/styles.css" />
<script src="js/eq2editor.js"></script>
<script src="js/ddtabmenu.js">
/***********************************************
* DD Tab Menu script- � Dynamic Drive DHTML code library (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
***********************************************/
</script>
<link rel="stylesheet" type="text/css" href="js/ddcolortabs.css" />

<!-- jQuery easyUI stuff -->
<link rel="stylesheet" type="text/css" href="jeasyui/themes/eq2emu/easyui.css" />  <!-- custom theme -->
<link rel="stylesheet" type="text/css" href="jeasyui/themes/icon.css" />  
<script type="text/javascript" src="jeasyui/jquery-1.8.0.min.js"></script>  
<script type="text/javascript" src="jeasyui/jquery.easyui.min.js"></script> 

</head>
<body>
<div id="site-container">
<div id="site-banner"><?php printf("%s %s", $GLOBALS['config']['app_name'], $GLOBALS['config']['app_version']); ?>
	<div id="db-picker">
		<?php
		if( is_array($eq2->userdata) )
		{
			$eq2->DBPicker();
			// On initial session, set current_database to the default datasource ID
			$GLOBALS['db_name'] = isset($_SESSION['current_database2']) ? $GLOBALS['database'][$_SESSION['current_database2']]['db_name'] : $GLOBALS['database'][$GLOBALS['config']['default_datasource_id']]['db_name'];
		}
  ?>
	</div>
	<div id="user-info">
		<?php
		if( is_array($eq2->userdata) )
		{
			printf("Logged in as: %s (%d messages)", $eq2->userdata['username'], 0);
		}
  ?>
	</div>
</div>
<?php 
if( is_array($eq2->userdata) )
{
	?>
<div id="TopMenu">
	<table cellspacing="0" id="TopMenu" border="0">
		<tr>
			<?php 
			// Always display Home tab
			printf('<td%s><a href="index.php?page=home">Home</a></td>', ( $Page == "home" || $Page == "help" ) || ( empty($_SERVER['QUERY_STRING']) ) ? ' class="tabOn"' : ' class="tabOff"');

			$empty_cell = 0;
			$generic_role = 31; // bitwise value of all GENERAL roles allowed to see every menu item

			if( M_CHARACTERS & $eq2->user_role || $generic_role & $eq2->user_role )
				printf('<td%s><a href="index.php?page=characters">Characters</a></td>', ( $Page == "characters" ) ? ' class="tabOn"' : ' class="tabOff"');
			else
				$empty_cell++;
				
			if( M_ITEMS & $eq2->user_role || $generic_role & $eq2->user_role )
				printf('<td%s><a href="index.php?page=items">Items</a></td>', ( $Page == "items" ) ? ' class="tabOn"' : ' class="tabOff"');
			else
				$empty_cell++;
				
			if( M_QUESTS & $eq2->user_role || $generic_role & $eq2->user_role )
				printf('<td%s><a href="index.php?page=quests">Quests</a></td>', ( $Page == "quests" ) ? ' class="tabOn"' : ' class="tabOff"');
			else
				$empty_cell++;
				
			if( M_SPELLS & $eq2->user_role || $generic_role & $eq2->user_role  )
				printf('<td%s><a href="index.php?page=spells">Spells</a></td>', ( $Page == "spells" ) ? ' class="tabOn"' : ' class="tabOff"');
			else
				$empty_cell++;
				
			if( M_SPAWNS & $eq2->user_role || $generic_role & $eq2->user_role )
				printf('<td%s><a href="index.php?page=spawns">Spawns</a></td>', ( $Page == "spawns" ) ? ' class="tabOn"' : ' class="tabOff"');
			else
				$empty_cell++;
				
			if( M_SCRIPTS & $eq2->user_role || $generic_role & $eq2->user_role )
				printf('<td%s><a href="index.php?page=scripts">Scripts</a></td>', ( $Page == "scripts" ) ? ' class="tabOn"' : ' class="tabOff"');
			else
				$empty_cell++;
				
			if( M_SERVER & $eq2->user_role || $generic_role & $eq2->user_role )
				printf('<td%s><a href="index.php?page=server">Server</a></td>', ( $Page == "server" ) ? ' class="tabOn"' : ' class="tabOff"');
			else
				$empty_cell++;
				
			if( M_ZONES & $eq2->user_role || $generic_role & $eq2->user_role )
				printf('<td%s><a href="index.php?page=zones">Zones</a></td>', ( $Page == "zones" ) ? ' class="tabOn"' : ' class="tabOff"');
			else
				$empty_cell++;
				
			if( M_ADMIN & $eq2->user_role || G_ADMIN & $eq2->user_role ) // this menu is for GM's or greater, not guides.
				printf('<td%s><a href="index.php?page=admin">Admin</a></td>', ( $Page == "admin" ) ? ' class="tabOn"' : ' class="tabOff"');
			else
				$empty_cell++;

			if( $empty_cell )
				for( $i = 0; $i < $empty_cell; $i++ )
					print('<td class="tabOff">&nbsp;</td>');
			?>
			<td class="tabOff"><a href="index.php?cmd=Logout">&nbsp;Logout</a>&nbsp;</td>
		</tr>
	</table>
</div>
<?php
}
else
{
	?>
<div id="login-box">
	<form action="index.php" method="post" name="Login">
	<table cellspacing="0" align="center">
			<tr>
				<td colspan="2" class="title">EQ2DB Login</td>
			</tr>
			<tr>
				<td class="label">Username:</td>
				<td><input type="text" name="lName" value="" class="text" /></td>
			</tr>
			<tr>
				<td class="label">Password:</td>
				<td><input type="password" name="lPass" value="" class="text" /></td>
			</tr>
			<tr>
				<td align="center" colspan="2"><input type="submit" name="cmd" value="Login" class="submit" /></td>
			</tr>
			<!--<tr>
				<td align="center" colspan="2">( <a href="index.php">Guest</a> )</td>
			</tr>-->
	</table>
	</form>
</div>
<?php
	include('footer.php');
	exit;
}
?>
<div id="main-body">