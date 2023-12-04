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

class eq2Zones
{
	
	
	public function __construct()
	{
		global $eq2;
		include_once("eq2ZonesDB.class.php");
		// open zones DB instance
		$this->db = new eq2ZonesDB();
		$user_role = intval($eq2->userdata['role']);
	}


	public function Start()
	{
		global $eq2;
		global $page_lc;
		global $type;

		print('<table cellspacing="0" id="main-body" class="main">');

		// start left menu
		printf('<tr><td class="sidebar" nowrap="nowrap"><strong>Options:</strong><br />');
		
		// nav stuff here
		printf('<br /><li><a href="%s">Reload Page</a><br />', $eq2->PageLink);
		if( isset($_GET['id']) ) 
			printf('<li><a href="%s">Back</a><br />', $eq2->BackLink);
		else
			printf('<li><br />');
		
		// Help Links / Stats Details
		printf('<br /><strong>Help:</strong><br />&nbsp;');
		printf('<br /><strong>Stats:</strong><br />&nbsp;');
		
		// start main page
		printf('</td><td class="page-text"><div id="PageTitle">%s</div>', $eq2->PageTitle);
		switch($page_lc)
		{
			case "help": 
				// use common (eq2Functions) class to display non-specific data
				$eq2->DisplaySiteText($page_lc, $_GET['cat']); 
				break;
				
			default: 
				switch($type)
				{
					case "edit"				: $this->ZoneEditor($_GET['id']); break;
					case "summary"			: break;
					default					: $this->ZoneLookup(); break;
				}
				break;
		}
		
		// end page
		print('</td></tr></table>');
	}


	/*
		Function: ZoneLookup()
		Purpose	: Search, load, edit zone data
	*/	
	public function ZoneLookup() 
	{
		global $eq2;
		$ret = false;
		
		$show_unpopulated = ( empty($_GET['a']) ) ? 1 : 0;
		?>
		<script language="javascript">
		<!--
		//Called from keyup on the search textbox.
		//Starts the AJAX request.
		function ZoneLookupAJAX() {
			if (searchReq.readyState == 4 || searchReq.readyState == 0) {
				var str = escape(document.getElementById('txtSearch').value);
				searchReq.open("GET", 'zones/eq2ZonesAjax.php?type=lookup&search=' + str, true);
				searchReq.onreadystatechange = handleSearchSuggest; 
				searchReq.send(null);
			}		
		}
		-->
		</script>
		<div id="SearchControls">
			<table>
				<tr>
					<td class="label">Pick:</td>
					<td>
						<select name="zoneID" onchange="dosub(this.options[this.selectedIndex].value)">
							<option value="index.php?page=zones&type=edit">Pick a Zone</option>
							<?= $this->db->GetZoneOptions($show_unpopulated); ?>
						</select>
						<input type="checkbox" name="show" value="index.php?page=zones<?= ( $show_unpopulated ) ? "&a=1" : "" ?>" onchange="dosub(this.value)"<?= ( !$show_unpopulated ) ? " checked" : "" ?> />
						Show All (unpopulated) Zones
					</td>
				</tr>
				<form action="index.php?page=zones" id="frmSearch" method="post">
				<tr>
					<td class="label">Lookup:</td>
					<td>
						<input type="text" id="txtSearch" name="txtSearch" alt="Search Criteria" onkeyup="ZoneLookupAJAX();" autocomplete="off" class="box" value="<?= isset($_POST['txtSearch']) ? stripslashes($_POST['txtSearch']) : '' ?>" onclick="this.value='';" />
						<input type="submit" id="cmdSearch" name="cmdSearch" value="Search" alt="Run Search" class="submit" />
						<input type="button" value="Clear" class="submit" onclick="dosub('index.php?page=zones');" />
						<?php if( (M_ZONES & $eq2->user_role) || (G_ADMIN & $eq2->user_role) ) { ?>
						<input type="button" value="Add" class="submit" onclick="dosub('index.php?page=zones&id=add');" />
						<?php } ?>
						<input type="hidden" name="cmd" value="ZoneByName" />
						<div id="search_suggest">
						</div>
					</td>
				</tr>
				</form>
			</table>
			<script language="JavaScript">
			<!--
			document.getElementById('txtSearch').focus = true;
			//-->
			</script>
		</div><!-- End SearchAll -->
		<br />
		<?php
		
		// Check if searchText was used to find a Zone
		if( isset($_POST['txtSearch']) && strlen($_POST['txtSearch']) > 0 )
		{
			$arr = $this->db->GetZoneByName($_POST['txtSearch']);
			if( is_array($arr) )
			{
				$this->DisplayZoneGrid($arr, 1);
			}
			else
			{
				$eq2->AddStatus('No zones match your search.');
			}
			$ret = true;
		}
		else if( isset($_POST['cmd']) && $_POST['cmd'] == "ZoneByName" )
		{
			$arr = $this->db->GetZoneByName("all");
			if( is_array($arr) )
			{
				$this->DisplayZoneGrid($arr);
			}
			else
			{
				$eq2->AddStatus('No zones match your search.');
			}
			$ret = true;
		}
		// If a zone selected, display ZoneEditor
		if( isset($_GET['id']) && $_GET['id'] > 0 && $_GET['id'] != 'add' )
		{
			$this->ZoneEditor($_GET['id']);
		}
		else if( isset($_GET['id']) && $_GET['id'] == 'add' )
		{
			$this->ZoneAdd();
		}
		?>
		<div id="EditorStatus">
			<?php 
			if( !empty($eq2->Status) ) $eq2->DisplayStatus(); 
			?>
		</div>
		<?php
		// is the return value ever really used?
		if( $ret ) 
			return;
	}


