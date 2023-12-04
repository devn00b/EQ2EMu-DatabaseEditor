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

/*
	Function: Class eq2Screens
	Purpose	: Common UI functions used throughout the application
*/
class eq2Screens
{
	// Screen globals
	public $MainTitle;
	public $MainWidth;
	public $MainClass;
	public $SectionTitle;
	public $SectionWidth;
	public $SectionClass;

	
	public $DebugGeneral;
	public $DebugData;
	public $DebugFunctions;
	public $DebugForms;
	public $items_array;
	
	public function __construct() 
	{
	}


	public function DisplayDebug()
	{
		?>
		<table class="debug">
			<?php if( $GLOBALS['config']['debug'] ) { ?>
			<tr height="30" valign="bottom">
				<td><strong>DEBUG General:</strong></td>
			</tr>
			<tr>
				<td><?= $this->DebugGeneral ?></td>
			</tr>
			<?php } ?>
			<?php if( $GLOBALS['config']['debug_func'] ) { ?>
			<tr height="30" valign="bottom">
				<td><strong>DEBUG Functions:</strong></td>
			</tr>
			<tr>
				<td><?= $this->DebugFunctions ?></td>
			</tr>
			<?php } ?>
			<?php if( $GLOBALS['config']['debug_forms'] ) { ?>
			<tr height="30" valign="bottom">
				<td><strong>DEBUG Form Data:</strong></td>
			</tr>
			<tr>
				<td><?= $this->DebugForms ?></td>
			</tr>
			<?php } ?>
			<?php if( $GLOBALS['config']['debug_query'] ) { ?>
			<tr height="30" valign="bottom">
				<td><strong>DEBUG Queries:</strong></td>
			</tr>
			<tr>
				<td><?= $this->eq2db->DebugQueries ?></td>
			</tr>
			<?php } ?>
			<?php if( $GLOBALS['config']['debug_data'] ) { ?>
			<tr height="30" valign="bottom">
				<td><strong>DEBUG Data:</strong></td>
			</tr>
			<tr>
				<td><?= $this->DebugData ?></td>
			</tr>
			<?php } ?>
		</table>
		<?php
	}


	public function DisplayStatus()
	{
		?>
		<table class="warning">
			<tr>
				<td><strong>Status:</strong> <?= $this->Status ?></td>
			</tr>
		</table>
		<?php
	}


	/*
		Function: DisplaySiteText()
		Purpose	: Displays the various eq2editor.site_text pages
		Params	: $type is site_text.type, and $subtype is site_text.subtype (for different levels of news/text/help
	*/
	public function DisplaySiteText($type, $subtype)
	{
		?>
		<table class="site_text">
		<?php 
			$row = $this->eq2db->GetSiteText($type, $subtype);
			if( is_array($row) )
			{
				foreach($row as $data)
				{
		?>
					<tr style="background-color:#ccc;">
						<td>
							<div style="float:left; font-weight:bold; font-size:13px;"><?= $data['title'] ?></div>
							<div style="float:right">by <?= $data['username'] ?> on <?= date('Y.m.d', $data['created_date']) ?></div>
						</td>
					</tr>
					<tr>
						<td style="border:1px #999999 solid;"><p><?= $data['description'] ?></p></td>
					</tr>
		<?php
				}
			}
			else
			{
				$this->AddStatus("No text found.");
			}
		?>
		</table>
		<div id="EditorStatus">
		<?php 
			if( !empty($this->Status) ) $this->DisplayStatus(); 
		?>
		</div>
		<?php
	}
	
	
	public function DrawInputTextBox($data, $field, $class, $readonly = 0, $tooltip = 0, $cols = 0)
	{
		printf('<td%s>', $cols > 1 ? ' colspan="' . $cols .'"' : '');
		printf('<input type="text" name="%s|%s" value="%s" class="%s"%s />', $_GET['type'], $field, $data[$field], $class, ( $readonly ) ? ' style="background-color:#ddd;" readonly' : '');
		printf('<input type="hidden" name="orig_%s" value="%s" />', $field, $data[$field]);
		if( $tooltip )
		{
			//$tip = $this->items_array[$data['item_id']];  // using a cached array seems to prevent the page from loading
			//$this->AddDebugGeneral("Item Array", $tip);
			printf('&nbsp;<a href="index.php?page=items&type=edit&id=%s" title="%s" target="_blank"><u>?</u></a>&nbsp;', $data['item_id'],  $this->eq2db->GetItemNameByID($data['item_id']));
		}
		print('</td>');
	}


