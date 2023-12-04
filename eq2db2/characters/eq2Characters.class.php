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

class eq2Characters
{

	public function __construct()
	{
		include_once("eq2CharactersDB.class.php");
		$this->db = new eq2CharactersDB();
	}
	
	/*
		
	*/
	public function Start()
	{
		global $eq2;
		global $type;
	
		print('<table cellspacing="0" id="main-body" class="main">');

		// start left menu
		print('<tr><td class="sidebar" nowrap="nowrap"><strong>Options:</strong><br>(none)<br>');

		// nav stuff here	
		printf('<br><li><a href="%s">Reload Page</a><br>', $eq2->PageLink);
		if( isset($_GET['id']) ) 
			printf('<li><a href="%s">Back</a><br>', $eq2->BackLink);
		else
			printf('<li><br>');
		
		// Help Links / Stats Details
		printf('<br><strong>Help:</strong><br>&nbsp;');
		
		printf('<br><strong>Stats:</strong><br>&nbsp;');
		$this->CharacterStats();
		
		// start main page
		printf('</td><td class="page-text"><div id="PageTitle">%s</div>', $eq2->PageTitle);
		switch($_GET['page'])
		{
			case "help": 
				// use common (eq2Functions) class to display non-specific data
				$eq2->DisplaySiteText($page_lc, $_GET['cat']); 
				break;
				
			default: 
				switch($type)
				{
					case "summary"	: break;
					case "edit"			: 
					default					: $this->CharacterLookup(); break;
				}
				break;
		}
		
		// end page
		print('</td></tr></table>');
	}


	private function CharacterLookup()
	{
		global $eq2;

		$characters = $this->db->GetCharacters();
		if( !is_array($characters) )
			return;
			
		?>
		<div id="SearchControls">
			<script>
			<!--
			//Called from keyup on the search textbox.
			//Starts the AJAX request.
			function CharacterLookupAJAX() {
				if (searchReq.readyState == 4 || searchReq.readyState == 0) {
					var str = escape(document.getElementById('txtSearch').value);
					searchReq.open("GET", 'characters/eq2CharactersAjax.php?type=lookup&search=' + str, true);
					searchReq.onreadystatechange = handleSearchSuggest; 
					searchReq.send(null);
				}		
			}
			-->
			</script>
			<table>
				<tr>
					<td class="LabelRight">Pick:</td>
					<td>
						<select name="charPicker" onchange="dosub(this.options[this.selectedIndex].value)">
							<option value="<?= $eq2->BackLink ?>">Pick a Character</option>
							<?php
							foreach($characters as $character)
								printf('<option value="%s%s&id=%s"%s>%s (%s)</option>', $eq2->BackLink, empty($_GET['type'])?"&type=edit":"", $character['id'], (isset($_GET['id']) && $_GET['id'] == $character['id']) ? " selected":"", $character['name'], $character['id']);
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="LabelRight">Lookup:</td>
					<td>
						<form action="index.php?page=characters&type=edit" id="frmSearch" method="post">
							<input type="text" id="txtSearch" name="txtSearch" alt="Search Criteria" onkeyup="CharacterLookupAJAX();" autocomplete="off" class="box" value="<?= isset($POST['txtSearch']) ? $_POST['txtSearch'] : null ?>" onclick="this.value='';" />
							<input type="submit" id="cmdSearch" name="cmdSearch" value="Search" alt="Run Search" class="submit" />
							<input type="button" value="Clear" class="submit" onclick="dosub('index.php?page=characters&type=edit');" />
							<input type="hidden" name="cmd" value="CharacterByName" />
							<div id="search_suggest">
							</div>
						</form>
					</td>
				</tr>
			</table>
			<script>
			<!--
			document.getElementById('txtSearch').focus = true;
			//-->
			</script>
		</div><!-- End SearchControls -->
		<br>
		<?php
		// Check if searchText was used to find a user
		if( isset($_POST['txtSearch']) && strlen($_POST['txtSearch']) > 0 )
		{
			$arr = $this->db->GetCharacterByName($_POST['txtSearch']);
			if( is_array($arr) )
			{
				$this->DisplayCharacterGrid($arr);
			}
			else
			{
				$eq2->AddStatus('No characters match your search.');
			}
			$ret = true;
		}
		else if( isset($_POST['cmd']) && $_POST['cmd'] == 'CharacterByName' )
		{
			//$eq2->AddStatus('Search must contain at least 1 letter/number.');
			//$ret = true;
			$arr = $this->db->GetCharacterByName("all");
			if( is_array($arr) )
			{
				$this->DisplayCharacterGrid($arr);
			}
			else
			{
				$eq2->AddStatus('No characters match your search.');
			}
			$ret = true;
		}
		// If a character is selected, display data associated with the character
		if( isset($_GET['id']) )
		{
			$this->CharacterEditor();
			$ret = true;
		}

		?>
		<div id="EditorStatus">
			<?php 
			if( !empty($eq2->Status) ) 
				$eq2->DisplayStatus(); 
			?>
		</div><!-- End EditorStatus -->
		<?php
		if ( isset($GLOBALS['refresh']) && $GLOBALS['refresh']==1 )
			$eq2->RefreshPage(2); // usually only happens after a record is deleted permanently
		
		// is this even used?
		//if( $ret ) 
		//	return $ret;
	} // CharacterLookup


