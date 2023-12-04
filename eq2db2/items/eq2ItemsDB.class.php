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

class eq2ItemsDB
{

	public function __construct()
	{
		global $eq2;
		// Transfer instance of eq2db to local db member
		$this->db = $eq2->eq2db;
	}
	
	public function GetItems() {
		global $eq2, $GLOBALS;
		
		if (isset($_POST['cmd']) && $_POST['cmd'] == "Search") {
			
			$eq2->AddDebugForm($_POST);
			
			$where = " WHERE";
			if (!$eq2->IsStringNullOrEmpty($_POST['txtSearch'])) {
				if (is_numeric($_POST['txtSearch'])) {
					$name = $_POST['txtSearch'];
					$where .= " name LIKE '%" . $name . "%' OR id = " . $name . " OR soe_item_id = " . $name . " OR soe_item_crc = " . $name;
				}
				else {
					$name = $this->db->SQLEscape($_POST['txtSearch']);
					$where .= " name LIKE '%" . $name . "%'";
				}
			}
			
			if (!$eq2->IsStringNullOrEmpty($_POST['minLevel']) && is_numeric($_POST['minLevel'])) {
				if (strlen($where) > 6) {
					$where .= " AND";
				}
				$where .= " adventure_default_level >= " . $_POST['minLevel'];
			}
			
			if (!$eq2->IsStringNullOrEmpty($_POST['maxLevel']) && is_numeric($_POST['maxLevel'])) {
				if (strlen($where) > 6) {
					$where .= " AND";
				}
				$where .= " adventure_default_level <= " . $_POST['maxLevel'];
			}
			
			if (!$eq2->IsStringNullOrEmpty($_POST['classPicker'])) {
				if (strlen($where) > 6) {
					$where .= " AND";
				}
				$where .= " (adventure_classes & " . pow(2, (int)$_POST['classPicker']) . ") > 0";
			}
			
			if (!$eq2->IsStringNullOrEmpty($_POST['itemTypePicker'])) {
				if (strlen($where) > 6) {
					$where .= " AND";
				}
				$where .= " item_type = " . $_POST['itemTypePicker'];
			}
			
			if (!$eq2->IsStringNullOrEmpty($_POST['itemSlotPicker'])) {
				if (strlen($where) > 6) {
					$where .= " AND";
				}
				$where .= " (slots & " . pow(2, (int)$_POST['itemSlotPicker']) . ") > 0";
			}
			
			if (!$eq2->IsStringNullOrEmpty($_POST['itemTierPicker']) && is_numeric($_POST['itemTierPicker'])) {
				if (strlen($where) > 6) {
					$where .= " AND";
				}
				
				if ($_POST['itemTierPicker'] == 1) {
					$where .= " (tier = 1 OR tier = 2)";
				}
				
				if ($_POST['itemTierPicker'] == 2) {
					$where .= " tier = 3";
				}
				
				if ($_POST['itemTierPicker'] == 3) {
					$where .= " (tier = 4 OR tier = 5 OR tier = 6)";
				}
				
				if ($_POST['itemTierPicker'] == 4) {
					$where .= " (tier = 7 OR tier = 8)";
				}
				
				if ($_POST['itemTierPicker'] == 5) {
					$where .= " (tier = 9 OR tier = 10 OR tier = 11)";
				}
				
				if ($_POST['itemTierPicker'] == 6) {
					$where .= " tier = 12";
				}
			}
			
			if ($where == " WHERE") {
				$where = "";
			}
			
			if (isset($_POST['itemResultsPicker'])) {
				$where .= " LIMIT " . $_POST['itemResultsPicker'];
			}
			else {
				$where .= " LIMIT 50";
			}
			
			$sql = sprintf("SELECT id, icon, name, description, adventure_default_level, tradeskill_default_level FROM %s.items%s;", $GLOBALS['db_name'], $where);
		}
		else {
			$sql = sprintf("SELECT id, icon, name, description, adventure_default_level, tradeskill_default_level FROM %s.items limit 50;", $GLOBALS['db_name']);
		}
		return $this->db->RunQueryMulti($sql);
	}
	
	public function GetItem()
	{
		$sql = sprintf("SELECT * FROM %s.items WHERE id = %d;", $GLOBALS['db_name'], $_GET['id']);
		return $this->db->RunQuerySingle($sql);
	}
	
	public function GetItemName($id)
	{
		$sql = sprintf("SELECT name FROM %s.items WHERE id = %d;", $GLOBALS['db_name'], $id);
		$data = $this->db->RunQuerySingle($sql);
		return $data['name'];
	}
	
	public function GetItemEffects($id)
	{
		$sql = sprintf("SELECT * FROM %s.item_effects WHERE item_id = %d;", $GLOBALS['db_name'], $id);
		return $this->db->RunQueryMulti($sql);
	}
	
	public function GetItemAppearance($id)
	{
		$sql = sprintf("SELECT * FROM %s.item_appearances WHERE item_id = %d;", $GLOBALS['db_name'], $id);
		return $this->db->RunQuerySingle($sql);
	}
}
?>