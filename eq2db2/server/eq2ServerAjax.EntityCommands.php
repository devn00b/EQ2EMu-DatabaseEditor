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

/*
//Send some headers to keep the user's browser from caching the response.
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header("Cache-Control: no-cache, must-revalidate" ); 
header("Pragma: no-cache" );
header("Content-Type: text/xml; charset=utf-8");
*/
//Get our database abstraction file
require('../common/config.php');
$GLOBALS['db_name'] = isset($_SESSION['current_database']) ? $GLOBALS['database'][$_SESSION['current_database']]['db_name'] : $GLOBALS['database'][$GLOBALS['config']['default_datasource_id']]['db_name'];

if( (isset($_REQUEST['search']) && $_REQUEST['search'] != '') && (isset($_REQUEST['pageOffset']) && $_REQUEST['pageOffset'] != '') )
{
	$offset = $_REQUEST['pageOffset'] * 100;
	if ($_REQUEST['search'] != "all")
	{
		$name = $eq2->eq2db->SQLEscape($_REQUEST['search']);
		$sql = sprintf("SELECT * FROM " . $GLOBALS['db_name'] . ".entity_commands WHERE command_text RLIKE '%s' ORDER BY id LIMIT " . $offset . ",100;", $name);
	}
	else
	{
		$sql = "SELECT * FROM " . $GLOBALS['db_name'] . ".entity_commands ORDER BY id LIMIT " . $offset . ",100;";
	}
	
	$results = $eq2->eq2db->RunQueryMulti($sql);
	if( is_array($results) )
	{
		foreach($results as $data)
		{
			echo '<tr class="form">';
				$eq2->DrawInputTextBox($data, "id", "small", 1);
				$eq2->DrawInputTextBox($data, "command_list_id", "small", 0);
				$eq2->DrawInputTextBox($data, "command_text", "large", 0);
				$eq2->DrawInputTextBox($data, "distance", "small", 0);
				$eq2->DrawInputTextBox($data, "command", "large", 0);
				$eq2->DrawInputTextBox($data, "error_text", "longtext", 0);
				$eq2->DrawInputTextBox($data, "cast_time", "medium", 0);
				$eq2->DrawInputTextBox($data, "spell_visual", "medium", 0);
				
				echo '<td nowrap="nowrap">';
					echo '<input type="submit" name="cmd" value="Update" class="Submit" onclick="submitForm(this);" />';
					echo '<input type="submit" name="cmd" value="Delete" class="Submit" onclick="submitForm(this);" />';
					echo '<input type="hidden" name="table" value="entity_commands" />';
					echo '<input type="hidden" name="object" value="Edit Entity Command" />';
				echo '</td>';
			echo '</tr>';
		}
	}
}
?>