	private function CharacterStats()
	{
		global $eq2;
	
	}	


	private function CharacterEditor()
	{
		global $eq2;

		// Build the Tab menu
		$current_tab_idx = ( isset($_GET['tab']) ) ? $_GET['tab'] : 'persona';
		$tab_array = array(
			'persona'			=> 'Persona',
			'inventory'		=> 'Inventory',
			'journal'			=> 'Journal',
			'knowledge'		=> 'Knowledge',
			'social'			=> 'Social',
			'appearance'	=> 'Appearance',
			'misc'				=> 'Misc'
		);
		$eq2->TabGenerator2($current_tab_idx, $tab_array);
		/*
			Persona		: Character+Details, Titles, Factions, Toggles
			Inventory	: Items, Buyback, Housing, Broker
			Journal		: Quests, Collections, Achievements
			Knowledge	: Spells, Skills, Skill Bars
			Social		: Friends, Ignore, Macros
			Appearance: Duh
			Misc			: Instances, Languages, Mail, 
		*/

		switch($current_tab_idx)
		{
			case "inventory"	: $this->Character_Inventory(); break;
			case "journal"		: $this->Character_Journal(); break;
			case "knowledge"	: $this->Character_Knowledge(); break;
			case "social"			: $this->Character_Social(); break;
			case "appearance"	: $this->Character_Appearance(); break;
			case "misc"				: $this->Character_Misc(); break;
			case "persona"		:
			default						: $this->Character_Persona(); break;
		}

	}
	

