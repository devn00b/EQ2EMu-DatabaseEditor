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

class eq2ScriptsDB
{

	public function __construct()
	{
		global $eq2;
		// Transfer instance of eq2db to local db member
		$this->db = $eq2->eq2db;
	}

	
	/*
		Function: 
		Purpose	: 
		Params	: 
	*/	
	public function GetLuaScriptPath($table, $id)
	{
		global $eq2;
		
		if( empty($table) || empty($id) )
		{
			$eq2->AddStatus("Table or ID not set in GetLuaScriptPath().");
			return;
		}
		$index_field = ( $table == "quests" ) ? "quest_id" : "id";
		$sql = sprintf("SELECT lua_script FROM ".$GLOBALS['db_name'].".%s WHERE %s = %lu;", $table, $index_field, $id);
		$row = $this->db->RunQuerySingle($sql);
		return ( !empty($row['lua_script']) ) ? $row['lua_script'] : "";
	}


	/*
		Function: GetQuestScriptsbyName()
		Purpose	: Searches multiple fields in the `quests` table for $var
		Params	: $var = $_POST['searchText']
	*/	
	public function GetQuestScriptsByName($var)
	{
		$quest_name = $this->db->SQLEscape($var);
		$sql = sprintf("SELECT * FROM ".$GLOBALS['db_name'].".quests WHERE `name` RLIKE '$quest_name' OR `type` RLIKE '$quest_name' OR `zone` RLIKE '$quest_name' OR `description` RLIKE '$quest_name' OR `lua_script` RLIKE '$quest_name';");
		return $this->db->RunQueryMulti($sql);
	}
	
	
	/*
		Function: GetQuestScriptsbyZone()
		Purpose	: Passes zones.id to GetZoneNameByID($id) to find lua_scripts containing the zones.name value
		Params	: $id = zones.id
	*/	
	public function GetQuestScriptsByZone($id)
	{
		$sql = sprintf("SELECT * FROM ".$GLOBALS['db_name'].".quests WHERE lua_script RLIKE '%s';", $this->db->GetZoneNameByID($id));
		return $this->db->RunQueryMulti($sql);
	}
	
	
	public function GetQuestScriptInfo($id)
	{
		$sql = sprintf("SELECT * FROM ".$GLOBALS['db_name'].".quests WHERE quest_id = %lu", $id);
		return $this->db->RunQuerySingle($sql);
	}
	
	
	/*
		Function: GetSpawnScriptsbyName()
		Purpose	: Performs 3 UNIONs across the 3 columns in spawn_scripts where scripts can be assigned
						: spawn_id, spawn_location_id and spawnentry_id
		Params	: $var is the name of the spawn in the spawn_script being compared
		Syntax	: GetSpawnScriptsbyName($var)
	*/	
	public function GetSpawnScriptsByName($var)
	{
		$spawn_name = $this->db->SQLEscape($var);
		$sql = "SELECT s2.id as sid, s2.name, s2.model_type, s4.min_level, s4.max_level, s4.enc_level, s4.gender, s1.* FROM ".$GLOBALS['db_name'].".spawn_scripts s1 
						JOIN ".$GLOBALS['db_name'].".spawn s2 ON s1.spawn_id = s2.id
						JOIN ".$GLOBALS['db_name'].".spawn_npcs s4 ON s2.id = s4.spawn_id
						WHERE s2.name RLIKE '$spawn_name' OR s1.lua_script RLIKE '$spawn_name'
						UNION
						SELECT s2.id as sid, s2.name, s2.model_type, s4.min_level, s4.max_level, s4.enc_level, s4.gender, s1.* FROM ".$GLOBALS['db_name'].".spawn_scripts s1 
						JOIN ".$GLOBALS['db_name'].".spawn_location_entry s3 ON s1.spawnentry_id = s3.id
						JOIN ".$GLOBALS['db_name'].".spawn s2 ON s3.spawn_id = s2.id
						JOIN ".$GLOBALS['db_name'].".spawn_npcs s4 ON s2.id = s4.spawn_id
						WHERE s2.name RLIKE '$spawn_name' OR s1.lua_script RLIKE '$spawn_name'
						UNION
						SELECT s2.id as sid, s2.name, s2.model_type, s4.min_level, s4.max_level, s4.enc_level, s4.gender, s1.* FROM ".$GLOBALS['db_name'].".spawn_scripts s1 
						JOIN ".$GLOBALS['db_name'].".spawn_location_entry s3 ON s1.spawn_location_id = s3.spawn_location_id
						JOIN ".$GLOBALS['db_name'].".spawn s2 ON s3.spawn_id = s2.id
						JOIN ".$GLOBALS['db_name'].".spawn_npcs s4 ON s2.id = s4.spawn_id
						WHERE s2.name RLIKE '$spawn_name' OR s1.lua_script RLIKE '$spawn_name';";
		return $this->db->RunQueryMulti($sql);
	}
	
	
	/*
		Function: GetSpawnScriptsbyZone()
		Purpose	: Performs 3 UNIONs across the 3 columns in spawn_scripts where scripts can be assigned
						: spawn_id, spawn_location_id and spawnentry_id
		Params	: $id is the zone ID (translated to zones.name) of the script being compared
		Syntax	: GetSpawnScriptsbyZone($id)
	*/	
	public function GetSpawnScriptsByZone($id)
	{
		$zone_id = $this->db->GetZoneNameByID($id);
		$sql = "SELECT DISTINCT s3.id as sid, s3.name, s3.model_type, s1.* FROM ".$GLOBALS['db_name'].".spawn_scripts s1 
						JOIN ".$GLOBALS['db_name'].".spawn s3 ON s1.spawn_id = s3.id
						JOIN ".$GLOBALS['db_name'].".spawn_location_entry s2 ON s3.id = s2.spawn_id
						JOIN ".$GLOBALS['db_name'].".spawn_location_placement s5 ON s2.spawn_location_id = s5.spawn_location_id
						WHERE s5.zone_id = '$id'
						UNION
						SELECT DISTINCT s3.id as sid, s3.name, s3.model_type, s1.* FROM ".$GLOBALS['db_name'].".spawn_scripts s1 
						JOIN ".$GLOBALS['db_name'].".spawn_location_entry s2 ON s1.spawnentry_id = s2.id
						JOIN ".$GLOBALS['db_name'].".spawn s3 ON s2.spawn_id = s3.id
						JOIN ".$GLOBALS['db_name'].".spawn_location_placement s5 ON s2.spawn_location_id = s5.spawn_location_id
						WHERE s5.zone_id = '$id'
						UNION
						SELECT DISTINCT s3.id as sid, s3.name, s3.model_type, s1.* FROM ".$GLOBALS['db_name'].".spawn_scripts s1 
						JOIN ".$GLOBALS['db_name'].".spawn_location_entry s2 ON s1.spawn_location_id = s2.spawn_location_id
						JOIN ".$GLOBALS['db_name'].".spawn s3 ON s2.spawn_id = s3.id
						JOIN ".$GLOBALS['db_name'].".spawn_location_placement s5 ON s2.spawn_location_id = s5.spawn_location_id
						WHERE s5.zone_id = '$id'";
		return $this->db->RunQueryMulti($sql);
		
		/* Original sql statement
		 * $sql = "SELECT DISTINCT s3.id as sid, s3.name, s3.model_type, s4.min_level, s4.max_level, s4.enc_level, s1.* FROM ".$GLOBALS['db_name'].".spawn_scripts s1 
						JOIN ".$GLOBALS['db_name'].".spawn s3 ON s1.spawn_id = s3.id
						JOIN ".$GLOBALS['db_name'].".spawn_npcs s4 ON s3.id = s4.spawn_id
						JOIN ".$GLOBALS['db_name'].".spawn_location_entry s2 ON s3.id = s2.spawn_id
						JOIN ".$GLOBALS['db_name'].".spawn_location_placement s5 ON s2.spawn_location_id = s5.spawn_location_id
						WHERE s5.zone_id = '$id'
						UNION
						SELECT DISTINCT s3.id as sid, s3.name, s3.model_type, s4.min_level, s4.max_level, s4.enc_level, s1.* FROM ".$GLOBALS['db_name'].".spawn_scripts s1 
						JOIN ".$GLOBALS['db_name'].".spawn_location_entry s2 ON s1.spawnentry_id = s2.id
						JOIN ".$GLOBALS['db_name'].".spawn s3 ON s2.spawn_id = s3.id
						JOIN ".$GLOBALS['db_name'].".spawn_npcs s4 ON s3.id = s4.spawn_id
						JOIN ".$GLOBALS['db_name'].".spawn_location_placement s5 ON s2.spawn_location_id = s5.spawn_location_id
						WHERE s5.zone_id = '$id'
						UNION
						SELECT DISTINCT s3.id as sid, s3.name, s3.model_type, s4.min_level, s4.max_level, s4.enc_level, s1.* FROM ".$GLOBALS['db_name'].".spawn_scripts s1 
						JOIN ".$GLOBALS['db_name'].".spawn_location_entry s2 ON s1.spawn_location_id = s2.spawn_location_id
						JOIN ".$GLOBALS['db_name'].".spawn s3 ON s2.spawn_id = s3.id
						JOIN ".$GLOBALS['db_name'].".spawn_npcs s4 ON s3.id = s4.spawn_id
						JOIN ".$GLOBALS['db_name'].".spawn_location_placement s5 ON s2.spawn_location_id = s5.spawn_location_id
						WHERE s5.zone_id = '$id'";
		 */
	}

	
	/*
		Function: 
		Purpose	: 
		Params	: 
	*/	
	public function GetSpawnScriptInfo($id)
	{
		$sql = sprintf("SELECT * FROM ".$GLOBALS['db_name'].".spawn_scripts WHERE id = %lu", $id);
		return $this->db->RunQuerySingle($sql);
	}
	
	
	/*
		Function: 
		Purpose	: 
		Params	: 
	*/	
	public function GetSpawnScriptUsers($id)
	{
		$sql = sprintf("SELECT lua_script FROM ".$GLOBALS['db_name'].".spawn_scripts WHERE id = %lu", $id);
		$row = $this->db->RunQuerySingle($sql);
		$lua = $row['lua_script'];
		
		$sql = "SELECT DISTINCT s3.id AS spawn_id, s3.name, s1.id, s5.zone_id FROM ".$GLOBALS['db_name'].".spawn_scripts s1 
						JOIN ".$GLOBALS['db_name'].".spawn s3 ON s1.spawn_id = s3.id
						JOIN ".$GLOBALS['db_name'].".spawn_npcs s4 ON s3.id = s4.spawn_id
						JOIN ".$GLOBALS['db_name'].".spawn_location_entry s2 ON s3.id = s2.spawn_id
						JOIN ".$GLOBALS['db_name'].".spawn_location_placement s5 ON s2.spawn_location_id = s5.spawn_location_id
						WHERE s1.lua_script = '$lua'
						UNION
						SELECT DISTINCT s3.id AS spawn_id, s3.name, s1.id, s5.zone_id FROM ".$GLOBALS['db_name'].".spawn_scripts s1 
						JOIN ".$GLOBALS['db_name'].".spawn_location_entry s2 ON s1.spawnentry_id = s2.id
						JOIN ".$GLOBALS['db_name'].".spawn s3 ON s2.spawn_id = s3.id
						JOIN ".$GLOBALS['db_name'].".spawn_npcs s4 ON s3.id = s4.spawn_id
						JOIN ".$GLOBALS['db_name'].".spawn_location_placement s5 ON s2.spawn_location_id = s5.spawn_location_id
						WHERE s1.lua_script = '$lua'
						UNION
						SELECT DISTINCT s3.id AS spawn_id, s3.name, s1.id, s5.zone_id FROM ".$GLOBALS['db_name'].".spawn_scripts s1 
						JOIN ".$GLOBALS['db_name'].".spawn_location_entry s2 ON s1.spawn_location_id = s2.spawn_location_id
						JOIN ".$GLOBALS['db_name'].".spawn s3 ON s2.spawn_id = s3.id
						JOIN ".$GLOBALS['db_name'].".spawn_npcs s4 ON s3.id = s4.spawn_id
						JOIN ".$GLOBALS['db_name'].".spawn_location_placement s5 ON s2.spawn_location_id = s5.spawn_location_id
						WHERE s1.lua_script = '$lua'";
		$row = $this->db->RunQueryMulti($sql);
		foreach($row as $data) 
			printf('<a href="?page=scripts&type=spawn&zone=%d&id=%lu">%s</a> (%lu)<br />', $data['zone_id'], $data['id'], $data['name'], $data['spawn_id']);
	}


