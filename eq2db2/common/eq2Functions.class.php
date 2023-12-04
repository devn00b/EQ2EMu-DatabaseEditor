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

include_once('eq2Screens.class.php');

/*
	Function: Class eq2Functions
	Purpose	: Common Core and Database functions used throughout the application
					: Provides default MySQL connection 'eq2db'
	Extends	: Putting global screen building operations in eq2Screens.class.php and extending eq2Functions
*/
class eq2Functions extends eq2Screens
{
	public $Status;
	
	public $userdata;
	public $PageLink;
	public $BackLink;
	public $script_name;

	public $eq2PlayerRaces = array(
		0 => "Barbarian",
		1 => "Dark Elf",
		2 => "Dwarf",
		3 => "Erudite",
		4 => "Froglok",
		5 => "Gnome",
		6 => "Half Elf",
		7 => "Halfling",
		8 => "High Elf",
		9 => "Human",
		10 => "Iksar",
		11 => "Kerra",
		12 => "Ogre",
		13 => "Ratonga",
		14 => "Troll",
		15 => "Wood Elf",
		16 => "Fae",
		17 => "Arasi",
		18 => "Sarnak",
		19 => "Freeblood",
		20 => "Aerakyn",
		255 => "All"
	);
	
	public $eq2Classes = array(
		0 => "Commoner",
		1 => "Fighter",
		11 => "Priest",
		21 => "Mage",
		31 => "Scout",
		2 => "Warrior",
		3	=> "Guardian",
		4 => "Berserker",
		5 => "Brawler",
		6 => "Monk",
		7 => "Bruiser",
		8 => "Crusader",
		9 => "Shadowknight",
		10 => "Paladin",
		12 => "Cleric",
		13 => "Templar",
		14 => "Inquisitor",
		15 => "Druid",
		16 => "Warden",
		17 => "Fury",
		18 => "Shaman",
		19 => "Mystic",
		20 => "Defiler",
		22 => "Sorcerer",
		23 => "Wizard",
		24 => "Warlock",
		25 => "Enchanter",
		26 => "Illusionist",
		27 => "Coercer",
		28 => "Summoner",
		29 => "Conjuror",
		30 => "Necromancer",
		32 => "Rogue",
		33 => "Swashbuckler",
		34 => "Brigand",
		35 => "Bard",
		36 => "Troubadour",
		37 => "Dirge",
		38 => "Predator",
		39 => "Ranger",
		40 => "Assassin",
		41 => "Animalist",
		42 => "Beastlord",
		43 => "Shaper",
		44 => "Channeler",
	 255 => "ALL"
	);
	
	public $eq2PlayerClasses = array(
		3	=> "GUARDIAN",
		4 => "BERSERKER",
		6 => "MONK",
		7 => "BRUISER",
		9 => "SHADOWKNIGHT",
		10 => "PALADIN",
		13 => "TEMPLAR",
		14 => "INQUISITOR",
		16 => "WARDEN",
		17 => "FURY",
		19 => "MYSTIC",
		20 => "DEFILER",
		23 => "WIZARD",
		24 => "WARLOCK",
		26 => "ILLUSIONIST",
		27 => "COERCER",
		29 => "CONJUROR",
		30 => "NECROMANCER",
		33 => "SWASHBUCKLER",
		34 => "BRIGAND",
		36 => "TROUBADOR",
		37 => "DIRGE",
		39 => "RANGER",
		40 => "ASSASSIN",
		41 => "ANIMALIST",
		42 => "BEASTLORD",
		43 => "SHAPER",
		44 => "CHANNELER"
	);

	public $eq2ItemTiers = array(
		1 => "COMMON",
		2 => "COMMON",
		3 => "UNCOMMON",
		4 => "TREASURED",
		5 => "TREASURED",
		6 => "TREASURED",
		7 => "LEGENDARY",
		8 => "TREASURED",
		9 => "FABLED",
		10 => "FABLED",
		11 => "FABLED",
		12 => "MYTHICAL",
	);