	/*
		Function: 
		Purpose	:	
		Params	: 
	*/
	private function ZoneEditor($id)
	{
		global $eq2;

		// Build the Tab menu
		$current_tab_idx = ( isset($_GET['tab']) ) ? $_GET['tab'] : 'general';
		$tab_array = array(
			'general'			=> 'General',
			'script'			=> 'Script',
			'locations'		=> 'Locations',
			'revive'			=> 'Revive Points',
			'list'				=> 'Zone List'
		);
		$eq2->TabGenerator2($current_tab_idx, $tab_array);
		/*
			General		: General Zone data (zones table)
			Script		: Zone Script editor
			Locations	: Zone (popup) Locations
			Revive		: Revive Points in a zone
			List			: Complete Zone List
		*/

		switch($current_tab_idx)
		{
			case "script"			: $this->Zone_Script(); break;
			case "locations"		: $this->Zone_Locations(); break;
			case "revive"			: $this->Zone_RevivePoints(); break;
			case "list"				: $this->Zone_List(); break;
			case "general"			:
			default					: $this->Zone_General(); break;
		}

	}
	
	
	private function ZoneAdd()
	{
		global $eq2;
		
		//Default zone vars
		$zone = array("instance_type"=>"NONE", "zone_type"=>"");
		// Build Toggles array()
		$zone_toggles = array('always_loaded', 'city_zone', 'force_group_to_zone');

		// Perform updates here
		if( isset($_POST['cmd']) )
		{
			// Loop through Toggles and see if any have been unset
			foreach( $zone_toggles as $toggles )
			{
				$toggle_settings	= sprintf('zones|%s', $toggles); // HACK!
				if( empty($_POST[$toggle_settings]) )
					$_POST[$toggle_settings] = 0;
			}
			
			switch($_POST['cmd']) 
			{
				case "Insert": $eq2->ProcessInsert(NULL); break;
			}
		}
		?>
		<br />&nbsp;
		<!-- Start ZoneEditor -->
		<div id="Editor">
			<table cellspacing="0" width="900">
			<form method="post">
				<tr>
					<td width="220" class="Title">&nbsp;</td>
					<td class="Title" align="center">Adding Zone</td>
					<td width="220" class="Title">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2" valign="top">
						<table cellspacing="0" width="600">
							<tr>
								<td colspan="2" class="SectionTitle">Zone Info</td>
							</tr>
							<tr>
								<td class="LabelRight">id:</td>
								<td><strong>new</strong></td>
							</tr>
							<tr>
								<td class="LabelRight">expansion_id:</td>
								<td><?php $eq2->EQ2ExpansionSelector("zones", "expansion_id", 0) ?></td>
							</tr>
							<tr>
								<td class="LabelRight">name:</td>
								<td>
									<input type="text" name="zones|name" value="" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">file:</td>
								<td>
									<input type="text" name="zones|file" value="" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">description:</td>
								<td>
									<input type="text" name="zones|description" value="" class="longtext" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">safe_xyz+heading:</td>
								<td>
									<input type="text" name="zones|safe_x" value="0" class="medium" />
									<input type="text" name="zones|safe_y" value="0" class="medium" />
									<input type="text" name="zones|safe_z" value="0" class="medium" />
									<input type="text" name="zones|safe_heading" value="0" class="medium" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">underworld:</td>
								<td>
									<input type="text" name="zone|underworld" value="0" class="large" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">xp_modifier:</td>
								<td>
									<input type="text" name="zones|xp_modifier" value="0" class="small" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">min_recommended:</td>
								<td>
									<input type="text" name="zones|min_recommended" value="0" class="small" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">max_recommended:</td>
								<td>
									<input type="text" name="zones|max_recommended" value="0" class="small" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">zone_type:</td>
								<td>
									<select name="zones|zone_type" class="yesno">
										<option></option>
										<option<?php if( $zone['zone_type']=="Indoor" ) print(' selected') ?>>Indoor</option>
										<option<?php if( $zone['zone_type']=="Outdoor" ) print(' selected') ?>>Outdoor</option>
									</select>
								</td>
							</tr>
							<tr>
								<td class="LabelRight">min_status:</td>
								<td>
									<input type="text" name="zones|min_status" value="0" class="small" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">min_level:</td>
								<td>
									<input type="text" name="zones|min_level" value="0" class="small" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">max_level:</td>
								<td>
									<input type="text" name="zones|max_level" value="0" class="small" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">starting_city:</td>
								<td><? $eq2->EQ2StartingCitySelector("zones", "start_zone", 0, 0) ?></td>
							</tr>
							<tr>
								<td class="LabelRight">instance_type:</td>
								<td>
									<?php $this->DisplayInstanceTypeDropdown($zone); ?>
								</td>
							</tr>
							<tr>
								<td class="LabelRight">default_reenter_time:</td>
								<td>
									<input type="text" name="zones|default_reenter_time" value="0" class="medium" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">default_reset_time:</td>
								<td>
									<input type="text" name="zones|default_reset_time" value="0" class="medium" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">default_lockout_time:</td>
								<td>
									<input type="text" name="zones|default_lockout_time" value="0" class="medium" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">shutdown_timer:</td>
								<td>
									<input type="text" name="zones|shutdown_timer" value="0" class="medium" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">zone_motd:</td>
								<td>
									<input type="text" name="zones|zone_motd" value="" class="longtext" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">login_checksum:</td>
								<td>
									<input type="text" name="zones|login_checksum" value="0" class="large" onclick="this.value='0';" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">lua_script:</td>
								<td>
									<input type="text" name="zones|lua_script" value="" class="longtext" />
								</td>
							</tr>
							<tr>
								<td colspan="2" align="center">Format: {ZoneScripts/zone_name.lua} Ex. <strong>ZoneScripts/ZoneName.lua</strong></td>
							</tr>
						</table>
					</td>
					<td valign="top">
						<table cellspacing="0" width="300">
							<tr>
								<td colspan="2" class="SectionTitle">Zone Toggles</td>
							</tr>
							<?php
							foreach($zone_toggles as $toggles)
							{
								$checked = ( $zone[$toggles] > 0 ) ? ' checked' : '';
								printf("<tr>\n<td width=\"50%%\" class=\"LabelRight\">%s:</td>\n<td><input type=\"checkbox\" name=\"zones|%s\" value=\"1\" class=\"chkbox\"%s />\n<input type=\"hidden\" name=\"orig_%s\" value=\"%s\" /></td>\n</tr>\n",
									$toggles, $toggles, $checked, $toggles, intval($zone[$toggles]));
							}
							?>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="3" align="center">
						<input type="submit" name="cmd" value="Insert" class="submit" />
						<input type="hidden" name="table" value="zones" />
						<input type="hidden" name="object" value="New Zone" />
					</td>
				</tr>
			</form>
			</table>
		</div>
		<!-- End zoneEditor -->
		<?php
	}


