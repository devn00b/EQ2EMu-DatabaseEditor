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
	die();


class eq2Database
{
	public $DebugQueries;
	
	public function __construct()
	{
		// echo $GLOBALS['database']['dbhost']. $GLOBALS['database']['dbuser']. $GLOBALS['database']['dbpass']. $GLOBALS['database']['dbname'];
		include_once('mysql.class.php');
		$this->db = new sql_db($GLOBALS['database'][0]['db_host'], $GLOBALS['database'][0]['db_user'], $GLOBALS['database'][0]['db_pass'], $GLOBALS['database'][0]['db_name']);
		if( !$this->db->db_connect_id )
			$this->DBError();
	}
	
	
	private function AddDebugQuery($func, $var)
	{
		$this->DebugQueries .= sprintf("<p>&nbsp;&nbsp;-- <strong>function %s:</strong><br />&nbsp;&nbsp;%s</p>", $func, $var);
	}
	

	public function FetchConfig()
	{
		$sql = "SELECT config_name, config_value FROM config;";
		if( !$result = $this->db->sql_query($sql) )
			$this->DBError($sql);
		else
			while( $data = $this->db->sql_fetchrow($result) ) 
				$GLOBALS['config'][$data['config_name']] = $data['config_value'];
		$this->db->sql_freeresult($result);
		
		$this->GetRoles();
	}
	
	public function LoadDataSources()
	{
		$sql = "SELECT * FROM datasources WHERE is_active = 1;";
		if( !$result = $this->db->sql_query($sql) )
			$this->DBError($sql);
		else
			$i = 1; // eq2editor DB = id 0, so start custom data configs at 1
			while( $data = $this->db->sql_fetchrow($result) ) 
			{
				$GLOBALS['database'][$data['id']] = $data;
				$i++;
			}
	}
	
	private function GetRoles()
	{
		// load menu roles constants
		$sql = "SELECT role_name, role_value FROM roles;";
		if( !$result = $this->db->sql_query($sql) )
			$this->DBError($sql);
		else
			while( $data = $this->db->sql_fetchrow($result) ) 
			{
				$role_name = strtoupper(preg_replace("/\:/","_", $data['role_name']));
				define($role_name, $data['role_value']);
			}
	}

	
	public function GetRoleList()
	{
		$sql = "SELECT role_description, role_value FROM roles;";
		if( !$result = $this->db->sql_query($sql) )
			$this->DBError($sql);
		else
			while( $data = $this->db->sql_fetchrow($result) ) 
				$roles_data[] = $data;

		return $roles_data;
	}

	
	public function GetTotalRows($table)
	{
		$sql = sprintf("SELECT COUNT(*) AS num FROM ".$GLOBALS['db_name'].".%s;", $table);
		$data = $this->RunQuerySingle($sql);
		return ( !empty($data) ) ? $data['num'] : 0;
	}


	/* Use this RunQuery to return a single result set */
	public function RunQuerySingle($sql)
	{
		global $eq2;
		
		if( $GLOBALS['config']['debug'] )
		{
			$eq2->AddDebugFunction(__FUNCTION__, "Enter");
			$this->AddDebugQuery(__FUNCTION__, $sql);
		}

		if( !$result = $this->db->sql_query($sql) )
			$this->DBError($sql);
		else
		{
			$num_rows = $this->db->sql_numrows($result);
			$rtn = $this->db->sql_fetchrow($result);
		}

		if( $GLOBALS['config']['debug'] )
		{
			$eq2->AddDebugData("RunQuerySingle", $rtn);
			$eq2->AddDebugFunction(__FUNCTION__, $num_rows." row(s)");
			$eq2->AddDebugFunction(__FUNCTION__, "Exit");
		}

		return $rtn;
	}
	
	
	/* Use this RunQuery to return a multiple-row result set */
	public function RunQueryMulti($sql)
	{
		global $eq2;
		
		if( $GLOBALS['config']['debug'] )
		{
			$eq2->AddDebugFunction(__FUNCTION__, "Enter");
			$this->AddDebugQuery(__FUNCTION__, $sql);
		}

		if( !$result = $this->db->sql_query($sql) )
			$this->DBError($sql);
		else
		{
			$num_rows = $this->db->sql_numrows($result);
			while( $data = $this->db->sql_fetchrow($result) ) 
				$rtn[] = $data;
		}
		
		if( $GLOBALS['config']['debug'] )
		{
			$eq2->AddDebugData("RunQueryMulti Data", isset($rtn) ? $rtn : null);
			$eq2->AddDebugFunction(__FUNCTION__, $num_rows." row(s)");
			$eq2->AddDebugFunction(__FUNCTION__, "Exit");
		}

		if ( isset($rtn) )
			return $rtn;
	}
	

