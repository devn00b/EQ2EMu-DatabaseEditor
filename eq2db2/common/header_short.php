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
//ForMe($GLOBALS); // prints a foreach of the array passed

// temp static value for dev, but should be replaced by DB Picker eventually
//$GLOBALS['db_name'] = "eq2world";

$eq2 = new eq2Functions(); // instantiate the $eq2 class

if( isset($_COOKIE['eq2db']) )
	$userdata = $eq2->GetCookie();

$PageTitle = sprintf("%s %s", $GLOBALS['config']['app_name'], $GLOBALS['config']['app_version']);

if( is_array($eq2->userdata) )
{
	$GLOBALS['db_name'] = isset($_SESSION['current_database2']) ? $GLOBALS['database'][$_SESSION['current_database2']]['db_name'] : $GLOBALS['database'][$GLOBALS['config']['default_datasource_id']]['db_name'];
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php print($PageTitle) ?></title>
<link type="text/css" rel="stylesheet" href="css/styles.css" />
<script type="text/javascript">
<!--
function dosub(subm) 
{ 
	if (subm != "") 
	{ 
		self.location=subm; 
	} 
}

function insert_prescript(qName)
{
	opener.document.forms['scriptheader'].prescript.value = qName;
	self.close();
}
function insert_postscript(qName)
{
	opener.document.forms['scriptheader'].postscript.value = qName;
	self.close();
}
function insert_startby(starter)
{
	opener.document.forms['scriptheader'].queststarter.value = starter;
	self.close();
}
//-->
</script>
</head>

<body>
<div id="site-container">
<div id="main-body">