	/*
		Function: 
		Purpose	:	
		Params	: 
	*/
	private function DisplayZoneGrid($zones)
	{
		global $eq2;
		
		?>
		<br />
		<div id="SelectGrid">
		<table cellspacing="0" id="SelectGrid" border="0">
			<tr>
				<td colspan="6" align="center" class="title">Zones</td>
			</tr>
			<tr>
				<th width="5%">&nbsp;</th>
				<th width="5%">ID</th>
				<th width="15%">Name</th>
				<th width="15%">File</th>
				<th width="40%">Description</th>
				<th width="20%">LUA</th>
			</tr>
			<?php
			
			$i = 0;
			foreach($zones as $data)
			{
				$RowColor = ( $i % 2 ) ? "row1" : "row2";
				//$zone_id = ( isset($_GET['zone']) ) ? $_GET['zone'] : $this->db->GetZoneIDFromLUAScript("Quests", $data['lua_script']);
			?>
			<tr class="<?= $RowColor ?>">
				<td class="detail">&nbsp;[&nbsp;<a href="?page=zones&type=edit&a=1&id=<?= $data['id'] ?>">Edit</a>&nbsp;]</td>
				<td class="detail" align="right">&nbsp;<?= $data['id'] ?>&nbsp;</td>
				<td class="detail">&nbsp;<?= $data['name'] ?></td>
				<td class="detail">&nbsp;<?= $data['file'] ?></td>
				<td class="detail">&nbsp;<?= $data['description'] ?></td>
				<td class="detail">&nbsp;<?= $data['lua_script'] ?></td>
			</tr>
			<?php
				$i++;
			}
		?>
		</table>
		</div><!-- End SelectGrid -->
		<?php
		$eq2->AddStatus($i . " records found.");
	}


