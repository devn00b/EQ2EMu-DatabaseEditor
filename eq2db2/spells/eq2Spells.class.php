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

class eq2Spells
{

	public function __construct()
	{
		include_once("eq2SpellsDB.class.php");
		$this->db = new eq2SpellsDB();
	}
	
	/*
		
	*/
	public function Start()
	{
		global $eq2;
		global $page_lc;
		global $type;
	
		print('<table cellspacing="0" id="main-body" class="main">');

		// start left menu
		print('<tr><td class="sidebar" nowrap="nowrap"><strong>Options:</strong><br />(none)<br />');

		// nav stuff here	
		printf('<br /><li><a href="%s">Reload Page</a><br />', $eq2->PageLink);
		if( isset($_GET['id']) ) 
			printf('<li><a href="%s">Back</a><br />', $eq2->BackLink);
		else
			printf('<li><br />');
		
		// Help Links / Stats Details
		printf('<br /><strong>Help:</strong><br />&nbsp;');
		
		printf('<br /><strong>Stats:</strong><br />&nbsp;');
		$this->SpellStats();
		
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
					case "summary"	: break;
					case "edit"			: 
					default					: $this->SpellLookup(); break;
				}
				break;
		}
		
		// end page
		print('</td></tr></table>');
	}


	// spells have to be done differently than characters... filter by class first, then display spells for that class (too many)
	private function SpellLookup()
	{
		global $eq2;

		?>
		<table class="SearchFilters">
			<tr>
				<th style="width:300px;">Filters</th>
				<th style="width:300px;">&nbsp;</th>
				<th style="text-align:left;">&nbsp;</th>
			</tr>
			<tr>
				<td><?php $this->TypePicker() ?></td>
				
				<?php
				if (isset($_GET['type'])) { ?>
					<td><?php $this->classificationPicker(); ?>
					<td>
						<?php
						// Commented this out and created the option box manually as this function will just tack on &class=
						// and not take you back to the search result which is the desired behavior in this case.
						//$eq2->ClassPicker();
						
						print('<select name="classPicker" onchange="dosub(this.options[this.selectedIndex].value)">');
						printf('<option value="%s">Pick a Class</option>', $eq2->PageLink);
						foreach($eq2->eq2Classes as $class=>$name)
							printf('<option value="index.php?page=spells&type=%s&classification=%s&class=%s"%s>%s (%s)</option>',
									$_GET['type'],
									$_GET['classification'],
									$class,
									(isset($_GET['class']) && $class == $_GET['class'] ) ? " selected" : "",
									$name,
									$class);								
							print('</select>');
						
						?>
					</td>
					<?php
				} 
				?>
			</tr>
			<tr>
				<?php
				if (isset($_GET['id'])) {
					?>
					<td colspan = 3>
						[ <a href="index.php?page=spells&type=<?= $_GET['type'] ?>">Back to Type</a> ]
						<?php if ( isset($_GET['classification']) ) { ?>&bull;&nbsp;[ <a href="index.php?page=spells&type=<?= $_GET['type'] ?>&classification=<?= $_GET['classification'] ?>">Back to Classification</a> ] <?php } ?>
						<?php if ( isset($_GET['class']) ) { ?>&bull;&nbsp;[ <a href="index.php?page=spells&type=all&classification=all&class=<?= $_GET['class'] ?>">Back to Class</a> ] <?php } ?>
					</td>
					<?php 
				}
				else {
				?>
				<td>Name<br />
					<form action="index.php?page=spells&type=edit" id="frmSearch" method="post">
						<input style="width:150px;" type="text" id="txtSearch" name="txtSearch" alt="Search Criteria" onkeyup="SpellLookupAJAX();" autocomplete="off" class="box" value="<?= isset($_POST['txtSearch']) ? $_POST['txtSearch'] : null ?>" onclick="this.value='';" />
						<input type="submit" id="cmdSearch" name="cmdSearch" value="Search" alt="Run Search" class="submit" />
						<input type="button" value="Clear" class="submit" onclick="dosub('index.php?page=spells&type=edit');" />
						<input type="hidden" name="cmd" value="SpellByName" />
						<div id="search_suggest">
						</div>
					</form>
				</td>
				<script>
					document.getElementById('txtSearch').focus = true;
					
					//Called from keyup on the search textbox.
					//Starts the AJAX request.
					function SpellLookupAJAX() {
						if (searchReq.readyState == 4 || searchReq.readyState == 0) {
							var str = escape(document.getElementById('txtSearch').value);
							searchReq.open("GET", 'spells/eq2SpellsAjax.php?type=lookup&search=' + str, true);
							searchReq.onreadystatechange = handleSearchSuggest; 
							searchReq.send(null);
						}		
					}
				</script>
				<?php 
				}
				?>
			</tr>
		</table>

		<br />
		<?php
		// Check if searchText was used to find a spell
		if( isset($_POST['txtSearch']) && strlen($_POST['txtSearch']) > 0 )
		{
			$arr = $this->db->GetSpellByName($_POST['txtSearch']);
			if( is_array($arr) )
			{
				$this->DisplaySpellGrid($arr);
			}
			else
			{
				$eq2->AddStatus('No spells match your search.');
			}
			$ret = true;
		}
		else if( isset($_POST['cmd']) && $_POST['cmd'] == 'SpellByName' )
		{
			//$eq2->AddStatus('Search must contain at least 1 letter/number.');
			//$ret = true;
			$arr = $this->db->GetSpellByName("all");
			if( is_array($arr) )
			{
				$this->DisplaySpellGrid($arr);
			}
			else
			{
				$eq2->AddStatus('No spells match your search.');
			}
			$ret = true;
		}
		// If a spell is selected, display data associated with the spell
		else if( isset($_GET['id']) )
		{
			$this->SpellEditor();
			$ret = true;
		}
		else if( isset($_GET['class']) ) 
		{ 
			$spells = $this->db->GetSpellsByClass($_GET['class']);
			if( !is_array($spells) )
				$eq2->AddStatus('No spells for this class!');
			else
				$this->DisplaySpellGrid($spells);
		}

		?>
		<div id="EditorStatus">
			<?php 
			if( !empty($eq2->Status) ) 
				$eq2->DisplayStatus(); 
			?>
		</div><!-- End EditorStatus -->
		<?php
		if( isset($GLOBALS['refresh']) && $GLOBALS['refresh']==1 )
			$eq2->RefreshPage(2); // usually only happens after a record is deleted permanently
		
		// is this even used?
		//if( $ret ) 
		//	return $ret;
	} // SpellLookup


	private function SpellStats()
	{
	}


	private function SpellEditor()
	{
		global $eq2;

		// Build the Tab menu
		$current_tab_idx = ( isset($_GET['tab']) ) ? $_GET['tab'] : 'general';
		$tab_array = array(
			'spell'			=> 'Spell',
			'tiers'			=> 'Tiers',
			'data'			=> 'Data',
			'effects'		=> 'Effects',
			'classes'		=> 'Classes',
			'script'		=> 'Script'
		);
		
		$class_id = $_GET['class'];
		$type = $_GET['type'];
		$classification = $_GET['classification'];
		$class = $_GET['class'];
		$spell_id = $_GET['id'];
		
		$querystring = sprintf("index.php?page=spells&type=%s", $_GET['type']);
		if( strlen($classification) > 0 )
			$querystring = sprintf("%s&classification=%s", $querystring, $_GET['classification']);
		if( $class > 0 )
			$querystring = sprintf("%s&class=%s", $querystring, $_GET['class']);
		$querystring .= sprintf("&id=%s", $spell_id);
				
				
		//$eq2->TabGenerator2($current_tab_idx, $tab_array);
		$eq2->TabGenerator($current_tab_idx, $tab_array, $querystring, false);

		switch($current_tab_idx)
		{
			case "tiers"		: $this->Spell_Tiers();	break;
			case "data"			: $this->Spell_Data();		break;
			case "effects"		: $this->Spell_Effects();	break;
			case "classes"		: $this->Spell_Classes();	break;
			case "script"		: $this->Spell_Script();	break;
			case "spell"		:
			default				: $this->Spells_General();	break;
		}

	}


	private function Spells_General()
	{
		global $eq2;

		if (isset($_POST['cmd'])) {
			switch($_POST['cmd']) {
				case "Save": $eq2->ProcessUpdate(); break;
			}
		}
		
		// Build Toggles array()
		$spells_toggles = array();
		$data = $this->db->GetSpellByID();
		
		$class_id = $_GET['class'];
		$type = $_GET['type'];
		$classification = $_GET['classification'];
		$class = $_GET['class'];
		$spell_id = $_GET['id'];
		
		$querystring = sprintf("index.php?page=spells&type=%s", $_GET['type']);
		if( strlen($classification) > 0 )
			$querystring = sprintf("%s&classification=%s", $querystring, $_GET['classification']);
		if( $class > 0 )
			$querystring = sprintf("%s&class=%s", $querystring, $_GET['class']);
		$querystring .= sprintf("&id=%s", $spell_id);
		?>
		<script>
		<!--
		function lockScriptName()
		{
		}
		-->
		</script>
		
		
		<form method="post" name="SpellForm">
		<table class="SubPanel" cellspacing="0">
			<tr>
				<td id="EditorStatus" colspan="2"><?php if( !empty($eq2->Status) ) $eq2->DisplayStatus(); ?></td>
			</tr>
			<tr>
			<tr>
				<td class="Title" colspan="2" align="center">
					Editing: <?= $data['name'] ?> (<?= $data['id'] ?>)
					<?php /*$spells->PrintOffsiteLinks();*/ ?>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<table class="SectionMain" cellspacing="0">
						<tr>
							<td class="SectionTitle">General</td>
						</tr>
						<tr>
							<td class="SectionBody">
							<fieldset><legend>Info and Links</legend> 
							<table cellspacing="0">
								<tr>
									<td width="10" class="Label">id:</td>
									<td class="ReadOnlyDetail"><?= $data['id'] ?><input type="hidden" name="orig_id" value="<?= $data['id'] ?>" /></td>
									<td width="120" class="Label">soe_spell_crc:</td>
									<td class="ReadOnlyDetail"><?= ( $data['soe_spell_crc'] > 0 ) ? $data['soe_spell_crc'] : "N/a" ?><input type="hidden" name="soe_spell_crc" value="<?= $data['soe_spell_crc'] ?>" /></td>
									<td width="130" class="Label">SOE Last Update:</td>
									<td class="ReadOnlyDetail"><?= ( $data['soe_last_update'] > 0 ) ? date('M.d.Y h:i:s', $data['soe_last_update']) : "N/a" ?><input type="hidden" name="soe_last_update" value="<?= $data['soe_last_update'] ?>" /></td>
									<td width="130" class="Label">Last Auto Update:</td>
									<td class="<?= $update_warning ?>"><?= ( $data['last_auto_update'] > 0 ) ? date('M.d.Y h:i:s', $data['last_auto_update']) : "N/a" ?><input type="hidden" name="last_auto_update" value="<?= $data['last_auto_update'] ?>" /></td>
								</tr>
							</table>
							</fieldset>
							</td>
						</tr>
						<tr>
							<td class="SectionBody">
							<fieldset><legend>Text</legend> 
							<table cellspacing="0">
								<tr>
									<td width="120" class="Label">name:</td>
									<td class="Detail">
										<input type="text" name="spells|name" value="<?php print($data['name']); ?>" style="width:150px;" />
										<input type="hidden" name="orig_name" value="<?= $data['name'] ?>" />
									</td>
									<td width="120" class="Label">lua_script:</td>
									<td class="Detail">
									<?php
										switch($data['spell_book_type'])
										{
											case 3:
												?>
												<select name="spells|lua_script" style="width:405px;">
													<option value="">---</option>
													<option value="Spells/Tradeskills/DurabilityAdd.lua"<?php if ( $data['lua_script']=='Spells/Tradeskills/DurabilityAdd.lua' ) echo " selected" ?>>DurabilityAdd.lua</option>
													<option value="Spells/Tradeskills/ProgressAdd.lua"<?php if ( $data['lua_script']=='Spells/Tradeskills/ProgressAdd.lua' ) echo " selected" ?>>ProgressAdd.lua</option>
													<option value="Spells/Tradeskills/DurabilityAddProgressAdd.lua"<?php if ( $data['lua_script']=='Spells/Tradeskills/DurabilityAddProgressAdd.lua' ) echo " selected" ?>>DurabilityAddProgressAdd.lua</option>
													<option value="Spells/Tradeskills/DurabilityModProgressAdd.lua"<?php if ( $data['lua_script']=='Spells/Tradeskills/DurabilityModProgressAdd.lua' ) echo " selected" ?>>DurabilityModProgressAdd.lua</option>
													<option value="Spells/Tradeskills/DurabilityModSuccessAdd.lua"<?php if ( $data['lua_script']=='Spells/Tradeskills/DurabilityModSuccessAdd.lua' ) echo " selected" ?>>DurabilityModSuccessAdd.lua</option>
													<option value="Spells/Tradeskills/ProgressModDurabilityAdd.lua"<?php if ( $data['lua_script']=='Spells/Tradeskills/ProgressModDurabilityAdd.lua' ) echo " selected" ?>>ProgressModDurabilityAdd.lua</option>
													<option value="Spells/Tradeskills/ProgressModSuccessAdd.lua"<?php if ( $data['lua_script']=='Spells/Tradeskills/ProgressModSuccessAdd.lua' ) echo " selected" ?>>ProgressModSuccessAdd.lua</option>
													<option value="Spells/Tradeskills/SuccessModDurabilityAdd.lua"<?php if ( $data['lua_script']=='Spells/Tradeskills/SuccessModDurabilityAdd.lua' ) echo " selected" ?>>SuccessModDurabilityAdd.lua</option>
													<option value="Spells/Tradeskills/SuccessModProgressAdd.lua"<?php if ( $data['lua_script']=='Spells/Tradeskills/SuccessModProgressAdd.lua' ) echo " selected" ?>>SuccessModProgressAdd.lua</option>
												</select>
												<?php
												break;
												
											default:
												printf('<input type="text" name="spells|lua_script" value="%s" style="width:405px;" />', $eq2->CheckLUAScriptExists($data['lua_script'])  ? $data['lua_script'] : ""); 
												break;
										}
									?>
									&nbsp;
									<input type="hidden" name="orig_lua_script" value="<?= $data['lua_script'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="Label">description:</td>
									<td colspan="3" class="Detail">
										<textarea name="spells|description" style="font:13px Arial, Helvetica, sans-serif; width:99%"><?php print($data['description']); ?></textarea>
										<input type="hidden" name="orig_description" value="<?= $data['description'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="Label">effect_message:</td>
									<td colspan="3" class="Detail">
										<input type="text" name="spells|effect_message" value="<?php print($data['effect_message']); ?>" style="width:500px" />
										<input type="hidden" name="orig_effect_message" value="<?= $data['effect_message'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="Label">success_message:</td>
									<td colspan="3" class="Detail">
										<input type="text" name="spells|success_message" value="<?php print($data['success_message']); ?>" style="width:500px" />
										<input type="hidden" name="orig_success_message" value="<?= $data['success_message'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="Label">fade_message:</td>
									<td colspan="3" class="Detail">
										<input type="text" name="spells|fade_message" value="<?php print($data['fade_message']); ?>" style="width:500px" />
										<input type="hidden" name="orig_fade_message" value="<?= $data['fade_message'] ?>" />
									</td>
								</tr>
							</table>
							</fieldset>
						</tr>
						<tr>
							<td class="SectionBody">
							<fieldset style="height:153px"><legend>Settings</legend> 
							<table cellspacing="0">
								<tr>
									<td width="100" class="Label">class_skill:</td>
									<td class="Detail">
										
										<select name="spells|class_skill" style="width:150px;">
											<option value="0">---</option>
											<?php $eq2->GetSkillsOptions($data['class_skill']); ?>
										</select>
										<input type="hidden" name="orig_class_skill" value="<?= $data['class_skill'] ?>" />
									</td>
									<td width="150" class="Label">mastery_skill:</td>
									<td class="Detail">
									
										<select name="spells|mastery_skill" style="width:150px;">
											<option value="0">---</option>
											<?php $eq2->GetSkillsOptions($data['mastery_skill']); ?>
										</select>
										<input type="hidden" name="orig_mastery_skill" value="<?= $data['mastery_skill'] ?>" />
									</td>
									<td width="200" class="Label">min_class_skill_req:</td>
									<td class="Detail">
										<input type="text" name="spells|min_class_skill_req" value="<?php print($data['min_class_skill_req']); ?>" style="width:50px;" />
										<input type="hidden" name="orig_min_class_skill_req" value="<?= $data['min_class_skill_req'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="Label">target_type:</td>
									<td class="Detail">
										<select name="spells|target_type" style="width:150px;">
											<option value="0"<?php if ( $data['target_type']==0 ) echo " selected" ?>>Self</option>
											<option value="1"<?php if ( $data['target_type']==1 ) echo " selected" ?>>Enemy</option>
											<option value="2"<?php if ( $data['target_type']==2 ) echo " selected" ?>>Group AE</option>
											<option value="3"<?php if ( $data['target_type']==3 ) echo " selected" ?>>Caster Pet</option>
											<option value="4"<?php if ( $data['target_type']==4 ) echo " selected" ?>>Enemy Pet</option>
											<option value="5"<?php if ( $data['target_type']==5 ) echo " selected" ?>>Enemy Corpse</option>
											<option value="6"<?php if ( $data['target_type']==6 ) echo " selected" ?>>Group Corpse</option>
											<option value="7"<?php if ( $data['target_type']==7 ) echo " selected" ?>>None</option>
											<option value="8"<?php if ( $data['target_type']==8 ) echo " selected" ?>>Raid or Group Friend</option>
											<option value="9"<?php if ( $data['target_type']==9 ) echo " selected" ?>>Other Group</option>
										</select>
										<input type="hidden" name="orig_target_type" value="<?= $data['target_type'] ?>" />
									</td>
									<td class="Label">(spell) type:</td>
									<td class="Detail">
										<select name="spells|type" style="width:150px;">
											<option value="0"<?php if ( $data['type']==0 ) echo " selected" ?>>Spell</option>
											<option value="1"<?php if ( $data['type']==1 ) echo " selected" ?>>Combat Art</option>
											<option value="2"<?php if ( $data['type']==2 ) echo " selected" ?>>Ability</option>
											<option value="3"<?php if ( $data['type']==3 ) echo " selected" ?>>Crafting</option>
											<option value="4"<?php if ( $data['type']==4 ) echo " selected" ?>>Passive</option>
										</select>
										<input type="hidden" name="orig_type" value="<?= $data['type'] ?>" />
									</td>
									<td class="Label">deity:</td>
									<td class="Detail">
										<input type="text" name="spells|deity" value="<?php print($data['deity']); ?>" style="width:50px;" />
										<input type="hidden" name="orig_deity" value="<?= $data['deity'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="Label">cast_type:</td>
									<td class="Detail">
										<select name="spells|cast_type" style="width:150px;">
											<option value="0"<?php if ( $data['cast_type']==0 ) echo " selected" ?>>Normal</option>
											<option value="1"<?php if ( $data['cast_type']==1 ) echo " selected" ?>>Toggle</option>
										</select>
										<input type="hidden" name="orig_cast_type" value="<?= $data['cast_type'] ?>" />
									</td>
									<td class="Label">spell_book_type:</td>
									<td class="Detail">
										<select name="spells|spell_book_type" style="width:150px;">
											<option value="0"<?php if ( $data['spell_book_type']==0 ) echo " selected" ?>>Spell</option>
											<option value="1"<?php if ( $data['spell_book_type']==1 ) echo " selected" ?>>Combat Art</option>
											<option value="2"<?php if ( $data['spell_book_type']==2 ) echo " selected" ?>>Ability</option>
											<option value="3"<?php if ( $data['spell_book_type']==3 ) echo " selected" ?>>Tradeskill</option>
											<option value="4"<?php if ( $data['spell_book_type']==4 ) echo " selected" ?>>Not Shown</option>
										</select>
										<input type="hidden" name="orig_spell_book_type" value="<?= $data['spell_book_type'] ?>" />
									</td>
									<td class="Label">linked_timer_id:</td>
									<td class="Detail">
										<input type="text" name="spells|linked_timer_id" value="<?php print($data['linked_timer_id']); ?>" style="width:50px;" />
										<input type="hidden" name="orig_linked_timer_id" value="<?= $data['linked_timer_id'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="Label">det_type:</td>
									<td class="Detail">
										<select name="spells|det_type" style="width:150px;">
											<option value="0">---</option>
											<option value="1"<?php if ( $data['det_type']==1 ) echo " selected" ?>>Trauma</option>
											<option value="2"<?php if ( $data['det_type']==2 ) echo " selected" ?>>Arcane</option>
											<option value="3"<?php if ( $data['det_type']==3 ) echo " selected" ?>>Noxious</option>
											<option value="4"<?php if ( $data['det_type']==4 ) echo " selected" ?>>Elemental</option>
											<option value="5"<?php if ( $data['det_type']==5 ) echo " selected" ?>>Curse</option>
										</select>
										<input type="hidden" name="orig_det_type" value="<?= $data['det_type'] ?>" />
									</td>
									<td class="Label">control_effect_type:</td>
									<td style="padding-left:4px;">
										<select name="spells|control_effect_type" style="width:150px;">
											<option value="0">---</option>
											<option value="1"<?php if ( $data['control_effect_type']==1 ) echo " selected" ?>>Mez</option>
											<option value="2"<?php if ( $data['control_effect_type']==2 ) echo " selected" ?>>Stifle</option>
											<option value="3"<?php if ( $data['control_effect_type']==3 ) echo " selected" ?>>Daze</option>
											<option value="4"<?php if ( $data['control_effect_type']==4 ) echo " selected" ?>>Stun</option>
										</select>
										<input type="hidden" name="orig_control_effect_type" value="<?= $data['control_effect_type'] ?>" />
									</td>
									<td class="Label">casting_flags:</td>
									<td class="Detail">
										<input type="text" name="spells|casting_flags" value="<?php print($data['casting_flags']); ?>" style="width:50px;" />
										<input type="hidden" name="orig_casting_flags" value="<?= $data['casting_flags'] ?>" />
									</td>
									<td colspan="4">&nbsp;</td>
								</tr>
								<tr>
									<td class="Label">savage_bar:</td>
									<td class="Detail">
										<input type="text" name="spells|savage_bar" value="<?php print($data['savage_bar']); ?>" style="width:145px;" />
										<input type="hidden" name="orig_savage_bar" value="<?= $data['savage_bar'] ?>" />
									</td>
									<td class="Label">savage_bar_slot:</td>
									<td class="Detail">
										<input type="text" name="spells|savage_bar_slot" value="<?php print($data['savage_bar_slot']); ?>" style="width:145px;" />
										<input type="hidden" name="orig_savage_bar_slot" value="<?= $data['savage_bar_slot'] ?>" />
									</td>
									<td class="Label">spell_type:</td>
									<td class="Detail">
										<select style="width:100px;" name="spells|spell_type">
											<option<?php if ($data['spell_type'] == "Unset") printf(" selected")?>>Unset</option>
											<option<?php if ($data['spell_type'] == "DD") printf(" selected")?>>DD</option>
											<option<?php if ($data['spell_type'] == "DoT") printf(" selected")?>>DoT</option>
											<option<?php if ($data['spell_type'] == "Heal") printf(" selected")?>>Heal</option>
											<option<?php if ($data['spell_type'] == "HoT-Ward") printf(" selected")?>>HoT-Ward</option>
											<option<?php if ($data['spell_type'] == "Debuff") printf(" selected")?>>Debuff</option>
											<option<?php if ($data['spell_type'] == "Buff") printf(" selected")?>>Buff</option>
											<option<?php if ($data['spell_type'] == "CombatBuff") printf(" selected")?>>CombatBuff</option>
											<option<?php if ($data['spell_type'] == "Taunt") printf(" selected")?>>Taunt</option>
											<option<?php if ($data['spell_type'] == "Detaunt") printf(" selected")?>>Detaunt</option>
											<option<?php if ($data['spell_type'] == "Rez") printf(" selected")?>>Rez</option>
											<option<?php if ($data['spell_type'] == "Cure") printf(" selected")?>>Cure</option>
										</select>
										<input type="hidden" name="orig_spell_type" value="<?= $data['spell_type'] ?>" />
									</td>
									<!-- <td colspan="2">&nbsp;</td> -->
								</tr>
							</table>
							</fieldset>
						</tr>
					</table>
				</td>
				<td valign="top">
					<table class="SectionToggles" cellspacing="0">
						<tr>
							<td class="SectionTitle">Misc</td>
						</tr>
						<tr>
							<td class="SecionBody">
								<fieldset><legend>Appearance</legend> 
								<table width="100%" border="0">
									<tr>
										<td class="Label"><span>icon</span></td>
										<td>
											<input type="text" name="spells|icon" value="<?php print($data['icon']); ?>" style="width:50px" />
											<input type="hidden" name="orig_icon" value="<?= $data['icon'] ?>" />
										</td>
									</tr>
									<tr>
										<td class="Label"><span>icon_heroic_op</span></td>
										<td>
											<input type="text" name="spells|icon_heroic_op" value="<?php print($data['icon_heroic_op']); ?>" style="width:50px" />&nbsp;
											<input type="hidden" name="orig_icon_heroic_op" value="<?= $data['icon_heroic_op'] ?>" />
										</td>
									</tr>
									<tr>
										<td class="Label"><span>icon_backdrop</span></td>
										<td>
											<input type="text" name="spells|icon_backdrop" value="<?php print($data['icon_backdrop']); ?>" style="width:50px" />&nbsp;
											<input type="hidden" name="orig_icon_backdrop" value="<?= $data['icon_backdrop'] ?>" />
										</td>
									</tr>
									<tr>
										<td class="Label">spell_visual:</td>
										<td>
											<input type="text" name="spells|spell_visual" value="<?php print($data['spell_visual']) ?>"  style="width:50px;" />
											<input type="hidden" name="orig_spell_visual" value="<?php print($data['spell_visual']) ?>" />
										</td>
									</tr>
									<tr>
										<td colspan="2" align="center">&nbsp;<input type="button" value="Lookup Effect" style="font-size:10px; width:100px;" onclick="javascript:window.open('spell_func.php?type=effects','luVS','width=1024,height=768,left=1,top=1,scrollbars=yes');" /></td>
									</tr>
								</table>
								</fieldset>
							</td>
						</tr>
						<tr>
							<td class="SectionBody">
								<fieldset><legend>Toggles</legend> 
								<table cellspacing="0">
									<tr>
										<td class="Label">affect_only_group_members:</td>
										<td>
											<select name="spells|affect_only_group_members" style="width:70px">
												<option value="0"<?php if( $data['affect_only_group_members'] == 0 ) print(" selected") ?>>false</option>
												<option value="1"<?php if( $data['affect_only_group_members'] == 1 ) print(" selected") ?>>true</option>
											</select>
											<input type="hidden" name="orig_affect_only_group_members" value="<?= $data['affect_only_group_members'] ?>" />
										</td>
									</tr>
									<tr>
										<td class="Label">persist_through_death:</td>
										<td>
											<select name="spells|persist_through_death" style="width:70px">
												<option value="0"<?php if( $data['persist_through_death'] == 0 ) print(" selected") ?>>false</option>
												<option value="1"<?php if( $data['persist_through_death'] == 1 ) print(" selected") ?>>true</option>
											</select>
											<input type="hidden" name="orig_persist_through_death" value="<?= $data['persist_through_death'] ?>" />
										</td>
									</tr>
									<tr>
										<td class="Label">duration_until_cancel</td>
										<td>
											<select name="spells|duration_until_cancel" style="width:70px">
												<option value="0"<?php if( $data['duration_until_cancel'] == 0 ) print(" selected") ?>>false</option>
												<option value="1"<?php if( $data['duration_until_cancel'] == 1 ) print(" selected") ?>>true</option>
											</select>
											<input type="hidden" name="orig_duration_until_cancel" value="<?= $data['duration_until_cancel'] ?>" />
										</td>
									</tr>
									<tr>
										<td class="Label">cast_while_moving:</td>
										<td>
											<select name="spells|cast_while_moving" style="width:70px">
												<option value="0"<?php if( $data['cast_while_moving'] == 0 ) print(" selected") ?>>false</option>
												<option value="1"<?php if( $data['cast_while_moving'] == 1 ) print(" selected") ?>>true</option>
											</select>
											<input type="hidden" name="orig_cast_while_moving" value="<?= $data['cast_while_moving'] ?>" />
										</td>
									</tr>
									<tr>
										<td class="Label">display_spell_tier:</td>
										<td>
											<select name="spells|display_spell_tier" style="width:70px">
												<option value="0"<?php if( $data['display_spell_tier'] == 0 ) print(" selected") ?>>false</option>
												<option value="1"<?php if( $data['display_spell_tier'] == 1 ) print(" selected") ?>>true</option>
											</select>
											<input type="hidden" name="orig_display_spell_tier" value="<?= $data['display_spell_tier'] ?>" />
										</td>
									</tr>
									<tr>
										<td class="Label">can_effect_raid:</td>
										<td>
											<select name="spells|can_effect_raid" style="width:70px">
												<option value="0"<?php if( $data['can_effect_raid'] == 0 ) print(" selected") ?>>false</option>
												<option value="1"<?php if( $data['can_effect_raid'] == 1 ) print(" selected") ?>>true</option>
											</select>
											<input type="hidden" name="orig_can_effect_raid" value="<?= $data['can_effect_raid'] ?>" />
										</td>
									</tr>
									<tr>
										<td class="Label">not_maintained</td>
										<td>
											<select name="spells|not_maintained" style="width:70px">
												<option value="0"<?php if( $data['not_maintained'] == 0 ) print(" selected") ?>>false</option>
												<option value="1"<?php if( $data['not_maintained'] == 1 ) print(" selected") ?>>true</option>
											</select>
											<input type="hidden" name="orig_not_maintained" value="<?= $data['not_maintained'] ?>" />
										</td>
									</tr>
									<tr>
										<td class="Label">interruptable:</td>
										<td>
											<select name="spells|interruptable" style="width:70px">
												<option value="0"<?php if( $data['interruptable'] == 0 ) print(" selected") ?>>false</option>
												<option value="1"<?php if( $data['interruptable'] == 1 ) print(" selected") ?>>true</option>
											</select>
											<input type="hidden" name="orig_interruptable" value="<?= $data['interruptable'] ?>" />
										</td>
									</tr>
									<tr>
										<td class="Label">friendly_spell</td>
										<td>
											<select name="spells|friendly_spell" style="width:70px">
												<option value="0"<?php if( $data['friendly_spell'] == 0 ) print(" selected") ?>>false</option>
												<option value="1"<?php if( $data['friendly_spell'] == 1 ) print(" selected") ?>>true</option>
											</select>
											<input type="hidden" name="orig_friendly_spell" value="<?= $data['friendly_spell'] ?>" />
										</td>
									</tr>
									<tr>
										<td class="Label">group_spell:</td>
										<td>
											<select name="spells|group_spell" style="width:70px">
												<option value="0"<?php if( $data['group_spell'] == 0 ) print(" selected") ?>>false</option>
												<option value="1"<?php if( $data['group_spell'] == 1 ) print(" selected") ?>>true</option>
											</select>
											<input type="hidden" name="orig_group_spell" value="<?= $data['group_spell'] ?>" />
										</td>
									</tr>
									<tr>
										<td class="Label">incurable:</td>
										<td>
											<select name="spells|incurable" style="width:70px">
												<option value="0"<?php if( $data['incurable'] == 0 ) print(" selected") ?>>false</option>
												<option value="1"<?php if( $data['incurable'] == 1 ) print(" selected") ?>>true</option>
											</select>
											<input type="hidden" name="orig_incurable" value="<?= $data['incurable'] ?>" />
										</td>
									</tr>
									<tr>
										<td class="Label">is_deity:</td>
										<td>
											<select name="spells|is_deity" style="width:70px">
												<option value="0"<?php if( $data['is_deity'] == 0 ) print(" selected") ?>>false</option>
												<option value="1"<?php if( $data['is_deity'] == 1 ) print(" selected") ?>>true</option>
											</select>
											<input type="hidden" name="orig_is_deity" value="<?= $data['is_deity'] ?>" />
										</td>
									</tr>
									<tr>
										<td class="Label">is_aa:</td>
										<td>
											<select name="spells|is_aa" style="width:70px">
												<option value="0"<?php if( $data['is_aa'] == 0 ) print(" selected") ?>>false</option>
												<option value="1"<?php if( $data['is_aa'] == 1 ) print(" selected") ?>>true</option>
											</select>
											<input type="hidden" name="orig_is_aa" value="<?= $data['is_aa'] ?>" />
										</td>
									</tr>
									<tr>
										<td class="Label"><span style="color:#F00">is_active</span>:</td>
										<td>
											<select name="spells|is_active" style="width:70px">
												<option value="0"<?php if( $data['is_active'] == 0 ) print(" selected") ?>>false</option>
												<option value="1"<?php if( $data['is_active'] == 1 ) print(" selected") ?>>true</option>
												<option value="2"<?php if( $data['is_active'] == 2 ) print(" selected") ?>>hidden</option>
											</select>
											<input type="hidden" name="orig_is_active" value="<?= $data['is_active'] ?>" />
										</td>
									</tr>
								</table>
								</fieldset>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<?php 
			if(true)//$eq2->CheckAccess(G_DEVELOPER)) 
			{ 
				// for now, we'll leave it disabled always
				$enable_soe = false;
				if( $enable_soe )
					$soe_button_text = 'title="Updates Spell data from SOE Data feed."';
				else
					$soe_button_text = 'title="When SOE updates their spell data, this button will re-sync with their data." disabled';
			?>
			<tr>
				<td align="center" colspan="2" valign="middle" height="40">
					<input type="submit" name="cmd" value="Save" class="submit" />&nbsp;
					<input type="button" value="Re-Index" class="submit" onclick="dosub('<?= $querystring ?>&action=reindex'); return false;" />&nbsp;
					<input type="button" value="Clone" class="submit" onclick="dosub('<?= $querystring ?>&action=clone'); return false;" />&nbsp;
					<input type="button" value="Delete" class="submit" onclick="dosub('<?= $querystring ?>&action=delete'); return false;" />&nbsp;
					<?php 
					if(true)//$eq2->CheckAccess(G_SUPERADMIN)) 
					{ 
					?>
					<input type="button" value="Insert" class="submit" onclick="dosub('<?= $querystring ?>&action=insert'); return false;" />&nbsp;
					<input type="button" value="Split" class="submit" onclick="dosub('<?= $querystring ?>&action=split'); return false;" />&nbsp;
					<?php 
					} 
					?>
					<input type="submit" name="cmd" value="SOE" class="submit" <?= $soe_button_text ?> />&nbsp;
					<input type="submit" name="cmd" value="RAW" class="submit" <?php /*if( !$spells->CheckRawSpellExists() )*/ print("disabled") ?> />&nbsp;
					<input type="hidden" name="object" value="<?= $data['name'] ?>" />
					<input type="hidden" name="table" value="spells" />
				</td>
			</tr>
			<?php 
			} 
			?>
		</table>
		</form>
		<?php		
	}
	
	function Spell_Tiers()
	{
		global $eq2;

		if (isset($_POST['cmd']))
		{
			echo("cmd is set");
			switch ($_POST['cmd'])
			{
				case "Update":
				echo("Update");
					$eq2->ProcessUpdate();
					break;

				case "Delete":
				echo("Delete");
					$eq2->ProcessDelete(NULL);
					break;

				case "Insert":
					$eq2->ProcessInsert(NULL);
					break;
			}
		}
		
		$spell = $this->db->GetSpellByID();
		$rows = $this->db->GetSpellTiers($_GET['id']);
	
		?>
		<div id="Editor">
			<table class="SubPanel" cellspacing="0" border="0">
				<tr>
					<td id="EditorStatus" colspan="2"><?php if( !empty($eq2->Status) ) $eq2->DisplayStatus(); ?></td>
				</tr>
				<tr>
					<td class="Title" colspan="2" align="center">
						Editing: <?= $spell['name'] ?> (<?= $spell['id'] ?>)
						<?php $this->PrintOffsiteLinks($spell); ?>
					</td>
				</tr>
				<?php

				foreach($rows as $row)
				{
					$tier = $row['tier'];
					$tier_id = $row['id'];
			
					if( $spell['is_aa'] && $tier > 0 )
					{
						$tier_text = sprintf("AA Level %s Data", $tier);
						$tier_options = $this->GetSpellAALevels($row['tier']); 
					}
					else
					{
						$tier_text = $this->PrintTierName($tier);
						$tier_options = $this->GetSpellTiers($row['tier']); 
					}
					?>
				<tr>
					<td valign="top" align="left">
						<form method="post" name="multiForm|<?php print($row['id']); ?>">
						<table class="SectionMainFloat" cellspacing="0" border="0">	
							<tr>
								<td colspan="8" class="SectionTitle"><?php print($tier_text); ?></td>
							</tr>
							<tr>
								<td width="120" class="Label">id:</td>
								<td width="70" class="Detail">
									<input type="text" name="spell_tiers|id" value="<?php print($row['id']) ?>"  style="width:50px;  background-color:#ddd;" readonly />
									<input type="hidden" name="orig_id" value="<?php print($row['id']) ?>" />
								</td>
								<td width="125" class="Label">spell_id:</td>
								<td width="70" class="Detail">
									<input type="text" name="spell_tiers|spell_id" value="<?php print($row['spell_id']) ?>"  style="width:50px; background-color:#ddd;" readonly />
									<input type="hidden" name="orig_spell_id" value="<?php print($row['spell_id']) ?>" />
								</td>
								<td align="right" width="100" class="Label"><?= ( $spell['is_aa'] ) ? "level" : "tier" ?>:</td>
								<td width="130" class="Detail">
									<select name="spell_tiers|tier" style="width:125px;">
										<?php print($tier_options); ?>
									</select>
									<input type="hidden" name="orig_tier" value="<?php print($row['tier']) ?>" />
								</td>
								<td colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td class="Label">hp_req:</td>
								<td class="Detail">
									<input type="text" name="spell_tiers|hp_req" value="<?php print($row['hp_req']); ?>" style="width:50px" />&nbsp;
									<input type="hidden" name="orig_hp_req" value="<?= $row['hp_req'] ?>" />
								</td>
								<td class="Label">hp_req_percent:</td>
								<td class="Detail">
									<input type="text" name="spell_tiers|hp_req_percent" value="<?php print($row['hp_req_percent']); ?>" style="width:50px" />&nbsp;
									<input type="hidden" name="orig_hp_req_percent" value="<?= $row['hp_req_percent'] ?>" />
								</td>
								<td class="Label">hp_upkeep:</td>
								<td class="Detail">
									<input type="text" name="spell_tiers|hp_upkeep" value="<?php print($row['hp_upkeep']); ?>" style="width:50px" />&nbsp;
									<input type="hidden" name="orig_hp_upkeep" value="<?= $row['hp_upkeep'] ?>" />
								</td>
								<td colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td class="Label">power_req:</td>
								<td class="Detail">
									<input type="text" name="spell_tiers|power_req" value="<?php print($row['power_req']); ?>" style="width:50px" />&nbsp;
									<input type="hidden" name="orig_power_req" value="<?= $row['power_req'] ?>" />
								</td>
								<td class="Label">power_req_percent:</td>
								<td class="Detail">
									<input type="text" name="spell_tiers|power_req_percent" value="<?php print($row['power_req_percent']); ?>" style="width:50px" />&nbsp;
									<input type="hidden" name="orig_power_req_percent" value="<?= $row['power_req_percent'] ?>" />
								</td>
								<td class="Label">power_upkeep:</td>
								<td class="Detail">
									<input type="text" name="spell_tiers|power_upkeep" value="<?php print($row['power_upkeep']); ?>" style="width:50px" />&nbsp;
									<input type="hidden" name="orig_power_upkeep" value="<?= $row['power_upkeep'] ?>" />
								</td>
								<td colspan="2">&nbsp;</td>
							</tr>
						
							<tr>
								<td class="Label">savagery_req:</td>
								<td class="Detail">
									<input type="text" name="spell_tiers|savagery_req" value="<?php print($row['savagery_req']); ?>" style="width:50px" />&nbsp;
									<input type="hidden" name="orig_savagery_req" value="<?= $row['savagery_req'] ?>" />
								</td>
								<td class="Label">savagery_req_percent:</td>
								<td class="Detail">
									<input type="text" name="spell_tiers|savagery_req_percent" value="<?php print($row['savagery_req_percent']); ?>" style="width:50px" />&nbsp;
									<input type="hidden" name="orig_savagery_req_percent" value="<?= $row['savagery_req_percent'] ?>" />
								</td>
								<td class="Label">savagery_upkeep:</td>
								<td class="Detail">
									<input type="text" name="spell_tiers|savagery_upkeep" value="<?php print($row['savagery_upkeep']); ?>" style="width:50px" />&nbsp;
									<input type="hidden" name="orig_savagery_upkeep" value="<?= $row['savagery_upkeep'] ?>" />
								</td>
								<td colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td class="Label">dissonance_req:</td>
								<td class="Detail">
									<input type="text" name="spell_tiers|dissonance_req" value="<?php print($row['dissonance_req']); ?>" style="width:50px" />&nbsp;
									<input type="hidden" name="orig_dissonance_req" value="<?= $row['dissonance_req'] ?>" />
								</td>
								<td class="Label">dissonance_req_percent:</td>
								<td class="Detail">
									<input type="text" name="spell_tiers|dissonance_req_percent" value="<?php print($row['dissonance_req_percent']); ?>" style="width:50px" />&nbsp;
									<input type="hidden" name="orig_dissonance_req_percent" value="<?= $row['dissonance_req_percent'] ?>" />
								</td>
								<td class="Label">dissonance_upkeep:</td>
								<td class="Detail">
									<input type="text" name="spell_tiers|dissonance_upkeep" value="<?php print($row['dissonance_upkeep']); ?>" style="width:50px" />&nbsp;
									<input type="hidden" name="orig_dissonance_upkeep" value="<?= $row['dissonance_upkeep'] ?>" />
								</td>
								<td colspan="2">&nbsp;</td>
							</tr>
						
							<tr>
								<td class="Label">req_concentration:</td>
								<td class="Detail">
									<input type="text" name="spell_tiers|req_concentration" value="<?php print($row['req_concentration']); ?>" style="width:50px" />&nbsp;
									<input type="hidden" name="orig_req_concentration" value="<?= $row['req_concentration'] ?>" />
								</td>
								<td colspan="2">&nbsp;</td>
								<td class="Label">resistibility:</td>
								<td class="Detail">
									<input type="text" name="spell_tiers|resistibility" value="<?php print($row['resistibility']); ?>" style="width:50px" />&nbsp;
									<input type="hidden" name="orig_resistibility" value="<?= $row['resistibility'] ?>" />
								</td>
								<td class="Label">hit_bonus:</td>
								<td class="Detail">
									<input type="text" name="spell_tiers|hit_bonus" value="<?php print($row['hit_bonus']); ?>" style="width:50px" />&nbsp;
									<input type="hidden" name="orig_hit_bonus" value="<?= $row['hit_bonus'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="Label" title="in 100ths of a second">cast_time:</td>
								<td class="Detail">
									<input type="text" name="spell_tiers|cast_time" value="<?php print($row['cast_time']); ?>" style="width:50px" />&nbsp;
									<input type="hidden" name="orig_cast_time" value="<?= $row['cast_time'] ?>" />
								</td>
								<td class="Label" title="in 10ths of a second">recovery:</td>
								<td class="Detail">
									<input type="text" name="spell_tiers|recovery" value="<?php print($row['recovery']); ?>" style="width:50px" />&nbsp;
									<input type="hidden" name="orig_recovery" value="<?= $row['recovery'] ?>" />
								</td>
								<td class="Label" title="in seconds">recast:</td>
								<td class="Detail">
									<input type="text" name="spell_tiers|recast" value="<?php print($row['recast']); ?>" style="width:50px" />&nbsp;
									<input type="hidden" name="orig_recast" value="<?= $row['recast'] ?>" />
								</td>
								<td class="Label" title="in 10ths of a second">call_frequency:</td>
								<td class="Detail">
									<input type="text" name="spell_tiers|call_frequency" value="<?php print($row['call_frequency']); ?>" style="width:50px" />&nbsp;
									<input type="hidden" name="orig_call_frequency" value="<?= $row['call_frequency'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="Label">radius:</td>
								<td class="Detail">
									<input type="text" name="spell_tiers|radius" value="<?php print($row['radius']); ?>" style="width:50px" />&nbsp;
									<input type="hidden" name="orig_radius" value="<?= $row['radius'] ?>" />
								</td>
								<td class="Label">max_aoe_targets:</td>
								<td class="Detail">
									<input type="text" name="spell_tiers|max_aoe_targets" value="<?php print($row['max_aoe_targets']); ?>" style="width:50px" />&nbsp;
									<input type="hidden" name="orig_max_aoe_targets" value="<?= $row['max_aoe_targets'] ?>" />
								</td>
								<td class="Label">range:</td>
								<td class="Detail">
									<input type="text" name="spell_tiers|range" value="<?php print($row['range']); ?>" style="width:50px" />&nbsp;
									<input type="hidden" name="orig_range" value="<?= $row['range'] ?>" />
								</td>
								<td class="Label">min_range:</td>
								<td class="Detail">
									<input type="text" name="spell_tiers|min_range" value="<?php print($row['min_range']); ?>" style="width:50px" />&nbsp;
									<input type="hidden" name="orig_min_range" value="<?= $row['min_range'] ?>" />
								</td>
							</tr>
							<tr>
								<td class="Label" title="in 10ths of a second">duration1:</td>
								<td class="Detail">
									<input type="text" name="spell_tiers|duration1" value="<?php print($row['duration1']); ?>" style="width:50px" />&nbsp;
									<input type="hidden" name="orig_duration1" value="<?= $row['duration1'] ?>" />
								</td>
								<td class="Label" title="in 10ths of a second">duration2:</td>
								<td class="Detail">
									<input type="text" name="spell_tiers|duration2" value="<?php print($row['duration2']); ?>" style="width:50px" />&nbsp;
									<input type="hidden" name="orig_duration2" value="<?= $row['duration2'] ?>" />
								</td>
								<td class="Label">given_by:</td>
								<td class="Detail">
									<?php 
									$options = NULL;
									foreach($this->eq2SOESpellClassifications as $classification) 
										$options .= sprintf('<option%s>%s</option>', ( $row['given_by'] == $classification ) ? " selected" : "", $classification);
									?>
									<select name="spell_tiers|given_by" style="width:150px;">
										<?php print($options); ?>
									</select>
									<input type="hidden" name="orig_given_by" value="<?= $row['given_by'] ?>" />
								</td>
								<td class="Label">unknown9:</td>
								<td class="Detail">
									<input type="text" name="spell_tiers|unknown9" value="<?php print($row['unknown9']); ?>" style="width:50px" />&nbsp;
									<input type="hidden" name="orig_unknown9" value="<?= $row['unknown9'] ?>" />
								</td>
							</tr>
							<?php if(true /*$eq2->CheckAccess(G_DEVELOPER)*/) { ?>
							<tr>
								<td colspan="8" align="center">
									<input type="submit" name="cmd" value="Update" class="submit" />&nbsp;
									<input type="submit" name="cmd" value="Delete" class="submit" />
									<input type="submit" name="cmd" value="SOE" class="submit" />&nbsp;
									<input type="hidden" name="object" value="<?= $spell['name'] ?>|<?= $spell['id'] ?>|Tier:<?= $row['tier'] ?>" />
									<input type="hidden" name="table" value="spell_tiers" />
									<input type="hidden" name="soe_spell_crc" value="<?php print($spell['soe_spell_crc']) ?>" />
									<input type="hidden" name="tier" value="<?php print($row['tier']) ?>" />
									<input type="hidden" name="deleteTier" value="1" />
								</td>
							</tr>
							<?php
							}
							?>
						</table>
						</form>
					</td>
				</tr>
				<?php
				}
	
				if( /*$eq2->CheckAccess(G_DEVELOPER) &&*/ !$spell['is_aa'] ) 
				{
					foreach($this->eq2SpellTiers as $key=>$val)
					{
						if( $key > $tier )
						{
							$next_tier = $key;
							break;
						}
					}
			
					if( $next_tier > 0 && $next_tier <= 12 )
					{
						$tier_options = $this->GetSpellTiers($next_tier); 
				
						/*
						 * Notes on the convoluted manner of inserting a new tier :|
						 * $tier_id = the PK of the record used as a base (so you don't have to re-type all tier data)
						 * $tier = the last known Tier ID
						 * The new tier ID is derived from the $tier_options selected
						 * $_POST['spell_id'] passes the spell ID
						 *
						 * Note that deleting 1 tier, or inserting 1 tier, inserts also a spell_data and spell_display_effects record(s)
						 */
						?>
				<tr>
					<td height="50" valign="bottom">Click &quot;Insert&quot; to add a new <em>tier</em> to this specific spell; tiers being Apprentice II, Adept I, or Master I, etc.</td>
				</tr>
				<tr>
					<td valign="top">
						<form method="post" name="singleForm|new">
						<table class="SectionMain" width="98.5%" cellspacing="0" border="0">
							<tr>
								<td colspan="8" class="SectionTitle">
									Add New Tier
								</td>
							</tr>
							<tr>
								<th width="100">id</th>
								<th width="200">spell_id</th>
								<th width="200">tier</th>
							</tr>
							<tr>
								<td align="center">
									<strong>new</strong>
									<input type="hidden" name="tier_id" value="<?php print($tier_id) ?>" />
									<input type="hidden" name="orig_tier" value="<?php print($tier) ?>" />
								</td>
								<td align="center">
									<input type="text" name="spell_id" value="<?php print($spell['id']) ?>" style="width:50px;  background-color:#ddd;" readonly />
								</td>
								<td align="center">
									<select name="new_tier" style="width:125px;">
										<?php print($tier_options); ?>
									</select>
								</td>
							</tr>
							<tr>
								<td align="center" colspan="3">
									<input type="submit" name="submit" value="Insert" class="submit" />
									<input type="hidden" name="cmd" value="inserttier" />
								</td>
							</tr>
						</table>
						<input type="hidden" name="object" value="<?= $spell['name'] ?>|<?= $spell['id'] ?>|Tier:<?= $next_tier ?>" />
						<input type="hidden" name="table" value="spell_tiers" />
						</form>
					</td>
				</tr>

			<?php 
			} // end next_tier
		}
	?>
		</table>
	</div>
	<?php
	}

	function Spell_Data()
	{
		global $eq2;

		if (isset($_POST['cmd']))
		{
			switch ($_POST['cmd'])
			{
				case "Update":
					$eq2->ProcessUpdate();
					break;

				case "Delete":
					$eq2->ProcessDelete(NULL);
					break;

				case "Insert":
					$eq2->ProcessInsert(NULL);
					break;
			}
		}

		$spell = $this->db->GetSpellByID();
		$rows = $this->db->GetSpellData($_GET['id']);
	
		?>

		<div id="Editor">
			<table class="SubPanel" cellspacing="0" border="0">

				<tr>
					<td id="EditorStatus" colspan="2"><?php if( !empty($eq2->Status) ) $eq2->DisplayStatus(); ?></td>
				</tr>

				<tr>
					<td class="Title" colspan="2" align="center">
						Editing: <?= $spell['name'] ?> (<?= $spell['id'] ?>)
						<?php $this->PrintOffsiteLinks($spell); ?>
					</td>
				</tr>

				<?php
					$tiers = 0;
                    if (is_array($rows)) {
                        foreach ($rows as $row) {
                            $tier = $row["tier"];
                            $tier_id = $row["id"];
            
                            $tier_text = $this->PrintTierName($tier);
                            $tier_options = $this->GetSpellTiers($row['tier']); ?>

						<tr>
							<td valign="top" align="left">
								<?php
                                if ($tiers != $row['tier']) {
                                    if ($tiers != 0) {
                                        ?>	
										</table>
										</form>
										<?php
                                    } ?>
									<form method="post" name="multiForm|<?php print($row['id']); ?>">
									<table class="SectionMainFloat" cellspacing="0" border="0">
										<tr>
											<td colspan="10" class="SectionTitle"><?php print($tier_text); ?></td>
										</tr>
										<tr>
											<td class="LabelLeft">id:</td>
											<td class="LabelLeft">spell_id:</td>
											<td class="LabelLeft">tier:</td>
											<td class="LabelLeft">index_field:</td>
											<td class="LabelLeft">value_type:</td>
											<td class="LabelLeft">value:</td>
											<td class="LabelLeft">value 2:</td>
										</tr>
										<?php
                                    $tiers = $row['tier'];
                                } ?>
								<tr>
									<td width="80" class="Detail">
										<input type="text" name="spell_data|id" value="<?php print($row['id']) ?>"  style="width:50px;  background-color:#ddd;" readonly />
										<input type="hidden" name="orig_id" value="<?php print($row['id']) ?>" />
									</td>				
									<td width="70" class="Detail">
										<input type="text" name="spell_data|spell_id" value="<?php print($row['spell_id']) ?>"  style="width:50px; background-color:#ddd;" readonly />
										<input type="hidden" name="orig_spell_id" value="<?php print($row['spell_id']) ?>" />
									</td>
									<td width="130" class="Detail">
										<select name="spell_data|tier" style="width:125px;">
											<?php print($tier_options); ?>
										</select>
										<input type="hidden" name="orig_tier" value="<?php print($row['tier']) ?>" />
									</td>
									<td width="130" class="Detail">
										<input type="text" name="spell_data|index_field" value="<?php print($row['index_field']); ?>" style="width:60px" />&nbsp;
										<input type="hidden" name="orig_index_field" value="<?= $row['index_field'] ?>" />
									</td>
									<td width="130" class="Detail">
									<select name="spell_data|value_type" style="width:70px">
											<option value="">---</option>
											<option<?php if($row['value_type']=="BOOL") echo " selected" ?>>BOOL</option>
											<option<?php if($row['value_type']=="FLOAT") echo " selected" ?>>FLOAT</option>
											<option<?php if($row['value_type']=="INT") echo " selected" ?>>INT</option>
											<option<?php if($row['value_type']=="STRING") echo " selected" ?>>STRING</option>
										</select>&nbsp;
										<input type="hidden" name="orig_value_type" value="<?= $row['value_type'] ?>" />
									</td>
									<td width="130" class="Detail">
										<input type="text" name="spell_data|value" value="<?php print($row['value']); ?>" style="width:50px" />&nbsp;
										<input type="hidden" name="orig_value" value="<?= $row['value'] ?>" />
									</td>
									<td width="130" class="Detail">
										<input type="text" name="spell_data|value2" value="<?php print($row['value2']); ?>" style="width:50px" />&nbsp;
										<input type="hidden" name="orig_value2" value="<?= $row['value2'] ?>" />
									</td>
									<td colspan="2">&nbsp;</td>
									<?php
                                    if (true /*$eq2->CheckAccess(G_DEVELOPER)*/) {
                                        ?>
										<td nowrap="nowrap">
											<input type="Submit" name="cmd" value="Update" class="submit" />
											<input type="Submit" name="cmd" value="Delete" class="submit" />
											<input type="hidden" name="orig_id" value="<?= $row['id'] ?>" />
											<input type="hidden" name="table" value="spell_data" />
											<input type="hidden" name="object" value="Edit Spell Data" />
										</td>				
										<?php
                                    } ?>
								</tr>
							</td>
						</tr>
						</form>
					<?php
                        } ?>
					</table>

					<?php
                    }
	
				if( /*$eq2->CheckAccess(G_DEVELOPER) &&*/ !$spell['is_aa'] ) 
				{
					foreach($this->eq2SpellTiers as $key=>$val)
					{
						if( $key > $tier )
						{
							$next_tier = $key;
							break;
						}
					}
			
					if( $next_tier > 0 && $next_tier <= 12 )
					{
						$tier_options = $this->GetSpellTiers($next_tier); 
				
						/*
						 * Notes on the convoluted manner of inserting a new tier :|
						 * $tier_id = the PK of the record used as a base (so you don't have to re-type all tier data)
						 * $tier = the last known Tier ID
						 * The new tier ID is derived from the $tier_options selected
						 * $_POST['spell_id'] passes the spell ID
						 *
						 * Note that deleting 1 tier, or inserting 1 tier, inserts also a spell_data and spell_display_effects record(s)
						 */
						?>
				<tr>
					<td height="50" valign="bottom">Click &quot;Insert&quot; to add a new <em>tier</em> to this specific spell; tiers being Apprentice II, Adept I, or Master I, etc.</td>
				</tr>
				<tr>
					<td valign="top">
					<form method="post" name="singleForm|new">
						<table class="SectionMain" width="99%" cellspacing="0" border="0">
							<tr>
								<td colspan="10" class="SectionTitle">Add New Data</td>
							</tr>
							<tr>
								<td class="LabelLeft">id:</td>
								<td class="LabelLeft">spell_id:</td>
								<td class="LabelLeft">tier:</td>
								<td class="LabelLeft">index_field:</td>
								<td class="LabelLeft">value_type:</td>
								<td class="LabelLeft">value:</td>
								<td class="LabelLeft">value 2:</td>
							</tr>
							<tr>
								<td width="80" class="Detail">
									<strong>new</strong>
									<input type="hidden" name="tier_id" value="<?php print($tier_id) ?>" />
									<input type="hidden" name="orig_tier" value="<?php print($tier) ?>" />
								</td>
								<td width="70" class="Detail">
									<input type="text" name="spell_data|spell_id" value="<?php print($spell['id']) ?>" style="width:50px;  background-color:#ddd;" readonly />
								</td>
								<td width="130" class="Detail">
									<select name="spell_data|tier" style="width:125px;">
										<?php print($tier_options); ?>
									</select>
								</td>
								</td>
									<td width="130" class="Detail">
										<input type="text" name="spell_data|index_field" value="0" style="width:50px" />&nbsp;
									</td>
								</td>

								</td>
									<td width="130" class="Detail">
										<select name="spell_data|value_type|new" style="width:80px">
											<option>---</option>
											<option>BOOL</option>
											<option>FLOAT</option>
											<option>INT</option>
											<option>STRING</option>
										</select>
									</td>
								</td>
								</td>
									<td width="130" class="Detail">
										<input type="text" name="spell_data|value" value="0" style="width:50px" />&nbsp;
									</td>
								</td>

								</td>
									<td width="130" class="Detail">
										<input type="text" name="spell_data|value2" value="0" style="width:50px" />&nbsp;
									</td>
								</td>
								<td colspan="2">&nbsp;</td>
								<td nowrap="nowrap">
									<input type="submit" name="cmd" value="Insert" class="submit" />
									<input type="hidden" name="object" value="<?= $spell['name'] ?>|<?= $spell['id'] ?>|Tier:<?= $next_tier ?>" />
									<input type="hidden" name="table" value="spell_data" />
								</td>
							</tr>
						</table>
					</form>
					</td>
				</tr>

			<?php 
			} // end next_tier
		}
	?>
		</table>
	</div>
	<?php
	}

	function Spell_Effects()
	{
		global $eq2;

		if (isset($_POST['cmd']))
		{
			switch ($_POST['cmd'])
			{
				case "Update":
					$eq2->ProcessUpdate();
					break;

				case "Delete":
					$eq2->ProcessDelete(NULL);
					break;

				case "Insert":
					$eq2->ProcessInsert(NULL);
					break;
			}
		}

		$spell = $this->db->GetSpellByID();
		$rows = $this->db->GetSpellEffects($_GET['id']);
	
		?>

		<div id="Editor">
			<table class="SubPanel" cellspacing="0" border="0">

				<tr>
					<td id="EditorStatus" colspan="2"><?php if( !empty($eq2->Status) ) $eq2->DisplayStatus(); ?></td>
				</tr>

				<tr>
					<td class="Title" colspan="2" align="center">
						Editing: <?= $spell['name'] ?> (<?= $spell['id'] ?>)
						<?php $this->PrintOffsiteLinks($spell); ?>
					</td>
				</tr>

				<?php
					$tiers = 0;
                    if (is_array($rows)) {
                        foreach ($rows as $row) {
                            $tier = $row["tier"];
                            $tier_id = $row["id"];
            
                            $tier_text = $this->PrintTierName($tier);
                            $tier_options = $this->GetSpellTiers($row['tier']); ?>

						
						<tr>
							<td valign="top" align="left">
								<?php
                                if ($tiers != $row['tier']) {
                                    if ($tiers != 0) {
                                        ?>	
										</table>
										</form>
										<?php
                                    } ?>
									<form method="POST" name="multiForm|<?php print($row['id']); ?>">
									<table class="SectionMainFloat" cellspacing="0" border="0">
										<tr>
											<td colspan="10" class="SectionTitle"><?php print($tier_text); ?></td>
										</tr>
										<tr>
											<td class="LabelLeft">id:</td>
											<td class="LabelLeft">spell_id:</td>
											<td class="LabelLeft">tier:</td>
											<td class="LabelLeft">percentage:</td>
											<td class="LabelLeft">description:</td>
											<td class="LabelLeft">bullet:</td>
											<td class="LabelLeft">index:</td>
										</tr>
										<?php
                                    $tiers = $row['tier'];
                                } ?>
								<tr>
									<td width="60" class="Detail">
										<input type="text" name="spell_display_effects|id" value="<?php print($row['id']) ?>"  style="width:50px;  background-color:#ddd;" readonly />
										<input type="hidden" name="orig_id" value="<?php print($row['id']) ?>" />
									</td>				
									<td width="60" class="Detail">
										<input type="text" name="spell_display_effects|spell_id" value="<?php print($row['spell_id']) ?>"  style="width:50px; background-color:#ddd;" readonly />
										<input type="hidden" name="orig_spell_id" value="<?php print($row['spell_id']) ?>" />
									</td>
									<td width="60" class="Detail">
										<select name="spell_display_effects|tier" style="width:90px;">
											<?php print($tier_options); ?>
										</select>
										<input type="hidden" name="orig_tier" value="<?php print($row['tier']) ?>" />
									</td>
									<td width="80" class="Detail">
										<input type="text" name="spell_display_effects|percentage" value="<?php print($row['percentage']); ?>" style="width:60px" />&nbsp;
										<input type="hidden" name="orig_percentage" value="<?= $row['percentage'] ?>" />
									</td>
									<td width="130" class="Detail">
										<textarea name="spell_display_effects|description" style="width:400px; height:50px;"><?php print($row['description']); ?></textarea>
										<input type="hidden" name="orig_description" value="<?= $row['description'] ?>" />
									</td>
									<td width="60" class="Detail">
										<input type="text" name="spell_display_effects|bullet" value="<?php print($row['bullet']); ?>" style="width:50px" />&nbsp;
										<input type="hidden" name="orig_bullet" value="<?= $row['bullet'] ?>" />
									</td>
									<td width="60" class="Detail">
										<input type="text" name="spell_display_effects|index" value="<?php print($row['index']); ?>" style="width:50px" />&nbsp;
										<input type="hidden" name="orig_index" value="<?= $row['index'] ?>" />
									</td>
									<td colspan="2">&nbsp;</td>
									<?php
                                    if (true /*$eq2->CheckAccess(G_DEVELOPER)*/) {
                                        ?>
										<td nowrap="nowrap">
											<input type="submit" name="cmd" value="Update" class="Submit" />
											<input type="submit" name="cmd" value="Delete" class="Submit" />
											<input type="hidden" name="orig_id" value="<?= $row['id'] ?>" />
											<input type="hidden" name="table" value="spell_display_effects" />
											<input type="hidden" name="object" value="Modify Spell Effects" />
										</td>				
										<?php
                                    } ?>
								</tr>
								</form>
							</td>
						</tr>
						
					<?php
                        } ?>
					</table>
					</form>
					<?php
                    }
	
				if( /*$eq2->CheckAccess(G_DEVELOPER) &&*/ !$spell['is_aa'] ) 
				{
					foreach($this->eq2SpellTiers as $key=>$val)
					{
						if( $key > $tier )
						{
							$next_tier = $key;
							break;
						}
					}
			
					if( $next_tier > 0 && $next_tier <= 12 )
					{
						$tier_options = $this->GetSpellTiers($next_tier); 
				
						/*
						 * Notes on the convoluted manner of inserting a new tier :|
						 * $tier_id = the PK of the record used as a base (so you don't have to re-type all tier data)
						 * $tier = the last known Tier ID
						 * The new tier ID is derived from the $tier_options selected
						 * $_POST['spell_id'] passes the spell ID
						 *
						 * Note that deleting 1 tier, or inserting 1 tier, inserts also a spell_data and spell_display_effects record(s)
						 */
						?>
				<tr>
					<td height="50" valign="bottom">Click &quot;Insert&quot; to add a new <em>effect</em> to this specific spell</td>
				</tr>
				<tr>
					<td valign="top">
						<form method="post" name="singleForm|new">
						<table class="SectionMain" width="99%" cellspacing="0" border="0">
							<tr>
								<td colspan="10" class="SectionTitle">Add New Effect</td>
							</tr>
							<tr>
								<td class="LabelLeft">id:</td>
								<td class="LabelLeft">spell_id:</td>
								<td class="LabelLeft">tier:</td>
								<td class="LabelLeft">percent:</td>
								<td class="LabelLeft">description:</td>
								<td class="LabelLeft">bullet:</td>
								<td class="LabelLeft">index:</td>
							</tr>
							<tr>
								<td width="60" class="Detail">
									<strong>new</strong>
									<input type="hidden" name="tier_id" value="<?php print($tier_id) ?>" />
									<input type="hidden" name="orig_tier" value="<?php print($tier) ?>" />
								</td>
								<td width="60" class="Detail">
									<input type="text" name="spell_display_effects|spell_id" value="<?php print($spell['id']) ?>" style="width:50px;  background-color:#ddd;" readonly />
								</td>
								<td width="60" class="Detail">
									<select name="spell_display_effects|tier" style="width:90px;">
										<?php print($tier_options); ?>
									</select>
								</td>
									<td width="80" class="Detail">
										<input type="text" name="spell_display_effects|percentage" value="100" style="width:60px" />&nbsp;
									</td>
								</td>
								<td width="130" class="Detail">
									<textarea name="spell_display_effects|description" style="width:400px; height:50px;"></textarea>
								</td>
								</td>
									<td width="80" class="Detail">
										<input type="text" name="spell_display_effects|bullet" value="0" style="width:50px" />&nbsp;
									</td>
								</td>
								</td>
									<td width="80" class="Detail">
										<input type="text" name="spell_display_effects|`index`" value="0" style="width:50px" />&nbsp;
									</td>
								</td>

								<td colspan="2">&nbsp;</td>
								<td nowrap="nowrap">
									<input type="Submit" name="cmd" value="Insert" class="submit" />
									<input type="hidden" name="object" value="<?= $spell['name'] ?>|<?= $spell['id'] ?>|Tier:<?= $next_tier ?>" />
									<input type="hidden" name="table" value="spell_display_effects" />
								</td>
							</tr>
						</table>
						</form>
					</td>
				</tr>

			<?php 
			} // end next_tier
		}
	?>
		</table>
	</div>
	<?php
	}
	//end of Spell_Effects

	function Spell_Classes()
	{
		global $eq2;

		if (isset($_POST['cmd']))
		{
			switch ($_POST['cmd'])
			{
				case "Update":
					$eq2->ProcessUpdate();
					break;

				case "Delete":
					$eq2->ProcessDelete(NULL);
					break;
			}
		}

		$spell = $this->db->GetSpellByID();
		$rows = $this->db->GetSpellClasses($_GET['id']);
	
		?>

		<div id="Editor">
			<table class="SubPanel" cellspacing="0" border="0">

				<tr>
					<td id="EditorStatus" colspan="2"><?php if( !empty($eq2->Status) ) $eq2->DisplayStatus(); ?></td>
				</tr>

				<tr>
					<td class="Title" colspan="2" align="center">
						Editing: <?= $spell['name'] ?> (<?= $spell['id'] ?>)
						<?php $this->PrintOffsiteLinks($spell); ?>
					</td>
				</tr>

				<form method="post" name="multiForm|<?php print($row['id']); ?>">	
					<tr>
						<td valign="top" align="left">
							<table class="SectionMainFloat" cellspacing="0" border="0">
								<tr>
									<td colspan="10" class="SectionTitle">Classes that use this spell</td>
								</tr>
								<tr>
									<td class="LabelLeft">id:</td>
									<td class="LabelLeft">spell_id:</td>
									<td class="LabelLeft">adventure_class_id:</td>
									<td class="LabelLeft">tradeskill_class_id:</td>
									<td class="LabelLeft">level:</td>
								</tr>

								<?php
								foreach ($rows as $row) {
									?>
								<tr>
									<td width="60" class="Detail">
										<input type="text" name="spell_classes|id" value="<?php print($row['id']) ?>"  style="width:50px;  background-color:#ddd;" readonly />
										<input type="hidden" name="orig_id" value="<?php print($row['id']) ?>" />
									</td>				
									<td width="70" class="Detail">
										<input type="text" name="spell_classes|spell_id" value="<?php print($row['spell_id']) ?>"  style="width:50px; background-color:#ddd;" readonly />
										<input type="hidden" name="orig_spell_id" value="<?php print($row['spell_id']) ?>" />
									</td>
									<td width="130" class="Detail">
										<?php
										print('<select name="classPicker">');
										//print('<option value="">Pick a Class</option>');

										foreach($eq2->eq2Classes as $class=>$name)

											printf('<option value="%s"%s>%s (%s)</option>', $class, (isset($_POST['classPicker']) && !$eq2->IsStringNullOrEmpty($_POST['classPicker']) && $_POST['classPicker'] == $class) ? " selected" : "", $name, $class);
										print('<option value="'.$class.'">'.$name.'</option></select>');
										?>
									</td>
									<td width="120" class="Detail">
										<select name="spell_classes|tradeskill_class_id" style="width:120px;">
											<?php print($tradeskill_class); ?>
										</select>
										<input type="hidden" name="orig_tier" value="<?php print($row['tradeskill_class_id']) ?>" />
									</td>
									<td width="70" class="Detail">
										<input type="text" name="spell_classes|level" value="<?php print($row['level']); ?>" style="width:50px" />&nbsp;
										<input type="hidden" name="orig_bullet" value="<?= $row['level'] ?>" />
									</td>
									
									<td colspan="2">&nbsp;</td>
									<?php
                                    if (true /*$eq2->CheckAccess(G_DEVELOPER)*/) {
                                        ?>
										<td style="text-align: top" nowrap="nowrap">
											<input type="Submit" name="cmd" value="Update" class="submit" />
											<input type="Submit" name="cmd" value="Delete" class="submit" />
											<input type="hidden" name="orig_id" value="<?= $row['id'] ?>" />
											<input type="hidden" name="table" value="spell_display_effects" />
											<input type="hidden" name="object" value="Edit Display Effects" />
										</td>				
										<?php
                                    } ?>
								</tr>
								<?php
							} ?>
							</table>
						</td>
					</tr>
				</form>
				<form method="post" name="addNewClass">
					<tr>
						<td valign="top" align="left">
							<table class="SectionMainFloat" cellspacing="0" border="0">
								<tr>
									<td colspan="10" class="SectionTitle">Add New Class</td>
								</tr>
								<tr>
									<td class="LabelLeft">id:</td>
									<td class="LabelLeft">spell_id:</td>
									<td class="LabelLeft">adventure_class_id:</td>
									<td class="LabelLeft">tradeskill_class_id:</td>
									<td class="LabelLeft">level:</td>
								</tr>

								<tr>
									<td width="60" class="Detail">
										<input type="text" name="spell_classes|id" value="<?php print($row['id']) ?>"  style="width:50px;  background-color:#ddd;" readonly />
										<input type="hidden" name="orig_id" value="<?php print($row['id']) ?>" />
									</td>				
									<td width="70" class="Detail">
										<input type="text" name="spell_classes|spell_id" value="<?php print($row['spell_id']) ?>"  style="width:50px; background-color:#ddd;" readonly />
										<input type="hidden" name="orig_spell_id" value="<?php print($row['spell_id']) ?>" />
									</td>
									<td width="130" class="Detail">
										<?php
										print('<select name="classPicker">');
										print('<option value="">Pick a Class</option>');

										foreach($eq2->eq2Classes as $class=>$name)
											printf('<option value="%s"%s>%s (%s)</option>', $class, (isset($_POST['classPicker']) && !$eq2->IsStringNullOrEmpty($_POST['classPicker']) && $_POST['classPicker'] == $class) ? " selected" : "", $name, $class);
										print('</select>');
										?>
									</td>

									<td width="120" class="Detail">
										<select name="spell_classes|tradeskill_class_id" style="width:120px;">
											<?php print($tradeskill_class); ?>
										</select>
										<input type="hidden" name="orig_tier" value="<?php print($row['tradeskill_class_id']) ?>" />
									</td>
									<td width="70" class="Detail">
										<input type="text" name="spell_classes|level" value="<?php print($row['level']); ?>" style="width:50px" />&nbsp;
										<input type="hidden" name="orig_bullet" value="<?= $row['level'] ?>" />
									</td>
									
									<td colspan="2">&nbsp;</td>
									<?php
                                    if (true /*$eq2->CheckAccess(G_DEVELOPER)*/) {
                                        ?>
										<td style="text-align: top" nowrap="nowrap">
											<input type="Submit" name="cmd" value="Update" class="submit" />
											<input type="Submit" name="cmd" value="Delete" class="submit" />
											<input type="hidden" name="orig_id" value="<?= $row['id'] ?>" />
											<input type="hidden" name="table" value="spell_display_effects" />
											<input type="hidden" name="object" value="Edit Display Effects" />
										</td>				
										<?php
                                    } ?>
                                </tr>
							</table>
						</td>
					</tr>
				</form>
		</table>
	</div>
	<?php
	}
	//end of GetSpellClasses

	/*
		Function: 
		Purpose	:	
		Params	: 
	*/
	private function DisplaySpellGrid($spells)
	{
		global $eq2;
		
		?>
		<br />
		<table class="SearchResultsTemp">
			<tr class="Title">
				<th width="75px">Item ID</th>
				<th width="70px">&nbsp;</th>
				<th width="45px">Icon</th>
				<th>Name</th>
				<th width="65px">class</th>
				<th width="45px">Level</th>
				<th width="25%">lua_script</th>
				<th width="45px">active</th>
			</tr>
			<?php
			
			$i = 0;
			foreach($spells as $data)
			{
				?>
				<tr>
					<td>&nbsp;<?= $data['id'] ?>&nbsp;</td>
					<td>
						<a href="http://census.daybreakgames.com/xml/get/eq2/spell/?crc=<?= $data['soe_spell_crc'] ?>&c:limit=100&c:sort=tier" target="_blank"><img src="images/soe.png" border="0" title="SOE" alt="SOE" height="20" /></a>
						<a href="http://eq2.wikia.com/wiki/<?= $data['name'] ?>" target="_blank"><img src="images/wikia.png" border="0" title="Wikia" alt="Wikia" height="20" /></a>
						<a href="http://eq2.zam.com/search.html?q=<?= $data['name'] ?>" target="_blank"><img src="images/zam.png" border="0" title="Zam" alt="Zam" height="20" /></a>
					</td>
					<td><img src="characters/eq2Icon.php?id=<?= $data['icon'] ?>&type=spells" /></td>
					<td><a href="<?= $eq2->PageLink ?>&tab=general&id=<?= $data['id'] ?>"><?= $data['name'] ?></a><br /><?= $data['description'] ?></td>
					<td>&nbsp;<?= $eq2->eq2Classes[$data['adventure_class_id']] ?></td>
					<td>&nbsp;<?= $data['level'] ?></td>
					<td>&nbsp;<?= $data['lua_script'] ?></td>
					<td align="center">&nbsp;<?= $data['is_active'] ? "<img src='images/nav_plain_green.png' />" : "<img src='images/nav_plain_red.png' />" ?></td>
				</tr>
				<?php
				$i++;
			}
		?>
		</table>
		
		<?php
		$eq2->AddStatus($i . " records found.");
	}
	

	private function TypePicker() {
		$typeArray = Array(
			0 => "Spells",
			1 => "Arts",
			2 => "Abilities",
			3 => "Tradeskills",
			4 => "Pcinnates"
		);
		
		$value = isset($_GET['classification']) ? "&classification=" . $_GET['classification'] : "";
		$value .= isset($_GET['class']) ? "&class=" . $_GET['class'] : "";
	
		print('<select name="typePicker" onchange="dosub(this.options[this.selectedIndex].value)">');
		print('<option value="index.php?page=spells">Pick a Spell Type</option>');
		printf('<option value="index.php?page=spells&type=all&classification=all"%s>All</option>', isset($_GET['type']) && $_GET['type'] == "all" ? " selected" : "");
		
		foreach($typeArray as $key=>$name)
			printf('<option value="index.php?page=spells&type=%s%s"%s>%s</option>', $key, $value,( isset($_GET['type']) && $key == $_GET['type'] && $_GET['type'] != "all" ) ? " selected" : "", $name);
		print('</select>');
	}
	
	
	private function classificationPicker()
	{
		$classificationArray = Array(
			"unset" => "Unset",
			"alternateadvancement" => "Alternateadvancement",
			"charactertrait" => "Charactertrait",
			"class" => "Class",
			"classtraining" => "Classtraining",
			"race" => "Race",
			"racialinnate" => "Racialinnate",
			"racialtradition" => "Racialtradition",
			"spellscroll" => "Spellscroll",
			"tradeskillclass" => "Tradeskillclass",
			"warderspell" => "Warderspell",
			"all" => "All"
		);
		
		$value = isset($_GET['class']) ? "&class=" . $_GET['class'] : "";
			
		print('<select name="tclassificationPicker" onchange="dosub(this.options[this.selectedIndex].value)">');
		printf('<option value="index.php?page=spells&type=%s">Pick a Classification</option>', $_GET['type']);
		
		foreach($classificationArray as $key=>$name)
			printf('<option value="index.php?page=spells&type=%s&classification=%s%s"%s>%s</option>', $_GET['type'], $key, $value, ( isset($_GET['classification']) && $key == $_GET['classification'] ) ? " selected" : "", $name);
		print('</select>');
	}
	
	private function GetSpellAALevels($level)
	{
		$ret = NULL;
		if( $level==0 )
			$ret = "<option value=\"0\">---</option>";
	
		for( $i = 1; $i <= 10; $i++ )
			$ret .= sprintf("<option%s>%s</option>", ( $i == $level ) ? " selected" : "", $i);
	
		return $ret;
	}
	
	private function PrintOffsiteLinks($spell)
	{
		?>
		<div style="float:right">
			<a href="http://census.daybreakgames.com/xml/get/eq2/spell/?crc=<?php print($spell['soe_spell_crc']); ?>&c:limit=100&c:sort=tier" target="_blank"><img src="images/soe.png" border="0" align="top" title="SOE" alt="SOE" height="20" /></a>
			<a href="http://eq2.wikia.com/wiki/<?php print($spell['name']); ?>" target="_blank"><img src="images/wikia.png" border="0" align="top" title="Wikia" alt="Wikia" height="20" /></a>
			<a href="http://eq2.zam.com/search.html?q=<?php print($spell['name']); ?>" target="_blank"><img src="images/zam.png" border="0" align="top" title="Zam" alt="Zam" height="20" /></a>								
		</div>
		<?php
	}
	
	var $eq2SpellTiers = array(
			1 => "Apprentice",
			2 => "Apprentice",
			3 => "Apprentice",
			4 => "Journeyman",
			5 => "Adept",
			7 => "Expert",
			9 => "Master",
		 10 => "Grandmaster",
		 11 => "Ancient",
		 12 => "Celestial"
	);
	
	var $eq2SOESpellClassifications = array(
			"unset",
			"alternateadvancement",
			"charactertrait",
			"class",
			"classtraining",
			"race",
			"racialinnate",
			"racialtradition",
			"spellscroll",
			"tradeskillclass",
			"warderspell",
			"all"
	);
	
	private function PrintTierName($tier)
	{
		return sprintf("Tier Data for: %s (%s)", $this->eq2SpellTiers[$tier], $tier);
	}
	
	private function GetSpellTiers($tier)
	{
		$ret = NULL;
		if( $tier==0 )
			$ret = "<option value=\"0\">---</option>";
				
		foreach($this->eq2SpellTiers as $key=>$val)
			$ret .= sprintf("<option value='%s'%s>%s</option>", $key, ( $tier == $key ) ? " selected" : "", $val);
	
		return $ret;
	}
}

?>