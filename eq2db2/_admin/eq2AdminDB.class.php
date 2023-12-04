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

class eq2AdminDB
{

	public function __construct()
	{
		global $eq2;
		// Transfer instance of eq2db to local db member
		$this->db = $eq2->eq2db;
	}

	public function GetDatasources()
	{
		$sql = "SELECT * FROM eq2editor.datasources;";
		return $this->db->RunQueryMulti($sql);
	}
	
	public function GetLast10Players()
	{
		$sql = "SELECT id, account_id, name, class, level, tradeskill_level, current_zone_id, last_played, admin_status FROM 
						".$GLOBALS['db_name'].".characters 
						ORDER BY last_played desc 
						LIMIT 0, 10;";
		return $this->db->RunQueryMulti($sql);
	}

	public function GetMessages($id)
	{
		$sql = sprintf("SELECT * FROM messages WHERE to_user_id = %lu ORDER BY message_date DESC;", $id);
		return $this->db->RunQueryMulti($sql);
	}


	public function GetMostActiveQuests()
	{
		$sql = "SELECT quests.quest_id, quests.lua_script, count(character_quests.quest_id) num_completed
						FROM ".$GLOBALS['db_name'].".quests, ".$GLOBALS['db_name'].".character_quests 
						WHERE quests.quest_id = character_quests.quest_id AND completed_date IS NOT NULL
						GROUP BY character_quests.quest_id
						ORDER BY num_completed desc LIMIT 0, 10;";
		return $this->db->RunQueryMulti($sql);
	}


	public function GetMostExperiencedPlayers()
	{
		$sql = "SELECT name, class, level, tradeskill_level, count(quest_id) as quests, admin_status 
						FROM ".$GLOBALS['db_name'].".characters, ".$GLOBALS['db_name'].".character_quests 
						WHERE characters.id = character_quests.char_id AND admin_status = 0
						GROUP BY characters.id
						ORDER BY level desc LIMIT 0, 10;";
		return $this->db->RunQueryMulti($sql);
	}


	public function GetUserByName($var)
	{
		if( $var != "all" )
		{
			$user_name = $this->db->SQLEscape($var);
			$sql = sprintf("SELECT id, username, role FROM users WHERE username RLIKE '%s';", $user_name);
		}
		else
		{
			//$sql = "SELECT id, username, role FROM users WHERE inactive = 0 ORDER BY username;";
			// trying user list including inactive
			$sql = "SELECT id, username, role FROM users WHERE  is_active = 1 ORDER BY username;";
		}
		return $this->db->RunQueryMulti($sql);
	}

	
	public function GetRoles()
	{
		$sql = "SELECT * FROM roles ORDER BY role_value;";
		return $this->db->RunQueryMulti($sql);
	}


	public function GetSessionData()
	{
		// Datasource for this query should always be the Dev server, because only dev characters should be logged into the Editor.
		$sql = "SELECT c.name, c.admin_status, session_time, session_page FROM sessions s, ".$GLOBALS['config']['dev_datasource'].".characters c WHERE s.session_user_id = c.id ORDER BY c.name;";
		return $this->db->RunQueryMulti($sql);
	}


	public function GetTableData()
	{
		$sql = "SELECT DISTINCT TABLE_NAME FROM information_schema.tables WHERE TABLE_SCHEMA = '".$GLOBALS['db_name']."' AND TABLE_ROWS > 0;";
		$table_data = $this->db->RunQueryMulti($sql);
		$server_stats = array();
		foreach($table_data as $data)
		{
			$server_stats[$data['TABLE_NAME']] = $this->db->GetTotalRows($data['TABLE_NAME']);
		}
		
		return $server_stats;
	}


	public function GetTotalAccounts()
	{
		$data = $this->db->RunQuerySingle("SELECT COUNT(DISTINCT account_id) AS unique_accounts FROM ".$GLOBALS['db_name'].".characters");
		return ( !empty($data) ) ? $data['unique_accounts'] : 0;
	}