	/*
		Function: GetZoneIDFromLUAScript()
		Purpose	: Parses /zonename/ from $type/zonename/lua in a tables lua_script field
		Params	: $type is Quests, SpawnScripts, ZoneScripts
		Syntax	: GetZoneIDFromLUAScript($type, $lua)
	*/	
	public function GetZoneIDFromLUAScript($type, $lua)
	{
		$zone = preg_replace("/$type\/(.*?)\/.*?$/i", "$1", $lua);
		return $this->db->GetZoneIDByName($zone);
	}
	

	/*
		Function: ZonesWithScriptsOptions()
		Purpose	: Builds a select forms <option> list with zone names for selection
		Params	: $table: if blank, build a full list of zones in game, else only (quests, spawn_scripts, zones) whose `lua_script` field is checked
						: $pop: if 0, show all zones regardless of spawn population, else show only zones that have spawn population
		Syntax	: ZonesWithScriptsOptions($table, $pop)
		Notes		: Trying to make this one function as generic as possible, but it's getting a little complex - may need re-design
	*/	
	public function GetZoneOptions($table = '', $pop = 0)
	{
		$sql 		= "SELECT id,name,description FROM ".$GLOBALS['db_name'].".zones";
		$where 	= ( $pop ) ? "id in (SELECT zone_id FROM ".$GLOBALS['db_name'].".spawn_location_placement)" : "";
		
		if( !empty($table) && $table != "zones" )
		{
			$sql .= ( strlen($where) > 0 ) ? sprintf(" WHERE %s AND name IN (%s)", $where, $this->ParseZoneListFromLua($table)) : sprintf(" WHERE name IN (%s)", $this->ParseZoneListFromLua($table));
		}
		else if( $table == "zones" )
		{
			$sql .= ( strlen($where) > 0 ) ? sprintf(" WHERE %s AND LENGTH(lua_script) > 0", $where) : sprintf(" WHERE LENGTH(lua_script) > 0");
		}
		else
		{
			$sql .= ( strlen($where) > 0 ) ? sprintf(" WHERE %s", $where) : "";
		}
		$sql .= " ORDER BY description;";
		
		$row = $this->db->RunQueryMulti($sql);
		
		$zone_options = NULL;
		foreach($row as $data) {
			$selected = ( isset($_GET['zone']) && $_GET['zone'] == $data['id'] ) ? " selected" : "";
			$zone_options .= sprintf("<option value=\"?page=%s&type=%s&zone=%d\"%s>%s (%s)</option>\n", 
				$_GET['page'], $_GET['type'], $data['id'], $selected, $data['description'], $data['name']);
		}
		return $zone_options;
	}
	
	
	/*
		Function: 
		Purpose	: 
		Params	: 
	*/	
	public function GetZoneScriptsByName($var)
	{
		$zone_name = $this->db->SQLEscape($var);
		$sql = sprintf("SELECT id, name, description, lua_script FROM ".$GLOBALS['db_name'].".zones WHERE (`name` RLIKE '$zone_name' OR `file` RLIKE '$zone_name' OR `description` RLIKE '$zone_name' OR `lua_script` RLIKE '$zone_name') AND LENGTH(lua_script) > 0;");
		return $this->db->RunQueryMulti($sql);
	}
	
	
	/*
		Function: 
		Purpose	: 
		Params	: 
	*/	
	public function GetZoneScriptsByZone($id)
	{
		$sql = sprintf("SELECT id, name, description, lua_script FROM ".$GLOBALS['db_name'].".zones WHERE id = %d AND LENGTH(lua_script) > 0;", $id);
		return $this->db->RunQueryMulti($sql);
	}
		
	
	/*
		Function: ParseZoneListFromLua()
		Purpose	: Reads lua_script fields in $type table to get a list of active scripted zones
		Params	: Quest, Spawn or Zone (scripts)
		Syntax	: ParseZoneListFromLua($type)
	*/	
	private function ParseZoneListFromLua($table)
	{
		switch($table)
		{
			case "quests":
				// parse /zonename/ from Quests/*/lua
				$sql = "SELECT DISTINCT SUBSTR(SUBSTRING_INDEX(lua_script, '/', 2), 8) AS zone_name FROM ".$GLOBALS['db_name'].".quests;";
				break;
				
			case "spawn_scripts":
				// parse /zonename/ from SpawnScripts/*/lua
				$sql = "SELECT DISTINCT SUBSTR(SUBSTRING_INDEX(lua_script, '/', 2), 14) AS zone_name FROM ".$GLOBALS['db_name'].".spawn_scripts;";
				break;

			case "zones":
				// zones are not parsed this way, get outta here!
				return;
				break;
		}
		$row = $this->db->RunQueryMulti($sql);
		
		$zone_names = NULL;
		if( is_array($row) )
			foreach($row as $data) 
				$zone_names .= ( isset($zone_names) ) ? sprintf(", '%s'", $data['zone_name']) : sprintf("'%s'", $data['zone_name']);
		return ( !empty($zone_names) ) ? $zone_names : "''";
	}
	
	
}
?>