/*  
    EQ2Editor:  Everquest II Database Editor v2.0
    Copyright (C) 2007  EQ2EMulator Development Team (http://www.eq2emulator.net)

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
<?php
define('IN_EDITOR', true);

include_once("common/header.php");

$page_lc = $Page;
$page_uc = ucfirst($Page);
$type = isset($_GET['type']) ? $_GET['type'] : "";

switch($Page)
{

	case "characters":
		include_once("characters/eq2Characters.class.php");
		$Characters = new eq2Characters;
		$Characters->Start();
		break;

	case "items":
		include_once("items/eq2Items.class.php");
		$Items = new eq2Items;
		$Items->Start();
		break;
		
	case "quests":
		include_once("quests/eq2Quests.class.php");
		$Quests = new eq2Quests;
		$Quests->Start();
		break;
				
	case "scripts":
		include_once("scripts/eq2Scripts.class.php");
		$Scripts = new eq2Scripts;
		$Scripts->Start();
		break;
				
	case "spawns":
		include_once("spawns/eq2Spawns.class.php");
		$Spawns = new eq2Spawns;
		$Spawns->Start();
		break;
				
	case "spells":
		include_once("spells/eq2Spells.class.php");
		$Spells = new eq2Spells;
		$Spells->Start();
		break;

	case "server":
		include_once("server/eq2Server.class.php");
		$Server = new eq2Server;
		$Server->Start();
		break;

	case "zones":
		include_once("zones/eq2Zones.class.php");
		$Zones = new eq2Zones;
		$Zones->Start();
		break;

	case "admin":
		include_once("_admin/eq2Admin.class.php");
		$Admin = new eq2Admin;
		$Admin->Start();
		break;
	
	case "help":
		$eq2->DisplaySiteText($page_lc, $_GET['cat']);
		break;
		
	default:
		include_once("site/eq2Site.class.php");
		$Site = new eq2Site;
		$Site->Start(); 
		break;
}
		
include_once("common/footer.php");
?>