	private function Character_Persona()
	{
		global $eq2;

		// Build Toggles array()
		$character_details_toggles = array('anonymous','roleplaying','afk','lfw','guild_recruiting','hide_illusion','guild_heraldry',
			'decline_duel','decline_trade','decline_group','decline_raid','decline_guild','decline_voice','decline_lon','hide_achievements');


		// Perform updates here
		if( isset($_POST['cmd']) )
		{
			// Loop through Toggles and see if any have been unset
			foreach( $character_details_toggles as $toggles )
			{
				$toggle_settings	= sprintf('character_details|%s', $toggles);
				// wow, what an epic hack... seriously?
				if( empty($_POST[$toggle_settings]) )
					$_POST[$toggle_settings] = 0;
			}
			
			switch($_POST['cmd']) 
			{
				case "Update": $eq2->ProcessUpdateMultipleTables(); break;
				case "MoveZone": $sql = sprintf("UPDATE %s.`characters` SET `current_zone_id`='12', `x`='0', `y`='0', `z`='0' WHERE `id` = %d", $GLOBALS['db_name'], $_GET['id']);
					//echo($sql);
					$eq2->eq2db->RunQuerySingle($sql);
					break;
				case "Delete": 
					$sql = sprintf("DELETE FROM characters WHERE id = %s", $_GET['id']);
					$eq2->ProcessDelete($sql);
					$eq2->AddStatus("<br>Character Deleted. Page will refresh in 2 seconds.");
					$GLOBALS['refresh'] = 1;
					return;
					break;
			}
		}

		// Load Character Info
		$character = $this->db->GetCharacter();
		if( !is_array($character) )
		{
			$eq2->AddStatus("No `character` data found.");
			return;
		}
		?>
		<!-- Start CharacterEditor -->
		<div id="Editor">
			<form method="post">
			<table cellspacing="0" width="770">
				<tr>
					<td width="220" class="Title">&nbsp;</td>
					<td class="Title" align="center">Editing Character: <?= $character['name'] ?></td>
					<td width="220" class="Title">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2" valign="top">
						<table cellspacing="0" width="550">
							<tr>
								<td colspan="2" class="SectionTitle">Character Info</td>
							</tr>
							<tr>
								<td width="40%" class="LabelRight">id:</td>
								<td width="60%">
									<input type="text" name="characters|id" value="<?= $character['id'] ?>" readonly />
									<input type="hidden" name="characters_orig_id" value="<?= $character['id'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">account_id:</td>
								<td>
									<input type="text" name="characters|account_id" value="<?= $character['account_id'] ?>" readonly />
									<input type="hidden" name="orig_account_id" value="<?= $character['account_id'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">name:</td>
								<td>
									<input type="text" name="characters|name" value="<?= $character['name'] ?>" />
									<input type="hidden" name="orig_name" value="<?= $character['name'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">class:</td>
								<td>
									<?php $eq2->EQ2PlayerClassSelector("characters", "class", $character['class']) ?>
								</td>
							</tr>
							<tr>
								<td class="LabelRight">race:</td>
								<td><?php $eq2->EQ2PlayerRaceSelector("characters", "race", $character['race']) ?></td>
							</tr>
							<tr>
								<td class="LabelRight">gender:</td>
								<td>
									<select name="characters|gender">
										<option value="0"<?php if( $character['gender']==0 ) print(' selected') ?>>Female</option>
										<option value="1"<?php if( $character['gender']==1 ) print(' selected') ?>>Male</option>
									</select>
									<input type="hidden" name="orig_gender" value="<?= $character['gender'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">level:</td>
								<td>
									<input type="text" name="characters|level" value="<?= $character['level'] ?>" class="small" />
									<input type="hidden" name="orig_level" value="<?= $character['level'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">tradeskill_level:</td>
								<td>
									<input type="text" name="characters|tradeskill_level" value="<?= $character['tradeskill_level'] ?>" class="small" />
									<input type="hidden" name="orig_tradeskill_level" value="<?= $character['tradeskill_level'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">deity:</td>
								<td>
									<select name="characters|deity">
										<option value="0"<?php if( $character['deity']==0 ) print(' selected') ?>>Neutral/Evil</option>
										<option value="1"<?php if( $character['deity']==1 ) print(' selected') ?>>Neutral/Good</option>
									</select>
									<input type="hidden" name="orig_deity" value="<?= $character['deity'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">current_zone_id:</td>
								<td><?php $eq2->EQ2ZoneSelector("characters", "current_zone_id", $character['current_zone_id'], 0) ?></td>
							</tr>
							<tr>
								<td class="LabelRight">xyz+heading:</td>
								<td>
									<input type="text" name="characters|x" value="<?= $character['x'] ?>" class="medium" />
									<input type="hidden" name="orig_x" value="<?= $character['x'] ?>" />
									<input type="text" name="characters|y" value="<?= $character['y'] ?>" class="medium" />
									<input type="hidden" name="orig_y" value="<?= $character['y'] ?>" />
									<input type="text" name="characters|z" value="<?= $character['z'] ?>" class="medium" />
									<input type="hidden" name="orig_z" value="<?= $character['z'] ?>" />
									<input type="text" name="characters|heading" value="<?= $character['heading'] ?>" class="medium" />
									<input type="hidden" name="orig_heading" value="<?= $character['heading'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">starting_city:</td>
								<td><?php $eq2->EQ2StartingCitySelector("characters", "current_zone_id", $character['starting_city'], 0) ?></td>
							</tr>
							<tr>
								<td class="LabelRight">deleted:</td>
								<td>
									<input type="checkbox" name="characters|deleted" value="1" class="chkbox"<?php if( $character['deleted'] > 0 ) print(' checked') ?> />
									<input type="hidden" name="orig_deleted" value="<?= $character['deleted'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">instance_id:</td>
								<td>
									<input type="text" name="characters|instance_id" value="<?= $character['instance_id'] ?>" class="small" />
									<input type="hidden" name="orig_instance_id" value="<?= $character['instance_id'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">admin_status:</td>
								<td>
									<input type="text" name="characters|admin_status" value="<?= $character['admin_status'] ?>" class="small" />
									<input type="hidden" name="orig_admin_status" value="<?= $character['admin_status'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">is_online:</td>
								<td>
									<select name="characters|is_online" class="yesno">
										<option value="0"<?php if( $character['is_online']==0 ) print(' selected') ?>>No</option>
										<option value="1"<?php if( $character['is_online']==1 ) print(' selected') ?>>Yes</option>
									</select>
									<input type="hidden" name="orig_is_online" value="<?= $character['is_online'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">created_date:</td>
								<td><?= $character['created_date'] ?></td>
							</tr>
							<tr>
								<td class="LabelRight">last_played:</td>
								<td><?= $character['last_played'] ?></td>
							</tr>
							<tr>
								<td class="LabelRight">last_saved:</td>
								<td><?= date('Y-m-d h:i:s', $character['last_saved']) ?></td>
							</tr>
							<tr>
								<td class="LabelRight">unix_timestamp:</td>
								<td><?= $character['unix_timestamp'] ?> (Date: <?= date('Y-m-d h:i:s', $character['unix_timestamp']) ?>)</td>
							</tr>
						</table>
					</td>
					<td valign="top">
						<?php
						// Load Character Details / Toggles
						$character_details = $this->db->GetCharacterDetails();
						if( !is_array($character_details) )
						{
							$eq2->AddStatus("No `character_details` data found.");
						}
						?>
						<table cellspacing="0" width="220">
							<tr>
								<td colspan="2" class="SectionTitle">Character Toggles</td>
							</tr>
							<?php
							foreach($character_details_toggles as $toggles)
							{
								// Not sure about the isset's added in this section
								$checked = ( isset($character_details[$toggles]) && $character_details[$toggles] > 0 ) ? ' checked' : '';
								printf("<tr>\n<td width=\"50%%\" class=\"LabelRight\">%s:</td>\n<td><input type=\"checkbox\" name=\"character_details|%s\" value=\"1\" class=\"chkbox\"%s />\n<input type=\"hidden\" name=\"orig_%s\" value=\"%s\" /></td>\n</tr>\n",
									$toggles, $toggles, $checked, $toggles, isset($character_details[$toggles]) ? intval($character_details[$toggles]) : 0);
							}
							?>
							<tr>
								<td colspan="2" height="75">&nbsp;</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="3" valign="top">
						<table cellspacing="0" width="780">
							<tr>
								<td colspan="4" class="SectionTitle">Character Details</td>
							</tr>
							<tr>
								<td width="40%" class="LabelRight">hp:</td>
								<td width="10%">	
									<input type="text" name="character_details|hp" value="<?= $character_details['hp'] ?>" class="medium" />
									<input type="hidden" name="orig_hp" value="<?= $character_details['hp'] ?>" />
									<input type="hidden" name="character_details|id" value="<?= $character_details['id'] ?>" />
									<input type="hidden" name="character_details_orig_id" value="<?= $character_details['id'] ?>" />
								</td>
								<td width="30%" class="LabelRight">attack:</td>
								<td width="20%">
									<input type="text" name="character_details|attack" value="<?= $character_details['attack'] ?>" class="small" />
									<input type="hidden" name="orig_attack" value="<?= $character_details['attack'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">power:</td>
								<td>
									<input type="text" name="character_details|power" value="<?= $character_details['power'] ?>" class="medium" />	
									<input type="hidden" name="orig_power" value="<?= $character_details['power'] ?>" />
								</td>
								<td class="LabelRight">mitigation:</td>
								<td>
									<input type="text" name="character_details|mitigation" value="<?= $character_details['mitigation'] ?>" class="small" />
									<input type="hidden" name="orig_mitigation" value="<?= $character_details['mitigation'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">max_hp:</td>
								<td>
									<input type="text" name="character_details|max_hp" value="<?= $character_details['max_hp'] ?>" class="medium" />
									<input type="hidden" name="orig_max_hp" value="<?= $character_details['max_hp'] ?>" />
								</td>
								<td class="LabelRight">avoidance:</td>
								<td>
									<input type="text" name="character_details|avoidance" value="<?= $character_details['avoidance'] ?>" class="small" />
									<input type="hidden" name="orig_avoidance" value="<?= $character_details['avoidance'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">max_power:</td>
								<td>
									<input type="text" name="character_details|max_power" value="<?= $character_details['max_power'] ?>" class="medium" />
									<input type="hidden" name="orig_max_power" value="<?= $character_details['max_power'] ?>" />
								</td>
								<td class="LabelRight">parry:</td>
								<td>
									<input type="text" name="character_details|parry" value="<?= $character_details['parry'] ?>" class="small" />
									<input type="hidden" name="orig_parry" value="<?= $character_details['parry'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">max_concentration:</td>
								<td>
									<input type="text" name="character_details|max_concentration" value="<?= $character_details['max_concentration'] ?>" class="small" />
									<input type="hidden" name="orig_max_concentration" value="<?= $character_details['max_concentration'] ?>" />
								</td>
								<td class="LabelRight">deflection:</td>
								<td>
									<input type="text" name="character_details|deflection" value="<?= $character_details['deflection'] ?>" class="small" />
									<input type="hidden" name="orig_deflection" value="<?= $character_details['deflection'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">xp:</td>
								<td>
									<input type="text" name="character_details|xp" value="<?= $character_details['xp'] ?>" class="large" />
									<input type="hidden" name="orig_xp" value="<?= $character_details['xp'] ?>" />
								</td>
								<td class="LabelRight">block:</td>
								<td>	
									<input type="text" name="character_details|block" value="<?= $character_details['block'] ?>" class="small" />
									<input type="hidden" name="orig_block" value="<?= $character_details['block'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">xp_needed:</td>
								<td>
									<input type="text" name="character_details|xp_needed" value="<?= $character_details['xp_needed'] ?>" class="large" />
									<input type="hidden" name="orig_xp_needed" value="<?= $character_details['xp_needed'] ?>" />
								</td>
								<td class="LabelRight">combat_voice:</td>
								<td>
									<input type="text" name="character_details|combat_voice" value="<?= $character_details['combat_voice'] ?>" class="small" />
									<input type="hidden" name="orig_combat_voice" value="<?= $character_details['combat_voice'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">xp_debt:</td>
								<td>
									<input type="text" name="character_details|xp_debt" value="<?= $character_details['xp_debt'] ?>" class="large" />
									<input type="hidden" name="orig_xp_debt" value="<?= $character_details['xp_debt'] ?>" />
								</td>
								<td class="LabelRight">emote_voice:</td>
								<td>
									<input type="text" name="character_details|emote_voice" value="<?= $character_details['emote_voice'] ?>" class="small" />
									<input type="hidden" name="orig_emote_voice" value="<?= $character_details['emote_voice'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">xp_vitality:</td>
								<td>
									<input type="text" name="character_details|xp_vitality" value="<?= $character_details['xp_vitality'] ?>" class="large" />
									<input type="hidden" name="orig_xp_vitality" value="<?= $character_details['xp_vitality'] ?>" />
								</td>
								<td colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td class="LabelRight">str:</td>
								<td>
									<input type="text" name="character_details|str" value="<?= $character_details['str'] ?>" class="small" />
									<input type="hidden" name="orig_str" value="<?= $character_details['str'] ?>" />
								</td>
								<td class="LabelRight">heat:</td>
								<td>
									<input type="text" name="character_details|heat" value="<?= $character_details['heat'] ?>" class="small" />
									<input type="hidden" name="orig_heat" value="<?= $character_details['heat'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">sta:</td>
								<td>
									<input type="text" name="character_details|sta" value="<?= $character_details['sta'] ?>" class="small" />
									<input type="hidden" name="orig_sta" value="<?= $character_details['sta'] ?>" />
								</td>
								<td class="LabelRight">cold:</td>
								<td>
									<input type="text" name="character_details|cold" value="<?= $character_details['cold'] ?>" class="small" />
									<input type="hidden" name="orig_cold" value="<?= $character_details['cold'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">agi:</td>
								<td>
									<input type="text" name="character_details|agi" value="<?= $character_details['agi'] ?>" class="small" />
									<input type="hidden" name="orig_agi" value="<?= $character_details['agi'] ?>" />
								</td>
								<td class="LabelRight">magic:</td>
								<td>
									<input type="text" name="character_details|magic" value="<?= $character_details['magic'] ?>" class="small" />
									<input type="hidden" name="orig_magic" value="<?= $character_details['magic'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">wis:</td>
								<td>
									<input type="text" name="character_details|wis" value="<?= $character_details['wis'] ?>" class="small" />
									<input type="hidden" name="orig_wis" value="<?= $character_details['wis'] ?>" />
								</td>
								<td class="LabelRight">mental:</td>
								<td>
									<input type="text" name="character_details|mental" value="<?= $character_details['mental'] ?>" class="small" />
									<input type="hidden" name="orig_mental" value="<?= $character_details['mental'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">intel:</td>
								<td>
									<input type="text" name="character_details|intel" value="<?= $character_details['intel'] ?>" class="small" />
									<input type="hidden" name="orig_intel" value="<?= $character_details['intel'] ?>" />
								</td>
								<td class="LabelRight">divine:</td>
								<td>
									<input type="text" name="character_details|divine" value="<?= $character_details['divine'] ?>" class="small" />
									<input type="hidden" name="orig_divine" value="<?= $character_details['divine'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">status_points:</td>
								<td>
									<input type="text" name="character_details|status_points" value="<?= $character_details['status_points'] ?>" class="small" />
									<input type="hidden" name="orig_status_points" value="<?= $character_details['status_points'] ?>" />
								</td>
								<td class="LabelRight">disease:</td>
								<td>
									<input type="text" name="character_details|disease" value="<?= $character_details['disease'] ?>" class="small" />
									<input type="hidden" name="orig_disease" value="<?= $character_details['disease'] ?>" />
								</td>
							</tr>
							<tr>
								<td colspan="2">&nbsp;</td>
								<td class="LabelRight">poison:</td>
								<td>
									<input type="text" name="character_details|poison" value="<?= $character_details['poison'] ?>" class="small" />
									<input type="hidden" name="orig_poison" value="<?= $character_details['poison'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">coin_copper:</td>
								<td>
									<input type="text" name="character_details|coin_copper" value="<?= $character_details['coin_copper'] ?>" class="small" />
									<input type="hidden" name="orig_coin_copper" value="<?= $character_details['coin_copper'] ?>" />
								</td>
								<td class="LabelRight">bank_copper:</td>
								<td>
									<input type="text" name="character_details|bank_copper" value="<?= $character_details['bank_copper'] ?>" class="small" />	
									<input type="hidden" name="orig_bank_copper" value="<?= $character_details['bank_copper'] ?>" />
								</td>
							</tr>
							<tr>
	
								<td class="LabelRight">coin_silver:</td>
								<td>
									<input type="text" name="character_details|coin_silver" value="<?= $character_details['coin_silver'] ?>" class="small" />
									<input type="hidden" name="orig_coin_silver" value="<?= $character_details['coin_silver'] ?>" />
								</td>
								<td class="LabelRight">bank_silver:</td>
								<td>
									<input type="text" name="character_details|bank_silver" value="<?= $character_details['bank_silver'] ?>" class="small" />
									<input type="hidden" name="orig_bank_silver" value="<?= $character_details['bank_silver'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">coin_gold:</td>
	
								<td>
									<input type="text" name="character_details|coin_gold" value="<?= $character_details['coin_gold'] ?>" class="small" />
									<input type="hidden" name="orig_coin_gold" value="<?= $character_details['coin_gold'] ?>" />
								</td>
								<td class="LabelRight">bank_gold:</td>
								<td>
									<input type="text" name="character_details|bank_gold" value="<?= $character_details['bank_gold'] ?>" class="small" />
									<input type="hidden" name="orig_bank_gold" value="<?= $character_details['bank_gold'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">coin_plat:</td>
								<td>
	
									<input type="text" name="character_details|coin_plat" value="<?= $character_details['coin_plat'] ?>" class="small" />
									<input type="hidden" name="orig_coin_plat" value="<?= $character_details['coin_plat'] ?>" />
								</td>
								<td class="LabelRight">bank_plat:</td>
								<td>
									<input type="text" name="character_details|bank_plat" value="<?= $character_details['bank_plat'] ?>" class="small" />
									<input type="hidden" name="orig_bank_plat" value="<?= $character_details['bank_plat'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">bind_zone_id:</td>
								<td>
									<input type="text" name="character_details|bind_zone_id" value="<?= $character_details['bind_zone_id'] ?>" class="small" />
									<input type="hidden" name="orig_bind_zone_id" value="<?= $character_details['bind_zone_id'] ?>" />
								</td>
								<td class="LabelRight">house_zone_id:</td>
								<td>
									<input type="text" name="character_details|house_zone_id" value="<?= $character_details['house_zone_id'] ?>" class="small" />
									<input type="hidden" name="orig_house_zone_id" value="<?= $character_details['house_zone_id'] ?>" />	
								</td>
							</tr>
							<tr>	
								<td class="LabelRight">bind_x:</td>
								<td>
									<input type="text" name="character_details|bind_x" value="<?= $character_details['bind_x'] ?>" class="small" />
									<input type="hidden" name="orig_bind_x" value="<?= $character_details['bind_x'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">bind_y:</td>	
								<td>
									<input type="text" name="character_details|bind_y" value="<?= $character_details['bind_y'] ?>" class="small" />
									<input type="hidden" name="orig_bind_y" value="<?= $character_details['bind_y'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">bind_z:</td>
								<td>
									<input type="text" name="character_details|bind_z" value="<?= $character_details['bind_z'] ?>" class="small" />
									<input type="hidden" name="orig_bind_z" value="<?= $character_details['bind_z'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">bind_heading:</td>
								<td>
									<input type="text" name="character_details|bind_heading" value="<?= $character_details['bind_heading'] ?>" class="small" />
									<input type="hidden" name="orig_bind_heading" value="<?= $character_details['bind_heading'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="LabelRight">pet_name:</td>
								<td colspan="3">
									<input type="text" name="character_details|pet_name" value="<?= $character_details['pet_name'] ?>" class="large" />
									<input type="hidden" name="orig_pet_name" value="<?= $character_details['pet_name'] ?>" />
								</td>
							</tr>
	
							<tr>
								<td class="LabelRight">biography:</td>
								<td colspan="3">
									<textarea name="character_details|biography" style="width:97%;"><?= $character_details['biography'] ?></textarea>
									<input type="hidden" name="orig_biography" value="<?= $character_details['biography'] ?>" />
								</td>
							</tr>
						</table>
					</td>
				<tr>
				</tr>
				<tr>
					<td colspan="3" class="Submit">
						<input type="submit" name="cmd" value="Update" />
						<input type="submit" name="cmd" value="MoveZone" />
						<input type="submit" name="cmd" value="Delete" />
						<input type="hidden" name="object" value="<?= $character['name'] ?>" />
						<input type="hidden" name="table" value="characters|character_details" />
					</td>
				</tr>
			</table>
			</form>
		</div>
		<!-- End CharacterEditor -->
		<?php
		/*
		$character_details = $this->db->GetCharacterDetails();
		if( !is_array($character_details) )
		{
			$eq2->AddStatus("No `character_details` data found.");
		}
		
		$character_factions = $this->db->GetCharacterFactions();
		if( !is_array($character_factions) )
		{
			$eq2->AddStatus("No `character_factions` data found.");
		}
		
		$character_titles = $this->db->GetCharacterTitles();
		if( !is_array($character_titles) )
		{
			$eq2->AddStatus("No `character_titles` data found.");
		}
		
		print_r($character_details);
		print_r($character_factions);
		print_r($character_titles);
		*/
	}
	

