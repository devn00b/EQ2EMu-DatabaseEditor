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

class eq2Server
{

	public function __construct()
	{
		include_once("eq2ServerDB.class.php");
		$this->db = new eq2ServerDB();
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
		$this->ServerStats();
		
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
					default					: $this->ServerEditor(); break;
				}
				break;
		}
		
		// end page
		print('</td></tr></table>');
	}
	
	
	// Put the search tools in its own function so it doesn't have to be repeated
	// for every table we plan to edit, might be best to put table specific search
	// tools in there own function to improve readability instead of all of them
	// crammed into this function
	private function SearchPanel()
	{
		?>
		<script>
		function ServerLookupAJAX(type) {
			if (searchReq.readyState == 4 || searchReq.readyState == 0) {
				var str = escape(document.getElementById('txtSearch').value);
				searchReq.open("GET", 'server/eq2ServerAjax.php?type=' + type + '&search=' + str, true);
				searchReq.onreadystatechange = handleSearchSuggest;
				searchReq.send(null);
			}
		}
		</script>
		<?php
		$menuArray = array(			
			'appearances' 				=> 'Core Data',
			'commands' 					=> 'Core Data', 
			'opcodes' 					=> 'Core Data', 
			'table_versions' 			=> 'Core Data', 
			'skills'					=> 'Core Data', 
			'variables' 				=> 'Core Data', 
			'visual_states' 			=> 'Core Data', 
			'zones' 					=> 'Core Data', 
			'collections' 				=> 'Game Data', 
			'conditionals' 				=> 'Game Data', 
			'emotes' 					=> 'Game Data', 
			'entity_commands' 			=> 'Game Data', 
			'factions' 					=> 'Game Data',
			'flight_paths'				=> 'Game Data',
			'groundspawns'				=> 'Game Data',
			'houses'					=> 'Game Data',
			'languages' 				=> 'Game Data',
			'loottable'					=> 'Game Data',
			'loot_global'				=> 'Game Data',
			'map_data' 					=> 'Game Data', 
			'merchants' 				=> 'Game Data', 
			'name_filter' 				=> 'Game Data', 
			'recipes' 					=> 'Game Data', 
			'revive_points' 			=> 'Game Data', 
			'rules' 					=> 'Game Data', 
			'titles' 					=> 'Game Data', 
			'transporters' 				=> 'Game Data', 
			'starting_factions' 		=> 'Player Data', 
			'starting_items' 			=> 'Player Data', 
			'starting_skillbar' 		=> 'Player Data', 
			'starting_skills' 			=> 'Player Data', 
			'starting_spells' 			=> 'Player Data', 
			'starting_titles' 			=> 'Player Data', 
			'starting_zones' 			=> 'Player Data', 
			'spawn_npc_equipment'		=> 'NPC Data', 
			'spawn_npc_skills' 		=> 'NPC Data', 
			//'spawn_npc_spells' 		=> 'NPC Data'
		);
		
		//foreach($menuArray as $menu=>$category)
		//	printf('%s => %s<br />', $menu, $category);
		
		?>
		<table class="SearchFilters">
			<tr>
				<th>Filters</th>
			</tr>
			<tr>
				<td class="label">Table:
					<select name="tablePicker" onchange="dosub(this.options[this.selectedIndex].value)">
					<option value="index.php?page=<?= $_GET['page'] ?>">Pick a Table</option>
						<?php
						$currCategory = NULL;
						foreach($menuArray as $menu=>$category)
						{
							$selected = ( isset($_GET['type']) && $menu == $_GET['type'] ) ? " selected" : "";
							
							if ($category != $currCategory) {
								$currCategory = $category;
								printf('<option value="index.php?page=%s">--- %s ---</option>', $_GET['page'], $category);
							}
							
							printf('<option value="index.php?page=%s&type=%s"%s>%s</option>', $_GET['page'], $menu, $selected, $menu);
						}
						?>
					</select>
				</td>
			</tr>
			<?php
			if (isset($_GET['type']))
			{
				switch ($_GET['type'])
				{
					case "entity_commands":
						// possibly refactor this code to improve readability
						?>
						<tr>
							<td class="label">Lookup:
								<form action="index.php?page=<?= $_GET['page'] ?>&type=<?= $_GET['type'] ?>" id="frmSearch" method="post">
									<input type="text" id="txtSearch" name="txtSearch" alt="Search Criteria" onkeyup="ServerLookupAJAX('<?= $_GET['type'] ?>');" autocomplete="off" class="box" value="<?= isset($_POST['txtSearch']) ? $_POST['txtSearch'] : '' ?>" onclick="this.value='';" />
									<input type="submit" id="cmdSearch" name="cmdSearch" value="Search" alt="Run Search" class="submit" />
									<input type="button" value="Clear" class="submit" onclick="dosub('index.php?page=<?= $_GET['page'] ?>&type=<?= $_GET['type'] ?>');" />
									<input type="button" value="Add" class="submit" onclick="dosub('index.php?page=server&type=entity_commands&id=add');" />
									<input type="hidden" name="cmd" value="EnitityCommandByName" />
									<div id="search_suggest">
									</div>
								</form>
								<script>
									document.getElementById('txtSearch').focus = true;
								</script>
							</td>
						</tr>
						<?php
						break;
					case "groundspawns":
						?>
						<tr>
							<td><input type="button" value="Add" class="submit" onclick="dosub('index.php?page=server&type=groundspawns&id=new');" /></td>
						</tr>
						<?php
						break;
					case "loottable":
						?>
						<tr>
							<td><input type="button" value="Add" class="submit" onclick="dosub('index.php?page=server&type=loottable&id=new');" /></td>
						</tr>
						<?php
						break;
					case "transporters":
						?>
						<tr>
							<td><input type="button" value="Add" class="submit" onclick="dosub('index.php?page=server&type=transporters&id=new');" /></td>
						</tr>
						<?php
						break;
					case "flight_paths":
						?>
						<tr>
							<td>
							<?php
								$zones = $this->db->GetFlightPathZones();
								if (is_array($zones)) {
									?>
									<select onchange="dosub(this.options[this.selectedIndex].value)">
									<option value="index.php?page=server&type=flight_paths">Select a zone</option>
									<?php
									foreach ($zones as $data) {
										?>
										<option value="index.php?page=server&type=flight_paths&zoneID=<?= $data['zone_id'] ?>"<?= isset($_GET['zoneID']) && $_GET['zoneID'] == $data['zone_id'] ? " selected" : "" ?>><?= $data['description'] ?></option>
										<?php
									}
									?>
									</select>
									<?php
								}
							?>
							</td>
						</tr>
						<?php
						break;
					case "recipes":
						?>
						<tr>
							<td>
								<?php 
								$books = $this->db->GetDistinctRecipeBooks();
								if (is_array($books)) {
									?>
									<select onchange="dosub(this.options[this.selectedIndex].value)">
									<option value="index.php?page=server&type=recipes">Select a book</option>
									<?php 
									foreach ($books as $data) {
										?>
										<option value="index.php?page=server&type=recipes&book=<?= $data['book'] ?>"<?= isset($_GET['book']) && $_GET['book'] == $data['book'] ? " selected" : "" ?>><?= $data['book']?></option>
										<?php 
									}
									?>
									</select>
									<?php
								}
								?>
							</td>
						</tr>
						<tr><td><br></td></tr>
						<tr>
							<td><input type="button" value="Add New" class="submit" onclick="dosub('index.php?page=server&type=recipes&id=new');" /></td>
						</tr>
						<?php
						break;
					case "factions":
						?>
						<tr><td><br></td></tr>
						<tr>
							<td><input type="button" value="Add New" class="submit" onclick="dosub('index.php?page=server&type=factions&id=new');" /></td>
						</tr>
						<?php
						break;
					case "collections":
						?>
						<tr><td><br></td></tr>
						<tr>
							<td><input type="button" value="Add New" class="submit" onclick="dosub('index.php?page=server&type=collections&id=new');" /></td>
						</tr>
						<?php
						break;
					case "merchants":
						?>
						<tr><td><br></td></tr>
						<tr>
							<td><input type="button" value="Add New" class="submit" onclick="dosub('index.php?page=server&type=merchants&id=new');" /></td>
						</tr>
						<?php
						break;
					case "houses":
						?>
						<tr><td><br></td></tr>
						<tr>
							<td><input type="button" value="Add New" class="submit" onclick="dosub('index.php?page=server&type=houses&id=new');" /></td>
						</tr>
						<?php
						break;
					default:
						break;
				}
			}
			?>
		</table>
		
		<?php
	}
	
	
	private function ServerEditor()
	{
		$this->SearchPanel();
		
		if( isset($_GET['type']) )
		{
			switch($_GET['type'])
			{
				case "entity_commands"	:	$this->EntityCommands();	break;
				case "groundspawns"		:	$this->GroundSpawns();		break;
				case "starting_items"	:	$this->StartingItems();		break;
				case "flight_paths"		:	$this->FlightPaths();		break;
				//case "spawn_npc_spells"	:	$this->NPCSpells();			break;
				case "loottable"		:	$this->LootTable();			break;
				case "transporters"		:	$this->Transporters();		break;
				case "recipes"			:	$this->Recipes();			break;
				case "factions"			:	$this->Factions();			break;
				case "collections"		:	$this->Collections();		break;
				case "loot_global"		:	$this->LootGlobal();		break;
				case "merchants"		:	$this->Merchants();			break;
				case "houses"			:	$this->Houses();			break;
				default:
					break;
			}
		}
		?>
		<div id="EditorStatus">
			<?php 
			if( !empty($eq2->Status) ) 
				$eq2->DisplayStatus(); 
			?>
		</div><!-- End EditorStatus -->
		<?php
	}
	
	
	private function ServerStats()
	{
	}
	
	
	private function EntityCommands()
	{
		global $eq2;
		
		if (isset($_POST['cmd']))
		{
			switch($_POST['cmd']) 
			{
				case "Update": $eq2->ProcessUpdate(NULL); break;
				case "Delete": $eq2->ProcessDelete(NULL); break;
				case "Insert": $eq2->ProcessInsert(NULL); break;
				case "EnitityCommandByName"	:
					if (isset($_POST['txtSearch']) && strlen($_POST['txtSearch']) > 0) {
						$entity_command_data = $this->db->GetEntityCommands($_POST['txtSearch']);
					}
					break;
			}
		}
		
		if (!isset($entity_command_data))
		{
			$entity_command_data = $this->db->GetEntityCommands("all");
		}
		
		?>
		
		<script type="text/javascript">
			var page = 1;
			
			function fetchMore() {
				if ( $(window).scrollTop() >= $(document).height()-$(window).height()-300 ) {
					$(window).unbind('scroll',fetchMore);																	// unbind scroll to prevent it firing multiple times
					var div = document.getElementById('LoadingDiv');
					div.style.visibility = "visible";
					
					var postData = { search: "<?php echo isset($_POST['txtSearch']) ? $_POST['txtSearch'] : 'all'; ?>", pageOffset: page };
					// $.post(page, post data to page, function for what to do with returned data from page);
					$.post('server/eq2ServerAjax.EntityCommands.php?type=entity_commands', postData,
							function(data) {
								div.style.visibility = "hidden";
								if(data.length>10){
									$('#DynamicData tr:last').after(data);
									page++;
									$(window).bind('scroll',fetchMore);
								}
							}).fail(function() { alert("failed"); });
				}
			}
			
			$(window).bind('scroll',fetchMore);
			
			function submitForm(element) {
				element.type = 'hidden';
		
				while(element.className != 'form')
					element = element.parentNode;
					
				var form = document.getElementById('poster');
				
				var inputs = element.getElementsByTagName('input');
				while(inputs.length > 0) 
					form.appendChild(inputs[0]);
					
				var selects = element.getElementsByTagName('select');
				while(selects.length > 0) 
					form.appendChild(selects[0]);
					
				var textareas = element.getElementsByTagName('textarea');
				while(textareas.length > 0) 
					form.appendChild(textareas[0]);
				
				form.submit();
			}
		</script>
		
		<?php
		if (isset($_GET['id']) && $_GET['id'] == 'add')
		{
			$this->EnitytCommandAdd();
			return;
		}
		?>
		
		<div id="Editor">
		<!-- Empty form to use as a work around to allow a form per table row -->
		<form id="poster" method="POST"></form>
		
		<!-- Editor -->
		<table cellspacing="0">
			<tr>
				<td width="220" class="Title">&nbsp;</td>
				<td class="Title" align="center">Editing: Entity Commands</td>
				<td width="220" class="Title">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3" valign="top">
					<!-- Added an id to this table to load data into it via javascript -->
					<table id ="DynamicData" cellspacing="0">
						<tr style="font-weight:bold;">
							<td width="55" class="SectionTitle">id</td>
							<td width="55" class="SectionTitle">command_list_id</td>
							<td width="100" class="SectionTitle">command_text</td>
							<td width="55" class="SectionTitle">distance</td>
							<td width="100" class="SectionTitle">command</td>
							<td width="195" class="SectionTitle">error_text</td>
							<td width="55" class="SectionTitle">cast_time</td>
							<td width="55" class="SectionTitle">spell_visual</td>
							<td width="120" colspan="2" class="SectionTitle">&nbsp;</td>
						</tr>
						
						<?php
						if (is_array($entity_command_data))
						{
							foreach ($entity_command_data as $data)
							{
								?>
								<tr class="form">
									<?php 
									$eq2->DrawInputTextBox($data, "id", "small", 1);
									$eq2->DrawInputTextBox($data, "command_list_id", "small", 0);
									$eq2->DrawInputTextBox($data, "command_text", "large", 0);
									$eq2->DrawInputTextBox($data, "distance", "small", 0);
									$eq2->DrawInputTextBox($data, "command", "large", 0);
									$eq2->DrawInputTextBox($data, "error_text", "longtext", 0);
									$eq2->DrawInputTextBox($data, "cast_time", "medium", 0);
									$eq2->DrawInputTextBox($data, "spell_visual", "medium", 0);
									?>
									<td nowrap="nowrap">
										<input type="submit" name="cmd" value="Update" class="Submit" onclick="submitForm(this);" />
										<input type="submit" name="cmd" value="Delete" class="Submit" onclick="submitForm(this);" />
										<input type="hidden" name="table" value="entity_commands" />
										<input type="hidden" name="object" value="Edit Entity Command" />
									</td>
								</tr>
								<?php
							}
						}
						?>
						
					</table>
				</td>
			</tr>
			<tr>
			<td colspan="10"><div id="LoadingDiv" align="center"> <!--<img src="images/loading.gif" />--> Loading...</div></td> <!-- loading.gif freezes no matter what I try so commented it out for now -->
			</tr>
		</table>
		</div>
		<?php
	}
	
	
	private function EnitytCommandAdd()
	{
		?>
		<div id="Editor">
		<table cellspacing="0" width="900">
			<tr>
				<td class="Title" align="center">Adding Entity Command</td>
			</tr>
			<tr><td align="center">
			<form method="POST" action="index.php?page=server&type=entity_commands">
				<table>
					<tr>
						<td colspan="2" class="Title" align="center">Entity Command Info</td>
					</tr>
					<tr>
						<td align="right">ID:</td>
						<td><strong>new</strong>&nbsp;(<?php echo $this->db->GetNextEntityCommandID(); ?>)</td>
					</tr>
					<tr>
						<td align="right">Command List ID:</td>
						<td><input type="text" name="entity_commands|command_list_id" value="" class="medium" /></td>
					</tr>
					<tr>
						<td align="right">Command Text:</td>
						<td><input type="text" name="entity_commands|command_text" value="" class="longtext" /></td>
					</tr>
					<tr>
						<td align="right">Distance:</td>
						<td><input type="text" name="entity_commands|distance" value="10" class="medium" /></td>
					</tr>
					<tr>
						<td align="right">Command:</td>
						<td><input type="text" name="entity_commands|command" value="" class="longtext" /></td>
					</tr>
					<tr>
						<td align="right">Error Text:</td>
						<td><input type="text" name="entity_commands|error_text" value="" class="longtext" /></td>
					</tr>
					<tr>
						<td align="right">Cast Time:</td>
						<td><input type="text" name="entity_commands|cast_time" value="0" class="medium" /></td>
					</tr>
					<tr>
						<td align="right">Spell Visual:</td>
						<td><input type="text" name="entity_commands|spell_visual" value="0" class="medium" /></td>
					</tr>
					<tr>
						<td colspan="2" align="center">
							<input type="submit" name="cmd" value="Insert" class="Submit" /> <!-- Button for submit -->
							&nbsp;
							<input type="submit" value="Cancel" class="Submit" onclick="dosub('index.php?page=server&type=entity_commands');" /> <!-- Button for cancel -->
							<input type="hidden" name="table" value="entity_commands" />
							<input type="hidden" name="object" value="New Entity Command" />
						</td>
					</tr>
				</table>
			</form>
			</td></tr>
		</table>
		</div>
		<?php
	}
	
	
	private function GroundSpawns()
	{
		global $eq2;
		
		if (isset($_GET['id']))
		{
			if ($_GET['id'] > 0)
			{
				$this->GroundSpawnEditor();
				return;
			}
			else if ($_GET['id'] == "new")
			{
				$this->AddGroundSpawn();
				return;
			}
		}
		
		?>
		<br />
		<div id="SelectGrid">
		
		<table id ="SelectGrid" cellspacing="0" border="0">
			<tr>
				<td class="title" align="center" colspan="6">Ground Spawns</td>
			</tr>
			<tr>
				<th width="5%">&nbsp;</th>
				<th width="5%">id</th>
				<th width="65%">tablename</th>
				<th width="10%">min_skill_level</th>
				<th width="10%">min_adventure_level</th>
				<th width="5%">enabled</th>
			</tr>
			
			<?php
			$groundSpawns = $this->db->GetGroundSpawns();
			if (is_array($groundSpawns))
			{
				$i = 0;
				foreach ($groundSpawns as $data)
				{
					$RowColor = ( $i % 2 ) ? "row1" : "row2";
					?>
					<tr class="<?= $RowColor ?>">
						<td>&nbsp;[&nbsp;<a href="index.php?page=server&type=groundspawns&id=<?= $data['groundspawn_id'] ?>">Edit</a>&nbsp;]&nbsp;</td>
						<td>&nbsp;<?= $data['groundspawn_id'] ?>&nbsp;</td>
						<td>&nbsp;<?= $data['tablename'] ?>&nbsp;</td>
						<td>&nbsp;<?= $data['min_skill_level'] ?>&nbsp;</td>
						<td>&nbsp;<?= $data['min_adventure_level'] ?>&nbsp;</td>
						<td>&nbsp;<?= $data['enabled'] ?>&nbsp;</td>
					</tr>
					<?php
					$i++;
				}
			}
			?>
		</table>
		</div>
		<?php
	}
	
	
	private function GroundSpawnEditor()
	{
		global $eq2;
		
		if (isset($_POST['cmd']))
		{
			switch ($_POST['cmd'])
			{
				case "Save": // Save a ground spawn
				case "Update": // Save a ground spawn item
					$eq2->ProcessUpdate();
					break;
				case "Delete": // Delete a ground spawn item
					$eq2->ProcessDelete(NULL);
					break;
				case "Insert": // Add a new ground spawn item
					$eq2->ProcessInsert(NULL);
					break;
			}
		}
		
		$groundspawn = $this->db->GetGroundSpawn($_GET['id']);
		$groundspawnitems = $this->db->GetGroundSpawnItems($_GET['id']);
		?>
		
		<div id="Editor">
			<table cellspacing="0" width="1045">
				<tr>
					<td class="Title" align="center">Editing Groundspawn: <?= $groundspawn['tablename'] ?> (<?= $groundspawn['groundspawn_id'] ?>)</td>
				</tr>
				<tr>
					<td>
						<form method="POST">
						<table cellspacing="0" >
							<tr>
								<td colspan="14" class="SectionTitle" align="center">Ground Spawn Info</td>
							</tr>
							<tr>
								<td class="LabelRight">ID:</td>
								<?php $eq2->DrawInputTextBox($groundspawn, "groundspawn_id", "small", 1); ?>
								<td class="LabelRight">Table Name:</td>
								<td colspan="3">
									<input type="text" name="groundspawns|tablename" value="<?= $groundspawn['tablename'] ?>" class="full" />
									<input type="hidden" name="orig_tablename" value="<?= $groundspawn['tablename'] ?>" />
								</td>
								<td class="LabelRight">Min Skill Level:</td>
								<?php $eq2->DrawInputTextBox($groundspawn, "min_skill_level", "medium", 0); ?>
								<td class="LabelRight">Min Adventure Level:</td>
								<?php $eq2->DrawInputTextBox($groundspawn, "min_adventure_level", "medium", 0); ?>
								<td class="LabelRight">Bonus Table:</td>
								<?php $eq2->DrawInputTextBox($groundspawn, "bonus_table", "medium", 0); ?>
								<td class="LabelRight">Enabled:</td>
								
								<td>
								<select name="groundspawns|enabled" class="yesno">
									<option<?php if($groundspawn['enabled'] == 1) print(' selected') ?> value="1">True</option>
									<option<?php if($groundspawn['enabled'] == 0) print(' selected') ?> value="0">False</option>
								</select>
								<input type="hidden" name="orig_enabled" value="<?= $groundspawn['enabled'] ?>" />
								</td>
								
							</tr>
							<tr><td><br /></td></tr>
							<tr>
								<td class="LabelRight">Harvest 1:</td>
								<?php $eq2->DrawInputTextBox($groundspawn, "harvest1", "medium", 0); ?>
								<td class="LabelRight">Harvest 3:</td>
								<?php $eq2->DrawInputTextBox($groundspawn, "harvest3", "medium", 0); ?>
								<td class="LabelRight">Harvest 5:</td>
								<?php $eq2->DrawInputTextBox($groundspawn, "harvest5", "medium", 0); ?>
								<td class="LabelRight">Harvest Imbue:</td>
								<?php $eq2->DrawInputTextBox($groundspawn, "harvest_imbue", "medium", 0); ?>
								<td class="LabelRight">Harvest Rare:</td>
								<?php $eq2->DrawInputTextBox($groundspawn, "harvest_rare", "medium", 0); ?>
								<td class="LabelRight">Harvest 10:</td>
								<?php $eq2->DrawInputTextBox($groundspawn, "harvest10", "medium", 0); ?>
								<td class="LabelRight">Harvest Coin:</td>
								<?php $eq2->DrawInputTextBox($groundspawn, "harvest_coin", "medium", 0); ?>
							</tr>
							<tr>
								<td colspan="14" align="center">
									<br /><input type="Submit" name="cmd" value="Save" class="submit" />
									<input type="hidden" name="orig_id" value="<?= $groundspawn['id'] ?>" />
									<input type="hidden" name="table" value="groundspawns" />
									<input type="hidden" name="object" value="Edit Groundspawn" />
								</td>
							</tr>
						</table>
						</form>
					</td>
				</tr>
				<tr>
					<td>
						<table id="SelectGrid" cellspacing="0" style="width: 1030px;">
							<tr>
								<td class="SectionTitle" align="center" colspan="8">Ground Spawn Item Info</td>
							</tr>
							<tr>
								<th>ID:</th>
								<th>Ground Spawn ID:</th>
								<th>Item ID:</th>
								<th>Plus Rare ID:</th>
								<th>Rare:</th>
								<th>Grid ID:</th>
								<th>Percent:</th>
								<th width="120"></th>
							</tr>
							<?php
							if (!empty($groundspawnitems))
							{
								foreach ($groundspawnitems as $data)
								{
									?>
									<form method="POST">
									<tr>
										<td>
											<input type="text" name="groundspawn_items|id" value="<?= $data['id'] ?>" class="medium" style="background-color:#ddd;" readonly />
											<input type="hidden" name="orig_id" value="<?= $data['id'] ?>" />
										</td>
										<td>
											<input type="text" name="groundspawn_items|groundspawn_id" value="<?= $data['groundspawn_id'] ?>" class="medium" style="background-color:#ddd;" readonly />
											<input type="hidden" name="orig_groundspawn_id" value="<?= $data['groundspawn_id'] ?>" />
										</td>
										<td>
											<input type="text" name="groundspawn_items|item_id" value="<?= $data['item_id'] ?>" class="xl" />
											<input type="hidden" name="orig_item_id" value="<?= $data['item_id'] ?>" />
											<a onclick="javascript:window.open('popup.php?type=item_search','lookup','width=1024,height=768,left=10,top=75,scrollbars=yes');"><img src="images/search.png" /></a>
										</td>
										<td>
											<input type="text" name="groundspawn_items|plus_rare_id" value="<?= $data['plus_rare_id'] ?>" class="xl" />
											<input type="hidden" name="orig_plus_rare_id" value="<?= $data['plus_rare_id'] ?>" />
										</td>
										<td>
											<select name="groundspawn_items|is_rare" class="yesno">
												<option value="0"<?= $data['is_rare'] == 0 ? "" : " selected" ?>>No</option>
												<option value="1"<?= $data['is_rare'] == 1 ? " selected" : "" ?>>Yes</option>
												<option value="2"<?= $data['is_rare'] == 2 ? " selected" : "" ?>>Imbue</option>
												<option value="3"<?= $data['is_rare'] == 3 ? " selected" : "" ?>>Spell Shard</option>
												<option value="4"<?= $data['is_rare'] == 4 ? " selected" : "" ?>>Foundation</option>
											</select>
											<input type="hidden" name="orig_is_rare" value="<?= $data['is_rare'] ?>" />
										</td>
										<td>
											<input type="text" name="groundspawn_items|grid_id" value="<?= $data['grid_id'] ?>" class="large" />
											<input type="hidden" name="orig_grid_id" value="<?= $data['grid_id'] ?>" />
										</td>
										<td>
											<input type="text" name="groundspawn_items|percent" value="<?= $data['percent'] ?>" class="xl" />
											<input type="hidden" name="orig_percent" value="<?= $data['percent'] ?>" />
										</td>
										<td nowrap="nowrap">
											<input type="submit" name="cmd" value="Update" class="submit" />
											<input type="submit" name="cmd" value="Delete" class="submit" />
											<input type="hidden" name="table" value="groundspawn_items" />
											<input type="hidden" name="object" value="Edit Groundspawn Item" />
										</td>
									</tr>
									</form>
									<?php
								}
							}
							?>
							<form method="POST">
							<tr>
								<td>new</td>
								<td><input type="text" name="groundspawn_items|groundspawn_id" value="<?= $groundspawn['groundspawn_id'] ?>" class="medium" style="background-color:#ddd;" readonly /></td>
								<td><input type="text" name="groundspawn_items|item_id" value="" class="xl" /></td>
								<td><input type="text" name="groundspawn_items|plus_rare_id" value="0" class="xl" /></td>
								<td>
								<select name="groundspawn_items|is_rare" class="yesno">
									<option value="0" selected>No</option>
									<option value="1">Yes</option>
									<option value="2">Imbue</option>
									<option value="3">Spell Shard</option>
									<option value="4">Foundation</option>
								</select>
								</td>
								
								<td><input type="text" name="groundspawn_items|grid_id" value="0" class="large" /></td>
								<td><input type="text" name="groundspawn_items|percent" value="0" class="xl" /></td>
								<td nowrap="nowrap">
									<input type="submit" name="cmd" value="Insert" class="submit" />
									<input type="hidden" name="table" value="groundspawn_items" />
									<input type="hidden" name="object" value="Add Groundspawn Item" />
								</td>
							</tr>
							</form>
						</table>
					</td>
				</tr>
			</table>
		</div>
		
		<?php
	}
	
	
	private function AddGroundSpawn()
	{
		$next_id = $this->db->GetNextGroundSpawnID();
		?>
		<div id="Editor">
			<table cellspacing="0" width="1045">
				<tr>
					<td class="Title" align="center">Adding New Groundspawn</td>
				</tr>
				<tr>
					<td>
						<form method="POST" action="index.php?page=server&type=groundspawns&id=<?= $next_id ?>">
						<table cellspacing="0" >
							<tr>
								<td colspan="14" class="SectionTitle" align="center">Ground Spawn Info</td>
							</tr>
							<tr>
								<td class="LabelRight">ID:</td>
								<td><input type="text" name="groundspawns|groundspawn_id" class="small" value="<?= $next_id ?>" /></td>
								
								<td class="LabelRight">Table Name:</td>
								<td colspan="3">
									<input type="text" name="groundspawns|tablename" class="full" />
								</td>
								
								<td class="LabelRight">Min Skill Level:</td>
								<td><input type="text" name="groundspawns|min_skill_level" class="medium" value="0" /></td>
								
								<td class="LabelRight">Min Adventure Level:</td>
								<td><input type="text" name="groundspawns|min_adventure_level" class="medium" value="0" /></td>
								
								<td class="LabelRight">Bonus Table:</td>
								<td><input type="text" name="groundspawns|bonus_table" class="medium" value="0" /></td>								
								
								<td class="LabelRight">Enabled:</td>								
								<td>
								<select name="groundspawns|enabled" class="yesno">
									<option selected value="1">True</option>
									<option value="0">False</option>
								</select>
								</td>
								
							</tr>
							<tr><td><br /></td></tr>
							<tr>
								<td class="LabelRight">Harvest 1:</td>
								<td><input type="text" name="groundspawns|harvest1" class="medium" value="70" /></td>
								
								<td class="LabelRight">Harvest 3:</td>
								<td><input type="text" name="groundspawns|harvest3" class="medium" value="20" /></td>
								
								<td class="LabelRight">Harvest 5:</td>
								<td><input type="text" name="groundspawns|harvest5" class="medium" value="8" /></td>
								
								<td class="LabelRight">Harvest Imbue:</td>
								<td><input type="text" name="groundspawns|harvest_imbue" class="medium" value="1" /></td>
								
								<td class="LabelRight">Harvest Rare:</td>
								<td><input type="text" name="groundspawns|harvest_rare" class="medium" value="0.7" /></td>
								
								<td class="LabelRight">Harvest 10:</td>
								<td><input type="text" name="groundspawns|harvest10" class="medium" value="0.3" /></td>
								
								<td class="LabelRight">Harvest Coin:</td>
								<td><input type="text" name="groundspawns|harvest_coin" class="medium" value="0" /></td>
							</tr>
							<tr>
								<td colspan="14" align="center">
									<br /><input type="Submit" name="cmd" value="Insert" class="submit" />
									<input type="hidden" name="table" value="groundspawns" />
									<input type="hidden" name="object" value="New Groundspawn" />
								</td>
							</tr>
						</table>
						</form>
					</td>
				</tr>
			</table>
		</div>
		<?php
	}
	
	
	private function LootTable()
	{
		global $eq2;
	
		if (isset($_GET['id']))
		{
			if ($_GET['id'] > 0)
			{
				$this->LootTableEditor();
				return;
			}
			else if ($_GET['id'] == "new")
			{
				$this->AddLootTable();
				return;
			}
		}
	
		?>
			<br />
			<div id="SelectGrid">
			
			<table id ="SelectGrid" cellspacing="0" border="0">
				<tr>
					<td class="title" align="center" colspan="6">Loot Tables</td>
				</tr>
				<tr>
					<th width="5%">&nbsp;</th>
					<th width="5%">id</th>
					<th width="60%">name</th>
					<th width="10%">mincoin</th>
					<th width="10%">maxcoin</th>
					<th width="10%">maxlootitems</th>
				</tr>
				
				<?php
				$loottables = $this->db->GetLootTables();
				if (is_array($loottables))
				{
					$i = 0;
					foreach ($loottables as $data)
					{
						$RowColor = ( $i % 2 ) ? "row1" : "row2";
						?>
						<tr class="<?= $RowColor ?>">
							<td>&nbsp;[&nbsp;<a href="index.php?page=server&type=loottable&id=<?= $data['id'] ?>">Edit</a>&nbsp;]&nbsp;</td>
							<td>&nbsp;<?= $data['id'] ?>&nbsp;</td>
							<td>&nbsp;<?= $data['name'] ?>&nbsp;</td>
							<td>&nbsp;<?= $data['mincoin'] ?>&nbsp;</td>
							<td>&nbsp;<?= $data['maxcoin'] ?>&nbsp;</td>
							<td>&nbsp;<?= $data['maxlootitems'] ?>&nbsp;</td>
						</tr>
						<?php
						$i++;
					}
				}
				?>
			</table>
			</div>
			<?php
	}
		
		
		private function LootTableEditor()
		{
			global $eq2;
			
			if (isset($_POST['cmd']))
			{
				switch ($_POST['cmd'])
				{
					case "Save": // Save a ground spawn
					case "Update": // Save a ground spawn item
						$eq2->ProcessUpdate();
						break;
					case "Delete": // Delete a ground spawn item
						$eq2->ProcessDelete(NULL);
						break;
					case "Insert": // Add a new ground spawn item
						$eq2->ProcessInsert(NULL);
						break;
				}
			}
			
			$loottable = $this->db->GetLootTable($_GET['id']);
			$lootdrops = $this->db->GetLoottableDrops($_GET['id']);
			$totalProbability = 0;
			?>
			
			<div id="Editor">
				<table cellspacing="0" width="1045">
					<tr>
						<td class="Title" align="center">Editing Loot Table: <?= $loottable['name'] ?> (<?= $loottable['id'] ?>)</td>
					</tr>
					<tr>
						<td>
							<form method="POST">
							<table cellspacing="0" style="width: 1030px;" >
								<tr>
									<td colspan="10" class="SectionTitle" align="center">Loot Table Info</td>
								</tr>
								<tr>
									<td class="LabelRight">ID:</td>
									<?php $eq2->DrawInputTextBox($loottable, "id", "small", 1); ?>
									<td class="LabelRight">Table Name:</td>
									<td colspan="3">
										<input type="text" name="loottable|name" value="<?= $loottable['name'] ?>" class="full" />
										<input type="hidden" name="orig_tablename" value="<?= $loottable['name'] ?>" />
									</td>
									<td class="LabelRight">Min Coins:</td>
									<?php $eq2->DrawInputTextBox($loottable, "mincoin", "medium", 0); ?>
									<td class="LabelRight">Max Coins:</td>
									<?php $eq2->DrawInputTextBox($loottable, "maxcoin", "medium", 0); ?>
								</tr>
								<tr><td><br /></td></tr>
								<tr>
									<td colspan="2"></td>
									<td class="LabelRight">Max Loot Items:</td>
									<?php $eq2->DrawInputTextBox($loottable, "maxlootitems", "medium", 0); ?>
									<td class="LabelRight">Item Chance:</td>
									<?php $eq2->DrawInputTextBox($loottable, "lootdrop_probability", "medium", 0); ?>
									<td class="LabelRight">Coin Chance:</td>
									<?php $eq2->DrawInputTextBox($loottable, "coin_probability", "medium", 0); ?>
								</tr>
								<tr>
									<td colspan="14" align="center">
										<br /><input type="Submit" name="cmd" value="Save" class="submit" />
										<input type="hidden" name="orig_id" value="<?= $loottable['id'] ?>" />
										<input type="hidden" name="table" value="loottable" />
										<input type="hidden" name="object" value="Edit LootTable" />
									</td>
								</tr>
							</table>
							</form>
						</td>
					</tr>
					<tr>
						<td>
							<table id="SelectGrid" cellspacing="0" style="width: 1030px;">
								<tr>
									<td class="SectionTitle" align="center" colspan="7">Loot Drop Info</td>
								</tr>
								<tr>
									<th>ID:</th>
									<th>Loot Table ID:</th>
									<th>Item ID:</th>
									<th>Item Charges:</th>
									<th>Equip Item:</th>
									<th>Probability:</th>
									<th width="120"></th>
								</tr>
								<?php
								if (!empty($lootdrops))
								{
									foreach ($lootdrops as $data)
									{
										?>
										<form method="POST">
										<tr>
											<td>
												<input type="text" name="lootdrop|id" value="<?= $data['id'] ?>" class="medium" style="background-color:#ddd;" readonly />
												<input type="hidden" name="orig_id" value="<?= $data['id'] ?>" />
											</td>
											<td>
												<input type="text" name="lootdrop|loot_table_id" value="<?= $data['loot_table_id'] ?>" class="medium" style="background-color:#ddd;" readonly />
												<input type="hidden" name="orig_loot_table_id_id" value="<?= $data['loot_table_id'] ?>" />
											</td>
											<td>
												<input type="text" name="lootdrop|item_id" value="<?= $data['item_id'] ?>" class="xl" />
												<input type="hidden" name="orig_item_id" value="<?= $data['item_id'] ?>" />
												<a onclick="javascript:window.open('popup.php?type=item_search','lookup','width=1024,height=768,left=10,top=75,scrollbars=yes');"><img src="images/search.png" /></a>
												<br>
												<div style="width:42px; height:42px; float:left; background-image: url(characters/eq2Icon.php?id=<?= $data['icon'] ?>);"></div>
												<?= $data['name'] ?>
												<br>
												<?php $eq2->PrintItemTier($data['tier']); ?> (<?= $data['tier'] ?>)
												
												
											</td>
											<td>
												<input type="text" name="lootdrop|item_charges" value="<?= $data['item_charges'] ?>" class="medium" />
												<input type="hidden" name="orig_item_charges" value="<?= $data['item_charges'] ?>" />
											</td>
											<td>
												<select name="lootdrop|equip_item" class="yesno">
													<option value="1"<?= $data['equip_item'] ? " selected" : "" ?>>True</option>
													<option value="0"<?= $data['equip_item'] ? "" : " selected" ?>>False</option>
												</select>
												<input type="hidden" name="orig_equip_item" value="<?= $data['equip_item'] ?>" />
											</td>
											<td>
												<input type="text" name="lootdrop|probability" value="<?= $data['probability'] ?>" class="medium" />
												<input type="hidden" name="orig_probability" value="<?= $data['probability'] ?>" />
												<?php $totalProbability += $data['probability']; ?>
											</td>
											<td nowrap="nowrap">
												<input type="submit" name="cmd" value="Update" class="submit" />
												<input type="submit" name="cmd" value="Delete" class="submit" />
												<input type="hidden" name="table" value="lootdrop" />
												<input type="hidden" name="object" value="Edit Loot Drop" />
											</td>
										</tr>
										</form>
										<?php
									}
								}
								?>
								<form method="POST">
								<tr>
									<td>new</td>
									<td><input type="text" name="lootdrop|loot_table_id" value="<?= $loottable['id'] ?>" class="medium" style="background-color:#ddd;" readonly /></td>
									<td>
										<input type="text" name="lootdrop|item_id" value="" class="xl" />
										<a onclick="javascript:window.open('popup.php?type=item_search','lookup','width=1024,height=768,left=10,top=75,scrollbars=yes');"><img src="images/search.png" /></a>
									</td>
									<td><input type="text" name="lootdrop|item_charges" value="1" class="medium" /></td>
									<td>
										<select name="lootdrop|equip_item" class="yesno">
											<option value="1">True</option>
											<option selected value="0">False</option>
										</select>
									</td>
									<td><input type="text" name="lootdrop|probability" value="25" class="medium" /></td>
									<td nowrap="nowrap">
										<input type="submit" name="cmd" value="Insert" class="submit" />
										<input type="hidden" name="table" value="lootdrop" />
										<input type="hidden" name="object" value="Add Loot Drop" />
									</td>
								</tr>
								<tr><td colspan="7">&nbsp;</td></tr>
								<tr><td colspan="7" align="center">
									<?php
										if ($totalProbability > 100)
											echo("<span style=\"color:#FF0000;\">");
										else if ($totalProbability < 100)
											echo("<span style=\"color:#0000FF;\">");
										else
											echo("<span style=\"color:#009900;\">");
									?>
									 Total Probability: <?= $totalProbability ?>
									 </span>
								</td></tr>
								</form>
							</table>
						</td>
					</tr>
				</table>
			</div>
			
			<?php
		}
		
		
		private function AddLootTable()
		{
			$next_id = $this->db->GetNextLootTableID();
			?>
			<div id="Editor">
				<table cellspacing="0" width="1045">
					<tr>
						<td class="Title" align="center">Adding New Loot Table</td>
					</tr>
					<tr>
						<td>
							<form method="POST" action="index.php?page=server&type=loottable&id=<?= $next_id ?>">
							<table cellspacing="0" width="98%" >
								<tr>
									<td colspan="10" class="SectionTitle" align="center">Loot Table Info</td>
								</tr>
								<tr>
									<td class="LabelRight">ID:</td>
									<td><input type="text" name="loottable|id" class="small" value="<?= $next_id ?>" /></td>
									
									<td class="LabelRight">Name:</td>
									<td colspan="3">
										<input type="text" name="loottable|name" class="full" />
									</td>
									
									<td class="LabelRight">Min Coins:</td>
									<td><input type="text" name="loottable|mincoin" class="medium" value="0" /></td>
									
									<td class="LabelRight">Max Coins:</td>
									<td><input type="text" name="loottable|maxcoin" class="medium" value="0" /></td>
								</tr>
								<tr><td><br /></td></tr>
								<tr>
									<td colspan="2"></td>
									<td class="LabelRight">Max Loot Items:</td>
									<td><input type="text" name="loottable|maxlootitems" class="medium" value="0" /></td>
									
									<td class="LabelRight">Item Chance:</td>
									<td><input type="text" name="loottable|lootdrop_probability" class="medium" value="100" /></td>
									
									<td class="LabelRight">Coin Chance:</td>
									<td><input type="text" name="loottable|coin_probability" class="medium" value="100" /></td>
								</tr>
								<tr>
									<td colspan="10" align="center">
										<br /><input type="Submit" name="cmd" value="Insert" class="submit" />
										<input type="hidden" name="table" value="loottable" />
										<input type="hidden" name="object" value="New LootTable" />
									</td>
								</tr>
							</table>
							</form>
						</td>
					</tr>
				</table>
			</div>
			<?php
		}
		
		private function Transporters()
		{
			global $eq2;
		
			if (isset($_GET['id']))
			{
				if ($_GET['id'] > 0)
				{
					$this->TransporterEditor();
					return;
				}
				else if ($_GET['id'] == "new")
				{
					$this->AddTransporter();
					return;
				}
			}
		
			?>
			<br />
			<div id="SelectGrid">
				
			<table id ="SelectGrid" cellspacing="0" border="0">
				<tr>
					<td class="title" align="center" colspan="6">Transporters</td>
				</tr>
				<tr>
					<th width="5%">&nbsp;</th>
					<th width="5%">id</th>
					<th width="10%">transporter_id</th>
					<th width="70%">display_name</th>
					<th width="10%">destination_zone_id</th>
				</tr>
						
				<?php
				$transporter = $this->db->GetTransporters();
				if (is_array($transporter))
				{
					$i = 0;
					foreach ($transporter as $data)
					{
						$RowColor = ( $i % 2 ) ? "row1" : "row2";
						?>
						<tr class="<?= $RowColor ?>">
							<td>&nbsp;[&nbsp;<a href="index.php?page=server&type=transporters&id=<?= $data['id'] ?>">Edit</a>&nbsp;]&nbsp;</td>
							<td>&nbsp;<?= $data['id'] ?>&nbsp;</td>
							<td>&nbsp;<?= $data['transport_id'] ?>&nbsp;</td>
							<td>&nbsp;<?= $data['display_name'] ?>&nbsp;</td>
							<td>&nbsp;<?= $data['destination_zone_id'] ?>&nbsp;</td>
						</tr>
						<?php
						$i++;
					}
				}
				?>
			</table>
			</div>
			<?php
		}
		
		
		private function TransporterEditor()
		{
			global $eq2;
				
			if (isset($_POST['cmd']))
			{
				switch ($_POST['cmd'])
				{
					case "Save": // Save a ground spawn
					case "Update": // Save a ground spawn item
						$eq2->ProcessUpdate();
						break;
					case "Delete": // Delete a ground spawn item
						$eq2->ProcessDelete(NULL);
						break;
					case "Insert": // Add a new ground spawn item
						$eq2->ProcessInsert(NULL);
						break;
				}
			}
				
			$transporter = $this->db->GetTransporter($_GET['id']);
			?>
					
			<div id="Editor">
				<table cellspacing="0" width="1045">
					<tr>
						<td class="Title" align="center">Editing Transporter: <?= $transporter['display_name'] ?> (<?= $transporter['id'] ?>)</td>
					</tr>
					<tr>
						<td>
							<form method="POST">
							<table cellspacing="0" style="width: 1030px;" >
								<tr>
									<td colspan="10" class="SectionTitle" align="center">Transporter Info</td>
								</tr>
								<tr>
									<td class="LabelRight">ID:</td>
									<?php $eq2->DrawInputTextBox($transporter, "id", "small", 1); ?>
									<td class="LabelRight">Transport ID:</td>
									<?php $eq2->DrawInputTextBox($transporter, "transport_id", "small"); ?>
									<td class="LabelRight">Transport Type:</td>
									<td>
										<select name="transporters|transport_type">
											<option value="Zone"<?= $transporter['transport_type'] == "Zone" ? " selected" : "" ?>>Zone</option>
											<option value="Location"<?= $transporter['transport_type'] == "Location" ? "selected" : "" ?>>Location</option>
											<option value="Generic Transport"<?= $transporter['transport_type'] == "Generic Transport" ? " selected" : "" ?>>Generic Transport</option>
										</select>
										<input type="hidden" name="orig_transport_type" value="<?= $transporter['transport_type'] ?>" />
									</td>
									<td class="LabelRight">Display Name:</td>
									<td colspan="3">
										<input type="text" name="transporters|display_name" value="<?= $transporter['display_name'] ?>" class="full" />
										<input type="hidden" name="orig_display_name" value="<?= $transporter['display_name'] ?>" />
									</td>
								</tr>
								<tr><td><br /></td></tr>
								<tr>
								
									<td class="LabelRight">Destination Zone:</td>
									<td><?php $eq2->EQ2ZoneSelector("transporters", "destination_zone_id", $transporter['destination_zone_id']); ?></td>
									<td class="LabelRight">Destination X:</td>
									<?php $eq2->DrawInputTextBox($transporter, "destination_x", "medium", 0); ?>
									<td class="LabelRight">Destination Y:</td>
									<?php $eq2->DrawInputTextBox($transporter, "destination_y", "medium", 0); ?>
									<td class="LabelRight">Destination Z:</td>
									<?php $eq2->DrawInputTextBox($transporter, "destination_z", "medium", 0); ?>
									<td class="LabelRight">Destination Heading:</td>
									<?php $eq2->DrawInputTextBox($transporter, "destination_heading", "medium", 0); ?>
								
								</tr>
								<tr><td><br /></td></tr>
								<tr>
									<td class="LabelRight">Trigger Zone:</td>
									<td><?php $eq2->EQ2ZoneSelector("transporters", "trigger_location_zone_id", $transporter['trigger_location_zone_id']); ?></td>
									<td class="LabelRight">Trigger X:</td>
									<?php $eq2->DrawInputTextBox($transporter, "trigger_location_x", "medium", 0); ?>
									<td class="LabelRight">Trigger Y:</td>
									<?php $eq2->DrawInputTextBox($transporter, "trigger_location_y", "medium", 0); ?>
									<td class="LabelRight">Trigger Z:</td>
									<?php $eq2->DrawInputTextBox($transporter, "trigger_location_z", "medium", 0); ?>
									<td class="LabelRight">Trigger Radius:</td>
									<?php $eq2->DrawInputTextBox($transporter, "trigger_radius", "medium", 0); ?>
								</tr>
								<tr><td><br /></td></tr>
								<tr>
									<td class="LabelRight">Min Level:</td>
									<?php $eq2->DrawInputTextBox($transporter, "min_level", "medium", 0); ?>
									<td class="LabelRight">Max Level:</td>
									<?php $eq2->DrawInputTextBox($transporter, "max_level", "medium", 0); ?>
									<td class="LabelRight">Required Quest:</td>
									<?php $eq2->DrawInputTextBox($transporter, "quest_req", "medium", 0); ?>
									<td class="LabelRight">Required Quest Step:</td>
									<?php $eq2->DrawInputTextBox($transporter, "quest_step_req", "medium", 0); ?>
									<td class="LabelRight">Required Completed Quest:</td>
									<?php $eq2->DrawInputTextBox($transporter, "quest_completed", "medium", 0); ?>
								</tr>
								<tr><td><br /></td></tr>
								<tr>
									<td class="LabelRight">Cost:</td>
									<?php $eq2->DrawInputTextBox($transporter, "cost", "medium", 0); ?>
									<td class="LabelRight">Message:</td>
									<td colspan="7">
										<input type="text" name="transporters|message" value="<?= $transporter['message'] ?>" class="full" />
										<input type="hidden" name="orig_message" value="<?= $transporter['message'] ?>" />
									</td>
								</tr>
								<tr><td><br /></td></tr>
								<tr>
									<td colspan="3"></td>
									<td class="LabelRight">Map X:</td>
									<?php $eq2->DrawInputTextBox($transporter, "map_x", "medium", 0); ?>
									<td class="LabelRight">Map Y:</td>
									<?php $eq2->DrawInputTextBox($transporter, "map_y", "medium", 0); ?>
								</tr>
								<tr>
									<td colspan="14" align="center">
										<br /><input type="Submit" name="cmd" value="Save" class="submit" />
										<input type="hidden" name="orig_id" value="<?= $transporter['id'] ?>" />
										<input type="hidden" name="table" value="transporters" />
										<input type="hidden" name="object" value="Edit Transporters" />
									</td>
								</tr>
							</table>
							</form>
						</td>
					</tr>
				</table>
			</div>
					
			<?php
		}
		
		private function AddTransporter()
		{
			global $eq2;
			$next_id = $this->db->GetNextTransportersID();
			?>					
			<div id="Editor">
				<table cellspacing="0" width="1045">
					<tr>
						<td class="Title" align="center">Editing Transporter: *NEW* (<?= $next_id ?>)</td>
					</tr>
					<tr>
						<td>
							<form method="POST" action="index.php?page=server&type=transporters&id=<?= $next_id ?>">
							<table cellspacing="0" style="width: 1030px;" >
								<tr>
									<td colspan="10" class="SectionTitle" align="center">Transporter Info</td>
								</tr>
								<tr>
									<td class="LabelRight">ID:</td>
									<td><input type="text" name="transporters|id" class="small" value="<?= $next_id ?>" /></td>
									<td class="LabelRight">Transport ID:</td>
									<td><input type="text" name="transporters|transport_id" class="small" value="0" /></td>
									<td class="LabelRight">Transport Type:</td>
									<td>
										<select name="transporters|transport_type">
											<option value="Zone" selected>Zone</option>
											<option value="Location">Location</option>
											<option value="Generic Transport">Generic Transport</option>
										</select>
									</td>
									<td class="LabelRight">Display Name:</td>
									<td colspan="3">
										<input type="text" name="transporters|display_name" value="" class="full" />
									</td>
								</tr>
								<tr><td><br /></td></tr>
								<tr>
									<td class="LabelRight">Destination Zone:</td>
									<td><?php $eq2->EQ2ZoneSelector("transporters", "destination_zone_id", 0); ?></td>
									<td class="LabelRight">Destination X:</td>
									<td><input type="text" name="transporters|destination_x" class="medium" value="0" /></td>
									<td class="LabelRight">Destination Y:</td>
									<td><input type="text" name="transporters|destination_y" class="medium" value="0" /></td>
									<td class="LabelRight">Destination Z:</td>
									<td><input type="text" name="transporters|destination_z" class="medium" value="0" /></td>
									<td class="LabelRight">Destination Heading:</td>
									<td><input type="text" name="transporters|destination_heading" class="medium" value="0" /></td>
								</tr>
								<tr><td><br /></td></tr>
								<tr>
									<td class="LabelRight">Trigger Zone:</td>
									<td><?php $eq2->EQ2ZoneSelector("transporters", "trigger_location_zone_id", 0); ?></td>
									<td class="LabelRight">Trigger X:</td>
									<td><input type="text" name="transporters|trigger_location_x" class="medium" value="-1" /></td>
									<td class="LabelRight">Trigger Y:</td>
									<td><input type="text" name="transporters|trigger_location_y" class="medium" value="-1" /></td>
									<td class="LabelRight">Trigger Z:</td>
									<td><input type="text" name="transporters|trigger_location_z" class="medium" value="-1" /></td>
									<td class="LabelRight">Trigger Radius:</td>
									<td><input type="text" name="transporters|trigger_radius" class="medium" value="-1" /></td>
								</tr>
								<tr><td><br /></td></tr>
								<tr>
									<td class="LabelRight">Min Level:</td>
									<td><input type="text" name="transporters|min_level" class="medium" value="0" /></td>
									<td class="LabelRight">Max Level:</td>
									<td><input type="text" name="transporters|max_level" class="medium" value="0" /></td>
									<td class="LabelRight">Required Quest:</td>
									<td><input type="text" name="transporters|quest_req" class="medium" value="0" /></td>
									<td class="LabelRight">Required Quest Step:</td>
									<td><input type="text" name="transporters|quest_step_req" class="medium" value="0" /></td>
									<td class="LabelRight">Required Completed Quest:</td>
									<td><input type="text" name="transporters|quest_completed" class="medium" value="0" /></td>
								</tr>
								<tr><td><br /></td></tr>
								<tr>
									<td class="LabelRight">Cost:</td>
									<td><input type="text" name="transporters|cost" class="medium" value="0" /></td>
									<td class="LabelRight">Message:</td>
									<td colspan="7">
										<input type="text" name="transporters|message" value="" class="full" />
									</td>
								</tr>
								<tr><td><br /></td></tr>
								<tr>
									<td colspan="3"></td>
									<td class="LabelRight">Map X:</td>
									<td><input type="text" name="transporters|map_x" class="medium" value="0" /></td>
									<td class="LabelRight">Map Y:</td>
									<td><input type="text" name="transporters|map_y" class="medium" value="0" /></td>
								</tr>
								<tr>
									<td colspan="14" align="center">
										<br /><br /><input type="Submit" name="cmd" value="Insert" class="submit" />
										<input type="hidden" name="table" value="transporters" />
										<input type="hidden" name="object" value="New Transporter" />
									</td>
								</tr>
							</table>
							</form>
						</td>
					</tr>
				</table>
			</div>						
			<?php
		}
	
	private function StartingItems()
	{
		global $eq2;

		//$eq2->items_array = $eq2->eq2db->GetItemNamesArray();

		// Build Toggles array()
		$starting_items_toggles = array('attuned');

		// Perform updates here
		if( isset($_POST['cmd']) )
		{
			// Loop through Toggles and see if any have been unset
			foreach( $starting_items_toggles as $toggles )
			{
				$toggle_settings	= sprintf('starting_items|%s', $toggles);
				// wow, what an epic hack... seriously?
				if( empty($_POST[$toggle_settings]) )
					$_POST[$toggle_settings] = 0;
			}
			
			switch($_POST['cmd']) 
			{
				case "Update": $eq2->ProcessUpdate(); break;
				case "Delete": $eq2->ProcessDelete(null); break;
				case "Insert": $eq2->ProcessInsert(null); break;
			}
		}
		?>
		<div id="Editor">
		<table cellspacing="0">
			<tr>
				<td width="220" class="Title">&nbsp;</td>
				<td class="Title" align="center">Editing: Starting Items</td>
				<td width="220" class="Title">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3" valign="top">
					<table cellspacing="0">
						<tr style="font-weight:bold;">
							<td width="35" class="SectionTitle">id</td>
							<td width="100" class="SectionTitle">class_id</td>
							<td width="100" class="SectionTitle">race_id</td>
							<td width="100" class="SectionTitle">type</td>
							<td width="75" class="SectionTitle">item_id</td>
							<td width="75" class="SectionTitle">creator</td>
							<td width="75" class="SectionTitle">condition_</td>
							<td width="55" class="SectionTitle">attuned</td>
							<td width="55" class="SectionTitle">count</td>
							<td width="120" colspan="2" class="SectionTitle">&nbsp;</td>
						</tr>
						<?php
						$starting_items_data = $this->db->GetStartingItemsData();
						if( is_array($starting_items_data) )
						{
							foreach($starting_items_data as $data)
							{
						?>
						<form method="post" name="siForm|<?php print($data['id']); ?>" />
						<tr>
							<?php
							$eq2->DrawInputTextBox($data, "id", "small", 1);
							$eq2->DrawClassSelector($data, "class_id", "small", 0);
							$eq2->DrawRaceSelector($data, "race_id", "small", 0);
							$eq2->DrawEquipTypeSelector($data, "type", "small", 0);
							$eq2->DrawInputTextBox($data, "item_id", "medium", 0, 1);
							$eq2->DrawInputTextBox($data, "creator", "large", 0);
							$eq2->DrawInputTextBox($data, "condition_", "small", 0);
							$eq2->DrawCheckbox($data, "attuned", "none", 0);
							$eq2->DrawInputTextBox($data, "count", "small", 0);
							?>
							<td nowrap="nowrap">
								<input type="submit" name="cmd" value="Update" class="Submit" />
								<input type="submit" name="cmd" value="Delete" class="Submit" />
							</td>
						</tr>
						<input type="hidden" name="table" value="starting_items" />
						</form>
						<?php
							} //foreach
						} // is_array
						?>
					</table>
				</td>
			</tr>
		</table>
	<?php
	}
	
	
	private function FlightPaths() {
		global $eq2;
		
		if (isset($_GET['id'])) {
			if ($_GET['id'] > 0) {
				$this->FlightPathEditor($_GET['id']);
				return;
			}
		}
		
		?>
		<br/> <br/>
		
		<table class="SearchResultsTemp">
			<tr class="Title">
				<th width="5%">&nbsp;</th>
				<th width="5%">ID</th>
				<th width="5%">Zone</th>
				<th width="85%">Name</th>
			</tr>
			<?php
			if (isset($_GET['zoneID'])) {
				$paths = $this->db->GetFlightPathsByZone($_GET['zoneID']);
			}
			else {
				$paths = $this->db->GetFlightPaths("all");
			}
			$count = 0;
			foreach ($paths as $path) {
				$count++;
				?>
				<tr>
					<td>&nbsp;[&nbsp;<a href="index.php?page=server&type=flight_paths&id=<?= $path['id'] ?>">Edit</a>&nbsp;]&nbsp;</td>
					<td><?= $path['id'] ?></td>
					<td><?= $path['zone_id'] ?></td>
					<td><?= $path['name'] ?></td>
				</tr>
				<?php
			}
			
			if ($count >= 50 && !isset($_GET['zoneID'])) {
				?>
				<tr>
					<td colspan="4" style="text-align: center; font-style: italic; color: #F00;">Max rows displayed (50) - apply additional filters</td>
				</tr>
				<?php
			}
			else {
				?>
				<tr>
					<td colspan="4" style="text-align: center; font-style: italic;"><?= $count ?> rows returned</td>
				</tr>
				<?php
			}
		?>
		</table>
		<?php
	}
	
	
	private function FlightPathEditor($id) {
		global $eq2;
		
		if (isset($_POST['cmd']))
		{
			switch ($_POST['cmd'])
			{
				case "Save": // Save a flight path
				case "Update": // Save a flight path location
					$eq2->ProcessUpdate();
					break;
				case "Delete": // Delete a flight path or location
					$eq2->ProcessDelete(NULL);
					break;
				case "Insert": // Add a new flight path location
					$eq2->ProcessInsert(NULL);
					break;
			}
		}
		
		$path = $this->db->GetFlightPath($id);
		$path_locs = $this->db->GetFlightPathLocations($id);
		?>
		<br /><br />
		<div id="Editor">
			<table cellspacing="0">
				<tr>
					<td class="Title" align="center">Editing Flight Path: <?= $path['name'] ?> (<?= $path['id'] ?>)</td>
				</tr>
				<tr>
					<td>
					<form method="POST">
						<table cellspacing="0">
							<tr>
								<td class="SectionTitle" width="35" align="left">ID</td>
								<td class="SectionTitle" width="50" align="left">Zone</td>
								<td class="SectionTitle" width="250" align="left">Name</td>
								<td class="SectionTitle" width="50" align="left">Speed</td>
								<td class="SectionTitle" width="35" align="left">Flying</td>
								<td class="SectionTitle" width="35" align="left">Dismount</td>
								<td class="SectionTitle">&nbsp;</td>
							</tr>
							<tr>
								<?php $eq2->DrawInputTextBox($path, 'id', 'small', 1); ?>
								<?php $eq2->DrawInputTextBox($path, 'zone_id', 'medium'); ?>
								<?php $eq2->DrawInputTextBox($path, 'name', 'full'); ?>
								<?php $eq2->DrawInputTextBox($path, 'speed', 'medium'); ?>
								
								<td>
									<select name="flight_paths|flying" class="yesno">
										<option value="1"<?= $path['flying'] ? " selected" : "" ?>>True</option>
										<option value="0"<?= $path['flying'] ? "" : " selected" ?>>False</option>
									</select>
									<input type="hidden" name="orig_flying" value="<?= $path['flying'] ?>" />
								</td>
								<td>
									<select name="flight_paths|early_dismount" class="yesno">
										<option value="1"<?= $path['early_dismount'] ? " selected" : "" ?>>True</option>
										<option value="0"<?= $path['early_dismount'] ? "" : " selected" ?>>False</option>
									</select>
									<input type="hidden" name="orig_early_dismount" value="<?= $path['early_dismount'] ?>" />
								</td>
								
								<td nowrap="nowrap">
									<input type="hidden" name="table" value="flight_paths" />
									<input type="submit" name="cmd" value="Save" class="Submit" />
									<input type="submit" name="cmd" value="Delete" class="Submit" />
									<input type="hidden" name="object" value="Modify Flight Path" />
								</td>
							</tr>
						</table>
					</form>
					</td>
				</tr>
				<tr>
					<td>
						<table id="SelectGrid" cellspacing="0" style="width: 98%;">
							<tr>
								<td class="SectionTitle" align="center" colspan="6">Flight Path Locations</td>
							</tr>
							<tr>
								<th>ID</th>
								<th>Flight Path</th>
								<th>X</th>
								<th>Y</th>
								<th>Z</th>
								<th>&nbsp;</th>
							</tr>
							<?php
							foreach ($path_locs as $loc) {
								?>
								<form method="POST">
									<tr>
										<?php $eq2->DrawInputTextBox($loc, 'id', 'medium', 1); ?>
										<?php $eq2->DrawInputTextBox($loc, 'flight_path', 'medium', 1); ?>
										<?php $eq2->DrawInputTextBox($loc, 'x', 'large'); ?>
										<?php $eq2->DrawInputTextBox($loc, 'y', 'large'); ?>
										<?php $eq2->DrawInputTextBox($loc, 'z', 'large'); ?>
										<td nowrap="nowrap">
											<input type="hidden" name="table" value="flight_paths_locations" />
											<input type="submit" name="cmd" value="Update" class="Submit" />
											<input type="submit" name="cmd" value="Delete" class="Submit" />
											<input type="hidden" name="object" value="Modify Flight Path Location" />
										</td>
									</tr>
								</form>
								<?php
							}
							?>
							<form method="POST">
								<tr>
									<td>new</td>
									<td><input type="text" name="flight_paths_locations|flight_path" value="<?= $path['id'] ?>" class="medium" style="background-color:#ddd;" readonly /></td>
									<td><input type="text" name="flight_paths_locations|x" value="" class="large" /></td>
									<td><input type="text" name="flight_paths_locations|y" value="" class="large" /></td>
									<td><input type="text" name="flight_paths_locations|z" value="" class="large" /></td>
									
									<td nowrap="nowrap">
										<input type="hidden" name="table" value="flight_paths_locations" />
										<input type="submit" name="cmd" value="Insert" class="Submit" />
										<input type="hidden" name="object" value="Add Flight Path Location" />
									</td>
								</tr>
							</form>
						</table>
					</td>
				</tr>
			</table>
		</div>
		
		<?php
	}
	
	private function NPCSpells() {
		global $eq2;
		
		$next_spell_list = $this->db->GetNextSpellListID();
		
		if (isset($_GET['id'])) {
			if ($_GET['id'] > 0) {
				$this->NPCSpellListEditor($_GET['id']);
				return;
			}
		}
		
		if (isset($_POST['cmd']))
		{
			switch ($_POST['cmd'])
			{
				case "Insert": // Add a new spell list
					$eq2->ProcessInsert(NULL);
					break;
			}
		}
		
		?>
		
		<br/><br/>
		
		<form method="POST">
		<table class="SearchFilters">
			<tr>
				<th colspan="9">Create New Spell List</th>
			</tr>
			<tr>
				<td>Spell List ID:</td>
				<?php $eq2->DrawInputTextBox($next_spell_list, 'spell_list_id', 'small'); ?>
				<td>Description: </td>
				<td><input type="text" name="spawn_npc_spells|description" value="" class="large" /></td>
				<td>Spell ID:</td>				
				<td><input type="text" name="spawn_npc_spells|spell_id" value="" class="large" /></td>
				<td>Spell Tier:</td>
				<td><input type="text" name="spawn_npc_spells|spell_tier" value="" class="large" /></td>
										
				<td nowrap="nowrap">
					<input type="hidden" name="table" value="spawn_npc_spells" />
					<input type="submit" name="cmd" value="Insert" class="Submit" />
					<input type="hidden" name="object" value="Add NPC Spell List" />
				</td>
			</tr>
		</table>
		</form>
		
		<br/> <br/>
				
		<table class="SearchResultsTemp">
			<tr class="Title">
				<th width="5%">&nbsp;</th>
				<th width="5%">ID</th>
				<th width="90%">Description</th>
			</tr>
			<?php
			$spell_lists = $this->db->GetNPCSpellLists("all");
			
			$count = 0;
			foreach ($spell_lists as $list) {
				$count++;
				?>
				<tr>
					<td>&nbsp;[&nbsp;<a href="index.php?page=server&type=spawn_npc_spells&id=<?= $list['spell_list_id'] ?>">Edit</a>&nbsp;]&nbsp;</td>
					<td><?= $list['spell_list_id'] ?></td>
					<td><?= $list['description'] ?></td>
				</tr>
				<?php
			}
			
			if ($count >= 50) {
				?>
				<tr>
					<td colspan="4" style="text-align: center; font-style: italic; color: #F00;">Max rows displayed (50) - apply additional filters</td>
				</tr>
				<?php
			}
			else {
				?>
				<tr>
					<td colspan="4" style="text-align: center; font-style: italic;"><?= $count ?> rows returned</td>
				</tr>
				<?php
			}
		?>
		</table>
		<?php
	}
	
	private function NPCSpellListEditor($id) {
		global $eq2;
	
		if (isset($_POST['cmd']))
		{
			switch ($_POST['cmd'])
			{
				case "Save": // Save a flight path
				case "Update": // Save a flight path location
					$eq2->ProcessUpdate();
					break;
				case "Delete": // Delete a flight path or location
					$eq2->ProcessDelete(NULL);
					break;
				case "Insert": // Add a new flight path location
					$eq2->ProcessInsert(NULL);
					break;
			}
		}
		
		$spell_list = $this->db->GetNPCSpellList($id);
		$spell_list_info = $this->db->GetSpellListInfo($id);
		?>
			<br /><br />
			<div id="Editor">
				<table cellspacing="0">
					<tr>
						<td class="Title" align="center">Editing NPC Spell List: <?= $spell_list_info['description'] ?> (<?= $spell_list_info['spell_list_id'] ?>)</td>
					</tr>
					<tr>
						<td>
						<form method="POST">
							<table cellspacing="0">
								<tr>
									<td class="SectionTitle" width="35" align="left">ID</td>
									<td class="SectionTitle" width="420" align="left">Description</td>
									<td class="SectionTitle">&nbsp;</td>
								</tr>
								<tr>
									<?php $eq2->DrawInputTextBox($spell_list_info, 'spell_list_id', 'small', 1); ?>
									<?php $eq2->DrawInputTextBox($spell_list_info, 'description', 'full'); ?>
									
									<td nowrap="nowrap">
										<input type="hidden" name="table" value="spawn_npc_spells" />
										<input type="hidden" name="orig_id" value="<?= $spell_list_info['id'] ?>" />
										<input type="submit" name="cmd" value="Save" class="Submit" />
										<input type="submit" name="cmd" value="Delete" class="Submit" />
										<input type="hidden" name="object" value="Modify NPC Spell List" />
									</td>
								</tr>
							</table>
						</form>
						</td>
					</tr>
					<tr>
						<td>
							<table id="SelectGrid" cellspacing="0" style="width: 98%;">
								<tr>
									<td class="SectionTitle" align="center" colspan="6">Spells</td>
								</tr>
								<tr>
									<th>ID</th>
									<th>Spell List ID</th>
									<th>Spell ID</th>
									<th>Spell Tier</th>
									<th>&nbsp;</th>
								</tr>
								<?php
								foreach ($spell_list as $list) {
									?>
									<form method="POST">
										<tr>
											<?php $eq2->DrawInputTextBox($list, 'id', 'medium', 1); ?>
											<?php $eq2->DrawInputTextBox($list, 'spell_list_id', 'medium', 1); ?>
											<?php $eq2->DrawInputTextBox($list, 'spell_id', 'large'); ?>
											<?php $eq2->DrawInputTextBox($list, 'spell_tier', 'large'); ?>
											<td nowrap="nowrap">
												<input type="hidden" name="table" value="spawn_npc_spells" />
												<input type="hidden" name="spawn_npc_spells|description" value="<?= $spell_list_info['description'] ?>" />
												<input type="hidden" name="orig_description" value="<?= $list['description'] ?>" />
												<input type="submit" name="cmd" value="Update" class="Submit" />
												<input type="submit" name="cmd" value="Delete" class="Submit" />
												<input type="hidden" name="object" value="Modify NPC Spells" />
											</td>
										</tr>
									</form>
									<?php
								}
								?>
								<form method="POST">
									<tr>
										<td>new</td>
										<td><input type="text" name="spawn_npc_spells|spell_list_id" value="<?= $spell_list_info['spell_list_id'] ?>" class="medium" style="background-color:#ddd;" readonly /></td>
										<td><input type="text" name="spawn_npc_spells|spell_id" value="" class="large" /></td>
										<td><input type="text" name="spawn_npc_spells|spell_tier" value="" class="large" /></td>
										
										<td nowrap="nowrap">
											<input type="hidden" name="table" value="spawn_npc_spells" />
											<input type="hidden" name="spawn_npc_spells|description" value="<?= $spell_list_info['description'] ?>" />
											<input type="submit" name="cmd" value="Insert" class="Submit" />
											<input type="hidden" name="object" value="Add NPC Spell Entry" />
										</td>
									</tr>
								</form>
							</table>
						</td>
					</tr>
				</table>
			</div>
			
			<?php
	}
	
	private function Recipes()
	{
		global $eq2;
	
		if (isset($_GET['id']))
		{
			if ($_GET['id'] > 0)
			{
				$this->RecipeEditor();
				return;
			}
			else if ($_GET['id'] == "new")
			{
				$this->AddRecipe();
				return;
			}
		}
	
		?>
		<br />
		<div id="SelectGrid">
				
		<table id ="SelectGrid" cellspacing="0" border="0">
			<tr>
				<td class="title" align="center" colspan="5">Recipes</td>
			</tr>
			<tr>
				<th width="5%">&nbsp;</th>
				<th width="5%">id</th>
				<th width="30%">name</th>
				<th width="30%">book</th>
				<th width="30%">device</th>
			</tr>
					
		<?php
			$book = isset($_GET['book']) ? $_GET['book'] : null;
			$recipes = $this->db->GetRecipes($book);
			if (is_array($recipes))
			{
				$i = 0;
				foreach ($recipes as $data)
				{
					$RowColor = ( $i % 2 ) ? "row1" : "row2";
		?>
					<tr class="<?= $RowColor ?>">
						<td>&nbsp;[&nbsp;<a href="index.php?page=server&type=recipes&id=<?= $data['recipe_id'] ?>">Edit</a>&nbsp;]&nbsp;</td>
						<td>&nbsp;<?= $data['recipe_id'] ?>&nbsp;</td>
						<td>&nbsp;<?= $data['name'] ?>&nbsp;</td>
						<td>&nbsp;<?= $data['book'] ?>&nbsp;</td>
						<td>&nbsp;<?= $data['device'] ?>&nbsp;</td>
					</tr>
		<?php
					$i++;
				}
		?>
				<tr>
					<td colspan="5" style="text-align: center; font-style: italic; color: #F00;"><?= $i ?> results</td>
				</tr>
		<?php 
			}
		?>
		</table>
		</div>
		<?php
	}
	
	private function RecipeEditor(){
		global $eq2;
		
		if (isset($_POST['cmd']))
		{
			switch ($_POST['cmd'])
			{
				case "Save":
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
		
		$recipe = $this->db->GetRecipe($_GET['id']);
		$recipeComponents = $this->db->GetRecipeComponents($_GET['id']);
		$recipeProducts = $this->db->GetRecipeProducts($_GET['id']);
		?>
		
		<div id="Editor">
			<table cellspacing="0" width="1045">
				<tr>
					<td class="Title" align="center">Editing Recipe: <?= $recipe['name'] ?> (<?= $recipe['recipe_id'] ?>)</td>
				</tr>
				<tr>
					<td>
						<form method="POST">
							<table cellspacing="0" >
								<tr>
									<td class="LabelRight">ID:</td>
									<?php $eq2->DrawInputTextBox($recipe, "recipe_id", "small", 1); ?>
									<td class="LabelRight">Tier</td>
									<?php $eq2->DrawInputTextBox($recipe, "tier", "small", 0); ?>
									<td class="LabelRight">level</td>
									<?php $eq2->DrawInputTextBox($recipe, "level", "small", 0); ?>
									<td class="LabelRight">icon</td>
									<?php $eq2->DrawInputTextBox($recipe, "icon", "large", 0); ?>
									<td>
										<div id="ItemIconImage" style="width:42px; height:42px; float:left; background-image: url(characters/eq2Icon.php?id=<?= $recipe['icon']?>);"></div>
									</td>
								</tr>
								<tr>
									<td class="LabelRight">skill_level</td>
									<?php $eq2->DrawInputTextBox($recipe, "skill_level", "small", 0); ?>
									<td class="LabelRight">Technique</td>
									<td colspan="3">
										<select name="recipes|technique" style="width:175px;">
											<option value="0">---</option>
											<?php $eq2->GetSkillsOptions($recipe['technique']); ?>
										</select>
										<input type="hidden" name="orig_technique" value="<?= $recipe['technique'] ?>" />
									</td>
									<td class="LabelRight">Knowledge</td>
									<td colspan="3">
										<select name="recipes|knowledge" style="width:175px;">
											<option value="0">---</option>
											<?php $eq2->GetSkillsOptions($recipe['knowledge']); ?>
										</select>
										<input type="hidden" name="orig_knowledge" value="<?= $recipe['knowledge'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="LabelRight">name</td>
									<?php $eq2->DrawInputTextBox($recipe, "name", "full", 0, 0, 4); ?>
									<td class="LabelRight">book</td>
									<?php $eq2->DrawInputTextBox($recipe, "book", "full", 0, 0, 3); ?>
								</tr>
								<tr>
									<td class="LabelRight">device</td>
									<td>
										<select name="recipes|device">
											<option>---</option>
											<option value="Chemistry Table"<?= $recipe['device'] == "Chemistry Table" ? " selected": "" ?>>Chemistry Table</option>
											<option value="Engraved Desk"<?= $recipe['device'] == "Engraved Desk" ? " selected" : "" ?>>Engraved Desk</option>
											<option value="Forge"<?= $recipe['device'] == "Forge" ? " selected" : "" ?>>Forge</option>
											<option value="Stove & Keg"<?= $recipe['device'] == "Stove & Keg" ? " selected" : "" ?>>Stove & Keg</option>
											<option value="Sewing Table & Mannequin"<?= $recipe['device'] == "Sewing Table & Mannequin" ? " selected": "" ?>>Sewing Table & Mannequin</option>
											<option value="Woodworking Table"<?= $recipe['device'] == "Woodworking Table" ? " selected" : "" ?>>Woodworking Table</option>
											<option value="Work Bench"<?= $recipe['device'] == "Work Bench" ? " selected" : "" ?>>Work Bench</option>
										</select>
										<input type="hidden" name="orig_device" value="<?= $recipe['device'] ?>" />
									</td>
									<td class="LabelRight">product_classes</td>
									<?php $eq2->DrawInputTextBox($recipe, "product_classes", "full", 0, 0, 2); ?>
								</tr>
								<tr>
									<td class="LabelRight">unknown2</td>
									<?php $eq2->DrawInputTextBox($recipe, "unknown2", "large"); ?>
									<td class="LabelRight">unknown3</td>
									<?php $eq2->DrawInputTextBox($recipe, "unknown3", "large"); ?>
									<td class="LabelRight">unknown4</td>
									<?php $eq2->DrawInputTextBox($recipe, "unknown4", "large"); ?>
								</tr>
								<tr>
									<td class="LabelRight">product_item_id</td>
									<?php $eq2->DrawInputTextBox($recipe, "product_item_id", "large"); ?>
									<td class="LabelRight">product_name</td>
									<?php $eq2->DrawInputTextBox($recipe, "product_name", "full", 0, 0, 2); ?>
									<td class="LabelRight">product_qty</td>
									<?php $eq2->DrawInputTextBox($recipe, "product_qty", "small"); ?>
									<td class="LabelRight">primary_comp_title</td>
									<?php $eq2->DrawInputTextBox($recipe, "primary_comp_title", "full"); ?>
								</tr>
								<tr>
									<td class="LabelRight">build_comp_title</td>
									<?php $eq2->DrawInputTextBox($recipe, "build_comp_title", "full", 0, 0, 2); ?>
									<td></td>
									<td class="LabelRight">build_comp_qty</td>
									<?php $eq2->DrawInputTextBox($recipe, "build_comp_qty", "large"); ?>
								</tr>
								<tr>
									<td class="LabelRight">build2_comp_title</td>
									<?php $eq2->DrawInputTextBox($recipe, "build2_comp_title", "full", 0, 0, 2); ?>
									<td></td>
									<td class="LabelRight">build2_comp_qty</td>
									<?php $eq2->DrawInputTextBox($recipe, "build2_comp_qty", "large"); ?>
								</tr>
								<tr>
									<td class="LabelRight">build3_comp_title</td>
									<?php $eq2->DrawInputTextBox($recipe, "build3_comp_title", "full", 0, 0, 2); ?>
									<td></td>
									<td class="LabelRight">build3_comp_qty</td>
									<?php $eq2->DrawInputTextBox($recipe, "build3_comp_qty", "large"); ?>
								</tr>
								<tr>
									<td class="LabelRight">build4_comp_title</td>
									<?php $eq2->DrawInputTextBox($recipe, "build4_comp_title", "full", 0, 0, 2); ?>
									<td></td>
									<td class="LabelRight">build4_comp_qty</td>
									<?php $eq2->DrawInputTextBox($recipe, "build4_comp_qty", "large"); ?>
								</tr>
								<tr>
									<td class="LabelRight">fuel_comp_title</td>
									<?php $eq2->DrawInputTextBox($recipe, "fuel_comp_title", "full", 0, 0, 2); ?>
									<td></td>
									<td class="LabelRight">fuel_comp_qty</td>
									<?php $eq2->DrawInputTextBox($recipe, "fuel_comp_qty", "large"); ?>
								</tr>
								<tr>
									<td colspan="9" align="center">
										<br />
										<input type="Submit" name="cmd" value="Save" class="submit" />
										<input type="hidden" name="orig_id" value="<?= $recipe['id'] ?>" />
										<input type="hidden" name="table" value="recipes" />
										<input type="hidden" name="object" value="Edit Recipe" />
									</td>
								</tr>
							</table>
						</form>
					</td>
				</tr>
				<tr></tr>
				<tr>
					<td>
						<table id="SelectGrid" cellspacing="0" style="width: 1030px;">
							<tr>
								<td class="SectionTitle" align="center" colspan="5">Recipe Components Info</td>
							</tr>
							<tr>
								<th>ID:</th>
								<th>Recipe ID:</th>
								<th>Item ID:</th>
								<th>Slot ID:</th>
								<th width="120"></th>
							</tr>
							<?php
							if (!empty($recipeComponents))
							{
								foreach ($recipeComponents as $data)
								{
									?>
									<form method="POST">
									<tr>
										<td>
											<input type="text" name="recipe_components|id" value="<?= $data['id'] ?>" class="medium" style="background-color:#ddd;" readonly />
											<input type="hidden" name="orig_id" value="<?= $data['id'] ?>" />
										</td>
										<td>
											<input type="text" name="recipe_components|recipe_id" value="<?= $data['recipe_id'] ?>" class="medium" style="background-color:#ddd;" readonly />
											<input type="hidden" name="orig_recipe_id" value="<?= $data['recipe_id'] ?>" />
										</td>
										<td>
											<input type="text" name="recipe_components|item_id" value="<?= $data['item_id'] ?>" class="xl" />
											<input type="hidden" name="orig_item_id" value="<?= $data['item_id'] ?>" />
											<a onclick="javascript:window.open('popup.php?type=item_search','lookup','width=1024,height=768,left=10,top=75,scrollbars=yes');"><img src="images/search.png" /></a>
										</td>
										<td>
											<input type="text" name="recipe_components|slot_id" value="<?= $data['slot_id'] ?>" class="large" />
											<input type="hidden" name="orig_slot_id" value="<?= $data['slot_id'] ?>" />
										</td>
										<td nowrap="nowrap">
											<input type="submit" name="cmd" value="Update" class="submit" />
											<input type="submit" name="cmd" value="Delete" class="submit" />
											<input type="hidden" name="table" value="recipe_components" />
											<input type="hidden" name="object" value="Edit Recipe Components" />
										</td>
									</tr>
									</form>
									<?php
								}
							}
							?>
							<form method="POST">
								<tr>
									<td>new</td>
									<td><input type="text" name="recipe_components|recipe_id" value="<?= $recipe['recipe_id'] ?>" class="medium" style="background-color:#ddd;" readonly /></td>
									<td><input type="text" name="recipe_components|item_id" value="" class="xl" /></td>								
									<td><input type="text" name="recipe_components|slot_id" value="0" class="large" /></td>
									<td nowrap="nowrap">
										<input type="submit" name="cmd" value="Insert" class="submit" />
										<input type="hidden" name="table" value="recipe_components" />
										<input type="hidden" name="object" value="Add Recipe Component" />
									</td>
								</tr>
							</form>
							<tr><td colspan=8 style="text-align: center; font-style: italic; color: #F00;">Note: Primary component (slot = 0) and fuel component (slot = 5) have to be set</td></tr>
						</table>
					</td>
				</tr>
				<tr></tr>
				<tr>
					<td>
						<table id="SelectGrid" cellspacing="0" style="width: 1030px;">
							<tr>
								<td class="SectionTitle" align="center" colspan="8">Recipe Products Info</td>
							</tr>
							<tr>
								<th>ID:</th>
								<th>Recipe ID:</th>
								<th>Stage:</th>
								<th>Product ID:</th>
								<th>Product Qty:</th>
								<th>Byproduct ID:</th>
								<th>Byproduct Qty:</th>
								<th width="120"></th>
							</tr>
							<?php
							if (!empty($recipeProducts))
							{
								foreach ($recipeProducts as $data)
								{
									?>
									<form method="POST">
									<tr>
										<td>
											<input type="text" name="recipe_products|id" value="<?= $data['id'] ?>" class="medium" style="background-color:#ddd;" readonly />
											<input type="hidden" name="orig_id" value="<?= $data['id'] ?>" />
										</td>
										<td>
											<input type="text" name="recipe_products|recipe_id" value="<?= $data['recipe_id'] ?>" class="medium" style="background-color:#ddd;" readonly />
											<input type="hidden" name="orig_recipe_id" value="<?= $data['recipe_id'] ?>" />
										</td>
										<td>
											<input type="text" name="recipe_products|stage" value="<?= $data['stage'] ?>" class="large" />
											<input type="hidden" name="orig_stage" value="<?= $data['stage'] ?>" />
										</td>
										<td>
											<input type="text" name="recipe_products|product_id" value="<?= $data['product_id'] ?>" class="xl" />
											<input type="hidden" name="orig_product_id" value="<?= $data['product_id'] ?>" />
											<a onclick="javascript:window.open('popup.php?type=item_search','lookup','width=1024,height=768,left=10,top=75,scrollbars=yes');"><img src="images/search.png" /></a>
										</td>
										<td>
											<input type="text" name="recipe_products|product_qty" value="<?= $data['product_qty'] ?>" class="large" />
											<input type="hidden" name="orig_product_qty" value="<?= $data['product_qty'] ?>" />
										</td>
										<td>
											<input type="text" name="recipe_products|byproduct_id" value="<?= $data['byproduct_id'] ?>" class="xl" />
											<input type="hidden" name="orig_byproduct_id" value="<?= $data['byproduct_id'] ?>" />
											<a onclick="javascript:window.open('popup.php?type=item_search','lookup','width=1024,height=768,left=10,top=75,scrollbars=yes');"><img src="images/search.png" /></a>
										</td>
										<td>
											<input type="text" name="recipe_products|byproduct_qty" value="<?= $data['byproduct_qty'] ?>" class="large" />
											<input type="hidden" name="orig_byproduct_qty" value="<?= $data['byproduct_qty'] ?>" />
										</td>
										<td nowrap="nowrap">
											<input type="submit" name="cmd" value="Update" class="submit" />
											<input type="submit" name="cmd" value="Delete" class="submit" />
											<input type="hidden" name="table" value="recipe_products" />
											<input type="hidden" name="object" value="Edit Recipe Products" />
										</td>
									</tr>
									</form>
									<?php
								}
							}
							?>
							<form method="POST">
								<tr>
									<td>new</td>
									<td><input type="text" name="recipe_products|recipe_id" value="<?= $recipe['recipe_id'] ?>" class="medium" style="background-color:#ddd;" readonly /></td>
									<td><input type="text" name="recipe_products|stage" value="" class="large" /></td>
									<td><input type="text" name="recipe_products|product_id" value="" class="xl" /></td>
									<td><input type="text" name="recipe_products|product_qty" value="0" class="large" /></td>
									<td><input type="text" name="recipe_products|byproduct_id" value="" class="xl" /></td>
									<td><input type="text" name="recipe_products|byproduct_qty" value="0" class="large" /></td>
									<td nowrap="nowrap">
										<input type="submit" name="cmd" value="Insert" class="submit" />
										<input type="hidden" name="table" value="recipe_products" />
										<input type="hidden" name="object" value="Add Recipe Products" />
									</td>
								</tr>
							</form>
							<tr><td colspan=8 style="text-align: center; font-style: italic; color: #F00;">Note: All stages (0-4) should have an entry</td></tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
		<?php 
	}
	
	private function AddRecipe() {
		Global $eq2;
		
		$next_id = $this->db->GetNextRecipeID();
		?>
				<div id="Editor">
					<table cellspacing="0" width="1045">
						<tr>
							<td class="Title" align="center">Adding New Recipe</td>
						</tr>
						<tr>
							<td>
								<form method="POST" action="index.php?page=server&type=recipes&id=<?= $next_id ?>">
								<table cellspacing="0" >
									<tr>
										<td colspan="14" class="SectionTitle" align="center">Recipe Info</td>
									</tr>
									
									<tr>
										<td class="LabelRight">ID:</td>
										<td><input type="text" name="recipes|recipe_id" class="small" value="<?= $next_id ?>" /></td>
										<td class="LabelRight">Tier</td>
										<td><input type="text" name="recipes|tier" class="small" value="0" /></td>
										<td class="LabelRight">level</td>
										<td><input type="text" name="recipes|level" class="small" value="0" /></td>
										<td class="LabelRight">icon</td>
										<td><input type="text" name="recipes|icon" class="large" value="0" /></td>
									</tr>
									<tr>
										<td class="LabelRight">skill_level</td>
										<td><input type="text" name="recipes|skill_level" class="small" value="0" /></td>
										<td class="LabelRight">Technique</td>
										<td colspan="3">
											<select name="recipes|technique" style="width:175px;">
												<option value="0">---</option>
												<?php $eq2->GetSkillsOptions(); ?>
											</select>
										</td>
										<td class="LabelRight">Knowledge</td>
										<td colspan="3">
											<select name="recipes|knowledge" style="width:175px;">
												<option value="0">---</option>
												<?php $eq2->GetSkillsOptions(); ?>
											</select>
										</td>
									</tr>
									<tr>
										<td class="LabelRight">name</td>
										<td colspan="4"><input type="text" name="recipes|name" class="full" value="" /></td>
										<td class="LabelRight">book</td>
										<td colspan="3"><input type="text" name="recipes|book" class="full" value="" /></td>
									</tr>
									<tr>
										<td class="LabelRight">device</td>
										<td>
											<select name="recipes|device">
												<option value="Chemistry Table">Chemistry Table</option>
												<option value="Engraved Desk">Engraved Desk</option>
												<option value="Forge">Forge</option>
												<option value="Stove & Keg">Stove & Keg</option>
												<option value="Sewing Table & Mannequin">Sewing Table & Mannequin</option>
												<option value="Woodworking Table">Woodworking Table</option>
												<option value="Work Bench">Work Bench</option>
											</select>
										</td>
										<td class="LabelRight">product_classes</td>
										<td colspan="2"><input type="text" name="recipes|product_classes" class="full" value="0" /></td>
									</tr>
									<tr>
										<td class="LabelRight">unknown2</td>
										<td><input type="text" name="recipes|unknown2" class="large" value="0" /></td>
										<td class="LabelRight">unknown3</td>
										<td><input type="text" name="recipes|unknown3" class="large" value="0" /></td>
										<td class="LabelRight">unknown4</td>
										<td><input type="text" name="recipes|unknown4" class="large" value="0" /></td>
									</tr>
									<tr>
										<td class="LabelRight">product_item_id</td>
										<td><input type="text" name="recipes|product_item_id" class="large" value="" /></td>
										<td class="LabelRight">product_name</td>
										<td colspan="2"><input type="text" name="recipes|product_name" class="full" value="" /></td>
										<td class="LabelRight">product_qty</td>
										<td><input type="text" name="recipes|product_qty" class="small" value="0" /></td>
										<td class="LabelRight">primary_comp_title</td>
										<td><input type="text" name="recipes|primary_comp_title" class="full" value="" /></td>
									</tr>
									<tr>
										<td class="LabelRight">build_comp_title</td>
										<td colspan="2"><input type="text" name="recipes|build_comp_title" class="full" value="" /></td>
										<td></td>
										<td class="LabelRight">build_comp_qty</td>
										<td><input type="text" name="recipes|build_comp_qty" class="large" value="0" /></td>
									</tr>
									<tr>
										<td class="LabelRight">build2_comp_title</td>
										<td colspan="2"><input type="text" name="recipes|build2_comp_title" class="full" value="" /></td>
										<td></td>
										<td class="LabelRight">build2_comp_qty</td>
										<td><input type="text" name="recipes|build2_comp_qty" class="large" value="0" /></td>
									</tr>
									<tr>
										<td class="LabelRight">build3_comp_title</td>
										<td colspan="2"><input type="text" name="recipes|build3_comp_title" class="full" value="" /></td>
										<td></td>
										<td class="LabelRight">build3_comp_qty</td>
										<td><input type="text" name="recipes|build3_comp_qty" class="large" value="0" /></td>
									</tr>
									<tr>
										<td class="LabelRight">build4_comp_title</td>
										<td colspan="2"><input type="text" name="recipes|build4_comp_title" class="full" value="" /></td>
										<td></td>
										<td class="LabelRight">build4_comp_qty</td>
										<td><input type="text" name="recipes|build4_comp_qty" class="large" value="0" /></td>
									</tr>
									<tr>
										<td class="LabelRight">fuel_comp_title</td>
										<td colspan="2"><input type="text" name="recipes|fuel_comp_title" class="full" value="" /></td>
										<td></td>
										<td class="LabelRight">fuel_comp_qty</td>
										<td><input type="text" name="recipes|fuel_comp_qty" class="large" value="0" /></td>
										
									</tr>
									<tr>
										<td colspan="14" align="center">
											<br /><input type="Submit" name="cmd" value="Insert" class="submit" />
											<input type="hidden" name="table" value="recipes" />
											<input type="hidden" name="object" value="New Recipe" />
										</td>
									</tr>
								</table>
								</form>
							</td>
						</tr>
					</table>
				</div>
				<?php
	}
	
	private function Factions()
	{
		global $eq2;
	
		if (isset($_GET['id']))
		{
			if ($_GET['id'] > 0)
			{
				$this->FactionEditor();
				return;
			}
			else if ($_GET['id'] == "new")
			{
				$this->AddFaction();
				return;
			}
		}
	
		?>
		<br>
		<div id="SelectGrid">
					
		<table id ="SelectGrid" cellspacing="0" border="0">
			<tr>
				<td class="title" align="center" colspan="5">Recipes</td>
			</tr>
			<tr>
				<th width="5%">&nbsp;</th>
				<th width="5%">ID</th>
				<th width="15%">Name</th>
				<th width="10%">Type</th>
				<th width="65%">Description</th>
			</tr>
						
			<?php
			$factions = $this->db->GetFactions();
			if (is_array($factions))
			{
				$i = 0;
				foreach ($factions as $data)
				{
					$RowColor = ( $i % 2 ) ? "row1" : "row2";
					?>
					<tr class="<?= $RowColor ?>">
						<td>&nbsp;[&nbsp;<a href="index.php?page=server&type=factions&id=<?= $data['id'] ?>">Edit</a>&nbsp;]&nbsp;</td>
						<td>&nbsp;<?= $data['id'] ?>&nbsp;</td>
						<td>&nbsp;<?= $data['name'] ?>&nbsp;</td>
						<td>&nbsp;<?= $data['type'] ?>&nbsp;</td>
						<td>&nbsp;<?= $data['description'] ?>&nbsp;</td>
					</tr>
					<?php
					$i++;
				}
				?>
				<tr>
					<td colspan="5" style="text-align: center; font-style: italic; color: #F00;"><?= $i ?> results</td>
				</tr>
				<?php 
			}
			?>
		</table>
		</div>
		<?php
	}
	
	private function FactionEditor()
	{
		global $eq2;
		
		if (isset($_POST['cmd']))
		{
			switch ($_POST['cmd'])
			{
				case "Save":
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
		
		$faction = $this->db->GetFaction($_GET['id']);
		$factionAlliances = $this->db->GetFactionAlliances($_GET['id']);
		?>
				
		<div id="Editor">
			<table cellspacing="0" width="1045">
				<tr>
					<td class="Title" align="center">Editing Faction: <?= $faction['name'] ?> (<?= $faction['id'] ?>)</td>
				</tr>
				<tr>
					<td>
						<form method="POST">
							<table cellspacing="0" >
								<tr>
									<td class="LabelRight">ID:</td>
									<?php $eq2->DrawInputTextBox($faction, "id", "small", 1); ?>
									<td class="LabelRight">Name</td>
									<?php $eq2->DrawInputTextBox($faction, "name", "full", 0, 0, 4); ?>
									<td class="LabelRight">Type</td>
									<?php $eq2->DrawInputTextBox($faction, "type", "full", 0, 0, 2); ?>
								</tr>
								<tr>
									<td class="LabelRight">Description</td>
									<td colspan="10">
										<textarea name="factions|description" style="font:13px Arial, Helvetica, sans-serif; width:99%"><?= $faction['description'] ?></textarea>
										<input type="hidden" name="orig_description" value="<?= $faction['description'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="LabelRight">default_level</td>
									<?php $eq2->DrawInputTextBox($faction, "default_level", "large"); ?>
									<td class="LabelRight">negative_change</td>
									<?php $eq2->DrawInputTextBox($faction, "negative_change", "large"); ?>
									<td class="LabelRight">positive_change</td>
									<?php $eq2->DrawInputTextBox($faction, "positive_change", "large"); ?>
								</tr>
								<tr>
									<td colspan="11" align="center">
										<br>
										<input type="Submit" name="cmd" value="Save" class="submit" />
										<input type="hidden" name="orig_id" value="<?= $faction['id'] ?>" />
										<input type="hidden" name="table" value="factions" />
										<input type="hidden" name="object" value="Edit Faction" />
									</td>
								</tr>
							</table>
						</form>
					</td>
				</tr>
				<tr></tr>
				<tr>
					<td>
						<table id="SelectGrid" cellspacing="0" style="width: 1030px;">
							<tr>
								<td class="SectionTitle" align="center" colspan="5">Faction Alliances</td>
							</tr>
							<tr>
								<th>ID:</th>
								<th>Faction ID:</th>
								<th>Friend Faction:</th>
								<th>Hostile Faction:</th>
								<th width="120"></th>
							</tr>
							<?php
							if (!empty($factionAlliances))
							{
								foreach ($factionAlliances as $data)
								{
									?>
									<form method="POST">
										<tr>
											<td>
												<input type="text" name="faction_alliances|id" value="<?= $data['id'] ?>" class="medium" style="background-color:#ddd;" readonly />
												<input type="hidden" name="orig_id" value="<?= $data['id'] ?>" />
											</td>
											<td>
												<input type="text" name="faction_alliances|faction_id" value="<?= $data['faction_id'] ?>" class="medium" style="background-color:#ddd;" readonly />
												<input type="hidden" name="orig_faction_id" value="<?= $data['faction_id'] ?>" />
											</td>
											<td>
												<input type="text" name="faction_alliances|friend_faction" value="<?= $data['friend_faction'] ?>" class="xl" />
												<input type="hidden" name="orig_friend_faction" value="<?= $data['friend_faction'] ?>" />
											</td>
											<td>
												<input type="text" name="faction_alliances|hostile_faction" value="<?= $data['hostile_faction'] ?>" class="large" />
												<input type="hidden" name="orig_hostile_faction" value="<?= $data['hostile_faction'] ?>" />
											</td>
											<td nowrap="nowrap">
												<input type="submit" name="cmd" value="Update" class="submit" />
												<input type="submit" name="cmd" value="Delete" class="submit" />
												<input type="hidden" name="table" value="faction_alliances" />
												<input type="hidden" name="object" value="Edit Faction Alliance" />
											</td>
										</tr>
									</form>
									<?php
								}
							}
							?>
							<form method="POST">
								<tr>
									<td>new</td>
									<td><input type="text" name="faction_alliances|faction_id" value="<?= $faction['id'] ?>" class="medium" style="background-color:#ddd;" readonly /></td>
									<td><input type="text" name="faction_alliances|friend_faction" value="0" class="large" /></td>								
									<td><input type="text" name="faction_alliances|hostile_faction" value="0" class="large" /></td>
									<td nowrap="nowrap">
										<input type="submit" name="cmd" value="Insert" class="submit" />
										<input type="hidden" name="table" value="faction_alliances" />
										<input type="hidden" name="object" value="Add Faction Alliance" />
									</td>
								</tr>
							</form>
							<tr><td colspan=8 style="text-align: center; font-style: italic; color: #F00;">Note: Only friend or hostile should be set NOT both</td></tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
		<?php 
	}
	
	private function AddFaction()
	{
		global $eq2;
		
		$next_id = $this->db->GetNextFactionID();
		?>
					
		<div id="Editor">
			<table cellspacing="0" width="1045">
				<tr>
					<td class="Title" align="center">Editing Faction: *NEW*</td>
				</tr>
				<tr>
					<td>
						<form method="POST" action="index.php?page=server&type=factions&id=<?= $next_id ?>">
							<table cellspacing="0" >
								<tr>
									<td class="LabelRight">ID:</td>
									<td><input type="text" name="factions|id" class="small" value="<?= $next_id ?>" /></td>
									<td class="LabelRight">Name</td>
									<td colspan="4"><input type="text" name="factions|name" class="full" value="" /></td>
									<td class="LabelRight">Type</td>
									<td colspan="2"><input type="text" name="factions|type" class="full" value="" /></td>
								</tr>
								<tr>
									<td class="LabelRight">Description</td>
									<td colspan="10">
										<textarea name="factions|description" style="font:13px Arial, Helvetica, sans-serif; width:99%"></textarea>
									</td>
								</tr>
								<tr>
									<td class="LabelRight">default_level</td>
									<td><input type="text" name="factions|default_level" class="large" value="0" /></td>
									<td class="LabelRight">negative_change</td>
									<td><input type="text" name="factions|negative_change" class="large" value="0" /></td>
									<td class="LabelRight">positive_change</td>
									<td><input type="text" name="factions|positive_change" class="large" value="0" /></td>
								</tr>
								<tr>
									<td colspan="11" align="center">
										<br>
										<input type="Submit" name="cmd" value="Insert" class="submit" />
										<input type="hidden" name="table" value="factions" />
										<input type="hidden" name="object" value="New Faction" />
									</td>
								</tr>
							</table>
						</form>
					</td>
				</tr>
			</table>
		</div>
		<?php 
	}
	
	private function Collections() {
		global $eq2;
		
		if (isset($_GET['id']))
		{
			if ($_GET['id'] > 0)
			{
				$this->CollectionEditor();
				return;
			}
			else if ($_GET['id'] == "new")
			{
				$this->AddCollection();
				return;
			}
		}
		
		?>
		<br />
		<div id="SelectGrid">
						
			<table id ="SelectGrid" cellspacing="0" border="0">
				<tr>
					<td class="title" align="center" colspan="5">Collections</td>
				</tr>
				<tr>
					<th width="5%">&nbsp;</th>
					<th width="5%">id</th>
					<th width="45%">collection_name</th>
					<th width="40%">collection_category</th>
					<th width="5%">level</th>
				</tr>
							
			<?php
			$collections = $this->db->GetCollections();
			if (is_array($collections))
			{
				$i = 0;
				foreach ($collections as $data)
				{
					$RowColor = ( $i % 2 ) ? "row1" : "row2";
					?>
					<tr class="<?= $RowColor ?>">
						<td>&nbsp;[&nbsp;<a href="index.php?page=server&type=collections&id=<?= $data['id'] ?>">Edit</a>&nbsp;]&nbsp;</td>
						<td>&nbsp;<?= $data['id'] ?>&nbsp;</td>
						<td>&nbsp;<?= $data['collection_name'] ?>&nbsp;</td>
						<td>&nbsp;<?= $data['collection_category'] ?>&nbsp;</td>
						<td>&nbsp;<?= $data['level'] ?>&nbsp;</td>
					</tr>
					<?php
					$i++;
				}
				?>
				<tr>
					<td colspan="5" style="text-align: center; font-style: italic; color: #F00;"><?= $i ?> results</td>
				</tr>
				<?php 
			}
			?>
			</table>
		</div>
		<?php
	}
	
	private function CollectionEditor() {
		global $eq2;
		
		if (isset($_POST['cmd']))
		{
			switch ($_POST['cmd'])
			{
				case "Save":
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
		
		$collection = $this->db->GetCollection($_GET['id']);
		$collectionDetails = $this->db->GetCollectionDetails($_GET['id']);
		$collectionRewards = $this->db->GetCollectionRewards($_GET['id']);
		?>
		<div id="Editor">
			<table cellspacing="0" style="width: 1045px;">
				<tr>
					<td class="Title" align="center">Editing Collection: <?= $collection['collection_name'] ?> (<?= $collection['id'] ?>)</td>
				</tr>
				<tr>
					<td>
						<form method="POST">
							<table cellspacing="0" style="width: 1030px;">
								<tr><td>&nbsp;</td></tr>
								<tr>
									<td class="LabelRight">ID:</td>
									<?php $eq2->DrawInputTextBox($collection, "id", "small", 1); ?>
									<td class="LabelRight">collection_name</td>
									<?php $eq2->DrawInputTextBox($collection, "collection_name", "full", 0, 0, 4); ?>
									<td class="LabelRight">collection_category</td>
									<?php $eq2->DrawInputTextBox($collection, "collection_category", "full", 0, 0, 3); ?>
									<td class="LabelRight">level</td>
									<?php $eq2->DrawInputTextBox($collection, "level", "small", 0); ?>
								</tr>
								<tr><td>&nbsp;</td></tr>
								<td nowrap="nowrap" colspan="13" align="center">
									<input type="submit" name="cmd" value="Update" class="submit" />
									<input type="submit" name="cmd" value="Delete" class="submit" />
									<input type="hidden" name="table" value="collections" />
									<input type="hidden" name="object" value="Edit Collection" />
								</td>
							</table>
						</form>
					</td>
				</tr>
				<tr></tr>
				<tr>
					<td>
						<table id="SelectGrid" cellspacing="0" style="width: 1030px;">
							<tr>
								<td class="SectionTitle" align="center" colspan="5">Collection Details</td>
							</tr>
									<tr>
										<th>ID:</th>
										<th>Collection ID:</th>
										<th>Item ID:</th>
										<th>Item Index:</th>
										<th width="120"></th>
									</tr>
									<?php
									if (!empty($collectionDetails))
									{
										foreach ($collectionDetails as $data)
										{
											?>
											<form method="POST">
											<tr>
												<td>
													<input type="text" name="collection_details|id" value="<?= $data['id'] ?>" class="medium" style="background-color:#ddd;" readonly />
													<input type="hidden" name="orig_id" value="<?= $data['id'] ?>" />
												</td>
												<td>
													<input type="text" name="collection_details|collection_id" value="<?= $data['collection_id'] ?>" class="medium" style="background-color:#ddd;" readonly />
													<input type="hidden" name="orig_collection_id" value="<?= $data['collection_id'] ?>" />
												</td>
												<td>
													<input type="text" name="collection_details|item_id" value="<?= $data['item_id'] ?>" class="xl" />
													<input type="hidden" name="orig_item_id" value="<?= $data['item_id'] ?>" />
													<a onclick="javascript:window.open('popup.php?type=item_search','lookup','width=1024,height=768,left=10,top=75,scrollbars=yes');"><img src="images/search.png" /></a>
													<?= $data['name'] ?>
												</td>
												<td>
													<input type="text" name="collection_details|item_index" value="<?= $data['item_index'] ?>" class="large" />
													<input type="hidden" name="orig_item_index" value="<?= $data['item_index'] ?>" />
												</td>
												<td nowrap="nowrap">
													<input type="submit" name="cmd" value="Update" class="submit" />
													<input type="submit" name="cmd" value="Delete" class="submit" />
													<input type="hidden" name="table" value="collection_details" />
													<input type="hidden" name="object" value="Edit Collection Details" />
												</td>
											</tr>
											</form>
											<?php
										}
									}
									?>
									<form method="POST">
										<tr>
											<td>new</td>
											<td><input type="text" name="collection_details|collection_id" value="<?= $collection['id'] ?>" class="medium" style="background-color:#ddd;" readonly /></td>
											<td><input type="text" name="collection_details|item_id" value="" class="xl" /></td>
											<td><input type="text" name="collection_details|item_index" value="0" class="large" /></td>
											<td nowrap="nowrap">
												<input type="submit" name="cmd" value="Insert" class="submit" />
												<input type="hidden" name="table" value="collection_details" />
												<input type="hidden" name="object" value="Add Collection Details" />
											</td>
										</tr>
									</form>
									<!-- <tr><td colspan=8 style="text-align: center; font-style: italic; color: #F00;">Note: Primary component (slot = 0) and fuel component (slot = 5) have to be set</td></tr> -->
								</table>
							</td>
						</tr>
						<tr></tr>
						<tr>
							<td>
								<table id="SelectGrid" cellspacing="0" style="width: 1030px;">
									<tr>
										<td class="SectionTitle" align="center" colspan="6">Collection Rewards</td>
									</tr>
									<tr>
										<th>ID:</th>
										<th>Collection ID:</th>
										<th>Reward Type:</th>
										<th>Reward Value:</th>
										<th>Reward Qty:</th>
										<th width="120"></th>
									</tr>
									<?php
									if (!empty($collectionRewards))
									{
										foreach ($collectionRewards as $data)
										{
											?>
											<form method="POST">
											<tr>
												<td>
													<input type="text" name="collection_rewards|id" value="<?= $data['id'] ?>" class="medium" style="background-color:#ddd;" readonly />
													<input type="hidden" name="orig_id" value="<?= $data['id'] ?>" />
												</td>
												<td>
													<input type="text" name="collection_rewards|collection_id" value="<?= $data['collection_id'] ?>" class="medium" style="background-color:#ddd;" readonly />
													<input type="hidden" name="orig_collection_id" value="<?= $data['collection_id'] ?>" />
												</td>
												<td>
													<select name="collection_rewards|reward_type">
														<option value="None"<?= $data['reward_type'] == "None" ? " selected" : "" ?>>None</option>
														<option value="Item"<?= $data['reward_type'] == "Item" ? " selected" : "" ?>>Item</option>
														<option value="Selectable"<?= $data['reward_type'] == "Selectable" ? " selected" : "" ?>>Selectable</option>
														<option value="Coin"<?= $data['reward_type'] == "Coin" ? " selected" : "" ?>>Coin</option>
														<option value="XP"<?= $data['reward_type'] == "XP" ? " selected" : "" ?>>XP</option>
													</select>
													<input type="hidden" name="orig_reward_type" value="<?= $data['reward_type'] ?>" />
												</td>
												<td>
													<input type="text" name="collection_rewards|reward_value" value="<?= $data['reward_value'] ?>" class="xl" />
													<input type="hidden" name="orig_reward_value" value="<?= $data['reward_value'] ?>" />
												</td>
												<td>
													<input type="text" name="collection_rewards|reward_quantity" value="<?= $data['reward_quantity'] ?>" class="large" />
													<input type="hidden" name="orig_reward_quantity" value="<?= $data['reward_quantity'] ?>" />
												</td>
												<td nowrap="nowrap">
													<input type="submit" name="cmd" value="Update" class="submit" />
													<input type="submit" name="cmd" value="Delete" class="submit" />
													<input type="hidden" name="table" value="collection_rewards" />
													<input type="hidden" name="object" value="Edit Collection Rewards" />
												</td>
											</tr>
											</form>
											<?php
										}
									}
									?>
									<form method="POST">
										<tr>
											<td>new</td>
											<td><input type="text" name="collection_rewards|collection_id" value="<?= $collection['id'] ?>" class="medium" style="background-color:#ddd;" readonly /></td>
											<td>
												<select name="collection_rewards|reward_type">
													<option value="None">None</option>
													<option value="Item">Item</option>
													<option value="Selectable">Selectable</option>
													<option value="Coin">Coin</option>
													<option value="XP">XP</option>
												</select>
											</td>
											<td><input type="text" name="collection_rewards|reward_value" value="" class="xl" /></td>
											<td><input type="text" name="collection_rewards|reward_quantity" value="0" class="large" /></td>
											<td nowrap="nowrap">
												<input type="submit" name="cmd" value="Insert" class="submit" />
												<input type="hidden" name="table" value="collection_rewards" />
												<input type="hidden" name="object" value="Add Collection Rewards" />
											</td>
										</tr>
									</form>
									<!--  <tr><td colspan=8 style="text-align: center; font-style: italic; color: #F00;">Note: All stages (0-4) should have an entry</td></tr> -->
								</table>
							</td>
						</tr>
					</table>
				</div>
				<?php 
	}
	
	private function AddCollection() {
		global $eq2;
				
		$next_id = $this->db->GetNextCollectionID();
		?>
		<div id="Editor">
			<table cellspacing="0" style="width: 1045px;">
				<tr>
					<td class="Title" align="center">Editing Collection: *NEW*</td>
				</tr>
				<tr>
					<td>
						<form method="POST" action="index.php?page=server&type=collections&id=<?= $next_id ?>">
							<table cellspacing="0" style="width: 1030px;">
								<tr><td>&nbsp;</td></tr>
								<tr>
									<td class="LabelRight">ID:</td>
									<td><input type="text" name="collections|id" class="small" value="<?= $next_id ?>" /></td>
									<td class="LabelRight">collection_name</td>
									<td><input type="text" name="collections|collection_name" class="full" value="" /></td>
									<td class="LabelRight">collection_category</td>
									<td><input type="text" name="collections|collection_category" class="full" value="" /></td>
									<td class="LabelRight">level</td>
									<td><input type="text" name="collections|level" class="small" value="1" /></td>
								</tr>
								<tr><td>&nbsp;</td></tr>
								<tr>
									<td nowrap="nowrap" colspan="8" align="center">
										<input type="submit" name="cmd" value="Insert" class="submit" />
										<input type="hidden" name="table" value="collections" />
										<input type="hidden" name="object" value="Add Collection" />
									</td>
								</tr>
							</table>
						</form>
					</td>
				</tr>
				
				<tr>
				</tr>
			</table>
		</div>
		<?php
	}
	
	private function LootGlobal() {
		global $eq2;
		
		if (isset($_POST['cmd']))
		{
			switch($_POST['cmd'])
			{
				case "Update": $eq2->ProcessUpdate(NULL); break;
				case "Delete": $eq2->ProcessDelete(NULL); break;
				case "Insert": $eq2->ProcessInsert(NULL); break;
			}
		}
		
		$lootTables = $this->db->GetLootTableNames();
		$lootGlobal = $this->db->GetGlobalLoot();
		
		?>
		<div id="Editor">
			<!-- Editor -->
			<table cellspacing="0">
				<tr>
					<td width="220" class="Title">&nbsp;</td>
					<td class="Title" align="center">Editing: Entity Commands</td>
					<td width="220" class="Title">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3" valign="top">
						<!-- Added an id to this table to load data into it via javascript -->
						<table id ="DynamicData" cellspacing="0">
							<tr style="font-weight:bold;">
								<td width="55" class="SectionTitle">id</td>
								<td width="55" class="SectionTitle">type</td>
								<td width="200" class="SectionTitle">loot_table</td>
								<td width="55" class="SectionTitle">value1</td>
								<td width="55" class="SectionTitle">value2</td>
								<td width="55" class="SectionTitle">value3</td>
								<td width="55" class="SectionTitle">value4</td>
								<td width="120" colspan="2" class="SectionTitle">&nbsp;</td>
							</tr>
							<?php
							if (is_array($lootGlobal))
							{
								foreach ($lootGlobal as $data)
								{
									?>
									<form method="POST">
										<tr>
											<td>
												<input type="text" name="loot_global|id" value="<?= $data['id'] ?>" class="small" style="background-color:#ddd;" readonly />
												<input type="hidden" name="orig_id" value="<?= $data['id'] ?>" />
											</td>
											<td>
												<select name="loot_global|type">
													<option value="Level"<?= $data['type'] == "Level" ? " selected" : "" ?>>Level</option>
													<option value="Racial"<?= $data['type'] == "Racial" ? " selected" : "" ?>>Racial</option>
													<option value="Zone"<?= $data['type'] == "Zone" ? " selected" : "" ?>>Zone</option>
												</select>
												<input type="hidden" name="orig_type" value="<?= $data['type'] ?>" />
											</td>
											<td>
												<select name="loot_global|loot_table">
													<?php 
													if (is_array($lootTables))
													{
														foreach ($lootTables as $loot)
														{
															?>
															<option value="<?= $loot['id'] ?>"<?= $data['loot_table'] == $loot['id'] ? " selected" : "" ?>><?= $loot['name'] ?></option>
															<?php
														}
													}
													?>
												</select>
												<input type="hidden" name="orig_loot_table" value="<?= $data['loot_table'] ?>" />
											</td>
											<td>
												<input type="text" name="loot_global|value1" value="<?= $data['value1'] ?>" class="large" />
												<input type="hidden" name="orig_value1" value="<?= $data['value1'] ?>" />
											</td>
											<td>
												<input type="text" name="loot_global|value2" value="<?= $data['value2'] ?>" class="large" />
												<input type="hidden" name="orig_value2" value="<?= $data['value2'] ?>" />
											</td>
											<td>
												<input type="text" name="loot_global|value3" value="<?= $data['value3'] ?>" class="large" />
												<input type="hidden" name="orig_value3" value="<?= $data['value3'] ?>" />
											</td>
											<td>
												<input type="text" name="loot_global|value4" value="<?= $data['value4'] ?>" class="large" />
												<input type="hidden" name="orig_value4" value="<?= $data['value4'] ?>" />
											</td>
											<td nowrap="nowrap">
												<input type="submit" name="cmd" value="Update" class="Submit" />
												<input type="submit" name="cmd" value="Delete" class="Submit" />
												<input type="hidden" name="table" value="loot_global" />
												<input type="hidden" name="object" value="Edit Loot Global" />
											</td>
										</tr>
									</form>
									<?php
								}
							}
							?>
							<form method="POST">
								<tr>
									<td>new</td>
									<td>
										<select name="loot_global|type">
											<option value="Level">Level</option>
											<option value="Racial">Racial</option>
											<option value="Zone">Zone</option>
										</select>
									</td>
									<td>
										<select name="loot_global|loot_table">
											<option value="0">Select a loot table</option>
											<?php 
											if (is_array($lootTables))
											{
												foreach ($lootTables as $loot)
												{
													?>
													<option value="<?= $loot['id'] ?>"><?= $loot['name'] ?></option>
													<?php
												}
											}
											?>
										</select>
									</td>
									<td>
										<input type="text" name="loot_global|value1" value="0" class="large" />
									</td>
									<td>
										<input type="text" name="loot_global|value2" value="0" class="large" />
									</td>
									<td>
										<input type="text" name="loot_global|value3" value="0" class="large" />
									</td>
									<td>
										<input type="text" name="loot_global|value4" value="0" class="large" />
									</td>
									<td nowrap="nowrap">
										<input type="submit" name="cmd" value="Insert" class="Submit" />
										<input type="hidden" name="table" value="loot_global" />
										<input type="hidden" name="object" value="Add Loot Global" />
									</td>
								</tr>
							</form>
						</table>
					</td>
				</tr>
			</table>
		</div>
		<?php
	}

	private function Merchants() {
		global $eq2;
		
		if (isset($_GET['id']))
		{
			if ($_GET['id'] > 0)
			{
				$this->MerchantEditor();
				return;
			}
			else if ($_GET['id'] == "new")
			{
				$this->AddMerchant();
				return;
			}
		}
		
		?>
		<br />
		<div id="SelectGrid">
						
			<table id ="SelectGrid" cellspacing="0" border="0">
				<tr>
					<td class="title" align="center" colspan="5">Merchants</td>
				</tr>
				<tr>
					<th width="5%">&nbsp;</th>
					<th width="5%">id</th>
					<th width="10%">merchant_id</th>
					<th width="10%">inventory_id</th>
					<th width="70%">description</th>
				</tr>
							
			<?php
			$merchants = $this->db->GetMerchants();
			if (is_array($merchants))
			{
				$i = 0;
				foreach ($merchants as $data)
				{
					$RowColor = ( $i % 2 ) ? "row1" : "row2";
					?>
					<tr class="<?= $RowColor ?>">
						<td>&nbsp;[&nbsp;<a href="index.php?page=server&type=merchants&id=<?= $data['id'] ?>">Edit</a>&nbsp;]&nbsp;</td>
						<td>&nbsp;<?= $data['id'] ?>&nbsp;</td>
						<td>&nbsp;<?= $data['merchant_id'] ?>&nbsp;</td>
						<td>&nbsp;<?= $data['inventory_id'] ?>&nbsp;</td>
						<td>&nbsp;<?= $data['description'] ?>&nbsp;</td>
					</tr>
					<?php
					$i++;
				}
				?>
				<tr>
					<td colspan="5" style="text-align: center; font-style: italic; color: #F00;"><?= $i ?> results</td>
				</tr>
				<?php 
			}
			?>
			</table>
		</div>
		<?php
	}

	private function MerchantEditor() {
		global $eq2;
		
		if (isset($_POST['cmd']))
		{
			switch ($_POST['cmd'])
			{
				case "Save":
				case "Update":
					$eq2->ProcessUpdate();
					break;
				case "Delete":
					$eq2->ProcessDelete(NULL);
					break;
				case "Insert":
					$eq2->ProcessInsert(NULL);
					break;
				case "Copy":
					$this->db->CopyMerchant($_GET['id']);
					break;
			}
		}
		
		$merchant = $this->db->GetMerchant($_GET['id']);
		$inventoryList = $this->db->GetMerchantInventoryList($merchant['inventory_id']);
		?>
		<div id="Editor">
			<table cellspacing="0" style="width: 1045px;">
				<tr>
					<td class="Title" align="center">Editing Merchant: <?= $merchant['description'] ?> (<?= $merchant['id'] ?>)</td>
				</tr>
				<tr>
					<td>
						<form method="POST">
							<table cellspacing="0" style="width: 1160px;">
								<tr><td>&nbsp;</td></tr>
								<tr>
									<td class="LabelRight">ID:</td>
									<?php $eq2->DrawInputTextBox($merchant, "id", "small", 1); ?>
									<td class="LabelRight">merchant_id</td>
									<?php $eq2->DrawInputTextBox($merchant, "merchant_id", "large", 1); ?>
									<td class="LabelRight">inventory_id</td>
									<?php $eq2->DrawInputTextBox($merchant, "inventory_id", "large", 0); ?>
									<td class="LabelRight">description</td>
									<?php $eq2->DrawInputTextBox($merchant, "description", "full", 0); ?>
								</tr>
								<tr><td>&nbsp;</td></tr>
								<td nowrap="nowrap" colspan="8" align="center">
									<input type="submit" name="cmd" value="Update" class="submit" />
									<input type="submit" name="cmd" value="Delete" class="submit" />
									<input type="submit" name="cmd" value="Copy" class="submit" />
									<input type="hidden" name="table" value="merchants" />
									<input type="hidden" name="object" value="Edit Merchants" />
								</td>
							</table>
						</form>
					</td>
				</tr>
				<tr></tr>
				<tr>
					<td>
						<table id="SelectGrid" cellspacing="0" style="width: 1160px;">
							<tr>
								<td class="SectionTitle" align="center" colspan="12">Inventory</td>
							</tr>
									<tr>
										<th>ID:</th>
										<th>Inventory ID</th>
										<th>Item ID</th>
										<th>Quantity</th>
										<th>Price Item1 ID</th>
										<th>Price Item1 Qty</th>
										<th>Price Item2 ID</th>
										<th>Price Item2 Qty</th>
										<th>Price Status</th>
										<th>Price Coins</th>
										<th>Price SC</th>
										<th width="120"></th>
									</tr>
									<?php
									if (!empty($inventoryList))
									{
										foreach ($inventoryList as $data)
										{
											?>
											<form method="POST">
											<tr>
												<td>
													<input type="text" name="merchant_inventory|id" value="<?= $data['id'] ?>" class="medium" style="background-color:#ddd;" readonly />
													<input type="hidden" name="orig_id" value="<?= $data['id'] ?>" />
												</td>
												<td>
													<input type="text" name="merchant_inventory|inventory_id" value="<?= $data['inventory_id'] ?>" class="medium" style="background-color:#ddd;" readonly />
													<input type="hidden" name="orig_inventory_id" value="<?= $data['inventory_id'] ?>" />
												</td>
												<td>
													<input type="text" name="merchant_inventory|item_id" value="<?= $data['item_id'] ?>" class="xl" />
													<input type="hidden" name="orig_item_id" value="<?= $data['item_id'] ?>" />
													<a onclick="javascript:window.open('popup.php?type=item_search','lookup','width=1024,height=768,left=10,top=75,scrollbars=yes');"><img src="images/search.png" /></a>
													<br>
													<div style="width:42px; height:42px; float:left; background-image: url(characters/eq2Icon.php?id=<?= $data['icon'] ?>);"></div>
													<?= $data['name'] ?>
													<br>
													<?php $eq2->PrintItemTier($data['tier']); ?> (<?= $data['tier'] ?>)
												</td>
												<td>
													<input type="text" name="merchant_inventory|quantity" value="<?= $data['quantity'] ?>" class="medium" />
													<input type="hidden" name="orig_quantity" value="<?= $data['quantity'] ?>" />
												</td>
												<td>
													<input type="text" name="merchant_inventory|price_item_id" value="<?= $data['price_item_id'] ?>" class="xl" />
													<input type="hidden" name="orig_price_item_id" value="<?= $data['price_item_id'] ?>" />
													<a onclick="javascript:window.open('popup.php?type=item_search','lookup','width=1024,height=768,left=10,top=75,scrollbars=yes');"><img src="images/search.png" /></a>
												</td>
												<td>
													<input type="text" name="merchant_inventory|price_item_qty" value="<?= $data['price_item_qty'] ?>" class="medium" />
													<input type="hidden" name="orig_price_item_qty" value="<?= $data['price_item_qty'] ?>" />
												</td>
												<td>
													<input type="text" name="merchant_inventory|price_item2_id" value="<?= $data['price_item2_id'] ?>" class="xl" />
													<input type="hidden" name="orig_price_item2_id" value="<?= $data['price_item2_id'] ?>" />
													<a onclick="javascript:window.open('popup.php?type=item_search','lookup','width=1024,height=768,left=10,top=75,scrollbars=yes');"><img src="images/search.png" /></a>
												</td>
												<td>
													<input type="text" name="merchant_inventory|price_item2_qty" value="<?= $data['price_item2_qty'] ?>" class="medium" />
													<input type="hidden" name="orig_price_item2_qty" value="<?= $data['price_item2_qty'] ?>" />
												</td>
												<td>
													<input type="text" name="merchant_inventory|price_status" value="<?= $data['price_status'] ?>" class="large" />
													<input type="hidden" name="orig_price_status" value="<?= $data['price_status'] ?>" />
												</td>
												<td>
													<input type="text" name="merchant_inventory|price_coins" value="<?= $data['price_coins'] ?>" class="large" />
													<input type="hidden" name="orig_price_coins" value="<?= $data['price_coins'] ?>" />
												</td>
												<td>
													<input type="text" name="merchant_inventory|price_stationcash" value="<?= $data['price_stationcash'] ?>" class="large" />
													<input type="hidden" name="orig_price_stationcash" value="<?= $data['price_stationcash'] ?>" />
												</td>
												<td nowrap="nowrap">
													<input type="submit" name="cmd" value="Update" class="submit" />
													<input type="submit" name="cmd" value="Delete" class="submit" />
													<input type="hidden" name="table" value="merchant_inventory" />
													<input type="hidden" name="object" value="Edit Merchant Inventory" />
												</td>
											</tr>
											</form>
											<?php
										}
									}
									?>
									<form method="POST">
										<tr>
											<td>new</td>
											<td><input type="text" name="merchant_inventory|inventory_id" value="<?= $merchant['inventory_id'] ?>" class="medium" style="background-color:#ddd;" readonly /></td>
											<td><input type="text" name="merchant_inventory|item_id" value="" class="xl" /><a onclick="javascript:window.open('popup.php?type=item_search','lookup','width=1024,height=768,left=10,top=75,scrollbars=yes');"><img src="images/search.png" /></a></td>
											<td><input type="text" name="merchant_inventory|quantity" value="255" class="medium" /></td>
											<td><input type="text" name="merchant_inventory|price_item_id" value="0" class="xl" /><a onclick="javascript:window.open('popup.php?type=item_search','lookup','width=1024,height=768,left=10,top=75,scrollbars=yes');"><img src="images/search.png" /></a></td>
											<td><input type="text" name="merchant_inventory|price_item_qty" value="0" class="medium" /></td>
											<td><input type="text" name="merchant_inventory|price_item2_id" value="0" class="xl" /><a onclick="javascript:window.open('popup.php?type=item_search','lookup','width=1024,height=768,left=10,top=75,scrollbars=yes');"><img src="images/search.png" /></a></td>
											<td><input type="text" name="merchant_inventory|price_item2_qty" value="0" class="medium" /></td>
											<td><input type="text" name="merchant_inventory|price_status" value="0" class="large" /></td>
											<td><input type="text" name="merchant_inventory|price_coins" value="0" class="large" /></td>
											<td><input type="text" name="merchant_inventory|price_stationcash" value="0" class="large" /></td>
											<td nowrap="nowrap">
												<input type="submit" name="cmd" value="Insert" class="submit" />
												<input type="hidden" name="table" value="merchant_inventory" />
												<input type="hidden" name="object" value="Add Merchant Inventory" />
											</td>
										</tr>
									</form>
									<!-- <tr><td colspan=8 style="text-align: center; font-style: italic; color: #F00;">Note: Primary component (slot = 0) and fuel component (slot = 5) have to be set</td></tr> -->
								</table>
							</td>
						</tr>
						<tr></tr>
					</table>
				</div>
				<?php 
	}

	private function AddMerchant() {
		global $eq2;

		$next_id = $this->db->GetNextMerchantID();
		$next_merchant_id = $this->db->GetNextMerchantID2();
		$next_inventory_id = $this->db->GetNextMerchantInventoryID();

		?>
		<div id="Editor">
			<table cellspacing="0" style="width: 1045px;">
				<tr>
					<td class="Title" align="center">Editing Merchant: *NEW*</td>
				</tr>
				<tr>
					<td>
						<form method="POST" action="index.php?page=server&type=merchants&id=<?= $next_id ?>">
							<table cellspacing="0" style="width: 1160px;">
								<tr><td>&nbsp;</td></tr>
								<tr>
									<td class="LabelRight">ID:</td>
									<td><input type="text" name="merchants|id" class="small" value="<?= $next_id ?>" /></td>
									<td class="LabelRight">merchant_id</td>
									<td><input type="text" name="merchants|merchant_id" class="large" value="<?= $next_merchant_id ?>" /></td>
									<td class="LabelRight">inventory_id</td>
									<td><input type="text" name="merchants|inventory_id" class="large" value="<?= $next_inventory_id ?>" /></td>
									<td class="LabelRight">description</td>
									<td><input type="text" name="merchants|description" class="full" value="" /></td>
								</tr>
								<tr><td>&nbsp;</td></tr>
								<td nowrap="nowrap" colspan="8" align="center">
									<input type="submit" name="cmd" value="Insert" class="submit" />
									<input type="hidden" name="table" value="merchants" />
									<input type="hidden" name="object" value="Add New Merchants" />
								</td>
							</table>
						</form>
					</td>
				</tr>
			</table>
		</div>

		<?php
	}

	private function Houses() {
		global $eq2;
		
		if (isset($_GET['id']))
		{
			if ($_GET['id'] > 0)
			{
				$this->HouseEditor();
				return;
			}
			else if ($_GET['id'] == "new")
			{
				$this->AddHouse();
				return;
			}
		}
		
		?>
		<br />
		<div id="SelectGrid">
						
			<table id ="SelectGrid" cellspacing="0" border="0">
				<tr>
					<td class="title" align="center" colspan="5">Houses</td>
				</tr>
				<tr>
					<th width="5%">&nbsp;</th>
					<th width="5%">id</th>
					<th width="90%">name</th>
				</tr>
							
			<?php
			$houses = $this->db->GetHouses();
			if (is_array($houses))
			{
				$i = 0;
				foreach ($houses as $data)
				{
					$RowColor = ( $i % 2 ) ? "row1" : "row2";
					?>
					<tr class="<?= $RowColor ?>">
						<td>&nbsp;[&nbsp;<a href="index.php?page=server&type=houses&id=<?= $data['id'] ?>">Edit</a>&nbsp;]&nbsp;</td>
						<td>&nbsp;<?= $data['id'] ?>&nbsp;</td>
						<td>&nbsp;<?= $data['name'] ?>&nbsp;</td>
					</tr>
					<?php
					$i++;
				}
				?>
				<tr>
					<td colspan="3" style="text-align: center; font-style: italic; color: #F00;"><?= $i ?> results</td>
				</tr>
				<?php 
			}
			?>
			</table>
		</div>
		<?php
	}

	private function HouseEditor() {
		global $eq2;
		
		if (isset($_POST['cmd']))
		{
			switch ($_POST['cmd'])
			{
				case "Save":
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
		
		$house = $this->db->GetHouse($_GET['id']);
		?>
		<div id="Editor">
			<table cellspacing="0" style="width: 1045px;">
				<tr>
					<td class="Title" align="center">Editing House: <?= $house['name'] ?> (<?= $house['id'] ?>)</td>
				</tr>
				<tr>
					<td>
						<form method="POST">
							<table cellspacing="0" style="width: 1160px;">
								<tr><td>&nbsp;</td></tr>
								<tr>
									<td class="LabelRight">ID:</td>
									<?php $eq2->DrawInputTextBox($house, "id", "small", 1); ?>
									<td class="LabelRight">name</td>
									<?php $eq2->DrawInputTextBox($house, "name", "full", 0); ?>
								</tr>
								<tr>									
									<td class="LabelRight">cost_coins</td>
									<?php $eq2->DrawInputTextBox($house, "cost_coins", "large", 0); ?>
									<td class="LabelRight">cost_status</td>
									<?php $eq2->DrawInputTextBox($house, "cost_status", "large", 0); ?>
									<td class="LabelRight">upkeep_coins</td>
									<?php $eq2->DrawInputTextBox($house, "upkeep_coins", "large", 0); ?>
									<td class="LabelRight">upkeep_status</td>
									<?php $eq2->DrawInputTextBox($house, "upkeep_status", "large", 0); ?>
								</tr>
								<tr>
									<td class="LabelRight">vault_slots</td>
									<?php $eq2->DrawInputTextBox($house, "vault_slots", "large", 0); ?>
									<td class="LabelRight">alignment</td>
									<?php $eq2->DrawInputTextBox($house, "alignment", "large", 0); ?>
									<td class="LabelRight">guild_level</td>
									<?php $eq2->DrawInputTextBox($house, "guild_level", "large", 0); ?>
									<td class="LabelRight">zone_id</td>
									<?php $eq2->DrawInputTextBox($house, "zone_id", "large", 0); ?>
								</tr>
								<tr>
									<td class="LabelRight">exit_zone_id</td>
									<?php $eq2->DrawInputTextBox($house, "exit_zone_id", "large", 0); ?>
									<td class="LabelRight">exit_x</td>
									<?php $eq2->DrawInputTextBox($house, "exit_x", "large", 0); ?>
									<td class="LabelRight">exit_y</td>
									<?php $eq2->DrawInputTextBox($house, "exit_y", "large", 0); ?>
									<td class="LabelRight">exit_z</td>
									<?php $eq2->DrawInputTextBox($house, "exit_z", "large", 0); ?>
								</tr>
								<tr>
									<td class="LabelRight">exit_heading</td>
									<?php $eq2->DrawInputTextBox($house, "exit_heading", "large", 0); ?>
								</tr>
								<tr><td>&nbsp;</td></tr>
								<td nowrap="nowrap" colspan="8" align="center">
									<input type="submit" name="cmd" value="Update" class="submit" />
									<input type="submit" name="cmd" value="Delete" class="submit" />
									<input type="hidden" name="table" value="houses" />
									<input type="hidden" name="object" value="Edit House" />
								</td>
							</table>
						</form>
					</td>
				</tr>
			</table>
		</div>
		<?php 
	}

	private function AddHouse() {
		global $eq2;

		$next_id = $this->db->GetNextHouseID();

		?>
		<div id="Editor">
			<table cellspacing="0" style="width: 1045px;">
				<tr>
					<td class="Title" align="center">Editing House: *NEW*</td>
				</tr>
				<tr>
					<td>
						<form method="POST" action="index.php?page=server&type=houses&id=<?= $next_id ?>">
							<table cellspacing="0" style="width: 1160px;">
								<tr><td>&nbsp;</td></tr>
								<tr>
									<td class="LabelRight">ID:</td>
									<td><input type="text" name="houses|id" class="small" value="<?= $next_id ?>" /></td>
									<td class="LabelRight">name</td>
									<td><input type="text" name="houses|name" class="full" value="" /></td>
								</tr>
								<tr>									
									<td class="LabelRight">cost_coins</td>
									<td><input type="text" name="houses|cost_coins" class="large" value="0" /></td>
									<td class="LabelRight">cost_status</td>
									<td><input type="text" name="houses|cost_status" class="large" value="0" /></td>
									<td class="LabelRight">upkeep_coins</td>
									<td><input type="text" name="houses|upkeep_coins" class="large" value="0" /></td>
									<td class="LabelRight">upkeep_status</td>
									<td><input type="text" name="houses|upkeep_status" class="large" value="0" /></td>
								</tr>
								<tr>
									<td class="LabelRight">vault_slots</td>
									<td><input type="text" name="houses|vault_slots" class="large" value="6" /></td>
									<td class="LabelRight">alignment</td>
									<td><input type="text" name="houses|alignment" class="large" value="0" /></td>
									<td class="LabelRight">guild_level</td>
									<td><input type="text" name="houses|guild_level" class="large" value="0" /></td>
									<td class="LabelRight">zone_id</td>
									<td><input type="text" name="houses|zone_id" class="large" value="0" /></td>
								</tr>
								<tr>
									<td class="LabelRight">exit_zone_id</td>
									<td><input type="text" name="houses|exit_zone_id" class="large" value="0" /></td>
									<td class="LabelRight">exit_x</td>
									<td><input type="text" name="houses|exit_x" class="large" value="0" /></td>
									<td class="LabelRight">exit_y</td>
									<td><input type="text" name="houses|exit_y" class="large" value="0" /></td>
									<td class="LabelRight">exit_z</td>
									<td><input type="text" name="houses|exit_z" class="large" value="0" /></td>
								</tr>
								<tr>
									<td class="LabelRight">exit_heading</td>
									<td><input type="text" name="houses|exit_heading" class="large" value="0" /></td>
								</tr>
								<tr><td>&nbsp;</td></tr>
								<td nowrap="nowrap" colspan="8" align="center">
									<input type="submit" name="cmd" value="Insert" class="submit" />
									<input type="hidden" name="table" value="houses" />
									<input type="hidden" name="object" value="Add New House" />
								</td>
							</table>
						</form>
					</td>
				</tr>
			</table>
		</div>

		<?php
	}
}

?>