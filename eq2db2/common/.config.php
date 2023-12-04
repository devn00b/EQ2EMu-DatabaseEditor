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
	die("Hack attempt recorded.");

session_start();

ini_set("display_errors", "On");
ini_set("display_startup_errors", "On");

function EQ2EditorErrorHandler($errno, $errstr, $errfile, $errline) {
	if (!(error_reporting() & $errno) || ($errno & (E_ERROR | E_USER_ERROR))) {
        //This error code is not included in error_reporting, so let it fall
        // through to the standard PHP error handler
        return false;
    }

	global $eq2, $DebugGeneralStartupNotices, $DebugGeneralStartupWarnings;

	$errstr = htmlspecialchars($errstr);

	$errstr = $errstr . " File : " . $errfile . " Line : " . $errline;

	switch ($errno) {
	case E_WARNING:
	case E_USER_WARNING:
		if (isset($eq2)) {
			$eq2->AddDebugGeneral("Warning", $errstr);
		}
		else {
			if (!isset($DebugGeneralStartupWarnings)) $DebugGeneralStartupWarnings = array();
			$DebugGeneralStartupWarnings[] = $errstr;
		}
        break;
	case E_NOTICE:
    case E_USER_NOTICE:
		if (isset($eq2)) {
			$eq2->AddDebugGeneral("Warning", $errstr);
		}
		else {
			if (!isset($DebugGeneralStartupNotices)) $DebugGeneralStartupNotices = array();
			$DebugGeneralStartupNotices[sizeof($DebugGeneralStartupNotices)] = $errstr;
		}
        break;
	default:
		//Not sure what this is, just let the default handler take this one.
		return false;
	}

	return true;
}

set_error_handler("EQ2EditorErrorHandler");

// Define default database $GLOBALS - NEVER display this data in DEBUG unless user_role & 16  (admin)
$GLOBALS['database'][0]['id'] 		 					= 0;
$GLOBALS['database'][0]['db_display_name']	= 'EQ2Editor';
$GLOBALS['database'][0]['db_name'] 					= 'eq2editor';
$GLOBALS['database'][0]['db_host'] 					= 'localhost';
$GLOBALS['database'][0]['db_port'] 					= '3306';
$GLOBALS['database'][0]['db_user'] 					= 'root';
$GLOBALS['database'][0]['db_pass'] 					= '';
$GLOBALS['database'][0]['db_description']		= 'Required DB for EQ2Editor';
$GLOBALS['database'][0]['db_world_id']			= 0;
$GLOBALS['database'][0]['is_active']				= 0;

require_once("eq2Functions.class.php");
$eq2 = new eq2Functions;

// Fetch the rest of our dynamic site configs from the `eq2editor`.`config` table
$eq2->LoadConfig();
//$eq2->ForMe($GLOBALS['database']);

require_once('eq2FormBuilder.class.php'); // instantiate as needed: $eq2form = new EQ2FormBuilder()

?>
