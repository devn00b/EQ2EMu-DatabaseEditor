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
define('IN_EDITOR', true);

//Send some headers to keep the user's browser from caching the response.
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header("Cache-Control: no-cache, must-revalidate" ); 
header("Pragma: no-cache" );
header("Content-Type: text/xml; charset=utf-8");

//Get our database abstraction file
require('../common/config.php');

if( isset($_COOKIE['eq2db']) )
{
	$eq2->userdata = $eq2->GetCookie();
	$eq2->user_role = intval($eq2->userdata['role']);
}

///Make sure that a value was sent.
if( isset($_GET['table']) && isset($_GET['field']) && isset($_GET['from']) && isset($_GET['to']) ) 
{
	$query = sprintf("UPDATE %s SET config_value = '%s' WHERE config_name = '%s' ", $_GET['table'], $_GET['to'], $_GET['field']); //print($query); exit;
	if( !$result = $eq2->eq2db->db->sql_query($query) )
	 	printf('<span style="color:red; font-weight:bold; font-size:15px;">FAILED!</span>: Could not write to the database<br />');	
	else
		print('<span style="color:green; font-weight:bold; font-size:15px;">Update Successful!</span><br />');
}
else
{
 	printf('<span style="color:red; font-weight:bold; font-size:15px;">Update FAILED!</span>: %s, %s, %s, %s<br />', $_GET['table'], $_GET['field'], $_GET['from'], $_GET['to']);	
}
?>