	public function RunQuery($type, $table, $object, $sql)
	{
		global $eq2;
		$rtn = '';
		
		if( $GLOBALS['config']['debug'] )
		{
			$eq2->AddDebugFunction(__FUNCTION__, "Enter");
			$this->AddDebugQuery(__FUNCTION__, $sql);
		}
		switch($type)
		{
			case "SELECT":
				if( !$result=$this->db->sql_query($sql) )
					$this->DBError($sql);
				else
					$rtn = $this->db->sql_fetchrow($result);
				break;
			case "INSERT":
			case "UPDATE":
			case "DELETE":
				if( $GLOBALS['config']['readonly'] )
				{
					$eq2->AddStatus("READ-ONLY MODE - No data updated!");
				}
				else
				{
					if( !$result = $this->db->sql_query($sql) )
						$this->DBError($sql);
					else
						$num_rows = $this->db->sql_affectedrows($result);
				}
				break;
		}
		if( $num_rows )
		{
			// Do not log SELECT, only update queries
			$var = array($table, $object, $sql);
			$this->SQLLog($var);
		}
		if( $GLOBALS['config']['debug'] )
		{
			$eq2->AddDebugData("RunQuery Data", $rtn);
			$eq2->AddDebugFunction(__FUNCTION__, $num_rows." row(s)");
			$eq2->AddDebugFunction(__FUNCTION__, "Exit");
		}
		return $num_rows;
	}
	
		
	public function GetUser($lname, $lpass)
	{
		global $eq2;
	
		if( $GLOBALS['config']['debug'] )
			$eq2->AddDebugFunction(__FUNCTION__, "Enter");
			
		$sql = sprintf("SELECT * FROM users WHERE username = '%s' AND password = '%s' LIMIT 0,1;", $this->db->sql_escape($lname), $this->db->sql_escape($lpass)); 
		$rtn = $this->RunQuerySingle($sql);

		if( $GLOBALS['config']['debug'] )
		{
			$eq2->AddDebugData("GetUser Data", $rtn);
			$eq2->AddDebugFunction(__FUNCTION__, "Exit");
		}

		return $rtn;
	}
	

	public function CheckUserRoles($role, $uid)
	{
		global $eq2;
	
		$sql = sprintf("SELECT COUNT(*) AS cnt FROM user_roles, roles WHERE roles.id = user_roles.user_role_id AND (role_name = '%s' OR role_name = 'g:admin') AND user_id = %lu;", $role, $uid); 
		$data = $this->RunQuerySingle($sql);

		if( $GLOBALS['config']['debug'] )
		{
			$eq2->AddDebugData("GetUserRoles Data", $data);
			$eq2->AddDebugFunction(__FUNCTION__, "Exit");
		}
		return ( isset($data) ) ? $data['cnt'] : 0;
	}
	

	/*
		Function: GetLuaScriptInfo($table, $id)
		Purpose	: Returns the main identifying info for a particular script (only)
	*/
	public function GetLuaScriptInfo($table, $script)
	{
		if( empty($table) || empty($script) )
		{
			$eq2->AddStatus("Table or Script not set in GetLuaScriptInfo().");
			return;
		}
		
		$index_field = ( $table == "quests" ) ? "quest_id" : "id";

		$sql = sprintf("SELECT * FROM ".$GLOBALS['db_name'].".%s WHERE %s = %lu;", $table, $index_field, $id);
		$row = $this->RunQuerySingle($sql);
		return ( !empty($row) ) ? $row : "";
	}
	

//	public function GetMenuData($menu)
//	{
//		$sql = sprintf("SELECT * FROM eq2editor.menus WHERE menu_page = '%s' OR menu_page = 'Generic' ORDER BY menu_section, menu_order;", $menu);
//		return $this->RunQueryMulti($sql);
//	}
	
	
	function GetSkillsList() 
	{
		$sql="select * from ".$GLOBALS['db_name'].".skills order by name";
		return $this->RunQueryMulti($sql);
	}