	public function GetTotalCharacters()
	{
		$data = $this->db->RunQuerySingle("SELECT COUNT(id) AS char_count FROM ".$GLOBALS['db_name'].".characters;");
		return ( !empty($data) ) ? $data['char_count'] : 0;
	}


	public function GetAverageLevel($char_count)
	{
		$data = $this->db->RunQuerySingle("SELECT SUM(level) AS average_levels FROM ".$GLOBALS['db_name'].".characters;");
		return ( !empty($data) ) ? $data['average_levels'] / $char_count : 0;
	}


	public function GetUserInfo()
	{
		$sql = sprintf("SELECT * FROM users WHERE id = %lu;", $_GET['id']);
		return $this->db->RunQuerySingle($sql);
	}


	public function GetUserNameByID($id)
	{
		$sql = sprintf("SELECT username FROM users WHERE id = %lu;", $id);
		return $this->db->RunQuerySingle($sql);
	}


	public function GetUserOptions()
	{
		$sql 		= "SELECT id,username,role FROM users ORDER BY username;";
		$row = $this->db->RunQueryMulti($sql);
		
		foreach($row as $data) {
			$selected = ( $_GET['id'] == $data['id'] ) ? " selected" : "";
			$user_options .= sprintf("<option value=\"?page=%s&type=%s&id=%d\"%s>%s (%s)</option>\n", 
				$_GET['page'], $_GET['type'], $data['id'], $selected, $data['username'], $data['id']);
		}
		return $user_options;
	}
	
	
	public function GetZonePopulationData()
	{
		$sql = "SELECT DISTINCT z.id as zid, z.description, z.name
						FROM ".$GLOBALS['db_name'].".zones z
						JOIN ".$GLOBALS['db_name'].".spawn_location_placement slp ON z.id = slp.zone_id
						GROUP BY z.id;";
		return $this->db->RunQueryMulti($sql);
	}
	

	public function getSpawnTypeTotalsByZone($type, $zone_id) {
		if($type == 'All') {
			$sql = sprintf("select count(distinct s1.id) as num from ".$GLOBALS['db_name'].".spawn_location_placement z1, ".$GLOBALS['db_name'].".spawn_location_entry z2, ".$GLOBALS['db_name'].".spawn s1 where z1.spawn_location_id = z2.spawn_location_id and z2.spawn_id = s1.id and z1.zone_id = %d;", $zone_id);
		} else {
			$sql = sprintf("select count(distinct s1.spawn_id) as num from ".$GLOBALS['db_name'].".spawn_location_placement z1, ".$GLOBALS['db_name'].".spawn_location_entry z2, ".$GLOBALS['db_name'].".spawn_%s s1 where z1.spawn_location_id = z2.spawn_location_id and z2.spawn_id = s1.spawn_id and z1.zone_id = %d;", $type, $zone_id);
		}
		
		$data = $this->db->RunQuerySingle($sql);
		return ( !empty($data) ) ? $data['num'] : 0;
	}
	
	
	public function getTotalQuestsByZone($zone_id)
	{
		$sql = sprintf("SELECT COUNT(distinct q.quest_id) as num FROM ".$GLOBALS['db_name'].".quests q INNER JOIN ".$GLOBALS['db_name'].".spawn s ON q.spawn_id = s.id INNER JOIN ".$GLOBALS['db_name'].".spawn_location_entry sle ON s.id = sle.spawn_id INNER JOIN ".$GLOBALS['db_name'].".spawn_location_placement slp ON sle.spawn_location_id = slp.spawn_location_id WHERE slp.zone_id = %d", $zone_id);
		$data = $this->db->RunQuerySingle($sql);
		return ( !empty($data) ) ? $data['num'] : 0;
	}
	
	
	public function GetAllNews()
	{
		$sql = "SELECT * FROM site_text ORDER BY created_date DESC;";
		return $this->db->RunQueryMulti($sql);
	}
	
	
	public function GetNews($id)
	{
		$sql = "SELECT * FROM site_text WHERE id = " . $id . ";";
		return $this->db->RunQuerySingle($sql);
	}
}
?>