	public function __construct() 
	{
		require_once('eq2Database.class.php');
		$this->eq2db = new eq2Database();

		global $DebugGeneralStartupWarnings, $DebugGeneralStartupNotices;
		if (isset($DebugGeneralStartupWarnings)) {
			foreach ($DebugGeneralStartupWarnings as $w) {
				$this->AddDebugGeneral("Warning", $w);
			}
			$DebugGeneralStartupWarnings = NULL;
		}
		if (isset($DebugGeneralStartupNotices)) {
			foreach ($DebugGeneralStartupNotices as $n) {
				$this->AddDebugGeneral("Notice", $n);
			}
			$DebugGeneralStartupNotices = NULL;
		}
	}


	/*
		Function: LoadConfig()
		Purpose	: Function call to database to get eq2editor.config records.
		Syntax	: $eq2->LoadConfig()
	*/
	public function LoadConfig()
	{
		$this->eq2db->FetchConfig();
		$this->eq2db->LoadDataSources();
	}


	/* Login/Session Functions */
	public function LoginUser()
	{
		if( $GLOBALS['config']['debug'] )
			$this->AddDebugFunction(__FUNCTION__, "Enter");
		
		// Fetch user data from DB
		$user_data = $this->eq2db->GetUser($_POST['lName'], md5($_POST['lPass']));

		if( $GLOBALS['config']['debug'] )
			$this->AddDebugData("userdata", $user_data);
		
		if( is_array($user_data) )
		{
			foreach($user_data as $key=>$val)
			{
				$this->userdata[$key] = $val;
			}
		
			$this->SaveCookie($this->userdata);		
		}

		if( $GLOBALS['config']['debug'] )
			$this->AddDebugFunction(__FUNCTION__, "Exit");
	}


	public function GetCookie()
	{
		if (isset($_COOKIE['eq2db'])) 
		{
			// 2009.08.25 - changed GetCookie to re-authenticate against DB in case password changes/doesn't match
			$user_data = $this->eq2db->GetUser($_COOKIE['eq2db']['name'], $_COOKIE['eq2db']['pass']);

			if( is_array($user_data) )
			{
				foreach($user_data as $key=>$val)
				{
					$this->userdata[$key] = $val;
				}
			}
			return $this->userdata;
		}
		return 0;
	}


	private function SaveCookie($userdata)
	{
		if( $GLOBALS['config']['debug'] ) 
			$this->AddDebugFunction(__FUNCTION__, "Enter");

		$cookie_timeout = time() + (60 * 60 * 24 * 30); // 30 day timeout
		// 2012.01.20 - removed cookie_path and cookie_domain from setcookie because IE9 and Chrome 10 would not set cookies properly (wtf?)
		setcookie("eq2db[id]", $this->userdata['id'], $cookie_timeout);
		setcookie("eq2db[name]", $this->userdata['username'], $cookie_timeout);
		setcookie("eq2db[pass]", $this->userdata['password'], $cookie_timeout);
		setcookie("eq2db[db]", $this->userdata['datasource_id'], $cookie_timeout); // added for AJAX db lookups

		if( $GLOBALS['config']['debug'] )
			$this->AddDebugFunction(__FUNCTION__, "Exit");
	}

	
	public function DeleteCookie()
	{
		if( $GLOBALS['config']['debug'] ) 
			$this->AddDebugFunction(__FUNCTION__, "Enter");
			
		if (isset($_COOKIE['eq2db'])) 
		{
			foreach ($_COOKIE['eq2db'] as $key => $val) 
			{
				setcookie("eq2db[$key]", 0, time() - 1);
			}
		}
		
		if( $GLOBALS['config']['debug'] )
			$this->AddDebugFunction(__FUNCTION__, "Exit");
	}


