<?php
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
if (!defined('IN_EDITOR'))
{
	die("Hack attempt recorded.");
}

class eq2SpawnsDB
{

	public function __construct()
	{
		global $eq2;
		// Transfer instance of eq2db to local db member
		$this->db = $eq2->eq2db;
    }
    
    public function GetZones($pop = 1)
	{
		$sql 		= "SELECT id,name,description FROM ".$GLOBALS['db_name'].".zones";
		$where 	= ( $pop ) ? " WHERE id in (SELECT zone_id FROM ".$GLOBALS['db_name'].".spawn_location_placement)" : "";
	
		$sql = sprintf("%s %s ORDER BY description;", $sql, $where);
		
		$row = $this->db->RunQueryMulti($sql);
		
		$zone_options = "";
		
		foreach($row as $data) {
			$selected = ( isset($_GET['id']) && $_GET['id'] == $data['id'] ) ? " selected" : "";
			$zone_options .= sprintf("<option value=\"?page=%s&zone=%s\"%s>%s</option>\n", 
				$_GET['page'], $data['id'], $selected, $data['description'], $data['name'], $data['id']);
		}
		return $zone_options;
	}
	
}

?>