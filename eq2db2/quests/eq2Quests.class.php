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

class eq2Quests
{

	public function __construct()
	{
		include_once("eq2QuestsDB.class.php");
		$this->db = new eq2QuestsDB();
	}
	
	
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
					case "edit"			: $this->QuestEditor($_GET['id']); break;
					default					: $this->LookupQuest();	break;
				}
				break;
		}
		
		// end page
		print('</td></tr></table>');
	}

	/*
		Function: LookupQuest()
		Purpose	: Search, load, edit quest data
	*/

	private function LookupQuest()
	{
		global $eq2;
		$ret = false;
		
		?>
		<script language="javascript">
		<!--
		//Called from keyup on the search textbox.
		//Starts the AJAX request.
		function QuestLookupAJAX() {
			if (searchReq.readyState == 4 || searchReq.readyState == 0) {
				var str = escape(document.getElementById('txtSearch').value);
				searchReq.open("GET", 'quests/eq2QuestsAjax.php?type=lookup&search=' + str, true);
				searchReq.onreadystatechange = handleSearchSuggest; 
				searchReq.send(null);
			}		
		}
		-->
		</script>
		<div>
			<table class="SearchFilters">
				<tr>
					<th style="width:300px;">Filters</th>
					<th style="width:300px;">&nbsp;</th>
					<th style="text-align:left;">&nbsp;</th>
				</tr>
				<tr>
					<td class="label">Pick:</td>
					<td>
						<select name="zoneID" onchange="dosub(this.options[this.selectedIndex].value)">
							<option value="index.php?page=quests">Pick a Zone</option>
							<?= $this->db->GetZone(); ?>
						</select>
					</td>
					<?php
						if (isset($_GET['zone'])) { ?>
							<td>
								<select name="zoneQuest" onchange="dosub(this.options[this.selectedIndex].value)">
									<option value="index.php?page=quests">Pick a Quest</option>
									<?= $this->db->GetZoneQuest($_GET['zone']); ?>
								</select>
							</td>
							<?php
						} 
					?>

				</tr>
				<form action="index.php?page=quests" id="frmSearch" method="post">
				<tr>
					<td class="label">Lookup:</td>
					<td>
						<input type="text" id="txtSearch" name="txtSearch" alt="Search Criteria" onkeyup="QuestLookupAJAX();" autocomplete="off" class="box" value="<?= isset($_POST['txtSearch']) ? stripslashes($_POST['txtSearch']) : '' ?>" onclick="this.value='';" />
						<input type="submit" id="cmdSearch" name="cmdSearch" value="Search" alt="Run Search" class="submit" />
						<input type="button" value="Clear" class="submit" onclick="dosub('index.php?page=quests');" />
						<?php if( (M_ZONES & $eq2->user_role) || (G_ADMIN & $eq2->user_role) ) { ?>
						<input type="button" value="Add" class="submit" onclick="dosub('index.php?page=quests&id=add');" />
						<?php } ?>
						<input type="hidden" name="cmd" value="QuestByName" />
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
		
		// Check if searchText was used to find a Quest
		if( isset($_POST['txtSearch']) && strlen($_POST['txtSearch']) > 0 )
		{
			$arr = $this->db->GetQuestByName($_POST['txtSearch']);
			if( is_array($arr) )
			{
				$this->DisplayQuestGrid($arr, 1);
			}
			else
			{
				$eq2->AddStatus('No quest match your search. tstSearch');
			}
			$ret = true;
		}
		else if( isset($_POST['cmd']) && $_POST['cmd'] == "QuestByName" )
		{
			$arr = $this->db->GetQuestByName("all");
			if( is_array($arr) )
			{
				$this->DisplayQuestGrid($arr);
			}
			else
			{
				$eq2->AddStatus('No quest match your search. cmd');
			}
			$ret = true;
		}
		// Check if combo box was used to find a Zone
		// Cynnar: Look for a better way to check 
		else if( isset($_GET['zone']) && ($_GET['tab']) != 'general' )
		{
			$arr = $this->db->GetQuestByZone($_GET['zone']);
			if( is_array($arr) )
			{
				$this->DisplayQuestGrid($arr);
			}
			else
			{
				$eq2->AddStatus('No quest match your search. Pick Zone');
			}
			$ret = true;
		}
		
		// If a quest is selected, display data associated with the quest
		if( isset($_GET['id']) && $_GET['id'] > 0 && $_GET['id'] != 'add' )
		{
			$this->QuestEditor($_GET['id']);
		}
		else if( isset($_GET['id']) && $_GET['id'] == 'add' )
		{
			$this->QuestAdd();
		}
		$ret = true;
		
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

	private function DisplayQuestDropdown($id)
	{
		$arr = $this->db->GetQuestByZone($_GET['zone']);
		print('<select name="QuestPicker" onchange="dosub(this.options[this.selectedIndex].value)">');
		print('<option value="index.php?page=quests">Pick a Quest</option>');
		printf($arr);
		
		print('</select>');
	}
	
	private function QuestEditor($id)
	{
		global $eq2;

		// Build the Tab menu
		$current_tab_idx = ( isset($_GET['tab']) ) ? $_GET['tab'] : 'general';
		$tab_array = array(
			'general'			=> 'General',
			'details'			=> 'Details',
			'script'				=> 'Script '
		);
		$eq2->TabGenerator2($current_tab_idx, $tab_array);
		/*
			General		: General Quest data (quests table)
			Script		: Quest Script editor
			Details			: Quest Rewards / Prereq Details
		*/
	
		switch($current_tab_idx)
		{
			case "script"			: $this->Quest_Script(); break;
			case "details"		: $this->Quest_Details(); break;
			case "general"			:
			default					: $this->Quest_General(); break;
		}

	}

	private function Quest_General()
	{
		global $eq2;

		if (isset($_POST['cmd'])) {
			switch($_POST['cmd']) {
				case "Update": $eq2->ProcessUpdate(); break;
				case "Delete": $eq2->ProcessDelete(); break;
			}
		}

		$quest = $this->db->GetQuestByID();
		if( !is_array($quest) )
		{
			$eq2->AddStatus("No `quest` data found.");
		}
		?>

		<div id="Editor">
			<form method="post">
			<table <table style="width: 770px; border-collapse: collapse; border-spacing: 0;">
				<tr>
					<td class="Title" colspan="3" align="center">Editing Quest: <?= $quest['name'] ?></td>
				</tr>
				<tr>
					<td colspan="2">
						<fieldset>
							<table style="width: 100%; border: 0; border-collapse: collapse; border-spacing: 0;">
								<tr>
									<td align="right">Quest ID:</td>
									<td>
										<input type="text" name="quest|id" value="<?= $quest['quest_id'] ?>" readonly style="width:55px;background-color:#ddd;" />
										<input type="hidden" name="orig_id" value="<?= $quest['quest_id'] ?>" />
									</td>
									<td align="right">Name:</td>
									<td>
										<input type="text" name="quest|name" value="<?= $quest['name'] ?>" />
										<input type="hidden" name="orig_name" value="<?= $quest['name'] ?>" />
									</td>
									<td align="right">Zone:</td>
									<td>
										<input type="text" name="quest|zone" value="<?= $quest['zone'] ?>" />
										<input type="hidden" name="orig_zone" value="<?= $quest['zone'] ?>" />
									</td>
								</tr>
								<tr>
								<tr>
									<td align="right">Type:</td>
									<td>
										<input Type="text" name="quest|type" value="<?= $quest['type'] ?>" />
										<input Type="hidden" name="orig_type" value="<?= $quest['type'] ?>" />
									</td>
									<td align="right">Level:</td>
									<td>
										<input Type="text" name="quest|level" value="<?= $quest['level'] ?>" />
										<input Type="hidden" name="orig_level" value="<?= $quest['level'] ?>" />
									</td>
									<td align="right">Enc Level:</td>
									<td>
										<input Type="text" name="quest|enc_level" value="<?= $quest['enc_level'] ?>" />
										<input Type="hidden" name="orig_enc_level" value="<?= $quest['enc_level'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="right">Description:</td>
									<td colspan="7">
										<textarea name="quest|description" style="font:12px Arial, Helvetica, sans-serif; width:96%; height: 150px;"><?php print($quest['description']); ?></textarea>
										<input type="hidden" name="orig_description" value="<?= $quest['description'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="right">Completed Text:</td>
									<td colspan="7">
										<textarea name="quest|completed_text" style="font:12px Arial, Helvetica, sans-serif; width:96%; height: 150px;"><?php print($quest['completed_text']); ?></textarea>
										<input type="hidden" name="orig_completed_text" value="<?= $quest['completed_text'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="right">Spawn ID:</td>
									<td>
										<input type="text" name="quest|spawn_id" value="<?= $quest['spawn_id'] ?>" />
										<input type="hidden" name="orig_spawn_id" value="<?= $quest['spawn_id'] ?>" />
									</td>
									<td align="right">LUA Script:</td>
									<td colspan="3">
										<input type="text" name="quest|lua_script" class="full" value="<?= $quest['lua_script'] ?>" />
										<input type="hidden" name="orig_lua_script" value="<?= $quest['lua_script'] ?>" />
									</td>
								</tr>
							</table>
						</fieldset>
					</td>
				</tr>
			</table>
			</form>
		</div>
		<!-- End zoneEditor -->
		<?php
	}

	private function Quest_Details()
	{
		global $eq2;

		$quest = $this->db->GetDetailsByQuestID();
		if( !is_array($quest) )
		{
			$eq2->AddStatus("No `quest` data found.");
		}
		?>
		
		<br />
		<table class="SearchResultsTemp">
			<tr class="Title">
				<th width="70px">ID</th>
				<th width="80px">Quest ID</th>
				<th width="45px">Type</th>
				<th width="45px">SubType</th>
				<th width="100px">Value</th>
				<th width="45px">Faction ID</th>
				<th width="25%">Quantity</th>
			</tr>
			<?php
			
			$i = 0;
			foreach($quest as $data)
			{
				?>
				<tr>
					<td contenteditable="false">&nbsp;<?= $data['id'] ?>&nbsp;</td>
					<td contenteditable="false">&nbsp;<?= $data['quest_id'] ?>&nbsp;</td>
					<td contenteditable="false">&nbsp;<?= $data['type'] ?></td>
					<td contenteditable="false">&nbsp;<?= $data['subtype'] ?></td>
					<td contenteditable="true">&nbsp;<?= $data['value'] ?></td>
					<td contenteditable="true">&nbsp;<?= $data['faction_id'] ?></td>
					<td contenteditable="true">&nbsp;<?= $data['quantity'] ?></td>
				</tr>
				<?php
				$i++;
			}
		?>
		</table>
		
		<?php
		$eq2->AddStatus($i . " records found.");
	}
	
	private function DisplayQuestGrid($quests)
	{
		global $eq2;
		
		?>
		<br />
		<table class="SearchResultsTemp">
			<tr class="Title">
				<th width="70px">Quest ID</th>
				<th width="80px">Icon</th>
				<th width="45px">Level</th>
				<th>Name / Description</th>
				<th width="100px">Zone</th>
				<th width="25%">lua_script</th>
			</tr>
			<?php
			
			$i = 0;
			foreach($quests as $data)
			{
				?>
				<tr>
					<td>&nbsp;<?= $data['quest_id'] ?>&nbsp;</td>
					<td>
						<a href="http://census.daybreakgames.com/xml/get/eq2/quest/?name=<?= $data['name'] ?>&c:limit=100&c:sort=tier" target="_blank"><img src="images/soe.png" border="0" title="SOE" alt="SOE" height="20" /></a>
						<a href="http://eq2.wikia.com/wiki/<?= $data['name'] ?>" target="_blank"><img src="images/wikia.png" border="0" title="Wikia" alt="Wikia" height="20" /></a>
						<a href="http://eq2.zam.com/search.html?q=<?= $data['name'] ?>" target="_blank"><img src="images/zam.png" border="0" title="Zam" alt="Zam" height="20" /></a>
					</td>
					<td>&nbsp;<?= $data['level'] ?></td>
					<td><a href="<?= $eq2->PageLink ?>&name=<?= $data['name'] ?>&id=<?= $data['quest_id'] ?>&tab=general"><?= $data['name'] ?></a><br /><br /><?= $data['description'] ?></td>
					<td>&nbsp;<?= $data['zone'] ?></td>
					<td>&nbsp;<?= $data['lua_script'] ?></td>
				</tr>
				<?php
				$i++;
			}
		?>
		</table>
		
		<?php
		$eq2->AddStatus($i . " records found.");
	}
	
	function addquest()
	{
		// just holding old Add code for now
		if( isset($_GET['add']) ) 
		{
		?>
		<div id="quest-wizard">
			Welcome to the Quest Script Wizard. Using the options below, build your new Quest Script!
			<div id="quest-options">
				<input type="button" value="Raw" onclick="javascript:window.open('scripts.php?page=quest&zone=<?= $_GET['zone'] ?>&id=<?= $_GET['id'] ?>&step=1', target='_self');" />&nbsp;-&nbsp;
				<input type="button" value="Header" onclick="javascript:window.open('scripts.php?page=quest&zone=<?= $_GET['zone'] ?>&id=<?= $_GET['id'] ?>&step=2', target='_self');" />&nbsp;-&nbsp;
				<input type="button" value="Init" onclick="javascript:window.open('scripts.php?page=quest&zone=<?= $_GET['zone'] ?>&id=<?= $_GET['id'] ?>&step=3', target='_self');" />&nbsp;-&nbsp;
				<input type="button" value="Functions" onclick="javascript:window.open('scripts.php?page=quest&zone=<?= $_GET['zone'] ?>&id=<?= $_GET['id'] ?>&step=4', target='_self');" />&nbsp;-&nbsp;
				<input type="button" value="Preview" onclick="javascript:window.open('<?= $eq2->PageLink ?>&preview=1', target='_self');" />
			</div> <!-- end quest-options -->
			<br />
			<div id="quest-steps">
			<?php
			switch($_GET['step']) 
			{
				case 2:
					$this->BuildQuestInit();
					break;
					
				case 2:
					$this->BuildQuestHeader();
					break;
					
				case 1:
				default:
					$this->BuildQuestHeader(); // temp - remove later
					//ChooseRawQuestData();
					break;
			}
	
			if( $_GET['preview'] ) 
			{ 
				// format preview
			?>
				<div id="quest-preview">PREVIEW:<br />
				<textarea name="quest-script" class="preview"><?= $script_text ?></textarea>
				</div>
			<?php
			}
			?>
			</div> <!-- end quest-steps -->
		</div> <!-- end quest-wizard -->
		<?php
		} // end id==add
	
	}	
}

?>