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

class eq2ItemSearchDB
{
	public function __construct()
	{
		global $eq2;
		$this->db = $eq2->eq2db;
	}
	
	
	public function SearchItems($name)
	{
		if (is_numeric($name)) {
			$sql = sprintf("SELECT id, item_type, name, description, soe_item_id, soe_item_crc FROM " . $GLOBALS['db_name'] . ".items WHERE name RLIKE '%s' OR soe_item_id = %s OR soe_item_crc = %s ORDER BY id LIMIT 0,100;", $name, $name, $name);
		}
		else {
			$search = $this->db->SQLEscape($name);
			$sql = sprintf("SELECT id, item_type, name, description, soe_item_id, soe_item_crc FROM " . $GLOBALS['db_name'] . ".items WHERE name RLIKE '%s' ORDER BY id LIMIT 0,100;", $search);
		}

		return $this->db->RunQueryMulti($sql);
	}
}
?>