	public function AddDebugGeneral($label, $data)
	{
		$this->DebugGeneral .= sprintf("<p>&nbsp;&nbsp;<strong>%s:</strong> %s</p>", $label, $data);
	}
	
	
	public function AddDebugData($name, $var)
	{
		if( is_array($var) )
		{
			$this->DebugData .= '<p>';
			foreach($var as $key=>$val)
				if( is_array($val) )
					foreach($val as $key2=>$val2)
						$this->DebugData .= sprintf("&nbsp;&nbsp;<strong>array[%s]</strong>: %s = %s<br />", $name, $key2, $val2);
				else
					$this->DebugData .= sprintf("&nbsp;&nbsp;<strong>array[%s]</strong>: %s = %s<br />", $name, $key, $val);
			$this->DebugData .= '</p>';
		}
		else
			$this->DebugData .= sprintf("<p>%s = %s</p>", $name, $var);
	}
	
	
	public function AddDebugFunction($func, $var)
	{
		$this->DebugFunctions .= sprintf("<p>&nbsp;&nbsp;<strong>function %s:</strong> %s</p>", $func, $var);
	}
	
	
	public function AddDebugForm($arr)
	{
		foreach($arr as $key=>$val)
		{
			if( is_array($val) )
				$this->DebugForms .= sprintf("<p>&nbsp;&nbsp;<strong>%s:</strong> %s</p>", $key, print_r($val));
			else
				$this->DebugForms .= sprintf("<p>&nbsp;&nbsp;<strong>%s:</strong> %s</p>", $key, $val);
		}
	}
	
	
	public function AddStatus($var)
	{
		$this->Status .= sprintf("&nbsp;&nbsp;%s", $var);
	}
	
	public function ClearStatus()
	{
		$this->Status = '';
	}
	

	/*
		Function: DBPicker()
		Purpose	: Sets the active datasource for this session
	*/
	public function DBPicker() {
		if( isset($_POST['db-picker']) )
			$_SESSION['current_database2']=$_POST['db-picker'];
		
		print('<form method="post">Active Database:&nbsp;<select name="db-picker" onchange="this.form.submit();" style="width:150px; font-size:11px;">');
		foreach($GLOBALS['database'] as $datasource)
		{
			if( $datasource['id'] > 0 ) { // do not list EQ2Editor DB in the picker
				$selected = ( isset($_SESSION['current_database2']) && $_SESSION['current_database2'] == $datasource['id'] ) ? " selected" : "";
				printf('<option value="%d"%s>%s</option>', $datasource['id'], $selected, $datasource['db_display_name']);
			}
		}
		print('</select></form>');
	}


	/*
		Function: ClassPicker()
		Purpose	: Builds a combobox of Classes
	*/
	public function ClassPicker() 
	{
		$URL = sprintf("%s?page=%s&type=%s", $_SERVER['PHP_SELF'], $_GET['page'], $_GET['type']);
	
		print('<select name="classPicker" onchange="dosub(this.options[this.selectedIndex].value)">');
		printf('<option value="%s">Pick a Class</option>', $this->PageLink);
		
		foreach($this->eq2Classes as $class=>$name)
			printf('<option value="%s&class=%s"%s>%s (%s)</option>', $this->PageLink, $class,( isset($_GET['class']) && $class == $_GET['class'] ) ? " selected" : "", $name, $class);
			
		print('</select>');
	}


	/*
		Function: SkillsPicker()
		Purpose	: Builds a combobox of Skills
	*/
	public function GetSkillsOptions($id) 
	{
		$skills_list = $this->eq2db->GetSkillsList();

		if( is_array($skills_list) )
		{
			print_r($skills_list);
			
			foreach($skills_list as $key=>$skill)
				printf('<option value="%s"%s>%s (%s)</option>', $skill['id'],( $id == $skill['id'] ) ? " selected" : "", $skill['name'], $skill['id']);
		}
	}
	