	private function Zone_General()
	{
		global $eq2;
		
		// Build Toggles array()
		$zone_toggles = array('always_loaded', 'city_zone', 'force_group_to_zone');

		// Perform updates here
		if( isset($_POST['cmd']) )
		{
			// Loop through Toggles and see if any have been unset
			foreach( $zone_toggles as $toggles )
			{
				$toggle_settings	= sprintf('zones|%s', $toggles); // HACK!
				if( empty($_POST[$toggle_settings]) )
					$_POST[$toggle_settings] = 0;
			}
			
			switch($_POST['cmd']) 
			{
				case "Update": $eq2->ProcessUpdate(); break;
				case "Delete": $eq2->ProcessDelete(); break;
			}
		}

		// Load Zone Info
		$zone = $this->db->GetZoneByID();
		if( !is_array($zone) )
		{
			$eq2->AddStatus("No `zone` data found.");
		}
		?>
		<!-- Start ZoneEditor -->
		<div id="Editor">
			<table cellspacing="0" width="900">
			<form method="post">
				<tr>
					<td width="220" class="Title">&nbsp;</td>
					<td class="Title" align="center">Editing Zone: <?= $zone['description'] ?></td>
					<td width="220" class="Title">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2" valign="top">
						<table cellspacing="0" width="600">
							<tr>
								<td colspan="2" class="SectionTitle">Zone Info</td>
							</tr>
							<tr>
								<td class="LabelRight">id:</td>
								<td>
									<input type="text" name="zones|id" value="<?= $zone['id'] ?>" readonly />
									<input type="hidden" name="orig_id" value="<?= $zone['id'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">expansion_id:</td>
								<td><?php $eq2->EQ2ExpansionSelector("zones", "expansion_id", $zone['expansion_id']) ?></td>
							</tr>
							<tr>
								<td class="LabelRight">name:</td>
								<td>
									<input type="text" name="zones|name" value="<?= $zone['name'] ?>" />
									<input type="hidden" name="orig_name" value="<?= $zone['name'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">file:</td>
								<td>
									<input type="text" name="zones|file" value="<?= $zone['file'] ?>" />
									<input type="hidden" name="orig_file" value="<?= $zone['file'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">description:</td>
								<td>
									<input type="text" name="zones|description" value="<?= $zone['description'] ?>" class="longtext" />
									<input type="hidden" name="orig_description" value="<?= $zone['description'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">safe_xyz+heading:</td>
								<td>
									<input type="text" name="zones|safe_x" value="<?= $zone['safe_x'] ?>" class="medium" />
									<input type="hidden" name="orig_safe_x" value="<?= $zone['safe_x'] ?>" />
									<input type="text" name="zones|safe_y" value="<?= $zone['safe_y'] ?>" class="medium" />
									<input type="hidden" name="orig_safe_y" value="<?= $zone['safe_y'] ?>" />
									<input type="text" name="zones|safe_z" value="<?= $zone['safe_z'] ?>" class="medium" />
									<input type="hidden" name="orig_safe_z" value="<?= $zone['safe_z'] ?>" />
									<input type="text" name="zones|safe_heading" value="<?= $zone['safe_heading'] ?>" class="medium" />
									<input type="hidden" name="orig_safe_heading" value="<?= $zone['safe_heading'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">underworld:</td>
								<td>
									<input type="text" name="zone|underworld" value="<?= $zone['underworld'] ?>" class="large" />
									<input type="hidden" name="orig_underworld" value="<?= $zone['underworld'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">xp_modifier:</td>
								<td>
									<input type="text" name="zones|xp_modifier" value="<?= $zone['xp_modifier'] ?>" class="small" />
									<input type="hidden" name="orig_xp_modifier" value="<?= $zone['xp_modifier'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">min_recommended:</td>
								<td>
									<input type="text" name="zones|min_recommended" value="<?= $zone['min_recommended'] ?>" class="small" />
									<input type="hidden" name="orig_min_recommended" value="<?= $zone['min_recommended'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">max_recommended:</td>
								<td>
									<input type="text" name="zones|max_recommended" value="<?= $zone['max_recommended'] ?>" class="small" />
									<input type="hidden" name="orig_max_recommended" value="<?= $zone['max_recommended'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">zone_type:</td>
								<td>
									<select name="zones|zone_type" class="yesno">
										<option></option>
										<option<?php if( $zone['zone_type']=="Indoor" ) print(' selected') ?>>Indoor</option>
										<option<?php if( $zone['zone_type']=="Outdoor" ) print(' selected') ?>>Outdoor</option>
									</select>
									<input type="hidden" name="orig_zone_type" value="<?= $zone['zone_type'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">min_status:</td>
								<td>
									<input type="text" name="zones|min_status" value="<?= $zone['min_status'] ?>" class="small" />
									<input type="hidden" name="orig_min_status" value="<?= $zone['min_status'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">min_level:</td>
								<td>
									<input type="text" name="zones|min_level" value="<?= $zone['min_level'] ?>" class="small" />
									<input type="hidden" name="orig_min_level" value="<?= $zone['min_level'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">max_level:</td>
								<td>
									<input type="text" name="zones|max_level" value="<?= $zone['max_level'] ?>" class="small" />
									<input type="hidden" name="orig_max_level" value="<?= $zone['max_level'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">starting_city:</td>
								<td><?php $eq2->EQ2StartingCitySelector("zones", "start_zone", $zone['start_zone'], 0) ?></td>
							</tr>
							<tr>
								<td class="LabelRight">instance_type:</td>
								<td>
									<?php $this->DisplayInstanceTypeDropdown($zone); ?>
									<input type="hidden" name="orig_instance_type" value="<?php echo $zone['instance_type']?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">default_reenter_time:</td>
								<td>
									<input type="text" name="zones|default_reenter_time" value="<?= $zone['default_reenter_time'] ?>" class="medium />
									<input type="hidden" name="orig_default_reenter_time" value="<?= $zone['default_reenter_time'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">default_reset_time:</td>
								<td>
									<input type="text" name="zones|default_reset_time" value="<?= $zone['default_reset_time'] ?>" class="medium" />
									<input type="hidden" name="orig_default_reset_time" value="<?= $zone['default_reset_time'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">default_lockout_time:</td>
								<td>
									<input type="text" name="zones|default_lockout_time" value="<?= $zone['default_lockout_time'] ?>" class="medium" />
									<input type="hidden" name="orig_default_lockout_time" value="<?= $zone['default_lockout_time'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">shutdown_timer:</td>
								<td>
									<input type="text" name="zones|shutdown_timer" value="<?= $zone['shutdown_timer'] ?>" class="medium" />
									<input type="hidden" name="orig_shutdown_timer" value="<?= $zone['shutdown_timer'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">zone_motd:</td>
								<td>
									<input type="text" name="zones|zone_motd" value="<?= $zone['zone_motd'] ?>" class="longtext" />
									<input type="hidden" name="orig_zone_motd" value="<?= $zone['zone_motd'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">login_checksum:</td>
								<td>
									<input type="text" name="zones|login_checksum" value="<?= $zone['login_checksum'] ?>" class="large" onclick="this.value='0';" />
									<input type="hidden" name="orig_login_checksum" value="<?= $zone['login_checksum'] ?>" />
									Click field to reset Login Update
								</td>
							</tr>
							<tr>
								<td class="LabelRight">lua_script:</td>
								<td>
									<input type="text" name="zones|lua_script" value="<?= $zone['lua_script'] ?>" class="longtext" />
									<input type="hidden" name="orig_lua_script" value="<?= $zone['lua_script'] ?>" />
								</td>
							</tr>
							<tr>
								<td colspan="2" align="center">Format: {ZoneScripts/zone_name.lua} Ex. <strong>ZoneScripts/<?= $zone['name'] ?>.lua</strong></td>
							</tr>
						</table>
					</td>
					<td valign="top">
						<table cellspacing="0" width="300">
							<tr>
								<td colspan="2" class="SectionTitle">Zone Toggles</td>
							</tr>
							<?php
							foreach($zone_toggles as $toggles)
							{
								$checked = ( $zone[$toggles] > 0 ) ? ' checked' : '';
								printf("<tr>\n<td width=\"50%%\" class=\"LabelRight\">%s:</td>\n<td><input type=\"checkbox\" name=\"zones|%s\" value=\"1\" class=\"chkbox\"%s />\n<input type=\"hidden\" name=\"orig_%s\" value=\"%s\" /></td>\n</tr>\n",
									$toggles, $toggles, $checked, $toggles, intval($zone[$toggles]));
							}
							?>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="3" align="center">
						<input type="submit" name="cmd" value="Update" class="submit" />
						<input type="submit" name="cmd" value="Delete" class="submit" />
						<input type="hidden" name="table" value="zones" />
						<input type="hidden" name="object" value="<?= $zone['name'] ?>" />
					</td>
				</tr>
			</form>
			</table>
		</div>
		<!-- End zoneEditor -->
		<?php
	}
	
	
	/*
		Function: 
		Purpose	:	
		Params	: 
	*/
	private function Zone_Script()
	{
		global $eq2;
		
		$zone_info = $this->db->GetZoneScriptInfo($_GET['id']);
		$script = $zone_info['lua_script'];
		
		if ( isset($_POST['cmd']) )
		{
			switch ($_POST['cmd'])
			{
				case "Update"	:	$eq2->SaveScript($script, $_POST['script']);//$this->SaveScript($script, isset($_POST['ScriptText']) ? $_POST['ScriptText'] : ''); break;
			}
		}
		
		?>
		<div id="Editor">
		<form method="post">
		<table cellspacing="0" width="100%">
			<tr>
				<td width="220" class="Title">&nbsp;</td>
				<td class="Title" align="center">Editing: <?= $script ?></td>
				<td width="220" class="Title">&nbsp;</td>
			</tr>
			<tr>
				<td valign="top">
					<table cellspacing="0" width="240">
						<tr>
							<td class="SectionTitle">ZoneScript Info</td>
						</tr>
						<tr>
							<td class="Label">id:</td>
						</tr>
						<tr>
							<td class="Detail"><?= $zone_info['id'] ?></td>
						</tr>
						<tr>
							<td class="Label">name:</td>
						</tr>
						<tr>
							<td class="Detail"><?= $zone_info['name'] ?></td>
						</tr>
						<tr>
							<td class="Label">description:</td>
						</tr>
						<tr>
							<td class="Detail"><?= $zone_info['description'] ?></td>
						</tr>
						<tr>
							<td class="Label">lua_script:</td>
						</tr>
						<tr>
							<td class="Detail">
							<?php
							if( $script )
								print(preg_replace("/ZoneScripts\/(.*?)$/", "$1", $script));
							else
								print("None.");
							?>
							</td>
						</tr>
						<?php
						if( $script )
						{
						?>
						<tr>
							<td class="Section">Script also used in:</td>
						</tr>
						<tr>
							<td><?php $this->db->GetZoneScriptUsers($script) ?></td>
						</tr>
						<?php
						}
						?>
					</table>
				</td>
				<td colspan="2" valign="top">
					<?php
					if( $script )
					{
					?>
						<table cellspacing="0" width="99%">
							<tr>
								<td class="SectionTitle">Script Body</td>
							</tr>
							<tr>
								<td height="500px">
									<?= $eq2->ScriptEditor($script) ?>			
								</td>
							</tr>
						</table>
					<?php
					}
					else
						$eq2->AddStatus('You must first define a `lua_script` on the GENERAL tab.')
					?>
				</td>
			</tr>
			<tr>
				<td colspan="3" class="Submit">
					<input type="hidden" name="ScriptPath" value="<?= $script ?>" />
					<input type="submit" name="cmd" id = "save" value="Update" class="submit" />&nbsp;
					<input type="submit" name="cmd" value="Delete" class="submit" />
					<script>
						$(function(){
							$('#save').click(function () {
								var mysave = $('#LuaEditor').html();
								$('#LuaScript').val(editor.getValue());
							});
						});
					</script>
				</td>
			</tr>
		</table>
		</form>
		</div><!-- End ScriptEditor -->
		<?php
	}