	public function DrawClassSelector($data, $field, $class, $readonly = 0)
	{
		print('<td>');
		printf('<select name="%s|%s" class="%s"%s>', $_GET['type'], $field, $class, ( $readonly ) ? ' style="background-color:#ddd;" disabled' : '');
		foreach($this->eq2Classes as $class_id=>$class_name)
		{
			$selected = ( $class_id == $data[$field] ) ? " selected" : "";
			printf('<option value="%s"%s>%s</option>', $class_id, $selected, $class_name);
		}
		printf('</select>');
		printf('<input type="hidden" name="orig_%s" value="%s" />', $field, $data[$field]);
		print('</td>');
	}
	
	
	public function DrawRaceSelector($data, $field, $class, $readonly = 0)
	{
		print('<td>');
		printf('<select name="%s|%s" class="%s"%s>', $_GET['type'], $field, $class, ( $readonly ) ? ' style="background-color:#ddd;" disabled' : '');
		foreach($this->eq2PlayerRaces as $race_id=>$race_name)
		{
			$selected = ( $race_id == $data[$field] ) ? " selected" : "";
			printf('<option value="%s"%s>%s</option>', $race_id, $selected, $race_name);
		}
		printf('</select>');
		printf('<input type="hidden" name="orig_%s" value="%s" />', $field, $data[$field]);
		print('</td>');
	}
	