	/*
	 Function: TabGenerator2()
	 Purpose	: Builds screen Tabs based on array '$tab_array'.
	 Syntax	: $eq2->TabGenerator($current_tab_idx, array(...))
	 Param		: Optional True/False param to strip check for `id` fields
	 : $query_string represents the data filters
	 Example	: $eq2->TabGenerator($current_tab_idx, array('general'=>'General','database'=>'Database','paths'=>'Paths','logs'=>'Logs','misc'=>'Misc'));
	 : where array $key = tab querystring value, and $val is the title text shown on the Tab.
	 */
	function TabGenerator($current_tab, $tab_array, $query_string, $keep_id = true)
	{
		if( is_array($tab_array) )
		{
			print('<div id="mmtabs"><ul>');
			foreach($tab_array as $key=>$val)
			{
				$tab_index = ( isset($current_tab) ) ? sprintf("&tab=%s", $key) : "";
				$is_active = ( $current_tab == $key ) ? ' id="activetab"' : '';
				$id = ( isset($_GET['id']) && $keep_id ) ? sprintf('&id=%s', $_GET['id']) : '';
				printf('<li%s><a href="%s%s%s"><span>%s</span></a></li>', $is_active, $query_string, $tab_index, $id, $val);
			}
			print('<div class="mmcolortabsline">&nbsp;</div></ul></div><br />');
		}
	}
	
	
	/*
		Function: TabGenerator2()
		Purpose	: Builds screen Tabs based on array '$tab_array'.
		Syntax	: $eq2->TabGenerator($current_tab_idx, array(...))
		Param		: Optional True/False param to strip check for `id` fields
		Example	: $eq2->TabGenerator($current_tab_idx, array('general'=>'General','database'=>'Database','paths'=>'Paths','logs'=>'Logs','misc'=>'Misc'));						 
				: where array $key = tab querystring value, and $val is the title text shown on the Tab.
	*/
	public function TabGenerator2($current_tab, $tab_array, $keep_id = true)
	{
		if( is_array($tab_array) )
		{
			print('<div id="mmtabs"><ul>');
			foreach($tab_array as $key=>$val)
			{
				$tab_index = ( isset($current_tab) ) ? sprintf("&tab=%s", $key) : "";
				$is_active = ( $current_tab == $key ) ? ' id="activetab"' : '';
				$id = ( isset($_GET['id']) && $keep_id ) ? sprintf('&id=%s', $_GET['id']) : '';
				printf('<li%s><a href="index.php?page=%s&type=%s%s%s"><span>%s</span></a></li>', $is_active, $_GET['page'], $_GET['type'], $tab_index, $id, $val);
			}
			print('<div class="mmcolortabsline">&nbsp;</div></ul></div><br />');
		}
	}