	/*
		Function: 
		Purpose	:	
		Params	: 
	*/
	private function Zone_Locations()
	{
		global $eq2;

		// Perform updates here
		if( isset($_POST['cmd']) )
		{
			switch($_POST['cmd']) 
			{
				case "Update": $eq2->ProcessUpdate(); break;
				case "Delete": $eq2->ProcessDelete(); break;
				case "Insert": $eq2->ProcessInsert(); break;
			}
		}

		$location_data = $this->db->GetLocationData();
		$zone_name = $eq2->eq2db->GetZoneNameByID($_GET['id']);
		?>
		<div id="Editor">
		<table cellspacing="0" width="100%">
			<tr>
				<td width="220" class="Title">&nbsp;</td>
				<td class="Title" align="center">Editing: <?= $zone_name ?></td>
				<td width="220" class="Title">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3" valign="top">
					<table cellspacing="0" width="750">
						<tr>
							<td colspan="8" class="SectionTitle">Locations</td>
						</tr>
						<tr>
							<td width="55">id</td>
							<td width="55">zone_id</td>
							<td width="105">grid_id</td>
							<td width="255">name</td>
							<td width="55">include_y</td>
							<td colspan="2">&nbsp;</td>
						</tr>
						<?php
						if( is_array($location_data) )
						{
							foreach($location_data as $data)
							{
						?>
						<form method="post" name="multiForm|<?php print($data['id']); ?>" />
						<tr>
							<td>
								<input type="text" name="locations|id" value="<?php print($data['id']) ?>" style="width:45px;  background-color:#ddd;" readonly />
								<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
							</td>
							<td>
								<input type="text" name="locations|zone_id" value="<?php print($data['zone_id']) ?>" style="width:45px;  background-color:#ddd;" readonly />
								<input type="hidden" name="orig_zone_id" value="<?php print($data['zone_id']) ?>" />
							</td>
							<td>
								<input type="text" name="locations|grid_id" value="<?php print($data['grid_id']) ?>" style="width:95px;" />
								<input type="hidden" name="orig_grid_id" value="<?php print($data['grid_id']) ?>" />
							</td>
							<td>
								<input type="text" name="locations|name" value="<?php print($data['name']) ?>" style="width:255px;" />
								<input type="hidden" name="orig_name" value="<?php print($data['name']) ?>" />
							</td>
							<td>
								<input type="text" name="locations|include_y" value="<?php print($data['include_y']) ?>" style="width:55px;" />
								<input type="hidden" name="orig_include_y" value="<?php print($data['include_y']) ?>" />
	
							</td>
							<td><input type="button" value="Edit" style="font-size:10px; width:60px" onclick="javascript:window.open('index.php?page=zones&type=edit&tab=locations&id=<?= $_GET['id'] ?>&details=1&lid=<?= $data['id'] ?>', target='_self')" /></td>
							<td><input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" /></td>
							<td><input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" /></td>
						</tr>
						<input type="hidden" name="object" value="<?= $zone_name ?>" />
						<input type="hidden" name="table" value="locations" />
						</form>
						<?php if( isset($_GET['lid']) && $_GET['lid']==$data['id'] ) { ?>
						<tr>
							<td>&nbsp;</td>
							<td colspan="7" valign="top">
								<table cellpadding="0" class="SubPanel">
									<tr>
										<td colspan="8" class="SectionTitle">Location Details</td>
									</tr>
									<tr>
										<td width="25">#</td>
										<td width="55">id</td>
										<td width="75">location_id</td>
										<td width="75">x</td>
										<td width="75">y</td>
										<td width="75">z</td>
										<td>&nbsp;</td>
									</tr>
								<?php
								$location_details = $this->db->GetLocationDetails();
								if( is_array($location_details) ) {
									$i = 1;
									foreach($location_details as $detail) {
									?>
									<form method="post" name="multiForm|<?php print($detail['id']); ?>" />
									<tr>
										<td><?= $i ?>.</td>
										<td>
											<input type="text" name="location_details|id" value="<?php print($detail['id']) ?>" style="width:45px;  background-color:#ddd;" readonly />
											<input type="hidden" name="orig_id" value="<?php print($detail['id']) ?>" />
										</td>
										<td>
											<input type="text" name="location_details|location_id" value="<?php print($detail['location_id']) ?>" style="width:75px;" />
											<input type="hidden" name="orig_location_id" value="<?php print($detail['location_id']) ?>" />
										</td>
										<td>
											<input type="text" name="location_details|x" value="<?php print($detail['x']) ?>" style="width:75px;" />
											<input type="hidden" name="orig_x" value="<?php print($detail['x']) ?>" />
										</td>
										<td>
											<input type="text" name="location_details|y" value="<?php print($detail['y']) ?>" style="width:75px;" />
											<input type="hidden" name="orig_y" value="<?php print($detail['y']) ?>" />
										</td>
										<td>
											<input type="text" name="location_details|z" value="<?php print($detail['z']) ?>" style="width:75px;" />
											<input type="hidden" name="orig_z" value="<?php print($detail['z']) ?>" />
				
										</td>
										<td>
											<input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" />
											<input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" />
										</td>
									</tr>
									<input type="hidden" name="object" value="<?= $zone_name ?>" />
									<input type="hidden" name="table" value="location_details" />
									</form>
									<?php
									$i++;
									} // foreach
									?>
									<form method="post" name="ldForm|new" />
									<tr>
										<td>&nbsp;</td>
										<td align="center"><strong>new</strong></td>
										<td><input type="text" name="location_details|location_id|new" value="<?= $_GET['lid'] ?>" style="width:75px;" /></td>
										<td><input type="text" name="location_details|x|new" value="0" style="width:75px;" onclick="this.value=''" /></td>
										<td><input type="text" name="location_details|y|new" value="0" style="width:75px;" onclick="this.value=''" /></td>
										<td><input type="text" name="location_details|z|new" value="0" style="width:75px;" onclick="this.value=''" /></td>
										<td><input type="submit" name="cmd" value="Insert" style="font-size:10px; width:60px" /></td>
									</tr>
									<input type="hidden" name="object" value="<?= $zone_name ?>" />
									<input type="hidden" name="table" value="location_details" />
									</form>
								<?php } //is_array() ?>
								</table>
								</td>
							</tr>
							<?php } // if (lid)
							} // foreach()
						} // is_array()
						?>
						<form method="post" name="lForm|new" />
						<tr>
							<td align="center"><strong>new</strong></td>
							<td><input type="text" name="locations|zone_id|new" value="<?= $_GET['id'] ?>" style="width:45px; background-color:#ddd" readonly /></td>
							<td><input type="text" name="locations|grid_id|new" value="0" style="width:95px;" onclick="this.value=''" /></td>
							<td><input type="text" name="locations|name|new" value="0" style="width:255px;" onclick="this.value=''" /></td>
							<td><input type="text" name="locations|include_y|new" value="0" style="width:55px;" onclick="this.value=''" /></td>
							<td><input type="submit" name="cmd" value="Insert" style="font-size:10px; width:60px" /></td>
						</tr>
						<tr>
							<td colspan="6" height="30" valign="bottom"><strong>Note:</strong> It is easier to add locations in-game using the /locations command.</td>
						</tr>
						<input type="hidden" name="object" value="<?= $zone_name ?>" />
						<input type="hidden" name="table" value="locations" />
						</form>
					</table>
				</td>
			</tr>
		</table>
	<?php
	}
	

