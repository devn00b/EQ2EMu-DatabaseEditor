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

class eq2QuestsDB
{

	public function __construct()
	{
		global $eq2;
		// Transfer instance of eq2db to local db member
		$this->db = $eq2->eq2db;
	}

    public function GetZone()
    {
        $sql        = "SELECT DISTINCT zone FROM ".$GLOBALS['db_name'].".quests";
        
        $row = $this->db->RunQueryMulti($sql);
        
        foreach($row as $data) {
            $selected = ( $_GET['zone'] == $data['zone'] ) ? " selected" : "";
            $zone_options .= sprintf("<option value=\"?page=%s&zone=%s\"%s>%s</option>\n", 
                $_GET['page'], $data['zone'], $selected, $data['zone']);
        }
        return $zone_options;
        
    }

    public function GetQuestByName($var)
    {
        $quest_name = $this->db->SQLEscape($var);
        $sql = "SELECT quest_id, level, name, description, zone, lua_script FROM ".$GLOBALS['db_name'].".quests WHERE (`name` RLIKE '$quest_name') ORDER BY NAME;";
        return $this->db->RunQueryMulti($sql);
    }
	
    public function GetQuestByZone($var)
    {
        $zone_name = $this->db->SQLEscape($var);
        $sql = "SELECT quest_id, level, name, description, zone, lua_script FROM ".$GLOBALS['db_name'].".quests WHERE (`zone` RLIKE '$zone_name') ORDER BY level ASC, name ASC;";
    
        return $this->db->RunQueryMulti($sql);
    }

    public function GetZoneQuest($var)
    {
        $zone_name = $this->db->SQLEscape($var);
        $sql = "SELECT quest_id, name, zone FROM ".$GLOBALS['db_name'].".quests WHERE (`zone` RLIKE '$zone_name') ORDER BY level ASC, name ASC;";
        $row = $this->db->RunQueryMulti($sql);

        foreach ($row as $data) {
            $selected = ( $_GET['name'] == $data['name'] ) ? " selected" : "";
            $zone_quest .= sprintf("<option value=\"?page=%s&zone=%s&name=%s&id=%d&tab=general\"%s>%s (%d)</option>\n",
                $_GET['page'], $data['zone'], $data['name'], $data['quest_id'], $selected, $data['name'], $data['quest_id']);
        }
        return $zone_quest;
    }

    public function GetQuestByID()
    {
        $sql = sprintf("SELECT * FROM ".$GLOBALS['db_name'].".quests WHERE quest_id = %d;", $_GET['id']);
        return $this->db->RunQuerySingle($sql);
    }

    public function GetDetailsByQuestID()
    {
        $sql = sprintf("SELECT * FROM ".$GLOBALS['db_name'].".quest_details WHERE quest_id = %d;", $_GET['id']);
        return $this->db->RunQueryMulti($sql);
    }
}
?>