	/*
		Function: GetExpansionData()
		Purpose	: Reads expansion data from eq2expansions table (provided with eq2editor)
		Params	: 
		Syntax	: 
	*/	
	public function GetExpansionData() 
	{
		$sql = sprintf("SELECT * FROM ".$GLOBALS['db_name'].".eq2expansions;");
		return $this->RunQueryMulti($sql);
	}	
	
	public function GetItemNamesArray() {
		$sql = sprintf("SELECT id, name FROM %s.items", $GLOBALS['db_name']);
		$data = $this->RunQueryMulti($sql);
		
		// reformat array into item[id]=name
		if( is_array($data) )
			foreach($data as $key=>$val)
				$rtn[$val[id]] = $val[name];
		return $rtn;
	}

	public function GetItemNameByID($id) {
		$sql = sprintf("SELECT name FROM ".$GLOBALS['db_name'].".items WHERE id = %lu", $id);
		$data = $this->RunQuerySingle($sql);
		return ( isset($data) ) ? $data['name'] : "Unknown Item: " . $id;
	}

	public function GetZoneDescriptionByID($id) 
	{
		$sql="select description from zones where id = $id;";
		$data = $this->RunQuerySingle($sql);
		return ( isset($data) ) ? $data['description'] : "Cannot fine zone description for zone id: " . $id;
	}	


	/*
		Function: GetZoneIDByName()
		Purpose	: Translates zones.name to zones.id
		Params	: $id = zones.name (the zone's friendly name)
		Syntax	: GetZoneIDByName('quests', 1)
	*/	
	public function GetZoneIDByName($name) 
	{
		$sql = sprintf("SELECT id FROM ".$GLOBALS['db_name'].".zones WHERE name = '%s';", $name);
		$data = $this->RunQuerySingle($sql);
		return ( isset($data) ) ? $data['id'] : 0;
	}	
	

	/*
		Function: GetZoneNameByID()
		Purpose	: Translates zones.id to zones.name
		Params	: $id = zones.id
		Syntax	: GetZoneNameByID('quests', 1)
	*/	
	public function GetZoneNameByID($id) 
	{
		$sql = sprintf("SELECT name FROM ".$GLOBALS['db_name'].".zones WHERE id = %d;", $id);
		$data = $this->RunQuerySingle($sql);
		return ( isset($data) ) ? $data['name'] : "Not Found.";
	}	
	

	/*
		Function: GetZoneIDName()
		Purpose	: Fetches full list of zones id,name data for building <select> form elements
		Params	: $pop = populated zones only
		Syntax	: GetZoneIDName(1)
	*/	
	public function GetZoneIDName($pop = 0) 
	{
		if( $pop ) 
			$sql = "SELECT id,name FROM ".$GLOBALS['db_name'].".zones WHERE id IN (SELECT DISTINCT zone_id FROM ".$GLOBALS['db_name'].".spawn_location_placement) ORDER BY name;";
		else
			$sql = "SELECT id,name FROM ".$GLOBALS['db_name'].".zones ORDER BY name;";
		$data = $this->RunQueryMulti($sql);

		return $data;
	}	
	

	/*
		Function: GetStartingCities()
		Purpose	: Fetches full list of zones id,name data for building <select> form elements
		Params	: 
		Syntax	: GetStartingCities(1)
	*/	
	public function GetStartingCities() 
	{
		$sql = "SELECT start_zone, name FROM ".$GLOBALS['db_name'].".zones WHERE start_zone > 0 ORDER BY start_zone;";
		return $this->RunQueryMulti($sql);
	}	
	

	function GetZoneOptionsByID($pop = 0) 
	{
		if( $pop ) 
			$sql = "SELECT id,name,file,description FROM ".$GLOBALS['db_name'].".zones ORDER BY description;";
		else
			$sql = "SELECT id,name,description FROM ".$GLOBALS['db_name'].".zones ORDER BY description;";
		$row = $this->RunQueryMulti($sql);

		print('<select name="zoneID" onChange="dosub(this.options[this.selectedIndex].value)" class="zone" />');
		print('<option>Pick a Zone</option>');

		foreach($row as $data) {
			$selected=( $_GET['zone'] == $data['id'] ) ? " selected" : "";
			printf("<option value=\"?page=%s&zone=%d\"$selected>%s (%s)</option>\n", 
				$_GET['page'], $data['id'], $data['description'], $data['name']);
		}

		print('</select>');
	}
			

