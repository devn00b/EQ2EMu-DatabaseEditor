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

class eq2Scripts
{

	public function __construct()
	{
		include_once("eq2ScriptsDB.class.php");
		$this->db = new eq2ScriptsDB();
	}
	

	/*
		Function: BuildQuestFromRawData()
		Purpose	:	Retrieves any raw data we have for a selected Quest and attempts to build a raw script
	*/
	private function BuildQuestFromRawData()
	{
	}
		

	/*
		Function: BuildQuestHeader()
		Purpose	:	Step 1 of the Quest Wizard, either initially or after selecting raw data
	*/
	private function BuildQuestHeader()
	{
		$ScriptName 		= $this->db->SQLEscape($_POST['script-name']);
		$ScriptPurpose 		= $this->db->SQLEscape($_POST['script-purpose']);
		$ScriptAuthor		= ( !empty($_POST['script-author']) ) ? $this->db->SQLEscape($_POST['script-author']) : $eq2->userdata['name'];
		$ScriptDate			= ( !empty($_POST['script-date']) ) ? $_POST['script-date'] : date('Y.m.d', time());
	
		$QuestZone			= $this->db->GetZoneNameByID($_GET['zone']);
	
		$RealScriptName = 'Quest/' . $QuestZone . '/' . $ScriptName;
		?>
		<form method="post" name="scriptheader">
		<table cellspacing="0" id="quest-steps" border="0">
			<tr>
				<td colspan="4" class="instruct">&nbsp;Fill in the values for the Quest Header (required) and click Save!</td>
			</tr>
			<tr>
				<td class="label" nowrap>* Script Name:&nbsp;</td>
				<td><input type="text" name="script-name" value="<?= $ScriptName ?>" class="field" /></td>
				<td class="label" nowrap>* Quest Name:&nbsp;</td>
				<td><input type="text" name="quest-name" value="<?= $QuestName ?>" class="field" /></td>
			</tr>
			<tr>
				<td class="label" nowrap>Script Purpose:&nbsp;</td>
				<td><input type="text" name="script-purpose" value="<?= $ScriptPurpose ?>" class="field" /></td>
				<td class="label" nowrap>* Quest Zone:&nbsp;</td>
				<td><input type="text" name="quest-zone" value="<?= $QuestZone ?>" class="field" /></td>
			</tr>
			<tr>
				<td class="label" nowrap>Script Author:&nbsp;</td>
				<td><input type="text" name="script-author" value="<?= $ScriptAuthor ?>" class="field" /></td>
				<td class="label" nowrap>Quest Starter:&nbsp;</td>
				<td><input type="text" name="queststarter" value="<?= $QuestStarter ?>" class="field" />&nbsp;&nbsp;<a href="javascript:OpenSearcher('startby');">Find</a></td>
			</tr> 
			<tr>
				<td class="label" nowrap>Script Date:&nbsp;</td>
				<td><input type="text" name="script-date" value="<?= $ScriptDate ?>" class="field" /></td>
				<td class="label" nowrap>* Quest Type:&nbsp;</td>
				<td><input type="text" name="questtype" value="<?= $QuestType ?>" class="field" /></td>
			</tr>
			<tr>
				<td class="label" nowrap>Script Path:&nbsp;</td>
				<td><?= $RealScriptName ?></td>
				<td class="label" nowrap>* Quest Level:&nbsp;</td>
				<td><input type="text" name="questlevel" value="<?= $QuestLevel ?>" class="field" /></td>
			</tr>
			<tr>
				<td class="label" nowrap>Preceded By:&nbsp;</td>
				<td><input type="text" name="prescript" value="<?= $PreScript ?>" class="field" />&nbsp;&nbsp;<a href="javascript:OpenSearcher('prescript');">Find</a></td>
				<td class="label" nowrap>Followed By:&nbsp;</td>
				<td><input type="text" name="postscript" value="<?= $PostScript ?>" class="field" />&nbsp;&nbsp;<a href="javascript:OpenSearcher('postscript');">Find</a></td>
			</tr>
			<tr>
				<td class="label" nowrap>* Quest Description:&nbsp;</td>
				<td colspan="3">
					<textarea name="questdesc"><?= $QuestDesc ?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="4" align="center">
					<input type="submit" name="cmd" value="Save" class="submit" />
					<input type="hidden" name="step" value="1" />
				</td>
			</tr>
			<tr>
				<td colspan="4" class="instruct">* denotes fields required for RegisterQuest()</td>
			</tr>
		</table>
		</form>
		<?php
	}	
	
	
	/*
		Function: BuildQuestInit()
		Purpose	:	Add/Remove/Change LUA script assignments in the function init() of a quest script
	*/
	private function BuildQuestInit()
	{
		$RealScriptName = 'Quest/' . $QuestZone . '/' . $ScriptName;
		?>
		<form method="post" name="scriptheader">
		<table cellspacing="0" id="quest-steps" border="0">
			<tr>
				<td colspan="4" class="instruct">&nbsp;Build the Init() function of your script, starting with RegisterQuest params.</td>
			</tr>
			<tr>
				<td colspan="4" class="section">RegisterQuest Params:</td>
			</tr>
			<tr>
				<td class="label">Script Name:&nbsp;</td>
				<td><input type="text" name="script-name" value="<?= $ScriptName ?>" class="field" /></td>
				<td class="label">Quest Name:&nbsp;</td>
				<td><input type="text" name="quest-name" value="<?= $QuestName ?>" class="field" /></td>
			</tr>
		</table>
		</form>
		<?php
	}
	
	
	/*
		Function: BuildQuestFunctions()
		Purpose	:	Add/Remove/Change LUA functions in a quest script
	*/
	private function BuildQuestFunctions()
	{
	}
	
	
	/*
		Function: DisplayHomePage()
		Purpose	:	Displays the Home/Welcome site_text for a given editor, plus left-menu
	*/
	public function Start()
	{
		global $eq2;

		$page_lc = $_GET['page'];
		$page_uc = ucfirst($_GET['page']);
		$type = isset($_GET['type']) ? $_GET['type'] : null;
		
		/* this was part of the script for picking quest starter from Quest Wizard - find a better way to handle the popup
		   like, building a popup.php?
		if( isset($_GET['mode']) )
			include("includes/header_short.php");
		else
			include("includes/header.php");

		switch($_GET['mode'])
		{
			case "search":
				switch($_GET['type'])
				{
					case "prescript":
					case "postscript":
						FindQuestByName();
						break;
					case "startby":
						FindQuestStarter();
						break;
				}
				break;
		
			default:
			// used to process Scripts Editor page here
		}

		if( isset($_GET['mode']) )
			include("includes/footer_short.php");
		else
			include("includes/footer.php");
		*/
		
		?>
		<table cellspacing="0" id="main-body" class="main">
			<tr>
				<td class="sidebar" nowrap="nowrap">
					<strong>Options:</strong><br />
					<li><a href="?page=<?= $page_lc ?>"><?= $page_uc ?> Home</a>
					<li><a href="?page=<?= $page_lc ?>&type=quest">Quest Scripts</a>
					<li><a href="?page=<?= $page_lc ?>&type=spawn">Spawn Scripts</a>
					<li><a href="?page=<?= $page_lc ?>&type=spell">Spell Scripts</a>
					<li><a href="?page=<?= $page_lc ?>&type=zone">Zone Scripts</a>
					<br />
					<br />
					<li><a href="<?= $eq2->PageLink ?>">Reload Page</a><br />
					<?php if( isset($_GET['id']) ) { ?>
					<li><a href="<?= $eq2->BackLink ?>">Back</a><br />
					<?php } ?>
					<br />
					<strong><?= $page_uc ?> Stats:</strong><br />
					&nbsp;
				</td>
				<td class="page-text">
					<div id="PageTitle"><?= $eq2->PageTitle ?></div>
					<?php
					switch($page_lc)
					{
						case "help": 
							// use common (eq2Functions) class to display non-specific data
							$eq2->DisplaySiteText($page_lc, $_GET['cat']); 
							break;
							
						default:
							switch($type)
							{
								case "quest": $this->QuestScriptEditor(); break;
								case "generic":
								case "spawn": $this->SpawnScriptEditor(); break;
								case "spell": print("Haven't figured this one out yet!"); break;
								case "zone"	: $this->ZoneScriptEditor(); break;
								default			: $eq2->DisplaySiteText('welcome', $page_lc);	break;
							}
							break;
					}
					?>
				</td>
			</tr>
		</table>
		<?php
	}


