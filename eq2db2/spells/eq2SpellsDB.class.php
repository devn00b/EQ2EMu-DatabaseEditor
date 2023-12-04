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

class eq2SpellsDB
{

	public function __construct()
	{
		global $eq2;
		// Transfer instance of eq2db to local db member
		$this->db = $eq2->eq2db;
	}

	public function GetSpells()
	{
		$sql = sprintf("SELECT id, name FROM %s.spells ORDER BY name;", $GLOBALS['db_name']);
		return $this->db->RunQueryMulti($sql);
	}


	public function GetSpellsByClass($class_id)
	{
		$sql = "SELECT DISTINCT s.id, s.name, s.description, s.icon, s.is_active, sc.adventure_class_id, sc.level, s.lua_script, s.soe_spell_crc
						FROM ".$GLOBALS['db_name'].".spells s
						JOIN ".$GLOBALS['db_name'].".spell_classes sc
						ON s.id = sc.spell_id
						JOIN " . $GLOBALS['db_name'] . ".spell_tiers st
						ON sc.spell_id = st.spell_id
						WHERE s.id LIKE '".$class_id."____'
						" . (isset($_GET['classification']) && $_GET['classification'] != "all" ? " AND st.given_by = '" . $_GET['classification'] . "'" : "") . "
						" . (isset($_GET['type']) && $_GET['type'] != "all" ? " AND s.type = " . $_GET['type'] : "") ."
						ORDER BY sc.level, s.name;";
		return $this->db->RunQueryMulti($sql);
	}


	public function GetSpellByID()
	{
		$sql = sprintf("SELECT * FROM %s.spells WHERE id = '%s' ORDER BY name;", $GLOBALS['db_name'], $_GET['id']);
		return $this->db->RunQuerySingle($sql);
	}
	
	
	public function GetSpellByName($var)
	{
		if( $var != "all" )
		{
			$spell_name = $this->db->SQLEscape($var);
			$sql = "SELECT s.id as spellid, s.name, s.lua_script, s.is_active, sc.* 
							FROM ".$GLOBALS['db_name'].".spells s
							JOIN ".$GLOBALS['db_name'].".spell_classes sc
								ON s.id = sc.spell_id
							WHERE s.name RLIKE '".$spell_name."'
							ORDER BY sc.level, s.name;";
		}
		else
		{
			$sql = "SELECT s.id as spellid, s.name, s.lua_script, s.is_active, sc.* 
							FROM ".$GLOBALS['db_name'].".spells s
							JOIN ".$GLOBALS['db_name'].".spell_classes sc
								ON s.id = sc.spell_id
							ORDER BY s.name;";

		}
		return $this->db->RunQueryMulti($sql);
	}
	
	public function GetSpellTiers($id)
	{
		$sql = "SELECT * FROM " . $GLOBALS['db_name'] . ".spell_tiers WHERE spell_id = " . $id . " ORDER BY tier;";
		return $this->db->RunQueryMulti($sql);
	}

	public function GetSpellData($id)
	{
		$sql = "SELECT * FROM " . $GLOBALS['db_name'] . ".spell_data WHERE spell_id = " . $id . " ORDER BY tier;";
		return $this->db->RunQueryMulti($sql);
	}

	public function GetSpellEffects($id)
	{
		$sql = "SELECT * FROM " . $GLOBALS['db_name'] . ".spell_display_effects WHERE spell_id = " . $id . " ORDER BY tier ASC, `index` ASC;";
		return $this->db->RunQueryMulti($sql);
	}

	public function GetSpellClasses($id)
	{
		$sql = "SELECT * FROM " . $GLOBALS['db_name'] . ".spell_classes WHERE spell_id = " . $id . " ORDER BY `id` ASC";
		return $this->db->RunQueryMulti($sql);
	}

}
?>