	public function DrawEquipTypeSelector($data, $field, $class, $readonly = 0)
	{
		print('<td>');
		printf('<select name="%s|%s" class="%s"%s>', $_GET['type'], $field, $class, ( $readonly ) ? ' style="background-color:#ddd;" disabled' : '');
		printf('<option%s>EQUIPPED</option>'		, ( $data['type']=="EQUIPPED" ) ? " selected" : "");
		printf('<option%s>NOT-EQUIPPED</option>', ( $data['type']=="NOT-EQUIPPED" ) ? " selected" : "");
		printf('</select>');
		printf('<input type="hidden" name="orig_%s" value="%s" />', $field, $data[$field]);
		print('</td>');
	}
	
	
	public function DrawCheckbox($data, $field, $class, $readonly = 0, $checked = 0)
	{
		print('<td>');
		printf('<input type="checkbox" name="%s|%s" value="%s" class="%s"%s%s />', $_GET['type'], $field, $data[$field], $class, /*( $data['attuned'] )*/ $checked ? " checked" : "", ( $readonly ) ? ' style="background-color:#ddd;" readonly' : '');
		printf('<input type="hidden" name="orig_%s" value="%s" />', $field, $data[$field]);
		print('</td>');
	}
	
	
	/*
		Function: EQ2ClassSelector()
		Purpose	: Builds combo box for EQ2 Classes using name="table|field" for update scripts.
				: Common UI component for use in multiple table updates
		Syntax	: $eq2->EQ2ClassSelector('table_name', 'field_name', $current_class_id)
		Example	: $eq2->EQ2ClassSelector("characters", "class", 0);
				: Selects class 0 for current 'characters' table, 'class' field
		Note	: A class_id value of 255 = ALL Classes, for those tables on class restriction
	*/
	public function EQ2ClassSelector($table, $field, $class_id)
	{
		printf('<select name="%s|%s">', $table, $field);
		foreach($this->eq2PlayerClasses as $key=>$val)
		{
			$selected = ( $key == $class_id ) ? " selected" : "";
			printf('<option value="%s"%s>%s</option>', $key, $selected, $val);
		}
		print('</select>');
		printf('<input type="hidden" name="orig_%s" value="%s" />', $field, $class_id);
	}
	
	
	/*
		Function: EQ2PlayerClassSelector()
		Purpose	: Builds combo box for EQ2 *Player* Classes using name="table|field" for update scripts.
				: Common UI component for use in multiple table updates
		Syntax	: $eq2->EQ2PlayerClassSelector('table_name', 'field_name', $current_class_id)
		Example	: $eq2->EQ2PlayerClassSelector("characters", "class", 0);
				: Selects class 0 for current 'characters' table, 'class' field
		Note	: A class_id value of 255 = ALL Classes, for those tables on class restriction
	*/
	public function EQ2PlayerClassSelector($table, $field, $class_id)
	{
		printf('<select name="%s|%s">', $table, $field);
		foreach($this->eq2PlayerClasses as $key=>$val)
		{
			$selected = ( $key == $class_id ) ? " selected" : "";
			printf('<option value="%s"%s>%s</option>', $key, $selected, ucfirst(strtolower($val)));
		}
		print('</select>');
		printf('<input type="hidden" name="orig_%s" value="%s" />', $field, $class_id);
	}
	
	
	/*
		Function: EQ2PlayerRaceSelector()
		Purpose	: Builds combo box for EQ2 Player Races using name="table|field" for update scripts.
				: Common UI component for use in multiple table updates
		Syntax	: $eq2->EQ2PlayerRaceSelector('table_name', 'field_name', $current_race_id)
		Example	: $eq2->EQ2PlayerRaceSelector("characters", "race", 0);
				: Selects race 0 for current 'characters' table, 'race' field
		Note	: A race_id value of 255 = ALL Races, for those tables on race restriction
	*/
	public function EQ2PlayerRaceSelector($table, $field, $race_id)
	{
		printf('<select name="%s|%s">', $table, $field);
		foreach($this->eq2PlayerRaces as $key=>$val)
		{
			$selected = ( $key == $race_id ) ? " selected" : "";
			printf('<option value="%s"%s>%s</option>', $key, $selected, ucfirst(strtolower($val)));
		}
		print('</select>');
		printf('<input type="hidden" name="orig_%s" value="%s" />', $field, $race_id);
	}
	
	
	/*
		Function: EQ2ZoneSelector()
		Purpose	: Builds combo box for EQ2 Zones using name="table|field" for update scripts.
		Syntax	: $eq2->EQ2ZoneSelector('table_name', 'field_name', $current_zone_id)
		Example	: $eq2->EQ2ZoneSelector("characters", "current_zone_id", 253);
				: Selects zone 253 (Queen's Colony) for current 'characters' table, 'current_zone_id' field
	*/
	public function EQ2ZoneSelector($table, $field, $zone_id, $pop = 0)
	{
		$zone_data = $this->eq2db->GetZoneIDName($pop);
		if( is_array($zone_data) )
		{
			printf('<select name="%s|%s">', $table, $field);
			print('<option value="0">Pick a Zone</option>');
			foreach($zone_data as $zone)
			{
				$selected = ( $zone['id'] == $zone_id ) ? " selected" : "";
				printf('<option value="%s"%s>%s</option>', $zone['id'], $selected, $zone['name']);
			}
			print('</select>');
			printf('<input type="hidden" name="orig_%s" value="%s" />', $field, $zone_id);
		}
	}
	
	
	/*
		Function: EQ2StartingCitySelector()
		Purpose	: Builds combo box for EQ2 Starting City zones using name="table|field" for update scripts.
		Syntax	: $eq2->EQ2StartingCitySelector('table_name', 'field_name', $starting_city)
		Example	: $eq2->EQ2StartingCitySelector("characters", "starting_city", 253);
				: Selects zone 253 (Queen's Colony) for current 'characters' table, 'starting_city' field
	*/
	public function EQ2StartingCitySelector($table, $field, $city_id)
	{
		$zone_data = $this->eq2db->GetStartingCities();
		if( is_array($zone_data) )
		{
			printf('<select name="%s|%s">', $table, $field);
			print('<option value="0">Pick a City</option>');
			foreach($zone_data as $zone)
			{
				$selected = ( $zone['start_zone'] == $city_id ) ? " selected" : "";
				printf('<option value="%s"%s>%s</option>', $zone['start_zone'], $selected, $zone['name']);
			}
			print('</select>');
			printf('<input type="hidden" name="orig_%s" value="%s" />', $field, $city_id);
		}
	}
	
	
	/*
		Function: EQ2ExpansionSelector()
		Purpose	: Builds combo box for EQ2 Expansions using name="table|field" for update scripts.
		Syntax	: $eq2->EQ2ExpansionSelector('table_name', 'field_name', $current_expansion)
		Example	: $eq2->EQ2ExpansionSelector("zones", "expansion_id", 1);
				: Selects expansion 1 (Classic) for current 'zones' table, 'expansion_id' field
	*/
	public function EQ2ExpansionSelector($table, $field, $expansion_id)
	{
		$expansion_data = $this->eq2db->GetExpansionData();
		if( is_array($expansion_data) )
		{
			printf('<select name="%s|%s">', $table, $field);
			print('<option value="0">Pick an Expansion</option>');
			foreach($expansion_data as $expansion)
			{
				$selected = ( $expansion['id'] == $expansion_id ) ? " selected" : "";
				printf('<option value="%s"%s>%s</option>', $expansion['id'], $selected, $expansion['expansion']);
			}
			print('</select>');
			printf('<input type="hidden" name="orig_%s" value="%s" />', $field, $expansion_id);
		}
	}
	
	
	public function ScriptEditor($ScriptPath)
	{
		?>
		<div id="LuaEditor" style="margin: 0; width: 100%; height: 100%"><?= $this->LoadScript($ScriptPath) ?></div>
		<input type="hidden" name="script" id="LuaScript" />
		<script type="text/javascript" src="ace/src-noconflict/ace.js" charset="utf-8"></script>
		<script type="text/javascript" src="ace/src-noconflict/ext-language_tools.js"></script>
		<script>
			var lang_tools = ace.require("ace/ext/language_tools");
			var editor = ace.edit("LuaEditor");
			editor.setTheme("ace/theme/textmate");
			editor.session.setMode("ace/mode/lua"); 
			lang_tools.setCompleters([lang_tools.snippetCompleter, lang_tools.keyWordCompleter]);
			editor.setOptions({
				enableLiveAutocompletion: true
			});
		</script>
		<?php
	}
}

?>