	/*
		Function: DisplayQuestsByZone($id)
		Purpose	:	Return search results for Quests by zone ID
		Params	: $id = zones.id
	*/
	function DisplayQuestsByZone($id)
	{
		global $eq2;
		
		$arr = $this->db->GetQuestScriptsByZone($id);
		
		if( is_array($arr) )
		{
			$this->DisplayQuestGrid($arr, 1);
		}
		else
		{
			$eq2->AddStatus('No Quests match your search.');
		}
	}
	
	
	/*
		Function: 
		Purpose	:	
		Params	: 
	*/
	private function DisplayQuestScriptGrid($quests)
	{
		global $eq2;
		
		?>
		<br />
		<div id="SelectGrid">
		<table id="SelectGrid">
			<tr>
				<td class="title">&nbsp;</td>
				<td colspan="4" align="center" class="title">Quest Scripts</td>
				<td class="title">&nbsp;</td>
			</tr>
			<tr>
				<th width="50">&nbsp;</th>
				<th width="50">quest_id</th>
				<th>name</th>
				<th width="200">type</th>
				<th width="200">zone</th>
				<th width="50">level</th>
			</tr>
			<?php
			
			$i = 0;
			foreach($quests as $data)
			{
				$RowColor = ( $i % 2 ) ? "row1" : "row2";
				$zone_id = ( isset($_GET['zone']) ) ? $_GET['zone'] : $this->db->GetZoneIDFromLUAScript("Quests", $data['lua_script']);
			?>
			<tr class="<?= $RowColor ?>">
				<td class="detail">&nbsp;[&nbsp;<a href="?page=scripts&type=quest&zone=<?= $zone_id ?>&id=<?= $data['quest_id'] ?>">Edit</a>&nbsp;]</td>
				<td class="detail" align="right">&nbsp;<?= $data['quest_id'] ?>&nbsp;</td>
				<td class="detail">&nbsp;<?= $data['name'] ?></td>
				<td class="detail" align="center">&nbsp;<?= $data['type'] ?></td>
				<td class="detail" align="center">&nbsp;<?= $data['zone'] ?></td>
				<td class="detail" align="center">&nbsp;<?= $data['level'] ?></td>
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


	/*
		Function: 
		Purpose	:	
		Params	: 
	*/
	private function DisplaySpawnScriptGrid($spawns)
	{
		global $eq2;
		
		?>
		<br />
		<div id="SelectGrid">
		<table>
			<tr>
				<td class="title">&nbsp;</td>
				<td colspan="6" align="center" class="title">Spawn Scripts</td>
				<td class="title">&nbsp;</td>
			</tr>
			<tr>
				<th width="50">&nbsp;</th>
				<th width="75">spawn_id</th>
				<th>name</th>
				<th width="75">model</th>
				<th width="75">levels</th>
				<th width="75">enc</th>
				<th width="75">entry</th>
				<th width="75">group</th>
			</tr>
			<?php
	
			$i = 0;
			foreach($spawns as $data)
			{
				$zone_id = ( isset($_GET['zone']) ) ? $_GET['zone'] : $this->db->GetZoneIDFromLUAScript("SpawnScripts", $data['lua_script']);
			?>
			<tr >
				<td class="detail">&nbsp;[&nbsp;<a href="?page=scripts&type=spawn&zone=<?= $zone_id ?>&id=<?= $data['id'] ?>">Edit</a>&nbsp;]</td>
				<td class="detail" align="right">&nbsp;<?= $data['sid'] ?>&nbsp;</td>
				<td class="detail">&nbsp;<?= $data['name'] ?></td>
				<td class="detail" align="center">&nbsp;<?= $data['model_type'] ?></td>
				<td class="detail" align="center">&nbsp;<!-- <?= $data['min_level'] ?>/<?= $data['max_level'] ?> --></td>
				<td class="detail" align="center">&nbsp;<!-- <?= $data['enc_level'] ?> --></td>
				<td class="detail" align="center">&nbsp;<?= $data['spawnentry_id'] ?></td>
				<td class="detail" align="center">&nbsp;<?= $data['spawn_location_id'] ?></td>
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
	
	private function DisplayGenericSpawnScriptGrid($scripts)
	{
		global $eq2;
	
		?>
		<br />
		<div id="SelectGrid">
		<table>
			<tr>
				<td class="title">&nbsp;</td>
				<td align="center" class="title">Generic Spawn Scripts</td>
				<td class="title">&nbsp;</td>
			</tr>
			<tr>
				<th width="50">&nbsp;</th>
				<th colspan="2">name</th>
			</tr>
			<?php

				$i = 0;
				foreach($scripts as $key => $value)
				{
					$RowColor = ( $i % 2 ) ? "row1" : "row2";
				?>
				<tr class="<?= $RowColor ?>">
					<td class="detail">&nbsp;[&nbsp;<a href="?page=scripts&type=generic&script=<?= $value ?>">Edit</a>&nbsp;]</td>
					<td class="detail" colspan="2">&nbsp;<?= $value ?></td>
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
		
		
	/*
		Function: 
		Purpose	:	
		Params	: 
	*/
	private function DisplayZoneScriptGrid($zones)
	{
		global $eq2;
		?>
		<br />
		<div id="SelectGrid">
		<table id="SelectGrid">
			<tr>
				<td class="title">&nbsp;</td>
				<td colspan="3" align="center" class="title">Zone Scripts</td>
				<td class="title">&nbsp;</td>
			</tr>
			<tr>
				<th width="50">&nbsp;</th>
				<th width="75">id</th>
				<th width="200">name</th>
				<th>description</th>
				<th width="200">lua_script</th>
			</tr>
			<?php
	
			$i = 0;
			foreach($zones as $data)
			{
				$RowColor = ( $i % 2 ) ? "row1" : "row2";
				$zone_id = ( isset($_GET['zone']) ) ? $_GET['zone'] : $this->db->GetZoneIDFromLUAScript("ZoneScripts", $data['lua_script']);
			?>
			<tr class="row2">
				<td class="detail">&nbsp;[&nbsp;<a href="?page=scripts&type=zone&zone=<?= $data['id'] ?>&id=<?= $data['id'] ?>">Edit</a>&nbsp;]</td>
				<td class="detail" align="right">&nbsp;<?= $data['id'] ?>&nbsp;</td>
				<td class="detail">&nbsp;<?= $data['name'] ?></td>
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
	
	
	/*
		Function: QuestScriptEditor()
		Purpose	: Search, load, edit quest scripts (LUA)
	*/	
	private function QuestScriptEditor() 
	{
		global $eq2;
		$ret = false;
		
		$this->PickerSearcher();
		
		// Check if searchText was used to find a Quest Script
		if( isset($_POST['txtSearch']) && strlen($_POST['txtSearch']) > 2 )
		{
			$arr = $this->db->GetQuestScriptsByName($_POST['txtSearch']);
			if( is_array($arr) )
			{
				$this->DisplayQuestScriptGrid($arr);
			}
			else
			{
				$eq2->AddStatus('No Quests match your search.');
			}
			$ret = true;
		}
		else if( isset($_POST['cmd']) && $_POST['cmd'] == 'QuestByName' )
		{
			$eq2->AddStatus('Search must contain at least 4 letters/numbers.');
			$ret = true;
		}

		// If a zone is selected, display quests associated with the zone
		if( isset($_GET['zone']) && $_GET['zone'] > 0 && empty($_GET['id']) )
		{
			$arr = $this->db->GetQuestScriptsByZone($_GET['zone']);
			if( is_array($arr) )
			{
				$this->DisplayQuestScriptGrid($arr);
			}
			else
			{
				$eq2->AddStatus('No Quests match your search.');
			}
			$ret = true;
		}
		
		// If a zone -and- quest have been selected, display ScriptEditor
		if( isset($_GET['zone']) && isset($_GET['id']) && $_GET['zone'] > 0 && $_GET['id'] > 0 )
		{
			$ScriptPath = $this->db->GetLuaScriptPath("quests", $_GET['id']);
			if ($ScriptPath && isset($_POST['cmd']) && $_POST['cmd'] == 'Update' && isset($_POST['script'])) {
				$eq2->SaveScript($ScriptPath, $_POST['script']);
			}
			
			?>
			<div id="Editor">
			<form method="post">
			<table cellspacing="0" width="100%">
				<tr>
					<td width="220" class="Title">&nbsp;</td>
					<td class="Title" align="center">Editing: <?= $ScriptPath ?></td>
					<td width="220" class="Title">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3" valign="top">
						<?php
						if( $ScriptPath )
						{
						?>
						<table cellspacing="0" width="99%">
							<tr>
								<td class="SectionTitle">Script Body</td>
							</tr>
							<tr>
								<td height="480px">
								<?php $eq2->ScriptEditor($ScriptPath); ?>
								</td>
							</tr>
						</table>
						<?php
						}
						else
							$this->AddStatus('You must first define a `lua_script` on the GENERAL tab.')
						?>
					</td>
				</tr>
				<tr>
					<td colspan="3" class="Submit">
						<input type="submit" name="cmd" id="save" value="Update" class="submit" onclick="SubmitScript(this);"/>&nbsp;
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
			$ret = true;
		}
		?>
		<div id="EditorStatus">
			<?php 
			if( !empty($eq2->Status) ) $eq2->DisplayStatus(); 
			?>
		</div><!-- End EditorStatus -->
		<?php
		if( $ret ) 
			return;

		// Dev Note: If we are adding a new script, we do it via the Quest, Spawn, Spell or Zone Editors - not here.
	} // QuestScriptEditor


	/*
		Function: SpawnScriptEditor()
		Purpose	: Search, load, edit spawn scripts (LUA)
	*/	
	private function SpawnScriptEditor() 
	{
		global $eq2;
		$ret = false;
		
		?>
		<div id="SearchAll">
			<table cellspacing="0" id="SearchAll">
				<form action="?page=scripts&type=spawn" method="post" name="SearchAllSpawns">
				<tr>
					<td width="75" align="right"><strong>Search All:</strong>&nbsp;</td>
					<td>
						&nbsp;<input type="text" name="searchText" value="<?= isset($_POST['searchText']) ? $_POST['searchText'] : "" ?>" class="box" />
						&nbsp;<input type="submit" name="submit" value="Search" class="submit" />
						&nbsp;<input type="button" value="Clear" class="submit" onclick="dosub('?page=spawn');" />
						<input type="hidden" name="cmd" value="SpawnByName" />
					</td>
				</tr>
				</form>
			</table>
			<script language="JavaScript">
			<!--
			document.SearchAllSpawns.searchText.focus();
			//-->
			</script>
		</div><!-- End SearchAll -->
		<div id="ZoneSelect">
			<table cellspacing="0" id="ZoneSelect" border="0">
				<tr>
					<td width="75" align="right"><strong>Pick Zone:</strong>&nbsp;</td>
					<td class="select">
						<select name="zoneID" onchange="dosub(this.options[this.selectedIndex].value)" class="zone">
							<option value="?page=scripts&type=spawn">Pick a Zone</option>
							<option value="?page=scripts&type=spawn&zone=Generic">Generic</option>
							<?php echo $this->db->GetZoneOptions('spawn_scripts', 1); ?>
						</select>
					</td>
				</tr>
			</table>
		</div><!-- End ZoneSelect -->
		<?php
		
		// Check if searchText was used to find a SpawnScript
		if( isset($_POST['searchText']) && strlen($_POST['searchText']) > 2 )
		{
			$arr = $this->db->GetSpawnScriptsByName($_POST['searchText']);
			if( is_array($arr) )
			{
				$this->DisplaySpawnScriptGrid($arr);
			}
			else
			{
				$eq2->AddStatus('No Quests match your search.');
			}
			$ret = true;
		}
		else if( isset($_POST['cmd']) && $_POST['cmd'] == 'SpawnByName' )
		{
			$eq2->AddStatus('Search must contain at least 4 letters/numbers.');
			$ret = true;
		}

		// If a zone is selected, display spawn_scripts associated with the zone
		if( isset($_GET['zone']) )
		{
			if ( $_GET['zone'] > 0 && empty($_GET['id']) )
			{
				$arr = $this->db->GetSpawnScriptsByZone($_GET['zone']);
				if( is_array($arr) )
				{
					$this->DisplaySpawnScriptGrid($arr);
				}
				else
				{
					$eq2->AddStatus('No SpawnScripts match your search.');
				}
				$ret = true;
			}
			else if ( $_GET['zone'] == "Generic") {
				$directory =  $GLOBALS['config']['script_path'] . "SpawnScripts/Generic/";
				$arr = array_diff(scandir($directory), array('..', '.'));
				if( is_array($arr) )
				{
					// Can't use this for generic spawn scripts, will need to make a custom one
					$this->DisplayGenericSpawnScriptGrid($arr);
				}
				else
				{
					$eq2->AddStatus('No SpawnScripts match your search.');
				}
				$ret = true;
			}
		}
		
		// If a zone -and- quest have been selected, display ScriptEditor
		if( isset($_GET['zone']) && isset($_GET['id']) && $_GET['zone'] > 0 && $_GET['id'] > 0 )
		{
			$ScriptPath = $this->db->GetLuaScriptPath("spawn_scripts", $_GET['id']);
		}
		
		if ( isset($_GET['type']) && $_GET['type'] == "generic" && isset($_GET['script']) ) {
			$ScriptPath = "SpawnScripts/Generic/" . $_GET['script'];
		}
		
		if( isset($ScriptPath) ) {

			if (isset($_POST['cmd']) && $_POST['cmd'] == 'Update' && isset($_POST['script'])) {
				$eq2->SaveScript($ScriptPath, $_POST['script']);
			}
			
			?>
			<div id="Editor">
			<table cellspacing="0" width="100%">
				<tr>
					<td width="220" class="Title">&nbsp;</td>
					<td class="Title" align="center">Editing: <?= $ScriptPath ?></td>
					<td width="220" class="Title">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3" valign="top">
					<?php
					if( $ScriptPath )
					{
					?>
						<table cellspacing="0" width="99%">
						<form method="post">
							<tr>
								<td class="SectionTitle">Script Body</td>
							</tr>
							<tr>
								<td height="480px">
								<?php $eq2->ScriptEditor($ScriptPath); ?>
								</td>
							</tr>
						</table>
					<?php
					}
					else
						$this->AddStatus('You must first define a `lua_script` on the GENERAL tab.')
					?>
					</td>
				</tr>
				<tr>
					<td colspan="3" class="Submit">
						<input type="submit" name="cmd" id="save" value="Update" class="submit" onclick="SubmitScript(this);"/>&nbsp;
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
			</form>
			</table>
			</div><!-- End ScriptEditor -->
			<?php
			$ret = true;
		}
		?>
		<div id="EditorStatus">
			<?php 
			if( !empty($eq2->Status) ) $eq2->DisplayStatus(); 
			?>
		</div><!-- End EditorStatus -->
		<?php
		if( $ret ) 
			return;

		// Dev Note: If we are adding a new script, we do it via the Quest, Spawn, Spell or Zone Editors - not here.
	} // SpawnScriptEditor
	

	/*
		Function: ZoneScriptEditor()
		Purpose	: Search, load, edit zone scripts (LUA)
	*/	
	function ZoneScriptEditor() 
	{
		global $eq2;
		$ret = false;
		
		?>
		<div id="SearchAll">
			<table cellspacing="0" id="SearchAll">
				<form action="?page=scripts&type=zone" method="post" name="SearchAllZones">
				<tr>
					<td width="75" align="right"><strong>Search All:</strong>&nbsp;</td>
					<td>
						&nbsp;<input type="text" name="searchText" value="<?= isset($_POST['searchText']) ? $_POST['searchText'] : "" ?>" class="box" />
						&nbsp;<input type="submit" name="submit" value="Search" class="submit" />
						&nbsp;<input type="button" value="Clear" class="submit" onclick="dosub('?page=zone');" />
						<input type="hidden" name="cmd" value="ZoneByName" />
					</td>
				</tr>
				</form>
			</table>
			<script language="JavaScript">
			<!--
			document.SearchAllZones.searchText.focus();
			//-->
			</script>
		</div><!-- End SearchAll -->
		<div id="ZoneSelect">
			<table cellspacing="0" id="ZoneSelect" border="0">
				<tr>
					<td width="75" align="right"><strong>Pick Zone:</strong>&nbsp;</td>
					<td class="select">
						<select name="zoneID" onchange="dosub(this.options[this.selectedIndex].value)" class="zone">
							<option value="?page=scripts&type=zone">Pick a Zone</option>
							<?= $this->db->GetZoneOptions('zones', 1); ?>
						</select>
					</td>
				</tr>
			</table>
		</div><!-- End ZoneSelect -->
		<?php
		
		// Check if searchText was used to find a SpawnScript
		if( isset($_POST['txtSearch']) && strlen($_POST['searchText']) > 2 )
		{
			$arr = $this->db->GetZoneScriptsByName($_POST['searchText']);
			if( is_array($arr) )
			{
				$this->DisplayZoneScriptGrid($arr);
			}
			else
			{
				$eq2->AddStatus('No ZoneScripts match your search.');
			}
			$ret = true;
		}
		else if( isset($_POST['cmd']) && $_POST['cmd'] == 'ZoneByName' )
		{
			$eq2->AddStatus('Search must contain at least 4 letters/numbers.');
			$ret = true;
		}

		// If a zone is selected, display spawn_scripts associated with the zone
		if( isset($_GET['zone']) && $_GET['zone'] > 0 && empty($_GET['id']) )
		{
			$arr = $this->db->GetZoneScriptsByZone($_GET['zone']);
			if( is_array($arr) )
			{
				$this->DisplayZoneScriptGrid($arr);
			}
			else
			{
				$eq2->AddStatus('No ZoneScripts match your search.');
			}
			$ret = true;
		}
		
		// If a zone -and- quest have been selected, display ScriptEditor
		if( isset($_GET['zone']) && isset($_GET['id']) && $_GET['zone'] > 0 && $_GET['id'] > 0 )
		{
			$ScriptPath = $this->db->GetLuaScriptPath("zones", $_GET['id']);
			if (isset($_POST['cmd']) && $_POST['cmd'] == 'Update' && isset($_POST['script'])) {
				$eq2->SaveScript($ScriptPath, $_POST['script']);
			}
			
			?>
			<div id="Editor">
			<table cellspacing="0" width="100%">
				<tr>
					<td width="220" class="Title">&nbsp;</td>
					<td class="Title" align="center">Editing: <?= $ScriptPath ?></td>
					<td width="220" class="Title">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3" valign="top">
					<?php
					if( $ScriptPath )
					{
					?>
						<table cellspacing="0" width="99%">
						<form method="post">
							<tr>
								<td class="SectionTitle">Script Body</td>
							</tr>
							<tr>
								<td height="480px">
								<?php $eq2->ScriptEditor($ScriptPath); ?>
								</td>
							</tr>
						</table>
					<?php
					}
					else
						$this->AddStatus('You must first define a `lua_script` on the GENERAL tab.')
					?>
					</td>
				</tr>
				<tr>
					<td colspan="3" class="Submit">
						<input type="submit" name="cmd" id="save" value="Update" class="submit" onclick="SubmitScript(this);"/>&nbsp;
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
			</form>
			</table>
			</div><!-- End ScriptEditor -->
			<?php
			$ret = true;
		}
		?>
		<div id="EditorStatus">
			<?php 
			if( !empty($eq2->Status) ) $eq2->DisplayStatus(); 
			?>
		</div><!-- End EditorStatus -->
		<?php
		if( $ret ) 
			return;

		// Dev Note: If we are adding a new script, we do it via the Quest, Spawn, Spell or Zone Editors - not here.
	} // ZoneScriptEditor
	
	
	// TODO: these are for the Quest Script Wizard
	function FindQuestByName()
	{
		global $eq2;
	
		?>
		<form method="post" name="find-quest">
		Enter partial quest name to search for:<br />
		<input type="text" name="quest-name" value="art" />
		<input type="submit" name="submit" value="Search" />
		<input type="hidden" name="cmd" value="search-quests" />
		</form>
		<?php
		if( isset($_POST['cmd']) )
		{
			if( strlen($_POST['quest-name']) > 2 )
				$arr = $eq2->eq2db->QuestLookup("name", $eq2->eq2db->db->sql_escape($_POST['quest-name']));
			else
				print("Criteria must be 3 or more characters.");
			if( is_array($arr) )
			{
			?>
				<table width="100%" cellspacing="0" border="1">
					<tr>
						<td width="50">&nbsp;</td>
						<td width="50">id</td>
						<td width="175">name</td>
						<td width="175">type</td>
						<td width="150">zone</td>
						<td width="40">level</td>
					</tr>
					<?php
					foreach($arr as $key)
						printf("<tr><td>[&nbsp;<a href=\"#\" onclick=\"insert_%s('%s'); return false;\">Select</a>&nbsp;]</td><td>&nbsp;%s</td><td>&nbsp;%s</td><td>&nbsp;%s</td><td>&nbsp;%s</td><td>&nbsp;%s</td></tr>\n",
							$_GET['type'], addslashes($key['name']), $key['quest_id'], $key['name'], $key['type'], $key['zone'], $key['level']);
					?>
				</table>
			<?php
			}
		}
	
	}
	
	function FindQuestStarter()
	{
		global $eq2;
		
		?>
		<form method="post" name="findstarter">
		Select zone NPC resides in:<br />
		<?php $eq2->eq2db->GetZoneSelectByID(1); ?>
		<br />
		Enter partial quest starter NPC name:<br />
		<input type="text" name="startedby" value="" /><br />
		
		<input type="submit" name="submit" value="Search" />
		<input type="hidden" name="cmd" value="searchstarters" />
		</form>
		<?php
		if( isset($_POST['cmd']) )
		{
			if( strlen($_POST['startedby']) > 2 )
			{
				$zone 		= ( !empty($_POST['zoneID']) ) ? $_POST['zoneID'] : 0;
				$startby 	= $eq2->eq2db->db->sql_escape($_POST['startedby']);
	
				$arr = $eq2->eq2db->SpawnLookup("name", $zone, $startby);
			}
			else
				print("Criteria must be 3 or more characters.");
			if( is_array($arr) )
			{
			?>
				<table width="100%" cellspacing="0" border="1">
					<tr>
						<td width="50">&nbsp;</td>
						<td width="100">id</td>
						<td width="250">name</td>
					</tr>
					<?php
					foreach($arr as $key)
						printf("<tr><td>[&nbsp;<a href=\"#\" onclick=\"insert_%s('%s'); return false;\">Select</a>&nbsp;]</td><td>&nbsp;%s</td><td>&nbsp;%s</td></tr>\n",
							$_GET['type'], addslashes($key['name']), $key['id'], $key['name']);
					?>
				</table>
			<?php
			}
		}
	}

	function PickerSearcher()
	{
	?>
		<div id="ZoneSelect">
			<table cellspacing="0" id="ZoneSelect" border="0">
				<tr>
					<td width="75" align="right"><strong>Pick Zone:</strong>&nbsp;</td>
					<td class="select">
						<select name="zoneID" onchange="dosub(this.options[this.selectedIndex].value)" class="zone">
							<option value="?page=scripts&type=quest">Pick a Zone</option>
							<?= $this->db->GetZoneOptions('quests', 1); ?>
						</select>
					</td>
				</tr>
			</table>
		</div><!-- End ZoneSelect -->
		<script>
		<!--
		//Called from keyup on the search textbox.
		//Starts the AJAX request.
		function QuestSearch() {
			if (searchReq.readyState == 4 || searchReq.readyState == 0) {
				var str = escape(document.getElementById('txtSearch').value);
				searchReq.open("GET", 'scripts/eq2ScriptsAjax.php?type=lookup&search=' + str, true);
				searchReq.onreadystatechange = handleSearchSuggest; 
				searchReq.send(null);
			}		
		}
		-->
		</script>
		<div id="SearchAll">
			<table cellspacing="0" id="SearchAll" border="0">
				<tr>
					<td width="75" align="right" valign="top"><strong>Search All:</strong>&nbsp;</td>
					<td>
						<form action="index.php?page=scripts&type=quest" id="frmSearch" method="post">
							<input type="text" id="txtSearch" name="txtSearch" alt="Search Criteria" onkeyup="QuestSearch();" autocomplete="off" class="box" />
							&nbsp;<input type="submit" id="cmdSearch" name="cmdSearch" value="Search" alt="Run Search" class="submit" />&nbsp;
							&nbsp;<input type="button" value="Clear" class="submit" onclick="dosub('?page=scripts&type=quest');" />
							&nbsp;<input type="button" value="Incomplete" class="submit" onclick="dosub('?page=scripts&type=quest&zone=0');" disabled="disabled" />
							<input type="hidden" name="cmd" value="QuestByName" />
							<div id="search_suggest">
							</div>
						</form>
					</td>
				</tr>
			</table>
			<script language="JavaScript">
			<!--
			document.getElementById('txtSearch').focus = true;
			//-->
			</script>
		</div><!-- End SearchAll -->
	<?php
	}
}
?>