	/*
		Function: DisplayRoleListToolTip()
		Purpose	: Builds a mouseover tool tip containing a list of all roles a user is a member of
		Params	: $roles is the bitwise value of the users `role` assignment
	*/
	public function DisplayRoleListToolTip($roles)
	{
		$rtn = '';
		//$rtn = sprintf("%d: ", $roles);
		if( $roles )
		{
			foreach($this->role_list as $key=>$val)
			{
				if( $roles & intval($val['role_value']) )
				{
					if( empty($rtn) )
						$rtn .= $val['role_description'];
					else
						$rtn .= sprintf(", %s", $val['role_description']);
				}
			}
		}
		else
		{
			$rtn .= "None.";
		}

		return $rtn;		
	}

	
	/*
		Function: SelectDataSource($ds = 0)
		Purpose	: Displays a list of current datasources supported by this editor
	*/
	public function SelectDataSource($ds = 0) 
	{
		foreach($GLOBALS['database'] as $datasource)
		{
			if( $datasource['id'] > 0 ) { // do not list EQ2Editor DB in the picker
				$selected = ( $datasource['id'] === $ds ) ? " selected" : "";
				$dsOptions .= printf('<option value="%s"%s>%s</option>', $datasource['id'], $selected, $datasource['db_display_name']);
			}
		}
		return $dsOptions;
	}

	
	public function ProcessInsert($sql)
	{
		if( $GLOBALS['config']['debug'] )
			$this->AddDebugFunction(__FUNCTION__, "Enter");

		if( $GLOBALS['config']['debug_forms'] )
			$this->AddDebugForm($_POST);


		if( empty($sql) )
		{
			$table = ( isset($_POST['table']) ) ? $_POST['table'] : "";
			if( !empty($table) )
			{
				$fields = null;
				$values = null;
				foreach($_POST as $key=>$val) {
					$chkKey = explode("|",$key);
					if( $chkKey[0]==$table ) {
						if( empty($fields) ) :
							$fields.=$chkKey[1];
							$values.="'".addslashes($val)."'";
						else :
							$fields.=", ".$chkKey[1];
							$values.=",'".addslashes($val)."'";
						endif;
					}
				}		
				if( !empty($fields) ) 
				{
					$sql = sprintf("INSERT INTO %s.%s (%s) VALUES (%s);", $GLOBALS['db_name'], $table, $fields, $values); 
					$this->eq2db->RunQuery('INSERT', $_POST['table'], $_POST['object'], $sql);
				}		
			}
		}
		else
		{
			$rows = $this->eq2db->RunQuery('INSERT', $_POST['table'], $_POST['object'], $sql);
			if( $rows )
				$this->AddStatus(sprintf("%s row(s) affected", $rows));
		}

		if( $GLOBALS['config']['debug'] )
			$this->AddDebugFunction(__FUNCTION__, "Exit");
	}
	
	
	public function ProcessUpdate($sql = NULL)
	{

		if( $GLOBALS['config']['debug'] )
			$this->AddDebugFunction(__FUNCTION__, "Enter");

		if( $GLOBALS['config']['debug_forms'] )
			$this->AddDebugForm($_POST);

		if( empty($sql) )
		{
			$table = ( isset($_POST['table']) ) ? $_POST['table'] : "";
			if( !empty($table) )
			{
				$id = $_POST['orig_id'];
				$sets='';
				foreach($_POST as $key=>$val) 
				{
					$chkKey = explode("|",$key);
					if( $chkKey[0]==$table ) {
						// has something changed?
						if( $_POST['orig_'.$chkKey[1]] != $val ) {
							if( empty($sets) ) :
								$sets .= sprintf("%s = '%s'", $chkKey[1], addslashes($val));
							else :
								$sets .= sprintf(", %s = '%s'", $chkKey[1], addslashes($val));
							endif;
						}
					}
				}
				
				if( !empty($sets) ) 
				{
					$sql = sprintf("UPDATE %s.%s SET %s WHERE id = %s;", $GLOBALS['db_name'], $table, $sets, $id);
					$rows = $this->eq2db->RunQuery('UPDATE', $_POST['table'], $_POST['object'], $sql);
					if( $rows )
						$this->AddStatus(sprintf("%s row(s) affected", $rows));
				}
			}
		}
		else
		{
			$rows = $this->eq2db->RunQuery('UPDATE', $_POST['table'], $_POST['object'], $sql);
			if( $rows )
				$this->AddStatus(sprintf("%s row(s) affected", $rows));
		}

		if( $GLOBALS['config']['debug'] )
			$this->AddDebugFunction(__FUNCTION__, "Exit");
			
	}
	
	
	public function ProcessDelete($sql)
	{
		if( $GLOBALS['config']['debug'] )
			$this->AddDebugFunction(__FUNCTION__, "Enter");

		if( $GLOBALS['config']['debug_forms'] )
			$this->AddDebugForm($_POST);

		if( empty($sql) )
		{
			$table = ( isset($_POST['table']) ) ? $_POST['table'] : "";
			if( !empty($table) )
			{
				$sql = sprintf("DELETE FROM %s.%s WHERE id = %s;", $GLOBALS['db_name'], $table, $_POST['orig_id']);
				$rows = $this->eq2db->RunQuery('UPDATE', $_POST['table'], $_POST['object'], $sql);
				if( $rows )
					$this->AddStatus(sprintf("%s row(s) affected", $rows));
			}
		}
		else
		{
			$rows = $this->eq2db->RunQuery(UPDATE, $_POST['table'], $_POST['object'], $sql);
			if( $rows )
				$this->AddStatus(sprintf("%s row(s) affected", $rows));
		}

		if( $GLOBALS['config']['debug'] )
			$this->AddDebugFunction(__FUNCTION__, "Exit");			
	}
	
	
	public function ProcessUpdateMultipleTables()
	{
		if( $GLOBALS['config']['debug'] )
			$this->AddDebugFunction(__FUNCTION__, "Enter");

		if( $GLOBALS['config']['debug_forms'] )
			$this->AddDebugForm($_POST);

		if( isset($_POST['table']) )
			$tables = explode("|", $_POST['table']);
		
		if( is_array($tables) )
		{
			foreach($tables as $table)
			{
				$id = $_POST[$table.'_orig_id'];
				$sets='';
				foreach($_POST as $key=>$val) 
				{
					$chkKey = explode("|",$key);
					if( $chkKey[0]==$table && strcmp($chkKey[1],"id") ) 
					{
						if( $_POST['orig_'.$chkKey[1]] != $val ) 
						{
							// debugging: printf("<tr><td><strong>chkKey[1]:</strong> %s</td><td><strong>key:</strong> %s</td><td><strong>val:</strong> %s</td>", $chkKey[1], $key, $val);
							if( empty($sets) )
								$sets .= sprintf("%s = '%s'", $chkKey[1], addslashes($val));
							else
								$sets .= sprintf(", %s = '%s'", $chkKey[1], addslashes($val));
						}
					}
				} // foreach(table)
				
				if( !empty($sets) ) 
				{
					$sql = sprintf("UPDATE %s.%s SET %s WHERE id = %s;", $GLOBALS['db_name'], $table, $sets, $id);
					$rows = $this->eq2db->RunQuery(UPDATE, $_POST['table'], $_POST['object'], $sql);
					if( $rows )
						$this->AddStatus( sprintf("%s: %s row%s affected<br />", $table, $rows, ( $rows==1 )?  "" : "s") );
				}
				
			} // foreach(tables)
		}
		
		if( $GLOBALS['config']['debug'] )
			$this->AddDebugFunction(__FUNCTION__, "Exit");
			
	}
	
	
	public static function SetPageTitleData()
	{
		$title = sprintf("%s", $GLOBALS['config']['app_short_name']);

		$Page = isset($_GET['page']) ? $_GET['page'] : null;
		$type = isset($_GET['type']) ? $_GET['type'] : null;
		switch($Page)
		{
			case "admin"			: $title .= " - Site Administration"; break;
			case "characters"		: $title .= " - Characters Editor"; break;
			case "items"			: $title .= " - Items Editor"; break;
			case "quests"			: $title .= " - Quests Editor"; break;

			case "scripts"		:
				switch($type)
				{
					case "quest":
						$title .= " - Quest Editor"; 
						break;
					case "spawn":
						$title .= " - Spawn Editor"; 
						break;
					case "spell":
						$title .= " - Spell Editor"; 
						break;
					case "zone":
						$title .= " - Zone Editor"; 
						break;
					default:
						$title .= " - Scripts Editor"; 
						break;
				}
				break;

			case "spawns"			: $title .= " - Spawns Editor"; break;
			case "spells"			: $title .= " - Spells Editor"; break;
			case "server"			: $title .= " - Server Settings"; break;
			case "zones"			: $title .= " - Zones Editor"; break;
			case "help"				: $title .= " - Editor Help"; break;
			case "editor"			: $title .= " - News Editor"; break;
			case "inactive"			: $title .= " - Inactive News"; break;
			case "archive"			: $title .= " - Archive News"; break;
			default					: $title .= " - Project News";
		}
		return $title;
	}

	
	/*
		Function: LoadScript()
		Purpise	: Loads a LUA script into the editor based on the full path of $script
		Syntax	: $eq2->LoadScript($script)
	*/
	public function LoadScript($script)
	{
		if( empty($script) ) 
			return "Must provide a script path/file!";

		if( empty($GLOBALS['config']['script_path']) )
		{
			$this->AddStatus("SCRIPT_PATH not set in config!");
			return;
		}
		else
		{
			$path = $GLOBALS['config']['script_path'];

			if( !preg_match("/.lua/",$script) ) 
				$script.=".lua";
			$file = $path . $script;
			
			if( file_exists($file) ) 
			{
				$line = '';
				if( !$f = fopen($file,'rb') ) 
				{
					$line = "Cannot open existing filename: $file";
				}
				while(!feof($f)) 
				{
					$line .= fgets($f);
				}
				fclose ($f); 
			}
		}
		return $line;		
	}