	private function Character_Inventory()
	{
		// Get the picture hex string from the db
		$hex = $this->db->GetCharacterPicture($_GET['id']);
		
		// Convert the hex string into a base64 string
		$base64 = base64_encode(pack('H*',$hex));
		
		$items = $this->db->GetEquippedItems($_GET['id']);
		?>
		<div style="width:536px; height:620px; background-color:#1C1D31;">
		<table>
			<tr>
				<td>
				<table>
				<tr><td><div style='background: url("images/slots/SlotHead.jpg") no-repeat; width: 40px; height: 40px;'><?php $this->GetItemIcon(2, $items); ?></div></td></tr>
				<tr><td><div style='background: url("images/slots/SlotCloak.jpg") no-repeat; width: 40px; height: 40px;'><?php $this->GetItemIcon(19, $items); ?></div></td></tr>
				<tr><td><div style='background: url("images/slots/SlotCharm1.jpg") no-repeat; width: 40px; height: 40px;'><?php $this->GetItemIcon(20, $items); ?></div></td></tr>
				<tr><td><div style='background: url("images/slots/SlotShoulders.jpg") no-repeat; width: 40px; height: 40px;'><?php $this->GetItemIcon(4, $items); ?></div></td></tr>
				<tr><td><div style='background: url("images/slots/SlotChest.jpg") no-repeat; width: 40px; height: 40px;'><?php $this->GetItemIcon(3, $items); ?></div></td></tr>
				<tr><td><div style='background: url("images/slots/SlotForearms.jpg") no-repeat; width: 40px; height: 40px;'><?php $this->GetItemIcon(5, $items); ?></div></td></tr>
				<tr><td><div style='background: url("images/slots/SlotHands.jpg") no-repeat; width: 40px; height: 40px;'><?php $this->GetItemIcon(6, $items); ?></div></td></tr>
				<tr><td><div style='background: url("images/slots/SlotLegs.jpg") no-repeat; width: 40px; height: 40px;'><?php $this->GetItemIcon(7, $items); ?></div></td></tr>
				<tr><td><div style='background: url("images/slots/SlotFeet.jpg") no-repeat; width: 40px; height: 40px;'><?php $this->GetItemIcon(8, $items); ?></div></td></tr>
				<tr><td><div style='background: url("images/slots/SlotFoodDrink.jpg") no-repeat; width: 40px; height: 40px;'><?php $this->GetItemIcon(22, $items); ?></div></td></tr>
				<tr><td><div style='background: url("images/slots/SlotFoodDrink.jpg") no-repeat; width: 40px; height: 40px;'><?php $this->GetItemIcon(23, $items); ?></div></td></tr>
				<tr><td><div style='background: url("images/slots/SlotPrimary.jpg") no-repeat; width: 40px; height: 40px;'><?php $this->GetItemIcon(0, $items); ?></div></td></tr>
				</table>
				</td>
				<td>
					<!-- Use the base64 string in an html img tag to display the picture -->
					<img width="430px" src="data:image/png;base64,<?= $base64 ?>" />
				</td>
				<td>
				<table>
				<tr><td><div style='background: url("images/slots/SlotCharm2.jpg") no-repeat; width: 40px; height: 40px;'><?php $this->GetItemIcon(21, $items); ?></div></td></tr>
				<tr><td><div style='background: url("images/slots/SlotEarLeft.jpg") no-repeat; width: 40px; height: 40px;'><?php $this->GetItemIcon(11, $items); ?></div></td></tr>
				<tr><td><div style='background: url("images/slots/SlotEarRight.jpg") no-repeat; width: 40px; height: 40px;'><?php $this->GetItemIcon(12, $items); ?></div></td></tr>
				<tr><td><div style='background: url("images/slots/SlotNeck.jpg") no-repeat; width: 40px; height: 40px;'><?php $this->GetItemIcon(13, $items); ?></div></td></tr>
				<tr><td><div style='background: url("images/slots/SlotRingLeft.jpg") no-repeat; width: 40px; height: 40px;'><?php $this->GetItemIcon(9, $items); ?></div></td></tr>
				<tr><td><div style='background: url("images/slots/SlotRingRight.jpg") no-repeat; width: 40px; height: 40px;'><?php $this->GetItemIcon(10, $items); ?></div></td></tr>
				<tr><td><div style='background: url("images/slots/SlotWristRight.jpg") no-repeat; width: 40px; height: 40px;'><?php $this->GetItemIcon(15, $items); ?></div></td></tr>
				<tr><td><div style='background: url("images/slots/SlotWristLeft.jpg") no-repeat; width: 40px; height: 40px;'><?php $this->GetItemIcon(14, $items); ?></div></td></tr>
				<tr><td><div style='background: url("images/slots/SlotWaist.jpg") no-repeat; width: 40px; height: 40px;'><?php $this->GetItemIcon(18, $items); ?></div></td></tr>
				<tr><td><div style='background: url("images/slots/SlotAmmo.jpg") no-repeat; width: 40px; height: 40px;'><?php $this->GetItemIcon(17, $items); ?></div></td></tr>
				<tr><td><div style='background: url("images/slots/SlotRange.jpg") no-repeat; width: 40px; height: 40px;'><?php $this->GetItemIcon(16, $items); ?></div></td></tr>
				<tr><td><div style='background: url("images/slots/SlotSecondary.jpg") no-repeat; width: 40px; height: 40px;'><?php $this->GetItemIcon(1, $items); ?></div></td></tr>
				</table>
				</td>
			</tr>
		</table>
		</div>
		<?php
	}
	
	
	private function GetItemIcon($slot, $items)
	{
		if (array_key_exists($slot, $items))
		{
			$data = $items[$slot];
			?>
			<img src='characters/eq2Icon.php?id=<?= $data['icon'] ?>' title="<?= $data['name'] ?> (<?= $data['item_id'] ?>)" />
			<?php
		}
	}
	