	/*
		Function: 
		Purpose	:	
		Params	: 
	*/
	private function Zone_RevivePoints()
	{
		global $eq2;

		// Perform updates here
		if( isset($_POST['cmd']) )
		{
			switch($_POST['cmd']) 
			{
				case "Update": $eq2->ProcessUpdate(); break;
				case "Delete": $eq2->ProcessDelete(); break;
				case "Insert": $eq2->ProcessInsert(); break;
			}
		}

		$revivepoint_data = $this->db->GetRevivePointData();
		$zone_name = $eq2->eq2db->GetZoneNameByID($_GET['id']);
		?>
		<div id="Editor">
		<table cellspacing="0">
			<tr>
				<td width="220" class="Title">&nbsp;</td>
				<td class="Title" align="center">Editing: <?= $zone_name ?></td>
				<td width="220" class="Title">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3" valign="top">
					<table cellspacing="0" width="99%">
						<tr>
							<td colspan="9" class="SectionTitle">Revive Points</td>
						</tr>
						<tr>
							<td>id</td>
							<td>location_name</td>
							<td>zone_id</td>
							<td>respawn_zone_id</td>
							<td>safe_x</td>
							<td>safe_y</td>
							<td>safe_z</td>
							<td>heading</td>
							<td>&nbsp;</td>
						</tr>
						<?php
						if( is_array($revivepoint_data) )
						{
							foreach($revivepoint_data as $data)
							{
						?>
						<form method="post" name="rpForm|<?php print($data['id']); ?>" />
						<tr>
							<td>
								<input type="text" name="revive_points|id" value="<?php print($data['id']) ?>" class="small" style="background-color:#ddd;" readonly />
								<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
							</td>
							<td>
								<input type="text" name="revive_points|location_name" value="<?php print($data['location_name']) ?>" class="longtext"/>
								<input type="hidden" name="orig_location_name" value="<?php print($data['location_name']) ?>" />
							</td>
							<td>
								<?php $eq2->EQ2ZoneSelector("revive_points", "zone_id", $data['zone_id'], 0) ?>
								<input type="hidden" name="orig_zone_id" value="<?php print($data['zone_id']) ?>" />
							</td>
							<td>
								<?php $eq2->EQ2ZoneSelector("revive_points", "respawn_zone_id", $data['respawn_zone_id'], 0) ?>
								<input type="hidden" name="orig_respawn_zone_id" value="<?php print($data['respawn_zone_id']) ?>" />
							</td>
							<td>
									<input type="text" name="revive_points|safe_x" value="<?= $data['safe_x'] ?>" class="medium" />
									<input type="hidden" name="orig_safe_x" value="<?= $data['safe_x'] ?>" />
							</td>
							<td>
									<input type="text" name="revive_points|safe_y" value="<?= $data['safe_y'] ?>" class="medium" />
									<input type="hidden" name="orig_safe_y" value="<?= $data['safe_y'] ?>" />
							</td>
							<td>
									<input type="text" name="revive_points|safe_z" value="<?= $data['safe_z'] ?>" class="medium" />
									<input type="hidden" name="orig_safe_z" value="<?= $data['safe_z'] ?>" />
							</td>
							<td>
									<input type="text" name="revive_points|heading" value="<?= $data['heading'] ?>" class="medium" />
									<input type="hidden" name="orig_heading" value="<?= $data['heading'] ?>" />
							</td>
							<td nowrap="nowrap">
								<input type="submit" name="cmd" value="Update" class="Submit" />
								<input type="submit" name="cmd" value="Delete" class="Submit" />
							</td>
						</tr>
							<input type="hidden" name="object" value="<?= $zone_name ?>" />
							<input type="hidden" name="table" value="revive_points" />
						</form>
						<?php
							} //foreach
						} // is_array
						?>
						<form method="post" name="rpForm|new" />
						<tr>
							<td><strong>new</strong></td>
							<td><input type="text" name="revive_points|location_name" value="" class="longtext"/></td>
							<td><?php $eq2->EQ2ZoneSelector("revive_points", "zone_id", 0, 0) ?></td>
							<td><?php $eq2->EQ2ZoneSelector("revive_points", "respawn_zone_id", 0, 0) ?></td>
							<td><input type="text" name="revive_points|safe_x" value="0" class="medium" /></td>
							<td><input type="text" name="revive_points|safe_y" value="0" class="medium" /></td>
							<td><input type="text" name="revive_points|safe_z" value="0" class="medium" /></td>
							<td><input type="text" name="revive_points|heading" value="0" class="medium" /></td>
							<td><input type="submit" name="cmd" value="Insert" class="Submit" /></td>
						</tr>
						<input type="hidden" name="object" value="<?= $zone_name ?>" />
						<input type="hidden" name="table" value="revive_points" />
						</form>
					</table>
				</td>
			</tr>
		</table>
	<?php
	}
	

