<?php
/*
	This is the back-end PHP file for the AJAX Suggest Tutorial
	
	You may use this code in your own projects as long as this 
	copyright is left	in place.  All code is provided AS-IS.
	This code is distributed in the hope that it will be useful,
 	but WITHOUT ANY WARRANTY; without even the implied warranty of
 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
	
	For the rest of the code visit http://www.DynamicAJAX.com
	
	Copyright 2006 Ryan Smith / 345 Technical / 345 Group.	
*/
define('IN_EDITOR', true);

// hack
$GLOBALS['db_name'] = isset($_SESSION['current_database']) ? $_SESSION['current_database'] : "eq2dev";

//Send some headers to keep the user's browser from caching the response.
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header("Cache-Control: no-cache, must-revalidate" ); 
header("Pragma: no-cache" );
header("Content-Type: text/xml; charset=utf-8");

//Get our database abstraction file
require('config.php');
///Make sure that a value was sent.
if( (isset($_GET['search']) && $_GET['search'] != '') && (isset($_GET['type']) && $_GET['type'] != '') ) 
{
	$search = addslashes($_GET['search']);

	switch($_GET['type'])
	{
		case "zone":
			$sql = "SELECT distinct(description) as search_text FROM ".$GLOBALS['db_name'].".zones WHERE description like '".$search."%' ORDER BY description";
			break;
			
		case "quest":
			$sql = "SELECT DISTINCT name as search_text FROM ".$GLOBALS['db_name'].".quests WHERE `name` LIKE '".$search."%' OR `type` LIKE '".$search."%' OR `zone` LIKE '".$search."%' OR `description` LIKE '".$search."%' OR `lua_script` LIKE '".$search."%' ORDER BY name";
			break;

		case "user":
			$sql = "SELECT distinct(username) as search_text FROM users WHERE username like '".$search."%' ORDER BY username";
			break;
			
		default:
			break;
	}

	$sql = $sql . " LIMIT 0,10";
	
	$results = $eq2->eq2db->RunQueryMulti($sql);
	if( is_array($results) )
	{
		foreach($results as $data)
		{
			//Return each page title seperated by a newline.
			echo $data['search_text'] . "\n";
		}
	}
	else
	{
		echo "No matches.\n";
	}
}
?>