	/*
		Function: LoadScript()
		Purpise	: Loads a LUA script into the editor based on the full path of $script
		Syntax	: $eq2->LoadScript($script)
	*/
	public function CheckLUAScriptExists($script)
	{
		if( empty($script) ) 
			return "Must provide a script path/file!";

		if( empty($GLOBALS['config']['script_path']) )
		{
			$this->AddStatus("SCRIPTS_PATH not set in config!");
			return;
		}
		else
		{
			$path = $GLOBALS['config']['script_path'];

			if( !preg_match("/.lua/",$script) ) 
				$script.=".lua";
			$file = $path . $script;
			
			if( file_exists($file) ) {
				return true;
			}
		}
		return false;
	}
	
	
	public function SaveScript($scriptName, $script)
	{
		if( empty($scriptName) ) 
			$this->AddStatus("Cannot save a blank script path/file!");
			
		//$pattern[0] = "/\\\/i";
		//$replace[0] = "/";
		$LUAScript = $script; //preg_replace($pattern, $replace, $script);
		
		if( empty($GLOBALS['config']['script_path']) )
			die("SCRIPT_PATH constant not set in config.php");
		else
			$path = $GLOBALS['config']['script_path'];
	
		$file = $path . $scriptName;

		if( $GLOBALS['config']['readonly'] )
			$this->AddStatus("READ-ONLY MODE - ".$file." not saved!");
		else
		{
			// This was set to help debug, leaving it in but commented out in case we have more issues
			/*
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(E_ALL);
			*/

			if( !$f = fopen($file,'w+') ) 
				die("Cannot create filename: $file");
				
			if (!fwrite($f, $LUAScript)) 
				die("Cannot write to file ($file)");
				
			fclose($f);
			$this->AddStatus("Successfully saved " . $scriptName . ".");
		}
	}