	/*
		Function: 
		Purpose	:	
		Params	: 
	*/
	private function Zone_List()
	{
		global $eq2;
		
		$zone_data = $this->db->GetZoneList();
		?>
		<div id="Editor">
		<table cellspacing="0">
			<tr>
				<td width="220" class="Title">&nbsp;</td>
				<td class="Title" align="center">Full Zone Listing</td>
				<td width="220" class="Title">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3" valign="top">
					<table cellspacing="0" width="99%">
						<tr>
							<td colspan="8" class="SectionTitle">Zones</td>
						</tr>
						<tr>
							<td width="100">zone_id</td>
							<td width="200">name</td>
							<td width="200">file</td>
							<td width="400">description</td>
							<td width="200">lua</td>
						</tr>
						<?php
						if( is_array($zone_data) )
						{
							$i = 1;
							foreach($zone_data as $zone)
							{
								$row_class = ( $i % 2 ) ? ' class="Row1"' : ' class="Row2"';
							?>
						<tr<?= $row_class ?>>
							<td width="50">[<a href="index.php?page=zones&type=edit&id=<?= $zone['id'] ?>">&nbsp;<?= $zone['id'] ?>&nbsp;</a>]</td>
							<td width="100"><?= $zone['name'] ?></td>
							<td width="100"><?= $zone['file'] ?></td>
							<td width="255" nowrap><?= $zone['description'] ?></td>
							<td width="100"><?= $zone['lua_script'] ?></td>
						</tr>
							<?php
								$i++;
							}
						}
						?>
						<tr>
							<td colspan="5"><?= $i ?> records found.</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	<?php
	}
	

