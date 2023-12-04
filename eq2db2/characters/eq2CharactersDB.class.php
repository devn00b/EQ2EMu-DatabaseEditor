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

class eq2CharactersDB
{

	public function __construct()
	{
		global $eq2;
		// Transfer instance of eq2db to local db member
		$this->db = $eq2->eq2db;
	}

	
	public function GetCharacterByName($var)
	{
		if( $var != "all" )
		{
			$character_name = $this->db->SQLEscape($var);
			$sql = sprintf("SELECT id, name, race, class, level, tradeskill_level, admin_status, is_online FROM %s.characters WHERE name RLIKE '%s';", $GLOBALS['db_name'], $character_name);
		}
		else
		{
			$sql = sprintf("SELECT id, name, race, class, level, tradeskill_level, admin_status, is_online FROM %s.characters ORDER BY name;", $GLOBALS['db_name']);
		}
		return $this->db->RunQueryMulti($sql);
	}

	
	public function GetCharacter()
	{
		$sql = sprintf("SELECT id, account_id, server_id, name, race, class, gender, deity, current_zone_id, level, tradeskill_level, x, y, z, heading, instance_id, starting_city, deleted, 
			unix_timestamp, created_date, last_played, last_saved, admin_status, is_online FROM %s.characters WHERE id = %d;", $GLOBALS['db_name'], $_GET['id']);
		return $this->db->RunQuerySingle($sql);
	}

	
	public function GetCharacters()
	{
		$sql = sprintf("SELECT id, name FROM %s.characters ORDER BY name;", $GLOBALS['db_name']);
		return $this->db->RunQueryMulti($sql);
	}

	
	public function GetCharacterDetails()
	{
		$sql = sprintf("SELECT * FROM %s.character_details WHERE char_id = %d;", $GLOBALS['db_name'], $_GET['id']);
		return $this->db->RunQuerySingle($sql);
	}

	
	public function GetCharacterFactions()
	{
		$sql = sprintf("SELECT * FROM %s.character_factions WHERE char_id = %d;", $GLOBALS['db_name'], $_GET['id']);
		return $this->db->RunQueryMulti($sql);
	}

	
	public function GetCharacterTitles()
	{
		$sql = sprintf("SELECT * FROM %s.character_titles WHERE char_id = %d;", $GLOBALS['db_name'], $_GET['id']);
		return $this->db->RunQueryMulti($sql);
	}

	
	public function GetCharacterPicture($id)
	{
		$sql = sprintf("SELECT picture FROM %s.character_pictures WHERE pic_type = 0 AND char_id = %s;", $GLOBALS['db_name'], $_GET['id']);
		$data = $this->db->RunQuerySingle($sql);
		return $data['picture'];
	}
	
	
	public function GetEquippedItems($id)
	{
		$sql = sprintf("SELECT c.slot, c.item_id, i.name, i.icon FROM %s.character_items c, %s.items i WHERE c.item_id = i.id AND c.`type` = 'EQUIPPED' AND c.char_id = %s;", $GLOBALS['db_name'], $GLOBALS['db_name'], $id);
		$items = Array();
		$data = $this->db->RunQueryMulti($sql);
		if (is_array($data))
		{
			foreach ($data as $item)
				$items[$item['slot']] = $item;
		}
		return $items;
	}
}

?>