	/*
		Function: RefreshPage()
		Purpise	: Forces meta refresh of page at $timer seconds
		Syntax	: $eq2->RefreshPage($timer)
	*/
	public function RefreshPage($timer)
	{
		if( $timer )
			printf('<meta http-equiv="refresh" content="%d;url=index.php?page=%s">', $timer, $_GET['page']);
		else
			printf('<meta http-equiv="refresh" content="5;url=index.php?page=%s">', $_GET['page']);

		$GLOBALS['refresh'] = 0; //reset back to no refresh
	}


	/*
		Function: ForMe()
		Purpise	: Just a quick way to dump arrays to the screen during development - no practical use otherwise.
		Syntax	: $eq2->ForMe($arr)
	*/
	public function ForMe($arr)
	{
		foreach($arr as $key=>$val)
		{
			if( is_array($val) )
				printf("%s:%s<br />", $key, print_r($val));
			else
				printf("%s:%s<br />", $key, $val);	
		}
	}
	
	public function IsStringNullOrEmpty($string) {
		return (!isset($string) || trim($string) === '');
	}

	public function PrintItemTier($tier) {
		$colorCode = "#FFFFFF";
		switch ($tier) {
			case 3:
				$colorCode = "#7DCEA0";
				break;
			case 4:
			case 5:
			case 6:
				$colorCode = "#85C1E9";
				break;
			case 7:
			case 8:
				$colorCode = "#F5CBA7";
				break;
			case 9:
			case 10:
			case 11:
				$colorCode = "#F1948A";
				break;
			case 12:
				$colorCode = "#D2B4DE";
				break;
		}
		echo("<span style=\"background-color: #000000; color: " . $colorCode .";\">");
		echo($this->eq2ItemTiers[$tier]);
		echo("</span>");
	}
}
?>