	/*
		Function: 
		Purpose	:	
		Params	: 
	*/
	private function ZoneStats()
	{
		global $eq2;
		
	}
	
	
	/*
		Function: SaveScript()
		Purpose	: Saves the lua file
		Params	: $scriptName is file name, $script is the actual lua script
		Notes	: Move this to eq2Functions.class.php and use it as a general save function for all scripts
	*/
	public function SaveScript($scriptName, $script)
	{
		global $eq2;
		
		if( empty($scriptName) ) 
			return "Cannot save a blank script path/file!";
			
		$pattern[0] = "/\\\/i";
		$replace[0] = "/";
		$LUAScript = preg_replace($pattern, $replace, $script);
		
		if( empty($GLOBALS['config']['script_path']) )
			die("SCRIPT_PATH constant not set in config.php");
		else
			$path = $GLOBALS['config']['script_path'];
	
		$file = $path . $scriptName;

		if( $GLOBALS['config']['readonly'] )
			$eq2->AddStatus("READ-ONLY MODE - ".$file." not saved!");
		else
		{
			if( !$f = fopen($file,'w') ) 
				die("Cannot create filename: $file");
				
			if (!fwrite($f, $LUAScript)) 
				die("Cannot write to file ($file)");
				
			fclose($f);
		}
	}
 
	private function DisplayInstanceTypeDropdown($zone)
	{
		$INSTANCE_TYPE_OPTS = array("NONE"=>"None", 
							"GROUP_LOCKOUT_INSTANCE"=>"Group Lockout",
							"GROUP_PERSIST_INSTANCE"=>"Group Persistent",
							"RAID_LOCKOUT_INSTANCE"=>"Raid Lockout",
							"RAID_PERSIST_INSTANCE"=>"Raid Persistent",
							"SOLO_LOCKOUT_INSTANCE"=>"Solo Lockout",
							"SOLO_PERSIST_INSTANCE"=>"Solo Persistent",
							"TRADESKILL_INSTANCE"=>"Tradeskill",
							"PUBLIC_INSTANCE"=>"Public",
							"PERSONAL_HOUSE_INSTANCE"=>"Player House",
							"GUILD_HOUSE_INSTANCE"=>"Guild Hall",
							"QUEST_INSTANCE"=>"Quest");


		echo '<select name="zones|instance_type" value="0">';
		
		foreach ($INSTANCE_TYPE_OPTS as $k=>$v) {
			$selected = $zone['instance_type'] == $k ? " selected" : "";
			printf('<option value="%s"%s>%s</option>', $k, $selected, $v);
		}

		echo '</select>';
	}
}

?>