	private function Character_Journal()
	{
	}
	

	private function Character_Knowledge()
	{
	}
	

	private function Character_Social()
	{
	}
	

	private function Character_Appearance()
	{
	}
	

	private function Character_Misc()
	{
	}


	/*
		Function: 
		Purpose	:	
		Params	: 
	*/
	private function DisplayCharacterGrid($characters)
	{
		global $eq2;
		
		?>
		<br>
		<div id="SelectGrid">
		<table cellspacing="0" id="SelectGrid" border="0">
			<tr>
				<td class="title">&nbsp;</td>
				<td colspan="7" align="center" class="title">Search Results</td>
				<td class="title">&nbsp;</td>
			</tr>
			<tr>
				<th width="5%">&nbsp;</th>
				<th width="5%">id</th>
				<th width="50%">name</th>
				<th width="10%">race</th>
				<th width="10%">class</th>
				<th width="10%">levels</th>
				<th width="5%">admin</th>
				<th width="5%">online</th>
				<th>&nbsp;</th>
			</tr>
			<?php
			
			$i = 0;
			foreach($characters as $data)
			{
				$RowColor = ( $i % 2 ) ? "row1" : "row2";
				//id, name, race, class, level, tradeskill_level, admin_status, is_online
			?>
			<tr class="<?= $RowColor ?>">
				<td class="detail">&nbsp;[&nbsp;<a href="<?= $eq2->PageLink ?>&id=<?= $data['id'] ?>">Edit</a>&nbsp;]</td>
				<td class="detail">&nbsp;<?= $data['id'] ?>&nbsp;</td>
				<td class="detail">&nbsp;<?= $data['name'] ?></td>
				<td class="detail">&nbsp;<?= $eq2->eq2PlayerRaces[$data['race']] ?></td>
				<td class="detail">&nbsp;<?= $eq2->eq2Classes[$data['class']] ?></td>
				<td class="detail">&nbsp;<?= $data['level'] ?>/<?= $data['tradeskill_level'] ?></td>
				<td class="detail">&nbsp;<?= $data['admin_status'] ?></td>
				<td class="detail">&nbsp;<? ( $data['is_online'] ) ? print("Yes") : print("No") ?></td>
				<td class="detail">&nbsp;</td>
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
	

}

?>