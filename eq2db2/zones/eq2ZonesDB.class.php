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

class eq2ZonesDB
{

	public function __construct()
	{
		global $eq2;
		// Transfer instance of eq2db to local db member
		$this->db = $eq2->eq2db;
	}

	
	/*
		Function: GetLocationData()
		Purpose	: Fetchs location data for a given zone from the `locations` table
		Params	: N/a
		Syntax	: GetLocationData()
	*/	
	public function GetLocationData()
	{
		$sql = sprintf("SELECT * FROM %s.locations WHERE zone_id = %d;", $GLOBALS['db_name'], $_GET['id']);
		return $this->db->RunQueryMulti($sql);
	}
	
	
	/*
		Function: GetLocationData()
		Purpose	: Fetchs location data for a given zone from the `locations` table
		Params	: N/a
		Syntax	: GetLocationData()
	*/	
	public function GetLocationDetails()
	{
		$sql = sprintf("SELECT * FROM %s.location_details WHERE location_id = %d;", $GLOBALS['db_name'], $_GET['lid']);
		return $this->db->RunQueryMulti($sql);
	}
	
	
	/*
		Function: GetZoneList()
		Purpose	: Returns an array of all zones
		Params	: N/A
		Syntax	: GetZoneList()
	*/	
	public function GetRevivePointData()
	{
		$sql = sprintf("SELECT * FROM ".$GLOBALS['db_name'].".revive_points WHERE zone_id = %d;", $_GET['id']);
		return $this->db->RunQueryMulti($sql);
	}


	/*
		Function: GetZoneOptions()
		Purpose	: Builds a select forms <option> list with zone names for selection
		Params	: $pop: if 0, show all zones regardless of spawn population, else show only zones that have spawn population
		Syntax	: GetZoneOptions($pop)
		Notes		: Trying to make this one function as generic as possible, but it's getting a little complex - may need re-design
	*/	
	public function GetZoneOptions($pop = 1)
	{
		$sql 		= "SELECT id,name,description FROM ".$GLOBALS['db_name'].".zones";
		$where 	= ( $pop ) ? " WHERE id in (SELECT zone_id FROM ".$GLOBALS['db_name'].".spawn_location_placement)" : "";
	
		$sql = sprintf("%s %s ORDER BY description;", $sql, $where);
		
		$row = $this->db->RunQueryMulti($sql);
		
		foreach($row as $data) {
			$selected = ( $_GET['id'] == $data['id'] ) ? " selected" : "";
			$zone_options .= sprintf("<option value=\"?page=%s&type=%s%s%s&id=%d\"%s>%s (%s)</option>\n", 
				$_GET['page'], ( isset($_GET['type']) ) ? $_GET['type'] : "edit", ( isset($_GET['a']) ) ? "&a=1" : "", ( isset($_GET['tab']) ) ? "&tab=".$_GET['tab'] : "", $data['id'], $selected, $data['description'], $data['name']);
		}
		return $zone_options;
	}
	
	
	/*
		Function: GetZoneByName()
		Purpose	: Returns an array of all zones that match $var, or 'all'
		Params	: $var = partial text to search multiple fields
		Syntax	: GetZoneByName($var)
	*/	
	public function GetZoneByName($var)
	{
		if( $var != "all" )
		{
			$zone_name = $this->db->SQLEscape($var);
			$sql = "SELECT id, name, file, description, lua_script FROM ".$GLOBALS['db_name'].".zones WHERE (`name` RLIKE '$zone_name' OR `file` RLIKE '$zone_name' OR `description` RLIKE '$zone_name' OR `lua_script` RLIKE '$zone_name') ORDER BY description;";
		}
		else
		{
			$sql = "SELECT id, name, file, description, lua_script FROM ".$GLOBALS['db_name'].".zones ORDER BY description;";
		}
		return $this->db->RunQueryMulti($sql);
	}


	/*
		Function: GetZoneByID()
		Purpose	: Returns an array of zones ID
		Params	: N/A
		Syntax	: GetZoneByID()
	*/	
	public function GetZoneByID()
	{
		$sql = sprintf("SELECT * FROM ".$GLOBALS['db_name'].".zones WHERE id = %d;", $_GET['id']);
		return $this->db->RunQuerySingle($sql);
	}
	
	
	/*
		Function: GetZoneList()
		Purpose	: Returns an array of all zones
		Params	: N/A
		Syntax	: GetZoneList()
	*/	
	public function GetZoneList()
	{
		$sql = sprintf("SELECT id, name, file, description, lua_script FROM ".$GLOBALS['db_name'].".zones;");
		return $this->db->RunQueryMulti($sql);
	}


	/*
		Function: 
		Purpose	: 
		Params	: 
		Syntax	: 
	*/	
	public function GetZoneScriptInfo($id)
	{
		$sql = sprintf("SELECT id, name, description, lua_script FROM ".$GLOBALS['db_name'].".zones WHERE id = %lu", $id);
		return $this->db->RunQuerySingle($sql);
	}
	
	
	/*
		Function: 
		Purpose	: 
		Params	: 
		Syntax	: 
	*/	
	public function GetZoneScriptUsers($var)
	{
		if( $var )
		{
			$sql = sprintf("SELECT id, name FROM ".$GLOBALS['db_name'].".zones WHERE lua_script = '%s'", $var);
			$row = $this->db->RunQueryMulti($sql);
			foreach($row as $data) 
				printf('<a href="index.php?page=zones&type=edit&tab=script&id=%d">%s</a> (%lu)<br />', $data['id'], $data['name'], $data['id']);
		}
		else
			printf("None.");
	}

}

?>