	function GetZoneSelectByID($pop = 0) 
	{
		if( $pop ) 
			$sql = "SELECT id,name,file,description FROM ".$GLOBALS['db_name'].".zones WHERE id in (SELECT zone_id FROM ".$GLOBALS['db_name'].".spawn_location_placement) ORDER BY description;";
		else
			$sql = "SELECT id,name,description FROM ".$GLOBALS['db_name'].".zones ORDER BY description;";
		if( $GLOBALS['config']['debug'] )
			$this->AddDebugQuery(__FUNCTION__, $sql);

		if( !$result = $this->db->sql_query($sql) )
			$this->DBError($sql);

		print('<select name="zoneID" class="zone" />');
		print('<option>Pick a Zone</option>');

		while($data = $this->db->sql_fetchrow($result)) {
			$selected=( $_REQUEST['zoneID'] == $data['id'] ) ? " selected" : "";
			printf("<option value=\"%d\"$selected>%s (%s)</option>\n", 
				$data['id'], $data['description'], $data['name']);
		}

		print('</select>');
	}
			

	public function QuestLookup($by = "id", $var)
	{
		switch($by)
		{
			case "name":
				$sql = sprintf("SELECT * FROM ".$GLOBALS['db_name'].".quests WHERE name rlike '%s';", $var);
				break;
		}

		if( $GLOBALS['config']['debug'] )
			$this->AddDebugQuery(__FUNCTION__, $sql);

		if( !$result=$this->db->sql_query($sql) )
		{
			$this->DBError($sql);
		}
		else
		{
			while( $data = $this->db->sql_fetchrow($result) ) 
			{
				$data_rows[] = $data;
			}
		}
		return ( isset($data_rows) ) ? $data_rows : "";
	}
	
	public function SpawnLookup($by = "id", $zone = 0, $var)
	{
		switch($by)
		{
			case "name":
				if( $zone > 0 )
				{
					$sql = sprintf("SELECT DISTINCT s.id, s.name FROM ".$GLOBALS['db_name'].".spawn s JOIN ".$GLOBALS['db_name'].".spawn_location_entry sle ON s.id = sle.spawn_id JOIN ".$GLOBALS['db_name'].".spawn_location_placement slp ON sle.spawn_location_id = slp.spawn_location_id WHERE name rlike '%s' AND slp.zone_id = %d;", $var, $zone);
				}
				else
				{
					$sql = sprintf("SELECT * FROM ".$GLOBALS['db_name'].".spawn WHERE name rlike '%s';", $var);
				}
				break;
		}
		
		if( $GLOBALS['config']['debug'] )
			$this->AddDebugQuery(__FUNCTION__, $sql);
		
		if( !$result=$this->db->sql_query($sql) )
		{
			$this->DBError($sql);
		}
		else
		{
			while( $data = $this->db->sql_fetchrow($result) ) 
			{
				$data_rows[] = $data;
			}
		}
		return ( isset($data_rows) ) ? $data_rows : "";
	}
	

	public function GetSiteText($type, $subtype)
	{
		$sql = "SELECT s.title, u.username, s.description, s.created_date FROM site_text s, users u WHERE s.author = u.id AND type = '$type' and subtype = '$subtype' AND s.is_active = 1;";
		return $this->RunQueryMulti($sql);
	}

	
	public function SQLEscape($str)
	{
		return $this->db->sql_escape($str);
	}	
	

	private function SQLLog($var)
	{
		global $eq2;
		
		// stuff insert, update, delete queries into db_log table
		if( $GLOBALS['config']['debug'] )
		{
			$eq2->AddDebugFunction(__FUNCTION__, "Enter");
			$eq2->AddDebugData(__FUNCTION__, $var);
		}
		
		
		if( $GLOBALS['config']['debug'] )
			$eq2->AddDebugFunction(__FUNCTION__, "Exit");

	}
	
		
	private function DBError($sql = '')
	{
		$error = $this->db->sql_error();
		$message = "<p align=center><strong>".$error['message']."</strong><br>"."Error Code: ".$error['code']."</p><p align=center>".$sql."</p>";
		die($message);
	}

}
?>
