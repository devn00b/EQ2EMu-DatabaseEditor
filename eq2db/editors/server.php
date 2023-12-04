<?php 
define('IN_EDITOR', true);
include("header.php"); 

if ( !$eq2->CheckAccess(M_SERVER) )
	die("ACCESS: Denied!");
	
include("../class/eq2.server.php");
$s = new EQ2Server();
include("../class/eq2.spawns.php");
$spawns = new eq2Spawns();
include("../class/eq2.items.php");
$eq2Items = new eq2Items();

$link = sprintf("%s",$_SERVER['SCRIPT_NAME']);

//This is the "save" button (it's an image input so kind of a pain)
if ($eq2->CheckAccess(G_DEVELOPER) && ($_GET['page'] ?? "") == "recipes") {
	if (isset($_POST['UpdateComponent_x'])) {
		$eq2->ProcessUpdates();
	}
	else if (isset($_POST['DeleteComponent_x'])) {
		$eq2->ProcessDeletes();
	}
	else if (isset($_POST['InsertComponent_x'])) {
		$eq2->ProcessInserts();
	}
}

if (($_GET['page'] ?? "") == "recipes" && ($_POST['cmd'] ?? "") == "Search") {
	//Build a link and forward the client
	$link = "server.php?page=recipes&search";
	if ($_POST['searchFilter|Name'] != "") {
		$link .= sprintf("&name=%s", $_POST['searchFilter|Name']);
	}
	if ($_POST['searchFilter|MinLvl'] != "") {
		$link .= sprintf("&minl=%s", $_POST['searchFilter|MinLvl']);
	}
	if ($_POST['searchFilter|MaxLvl'] != "") {
		$link .= sprintf("&maxl=%s", $_POST['searchFilter|MaxLvl']);
	}
	if ($_POST['searchFilter|Class'] != -1) {
		$link .= sprintf("&cls=%s", $_POST['searchFilter|Class']);
	}
	if (!isset($_POST['searchFilter|ap'])) {
		$link .= "&ap=0";
	}
	if (isset($_POST['searchFilter|mc'])) {
		$link .= "&mc=1";
	}
	header("Location: " . $link);
	exit;
}

if (($_GET['page'] ?? "") == "recipe_comp" && ($_POST['cmd'] ?? "") == "Search") {
	header("Location: server.php?page=recipe_comp&search=".($_POST['searchFilter|Name'] ?? ""));
	exit;
}
else if (($_GET['page'] ?? "") == "loot_table" && ($_POST['cmd'] ?? "") == "Search") {
	header("Location: server.php?page=loot_table&search=".($_POST['searchFilter|Name'] ?? ""));
	exit;
}

if( isset($_POST['cmd']) ) 
{
	// do updates/deletes here
	switch(strtolower($_POST['cmd'])) {
		case "insert":
			$s->PreInsert();
			$insert_res = $eq2->ProcessInserts();
			$s->PostInsert($insert_res);
		break;
		case "update":
			$s->PreUpdate();
			$eq2->ProcessUpdates();
			$s->PostUpdate();
			break;
		case "delete": $eq2->ProcessDeletes(); break;
	}
	
} 

?>
<div id="Editor">
<table class="SubPanel" cellspacing="0" border="0">
	<tr>
		<td class="Title" colspan="2">Server Data</td>
	</tr>
	<tr>
		<td valign="top"><!-- Left Menu -->
		<?php $s->GenerateNavigationMenu(); ?>
		</td><!-- End of Left Menu -->
		<td width="100%" valign="top"><!-- Main Page -->
			<table class="SectionMainFloat" cellspacing="0" border="0" style="width:100%">
				<tr>
					<td class="SectionBody">
					<?php
					// if viewing changelogs, do it before other options are called
					if( isset($_GET['cl']) )
						$eq2->DisplayChangeLogPicker($s->EQ2ServerTables);
					else {
						switch($_GET['page']) 
						{
							//This looks like vanguard code...
							//case "locations"						: $s->StartingLocations(); break;
							//case "opcodes"							: $s->OpcodeEditor(); break;
							case "variables"						: $s->ServerVariables(); break;
							case "groundspawns"					: groundspawns(); break;
							case "groundspawn_items"		: groundspawn_items(); break;
							case "entity_commands"			: entity_commands(); break;
							case "commands"							: commands(); break;
							case "collections"					: collections(); break;
							case "starting_spells"			: starting_spells(); break;
							case "starting_factions"        : starting_factions(); break;
							case "npc_spells"			: npc_spells(); break;
							case "recipes"				: recipes(); break;
							case "recipe_comp"          : recipe_comp(); break;
							case "loot_table"           : loot_table(); break;
							case "loot_global"          : loot_global(); break;
							/*
							case "spawn_npc_equipment"	: spawn_npc_equipment(); break;
							case "spawn_npc_skills"			: spawn_npc_skills(); break;
							case "starting_skills"			: starting_skills(); break;
							case "starting_skillbar"		: starting_skillbar(); break;
							case "starting_languages"		: starting_languages(); break;
							case "starting_items"				: starting_items(); break;
							case "starting_details"			: starting_details(); break;
							case "merchants"						: merchants(); break;
							case "skills"								: skills(); break;
							case "factions"							: factions(); break;
							case "faction_alliances"		: faction_alliances(); break;
							case "appearances"					: appearances(); break;
							case "conditionals"					: conditionals(); break;
							case "rules"								: rules(); break;
							case "ttiles"								: titles(); break;
							case "transporters"					: transporters(); break;
							case "table_versions"				: table_versions(); break;
							case "name_filter"					: name_filter(); break;
							case "languages"						: languages(); break;			
							case "emotes"								: emotes(); break;
							case "map_data"							: map_data(); break;
							case "guild_event_defaults"	: guild_event_defaults(); break;
							case "guild_ranks_defaults"	: guild_ranks_defaults(); break;
							*/
							default											:	$s->NotImplemented(); break;
						}
					}
					?>					
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>
<?php
include("footer.php");
exit;




/* refactor all the shit below this line */


$table_array = $eq2->GetTableArray("Server");

if( isset($_GET['cl']) ) {
	?>
	<table>
		<tr>
			<td align="right" nowrap="nowrap"><strong>Choose ChangeLog:&nbsp;</strong></td>
			<td>
				<select name="changelog" onchange="dosub(this.options[this.selectedIndex].value)">
				<option value="<? $link ?>">Pick Table</option>
				<?php 
				foreach($table_array as $key=>$val)
				{
					$selected = $val == $_GET['t'] ? " selected" : "";
					$url = sprintf("%s?p=%s", $link, $val);
					printf('<option value="server.php?cl=history&t=%s"%s>%s</option>', $val, $selected, $val);
				}
				?>
				</select>
			</td>
			<?php 
			if( isset($_GET['t']) ) 
			{ 
				$table = ( isset($_GET['t']) ) ? $_GET['t'] : "";
				$editor_id = ( isset($_GET['c']) ) ? $_GET['c'] : 0;
			?>
			<td>Limit by user:&nbsp;
				<select name="char_id" onchange="dosub(this.options[this.selectedIndex].value)">
					<?= $eq2->getDBTeamSelector($table,$editor_id) ?>
				</select>
			</td>
			<?php } ?>
		</tr>
	</table>
	<?php
	if( !empty($table) ) {
		// TODO: Changelog per item, all data
		printf("<p><b>All changes to the `<i>%s</i>` table on record - copy/paste to your SQL query window to apply changes to your database.</b></p>",$table);
		printf("-- Changes to table: `%s`<br />",$table);
		$eq2->showChangeLog($table,$editor_id);
	}
	exit;
}

/* display editor(s) */
$link = sprintf("%s",$_SERVER['SCRIPT_NAME']);
?>
	<div id="sub-menu1">
		<table cellspacing="0">
			<tr>
				<td align="right" nowrap="nowrap"><strong>Choose Editor:&nbsp;</strong></td>
				<td>
					<select name="editor" onchange="dosub(this.options[this.selectedIndex].value)">
					<option value="<? $link ?>">Pick Table</option>
					<?php 
					foreach($table_array as $key=>$val)
					{
						$selected = $val == $_GET['p'] ? " selected" : "";
						$url = sprintf("%s?p=%s", $link, $val);
						printf('<option value="%s"%s>%s</option>', $url, $selected, $val);
					}
					?>
					</select> <a href="server.php?<?= $_SERVER['QUERY_STRING'] ?>">Reload Page</a>
				</td>
			</tr>
		</table>
	</div>
<?php


/* Functions */
function groundspawns() 
{
	global $eq2, $objectName;

	$table="groundspawns";
?>
	<table border="0" cellpadding="5" id="EditorTable">
		<tr>
			<td valign="top">
				<fieldset><legend>groundspawns</legend>
				<table width="100%" cellpadding="2" border="0">
						<?php
						$i = 0;
						$query=sprintf("select * from %s.groundspawns", DEV_DB);
						foreach ($eq2->RunQueryMulti($query) as $data) {
						?>
						<?php if ($i++ % 10 == 0) : ?>
						<tr>
							<th>id</th>
							<th>name</th>
							<th>min_skill</th>
							<th>min_adv_lvl</th>
							<th>bonus_table</th>
							<th>harvest1</th>
							<th>harvest3</th>
							<th>harvest5</th>
							<th>harvest_imbue</th>
							<th>harvest_rare</th>
							<th>harvest10</th>
							<th>harvest_coin</th>
							<th>enabled</th>
						</tr>
						<?php endif; ?>
					<form method="post" name="multiForm|<?php print($data['id']); ?>" />
					<tr>
						<td>
							<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
							<input type="text" name="groundspawns|groundspawn_id" value="<?php print($data['groundspawn_id']) ?>" style="width:45px;" readonly />
							<input type="hidden" name="orig_groundspawn_id" value="<?php print($data['groundspawn_id']) ?>" />
						</td>
						<td>
							<input type="text" name="groundspawns|tablename" value="<?php print($data['tablename']) ?>" style="width:145px;" />
							<input type="hidden" name="orig_tablename" value="<?php print($data['tablename']) ?>" />
						</td>
						<td>
							<input type="text" name="groundspawns|min_skill_level" value="<?php print($data['min_skill_level']) ?>" style="width:45px;" />
							<input type="hidden" name="orig_min_skill_level" value="<?php print($data['min_skill_level']) ?>" />
						</td>
						<td>
							<input type="text" name="groundspawns|min_adventure_level" value="<?php print($data['min_adventure_level']) ?>" style="width:45px;" />
							<input type="hidden" name="orig_min_adventure_level" value="<?php print($data['min_adventure_level']) ?>" />
						</td>
						<td>
							<input type="text" name="groundspawns|bonus_table" value="<?php print($data['bonus_table']) ?>" style="width:45px;" />
							<input type="hidden" name="orig_bonus_table" value="<?php print($data['bonus_table']) ?>" />
						</td>
						<td>
							<input type="text" name="groundspawns|harvest1" value="<?php print($data['harvest1']) ?>" style="width:45px;" />
							<input type="hidden" name="orig_harvest1" value="<?php print($data['harvest1']) ?>" />
						</td>
						<td>
							<input type="text" name="groundspawns|harvest3" value="<?php print($data['harvest3']) ?>" style="width:45px;" />
							<input type="hidden" name="orig_harvest3" value="<?php print($data['harvest3']) ?>" />
						</td>
						<td>
							<input type="text" name="groundspawns|harvest5" value="<?php print($data['harvest5']) ?>" style="width:45px;" />
							<input type="hidden" name="orig_harvest5" value="<?php print($data['harvest5']) ?>" />
						</td>
						<td>
							<input type="text" name="groundspawns|harvest_imbue" value="<?php print($data['harvest_imbue']) ?>" style="width:45px;" />
							<input type="hidden" name="orig_harvest_imbue" value="<?php print($data['harvest_imbue']) ?>" />
						</td>
						<td>
							<input type="text" name="groundspawns|harvest_rare" value="<?php print($data['harvest_rare']) ?>" style="width:45px;" />
							<input type="hidden" name="orig_harvest_rare" value="<?php print($data['harvest_rare']) ?>" />
						</td>
						<td>
							<input type="text" name="groundspawns|harvest10" value="<?php print($data['harvest10']) ?>" style="width:45px;" />
							<input type="hidden" name="orig_harvest10" value="<?php print($data['harvest10']) ?>" />
						</td>
						<td>
							<input type="text" name="groundspawns|harvest_coin" value="<?php print($data['harvest_coin']) ?>" style="width:45px;" />
							<input type="hidden" name="orig_harvest_coin" value="<?php print($data['harvest_coin']) ?>" />
						</td>
						<td>
							<input type="text" name="groundspawns|enabled" value="<?php print($data['enabled']) ?>" style="width:45px;" />
							<input type="hidden" name="orig_enabled" value="<?php print($data['enabled']) ?>" />
						</td>
					</tr>
					<?php if($eq2->CheckAccess(G_DEVELOPER)) : ?>
					<tr>
					<td>
						<a href="server.php?page=groundspawn_items&id=<?php echo $data['groundspawn_id'] ?>">Items</a>&nbsp;
					</td>
					<td colspan="3">
						<input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" />
						<input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" />
					</td>
					<td colspan="9"></td>
					</tr>
					<?php endif; ?>
					<input type="hidden" name="objectName" value="<?= $objectName ?>" />
					<input type="hidden" name="table_name" value="<?= $table ?>" />
					</form>
				<?php
				}
				?>
				<?php if($eq2->CheckAccess(G_DEVELOPER)) { ?>
        	<tr>
          	<td colspan="13"><strong>Insert New Record</strong></td>
    		</tr>
		 			<tr>
						<th>id</th>
						<th>name</th>
						<th>min_skill</th>
						<th>min_adv_lvl</th>
						<th>bonus_table</th>
						<th>harvest1</th>
						<th>harvest3</th>
						<th>harvest5</th>
						<th>harvest_imbue</th>
						<th>harvest_rare</th>
						<th>harvest10</th>
						<th>harvest_coin</th>
						<th>enabled</th>
					</tr>
					<form method="post" name="sdForm|new">
					<tr>
						<td align="center"><strong>new</strong></td>
						<td><input type="text" name="groundspawns|tablename|new" value="" style="width:145px;" /></td>
						<td><input type="text" name="groundspawns|min_skill_level|new" value="0" style="width:45px;" /></td>
						<td><input type="text" name="groundspawns|min_adventure_level|new" value="0" style="width:45px;" /></td>
						<td><input type="text" name="groundspawns|bonus_table|new" value="0" style="width:45px;" /></td>
						<td><input type="text" name="groundspawns|harvest1|new" value="70" style="width:45px;" /></td>
						<td><input type="text" name="groundspawns|harvest3|new" value="20" style="width:45px;" /></td>
						<td><input type="text" name="groundspawns|harvest5|new" value="8" style="width:45px;" /></td>
						<td><input type="text" name="groundspawns|harvest_imbue|new" value="1" style="width:45px;" /></td>
						<td><input type="text" name="groundspawns|harvest_rare|new" value="0.7" style="width:45px;" /></td>
						<td><input type="text" name="groundspawns|harvest10|new" value="0.3" style="width:45px;" /></td>
						<td><input type="text" name="groundspawns|harvest_coin|new" value="0" style="width:45px;" /></td>
						<td><input type="text" name="groundspawns|enabled|new" value="1" style="width:45px;" /></td>
						<input type="hidden" name="table_name" value="<?= $table ?>" />
					</tr>
					<tr>
					<td><input type="submit" name="cmd" value="Insert" style="font-size:10px; width:60px" /></td>
					</tr>		
					</form>
				<?php } ?>
				</table>
				</fieldset>
			</td>
			</form>
		</tr>
	</table>
<?php
}



function groundspawn_items() 
{
	global $eq2, $spawns, $eq2Items;

	$gsid_array = $spawns->GetGroundSpawnIDs();
	$table="groundspawn_items";
?>
	<table border="0" cellpadding="5" id="EditorTable">
		<tr>
			<td valign="top">
				<fieldset><legend>groundspawn_items</legend>
				<table width="100%" cellpadding="2" border="0">
					<tr>
						<th>id</td>
						<th>groundspawn_id</th>
						<th>item_id</th>
						<th>is_rare</th>
						<th>grid_id</th>
						<th colspan="2">&nbsp;</th>
					</tr>

						<?php
						$query=sprintf("select * from %s.groundspawn_items WHERE groundspawn_id = %s", DEV_DB, $_GET['id'] ?? 0);
						foreach ($eq2->RunQueryMulti($query) as $data) {
						?>
					<form method="post" name="multiForm|<?php print($data['id']); ?>" />
					<tr>
						<td>
							<input type="text" name="groundspawn_items|id" value="<?php print($data['id']) ?>" style="width:45px;  background-color:#ddd;" readonly />
							<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
						</td>
						<td>
              <select name="groundspawn_items|groundspawn_id" style="width:130px;">
                <?php 
                if( is_array($gsid_array) )
                {
                  foreach($gsid_array as $key=>$val)
                  {
                    $selected = ($key == $data['groundspawn_id']) ? " selected" : "";
                    printf('<option value="%s"%s>%s - %s</option>', $key, $selected, $key, $val);
                  }
                }
                ?>
              </select>
							<input type="hidden" name="orig_groundspawn_id" value="<?php print($data['groundspawn_id']) ?>" />
						</td>
						<td>
							<input type="text" name="groundspawn_items|item_id" value="<?php print($data['item_id']) ?>" style="width:105px;" />
							<input type="hidden" name="orig_item_id" value="<?php print($data['item_id']) ?>" />
              &nbsp;<a href="items.php?show=items&id=<?php print($data['item_id']) ?>&type=<?php echo $eq2Items->GetItemType($data['item_id']) ?>" target="_blank"><u><?= $eq2->getItemName($data['item_id']) ?></u></a>&nbsp;
						</td>
						<td>
							<input type="text" name="groundspawn_items|is_rare" value="<?php print($data['is_rare']) ?>" style="width:45px;" />
							<input type="hidden" name="orig_is_rare" value="<?php print($data['is_rare']) ?>" />
						</td>
						<td>
							<input type="text" name="groundspawn_items|grid_id" value="<?php print($data['grid_id']) ?>" style="width:105px;" />
							<input type="hidden" name="orig_grid_id" value="<?php print($data['grid_id']) ?>" />
						</td>
						<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" /><?php } ?></td>
						<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" /><?php } ?></td>
					</tr>
					<input type="hidden" name="table_name" value="<?= $table ?>" />
					</form>
				<?php
				}
				
				if($eq2->CheckAccess(G_DEVELOPER)) { ?>
        	<tr>
          	<td colspan="7"><strong>Insert New Record</strong></td>
          </tr>
					<form method="post" name="sdForm|new" />
					<tr>
						<td align="center"><strong>new</strong></td>
						<td>
              <select name="groundspawn_items|groundspawn_id" style="width:100px;">
                <?php 
                if( is_array($gsid_array) )
                {
                  foreach($gsid_array as $key=>$val) {
					$selected = ($key == $_GET['id']) ? " selected" : "";
                    printf('<option value="%s"%s>%s - %s</option>', $key, $selected, $key, $val);
				  }
                }
                ?>
              </select>
						</td>
						<td>
							<input type="text" name="groundspawn_items|item_id|new" value="0" style="width:105px;" />
              &nbsp;Must be a valid Item ID&nbsp;
						</td>
						<td>
							<input type="text" name="groundspawn_items|is_rare|new" value="0" style="width:45px;" />
						</td>
						<td>
							<input type="text" name="groundspawn_items|grid_id|new" value="0" style="width:105px;" />
						</td>
						<td>
							<input type="submit" name="cmd" value="Insert" style="font-size:10px; width:60px" />
						</td>
					</tr>
					<input type="hidden" name="table_name" value="<?= $table ?>" />
					</form>
				<?php } ?>
				</table>
				</fieldset>
			</td>
		</tr>
	</table>
<?php
}


function guild_event_defaults() {
	global $vgo,$objectName,$link;

	$table="guild_event_defaults";
?>
	<table border="0" cellpadding="5">
		<tr>
			<td valign="top">
				<fieldset><legend>General</legend>
				<table width="100%" cellpadding="0" border="0">
					<tr>
						<td colspan="3">
							<span class="heading">Editing: <?= $objectName ?></span><br />&nbsp;
						</td>
					</tr>
					<tr>
						<td width="55">id</td>
						<td width="55">event_id</td>
						<td width="355">event_name</td>
						<td width="55">retain</td>
						<td width="55">broadcast</td>
						<td colspan="2">&nbsp;</td>
					</tr>

						<?php
						$query=sprintf("select * from %s order by event_name",$table);
						$result=$eq2->db->sql_query($query);
						while($data=$eq2->db->sql_fetchrow($result)) {
						?>
					<form method="post" name="multiForm|<?php print($data['id']); ?>" />
					<tr>
						<td>
							<input type="text" name="guild_event_defaults|id" value="<?php print($data['id']) ?>" style="width:45px;  background-color:#ddd;" readonly />
							<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
						</td>
						<td>
							<input type="text" name="guild_event_defaults|event_id" value="<?php print($data['event_id']) ?>" style="width:45px;" />
							<input type="hidden" name="orig_event_id" value="<?php print($data['event_id']) ?>" />
						</td>
						<td>
							<input type="text" name="guild_event_defaults|event_name" value="<?php print($data['event_name']) ?>" style="width:345px;" />
							<input type="hidden" name="orig_event_name" value="<?php print($data['event_name']) ?>" />
						</td>
						<td>
							<input type="text" name="guild_event_defaults|retain" value="<?php print($data['retain']) ?>" style="width:45px;" />
							<input type="hidden" name="orig_retain" value="<?php print($data['retain']) ?>" />
						</td>
						<td>
							<input type="text" name="guild_event_defaults|broadcast" value="<?php print($data['broadcast']) ?>" style="width:45px;" />
							<input type="hidden" name="orig_broadcast" value="<?php print($data['broadcast']) ?>" />
						</td>
						<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" /><?php } ?></td>
						<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" /><?php } ?></td>
					</tr>
					<input type="hidden" name="objectName" value="<?= $objectName ?>" />
					<input type="hidden" name="tableName" value="<?= $table ?>" />
					</form>
				<?php
				}
				?>
				<?php if($eq2->CheckAccess(G_DEVELOPER)) { ?>
					<form method="post" name="sdForm|new" />
					<tr>
						<td align="center"><strong>new</strong></td>
						<td>
							<input type="text" name="guild_event_defaults|event_id|new" value="<?= $eq2->GetNextPK('guild_event_defaults', 'event_id') ?>" style="width:45px;" />
						</td>
						<td>
							<input type="text" name="guild_event_defaults|event_name|new" value="" style="width:345px;" />
						</td>
						<td>
							<input type="text" name="guild_event_defaults|retain|new" value="1" style="width:45px;" />
						</td>
						<td>
							<input type="text" name="guild_event_defaults|broadcast|new" value="1" style="width:45px;" />
						</td>
						<td>
							<input type="submit" name="cmd" value="Insert" style="font-size:10px; width:60px" />
						</td>
					</tr>
					<input type="hidden" name="tableName" value="<?= $table ?>" />
					</form>
				<?php } ?>
				</table>
				</fieldset>
			</td>
		</tr>
	</table>
<?php
}


function guild_ranks_defaults() {
	global $vgo,$objectName,$link;

	$table="guild_ranks_defaults";
?>
	<table border="0" cellpadding="5">
		<tr>
			<td width="680" valign="top">
				<fieldset><legend>General</legend>
				<table width="100%" cellpadding="0" border="0">
					<tr>
						<td colspan="7">
							<span class="heading">Editing: guild_ranks_defaults</span><br />This table is the default Guild Ranks for all guilds on your server and their permissions.&nbsp;<br />
							&nbsp;	
						</td>
					</tr>
					<tr>
						<td width="55">id</td>
						<td width="55">rank_id</td>
						<td width="105">rank_name</td>
						<td width="255">permission1</td>
						<td width="255">permission2</td>
						<td colspan="2">&nbsp;</td>
					</tr>

						<?php
						$query=sprintf("select * from %s",$table);
						$result=$eq2->db->sql_query($query);
						while($data=$eq2->db->sql_fetchrow($result)) {
						?>
					<form method="post" name="multiForm|<?php print($data['id']); ?>" />
					<tr>
						<td>
							<input type="text" name="guild_ranks_defaults|id" value="<?php print($data['id']) ?>" style="width:45px;  background-color:#ddd;" readonly />
							<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
						</td>
						<td>
							<input type="text" name="guild_ranks_defaults|rank_id" value="<?php print($data['rank_id']) ?>" style="width:45px;" />
							<input type="hidden" name="orig_rank_id" value="<?php print($data['rank_id']) ?>" />
						</td>
						<td>
							<input type="text" name="guild_ranks_defaults|rank_name" value="<?php print($data['rank_name']) ?>" style="width:105px;" />
							<input type="hidden" name="orig_rank_name" value="<?php print($data['rank_name']) ?>" />
						</td>
						<td>
							<input type="text" name="guild_ranks_defaults|permission1" value="<?php print($data['permission1']) ?>" style="width:245px;" />
							<input type="hidden" name="orig_permission1" value="<?php print($data['permission1']) ?>" />
						</td>
						<td>
							<input type="text" name="guild_ranks_defaults|permission2" value="<?php print($data['permission2']) ?>" style="width:245px;" />
							<input type="hidden" name="orig_permission2" value="<?php print($data['permission2']) ?>" />
						</td>
						<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" /><?php } ?></td>
						<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" /><?php } ?></td>
					</tr>
					<input type="hidden" name="objectName" value="<?= $objectName ?>" />
					<input type="hidden" name="tableName" value="<?= $table ?>" />
					</form>
				<?php
				}
				?>
				</table>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td>List of Permissions:<br />
				<table>
					<tr><td>GUILD_PERMISSIONS_INVITE</td><td>0 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_RMEOVE_MEMBER</td><td>1 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_PROMOTE_MEMBER</td><td>2 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_DEMOTE_MEMBER</td><td>3 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_CHANGE_MOTD</td><td>6 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_CHANGE_PERMISSIONS</td><td>7 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_CHANGE_RANK_NAMES</td><td>8 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_SEE_OFFICER_NOTES</td><td>9 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_EDIT_OFFICER_NOTES</td><td>10 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_SEE_OFFICER_CHAT</td><td>11 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_SPEAK_IN_OFFICER_CHAT</td><td>12 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_SEE_GUILD_CHAT</td><td>13 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_SPEAK_IN_GUILD_CHAT</td><td>14 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_EDIT_PERSONAL_NOTES</td><td>15 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_EDIT_PERSONAL_NOTES_OTHERS</td><td>16 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_EDIT_EVENT_FILTERS</td><td>17 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_EDIT_EVENTS</td><td>18 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_PURCHASE_STATUS_ITEMS</td><td>19 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_DISPLAY_GUILD_NAME</td><td>20 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_SEND_EMAIL_TO_GUILD</td><td>21 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_BANK1_SEE_CONTENTS</td><td>22 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_BANK2_SEE_CONTENTS</td><td>23 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_BANK3_SEE_CONTENTS</td><td>24 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_BANK4_SEE_CONTENTS</td><td>25 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_BANK1_DEPOSIT</td><td>26 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_BANK2_DEPOSIT</td><td>27 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_BANK3_DEPOSIT</td><td>28 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_BANK4_DEPOSIT</td><td>29 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_BANK1_WITHDRAWL</td><td>30 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_BANK2_WITHDRAWL</td><td>31 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_BANK3_WITHDRAWL</td><td>32 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_BANK4_WITHDRAWL</td><td>33 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_EDIT_RECRUITING_SETTINGS</td><td>35 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_MAKE_OTHERS_RECRUITERS</td><td>36 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_SEE_RECRUITING_SETTINGS</td><td>37 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_ASSIGN_POINTS</td><td>43 bit</td></tr>
					<tr><td>GUILD_PERMISSIONS_RECEIVE_POINTS</td><td>44 bit</td></tr>
				</table>
			</td>
		</tr>
	</table>
<?php
}


function appearances()
{
	global $vgo,$objectName,$link;
?>
	<table border="0" cellpadding="5">
		<tr>
			<td colspan="3">
				<span class="heading">Editing: appearances</span><br />This view servers available appearances (read-only).
			</td>
		</tr>
	</table>
<?php
}


function collections() {
	global $eq2, $link;

	$cat = $_GET['cat'] ?? NULL;

	// build collection_category picker
	$sql = sprintf("SELECT DISTINCT collection_category FROM %s.collections ORDER BY collection_category", DEV_DB);
	$category_options = "";
	foreach ( $eq2->RunQueryMulti($sql) as $data) 
	{
		$tmpCat = $data['collection_category'];
		if ($tmpCat == "") {
			$tmpCat = "No Category";
		}
		$selected = ( $cat == $tmpCat ) ? " selected" : "";
		$category_options .= sprintf('<option value="%s?page=collections&cat=%s"%s>%s</option>', $link, $tmpCat, $selected, $tmpCat);
	}
	
	// build collection_name picker
	if( $cat )
	{
		$tmpCat = $cat;
		if ($cat == "No Category") $tmpCat = "";
		$sql = sprintf("SELECT id, collection_name, level FROM %s.collections WHERE collection_category = '%s' ORDER BY collection_name", DEV_DB, $eq2->SQLEscape($tmpCat));
		$name_options = "";
		$id = $_GET['id'] ?? NULL;
		foreach ( $eq2->RunQueryMulti($sql) as $data ) 
		{
			$selected = ( $id == $data['id'] ) ? " selected" : "";
			$name_options .= sprintf('<option value="%s?page=collections&cat=%s&id=%s"%s>%s (%s)</option>', $link, $cat, $data['id'], $selected, $data['collection_name'], $data['level']);
		}
	}
	
	//display collection edit options
	?>
	<table>
		<tr>
			<td valign="top">
				<select name="c" onchange="dosub(this.options[this.selectedIndex].value)">
				<option value="<?php printf("%s?page=collections", $link) ?>">Pick a Category</option>
				<option value="<?php printf("%s?page=collections&cat=%s&new", $link, $cat) ?>">Add New</option>
				<?= $category_options ?>
				</select>&nbsp;
			</td>
			<?php	if( $cat ) {	?>
      <td valign="top">
        <select name="n" onchange="dosub(this.options[this.selectedIndex].value)">
        <option value="<?php printf("%s?page=collections&cat=%s", $link, $cat) ?>">Pick a Name</option>
		<option value="<?php printf("%s?page=collections&cat=%s&new", $link, $cat) ?>">Add New</option>
        <?= $name_options ?>
        </select>&nbsp;<a href="server.php?<?= $_SERVER['QUERY_STRING'] ?>">Reload Page</a>
      </td>
      <?php } ?>
    </tr>
  </table>
	<?php

	$table		= "collections";

	if( isset($_GET['id']) )
	{
		$id 			= $_GET['id'];

		$query = sprintf("select * from %s.collections where id = %d", DEV_DB, $id);
		if( $data = $eq2->RunQuerySingle($query) ) 
		{
			$objectName = $data['collection_name'];
		?>
	<fieldset><legend>Collection</legend>
    <table border="0" cellpadding="5">
    	<form method="post" name="Form1">
      <tr>
        <td width="700" valign="top">
          <table width="100%" cellpadding="4" border="0">
            <tr>
              <td colspan="3"><span class="heading"><?= $objectName ?></span><br />&nbsp;</td>
            </tr>
            <tr>
              <td align="right">id:</td>
              <td>
                <input type="text" name="collections|id" value="<?php print($data['id']) ?>" style="width:45px;  background-color:#ddd;" readonly />
                <input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
              </td>
            </tr>
            <tr>
              <td align="right">collection_name:</td>
              <td>
                <input type="text" name="collections|collection_name" value="<?php print($data['collection_name']) ?>" style="width:250px;" />
                <input type="hidden" name="orig_collection_name" value="<?php print($data['collection_name']) ?>" />
              </td>
            </tr>
            <tr>
              <td align="right">collection_category:</td>
              <td>
                <input type="text" name="collections|collection_category" value="<?php print($data['collection_category']) ?>" style="width:200px;" />
                <input type="hidden" name="orig_collection_category" value="<?php print($data['collection_category']) ?>" />
              </td>
            </tr>
            <tr>
              <td align="right">level:</td>
              <td>
                <input type="text" name="collections|level" value="<?php print($data['level']) ?>" style="width:45px;" />
                <input type="hidden" name="orig_level" value="<?php print($data['level']) ?>" />
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <?php if($eq2->CheckAccess(G_DEVELOPER)) { ?>
      <tr>
        <td colspan="4" align="center">
          <input type="submit" name="cmd" value="Update" style="width:100px;" />&nbsp;
          <input type="submit" name="cmd" value="Delete" style="width:100px;" />&nbsp;
          <input type="hidden" name="table_name" value="<?= $table ?>" />
        </td>
      </tr>
      <?php } ?>
      </form>
    </table>
	</fieldset>
    <?php
		} 
		
		?>
		<fieldset><legend>Collection Details</legend>
		<table cellpadding="5" class="EditorTable">
			<?php $query = sprintf("SELECT * FROM %s.collection_details WHERE `collection_id` = %s ORDER BY `item_index`;", DEV_DB, $id);
			$res = $eq2->RunQueryMulti($query);

			//Let's manipulate our results so we can create a new insert row easily
			$newidx = count($res);
			$res[$newidx]['id'] = "new";
			$res[$newidx]['collection_id'] = $id;
			$res[$newidx]['item_id'] = "";
			$res[$newidx]['item_index'] = $newidx;

			foreach ($res as $idx=>$data) : ?>
			<tr>
				<?php if ($idx == $newidx || $idx == 0) : ?>
					<tr>
						<th>id</th>
						<th>collection_id</th>
						<th>item_id</th>
						<th>item_name</th>
						<th>index</th>
						<th colspan="2"></th>
					</tr>
				<?php endif; ?>
				<form method="post" name="CollectionDetailsForm|<?php echo $idx ?>">
				<td>
					<strong><?php echo $data['id'] ?></strong>
					<?php $eq2->GenerateRowOrigValues($data); ?>
				</td>
				<td align="center">
					<input type="text" name="collection_details|collection_id" value="<?php echo $id ?>" readonly style="width:45px" />
				</td>
				<td>
					<input type="text" name="collection_details|item_id" value="<?php echo $data['item_id'] ?>" style="width:45px" />
				</td>
				<td>
					<?php printf('<a href="items.php?id=%s">%s</a>', $data['item_id'], $eq2->getItemName($data['item_id'])); ?>
				</td>
				<td>
					<input type="text" name="collection_details|item_index" value="<?php echo $data['item_index'] ?>" style="width:45px" />
				</td>
				<?php if ($eq2->CheckAccess(G_DEVELOPER) ) : ?>
					<input type="hidden" name="table_name" value="collection_details" />
					<?php if ($idx == $newidx) : ?>
						<td>
							<input type="submit" name="cmd" value="Insert" />
						</td>		
					<?php else : ?>
						<td>
							<input type="submit" name="cmd" value="Update" />
						</td>
						<td>
							<input type="submit" name="cmd" value="Delete" />
						</td>
					<?php endif; ?>
				<?php endif; ?>
				</form>
			</tr>
			<?php endforeach; ?>
		</table>
		</fieldset>
		<br>
		<fieldset><legend>Collection Rewards</legend>
		<table cellpadding="5" class="EditorTable">
			<?php $query = sprintf("SELECT * FROM %s.collection_rewards WHERE `collection_id` = %s;", DEV_DB, $id);
			$res = $eq2->RunQueryMulti($query);

			//Let's manipulate our results so we can create a new insert row easily
			$newidx = count($res);
			$res[$newidx]['id'] = "new";
			$res[$newidx]['collection_id'] = $id;
			$res[$newidx]['reward_type'] = "None";
			$res[$newidx]['reward_value'] = 0;
			$res[$newidx]['reward_quantity'] = 1;

			foreach ($res as $idx=>$data) : ?>
			<tr>
				<?php if ($idx == $newidx || $idx == 0) : ?>
					<tr>
						<th>id</th>
						<th>collection_id</th>
						<th>reward_type</th>
						<th>reward_value</th>
						<th>item_name</th>
						<th>reward_quantity</th>
						<th colspan="2"></th>
					</tr>
				<?php endif; ?>
				<form method="post" name="CollectionRewardsForm|<?php echo $idx ?>">
				<td>
					<strong><?php echo $data['id'] ?></strong>
					<?php $eq2->GenerateRowOrigValues($data); ?>
				</td>
				<td align="center">
					<input type="text" name="collection_rewards|collection_id" value="<?php echo $id ?>" readonly style="width:45px" />
				</td>
				<td>
					<?php $reward_type = $data['reward_type']; ?>
					<select name="collection_rewards|reward_type">
						<option <?php echo $reward_type == "None" ? "selected" : "" ?> >None</option>
						<option <?php echo $reward_type == "Item" ? "selected" : "" ?> >Item</option>
						<option <?php echo $reward_type == "Selectable" ? "selected" : "" ?> >Selectable</option>
						<option <?php echo $reward_type == "Coin" ? "selected" : "" ?> >Coin</option>
						<option <?php echo $reward_type == "XP" ? "selected" : "" ?> >XP</option>
					</select>
				</td>
				<td align="center">
					<input type="text" name="collection_rewards|reward_value" value="<?php echo $data['reward_value'] ?>" style="width:45px" />
				</td>
				<td>
					<?php 
					if ($reward_type == "Selectable" || $reward_type == "Item") 
						printf('<a href="items.php?id=%s">%s</a>', $data['reward_value'], $eq2->getItemName($data['reward_value'])); 
					?>
				</td>
				<td>
					<input type="text" name="collection_rewards|reward_quantity" value="<?php echo $data['reward_quantity'] ?>" style="width:45px" />
				</td>
				<?php if ($eq2->CheckAccess(G_DEVELOPER) ) : ?>
					<input type="hidden" name="table_name" value="collection_rewards" />
					<?php if ($idx == $newidx) : ?>
						<td>
							<input type="submit" name="cmd" value="Insert" />
						</td>		
					<?php else : ?>
						<td>
							<input type="submit" name="cmd" value="Update" />
						</td>
						<td>
							<input type="submit" name="cmd" value="Delete" />
						</td>
					<?php endif; ?>
				<?php endif; ?>
				</form>
			</tr>
			<?php endforeach; ?>
		</table>
		</fieldset>
		<?php
	}
	else if (isset($_GET['new']))
	{
		if( $eq2->CheckAccess(G_DEVELOPER) ) 
		{
		?>
	  
	    <fieldset><legend>Collection</legend>
        <table border="0" cellpadding="5">
        <form method="post" name="CollectionForm|new" >
        <tr>
          <td width="680" valign="top">
            <table width="100%" cellpadding="0" border="1">
              <tr>
                <td colspan="4"><span class="heading">NEW</span><br />&nbsp;</td>
              </tr>
              <tr>
                <td align="right">id:</td>
				<td><strong>new</strong></td>
              </tr>
              <tr>
                <td align="right">collection_name:</td>
                <td><input type="text" name="collections|collection_name|new" value="" style="width:250px;" /></td>
              </tr>
              <tr>
                <td align="right">collection_category:</td>
                <td><input type="text" name="collections|collection_category|new" value="<?php if ($cat == "No Category") $cat = ""; echo $cat; ?>" style="width:200px;" /></td>
              </tr>
              <tr>
                <td align="right">level:</td>
                <td><input type="text" name="collections|level|new" value="0" style="width:45px;" /></td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td colspan="4" align="center">
            <input type="submit" name="cmd" value="Insert" style="width:100px;" />&nbsp;
            <input type="hidden" name="table_name" value="<?= $table ?>" />
          </td>
        </tr>
		</fieldset>
        </table>
		<?php
		}
	}
}


function entity_commands() {
	global $eq2, $s, $spawns;

	$table="entity_commands";
?>
	<table border="0" cellpadding="5" id="EditorTable">
		<tr>
			<td>
				<?php $ecID = $_GET['ec'] ?? 0 ?>
				<strong>Lookup:</strong>
				<input type="text" id="ECmdText" value="<?php echo $ecID ? $spawns->GetEntityCmdString($ecID) : "" ?>" onkeyup="EntityCmdLookupAJAX('ECmdText','ECmdSuggest','ECmdText','ECmdID',false)" />
				<div id="ECmdSuggest"></div>
				<input type="hidden" id="ECmdID" onchange="dosub('server.php?page=entity_commands&ec='+this.value)" />
			</td>
		</tr>
		<tr>
			<td valign="top">
				<fieldset><legend>Entity Commands</legend>
				<table>
				<?php if($eq2->CheckAccess(G_DEVELOPER)) : ?>
					<tr>
						<th width="55">id</th>
						<th width="55">cmd_list_id</th>
						<th width="205">command_text</th>
						<th width="55">distance</th>
						<th width="205">command</th>
						<th width="55">error_text</th>
						<th width="55">cast_time</th>
						<th width="55">spell_visual</th>
						<th colspan="2"></th>
					</tr>
					<form method="post" name="sdForm|new" >
					<tr>
						<td align="center"><strong>new</strong></td>
						<td>
							<input type="text" name="entity_commands|command_list_id|new" value="<?php echo $ecID ? $ecID : $s->GetNextEntityCommandListID();?>"  style="width:50px;" />
						</td>
						<td>
							<input type="text" name="entity_commands|command_text|new" value=""  style="width:200px;" />
						</td>
						<td>
							<input type="text" name="entity_commands|distance|new" value="10.0"  style="width:50px;" />
						</td>
						<td>
							<input type="text" name="entity_commands|command|new" value=""  style="width:200px;" />
						</td>
						<td>
							<input type="text" name="entity_commands|error_text|new" value=""  style="width:50px;" />
						</td>
						<td>
							<input type="text" name="entity_commands|cast_time|new" value="0"  style="width:50px;" />
						</td>
						<td>
							<input type="text" name="entity_commands|spell_visual|new" value="0"  style="width:50px;" />
						</td>
						<td>
							<input type="submit" name="cmd" value="Insert" style="font-size:10px; width:60px" />
							<input type="hidden" name="table_name" value="<?= $table ?>" />
						</td>
					</tr>
					</form>
				<?php endif; ?>
				<?php
				$query=sprintf("select * from %s.entity_commands where command_list_id = %s order by command_list_id, command_text", DEV_DB, $ecID);
				$i = 0;
				foreach ($eq2->RunQueryMulti($query) as $data) :
				?>
					<form method="post" name="multiForm|<?php print($data['id']); ?>">
					<tr>
						<td>
							<input type="text" name="entity_commands|id" value="<?php print($data['id']) ?>"  style="width:50px;  background-color:#ddd;" readonly />
							<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
						</td>
						<td>
							<input type="text" name="entity_commands|command_list_id" value="<?php print($data['command_list_id']) ?>"  style="width:50px;" />
							<input type="hidden" name="orig_command_list_id" value="<?php print($data['command_list_id']) ?>" />
						</td>
						<td>
							<input type="text" name="entity_commands|command_text" value="<?php print($data['command_text']) ?>"  style="width:200px;" />
							<input type="hidden" name="orig_command_text" value="<?php print($data['command_text']) ?>" />
						</td>
						<td>
							<input type="text" name="entity_commands|distance" value="<?php print($data['distance']) ?>"  style="width:50px;" />
							<input type="hidden" name="orig_distance" value="<?php print($data['distance']) ?>" />
						</td>
						<td>
							<input type="text" name="entity_commands|command" value="<?php print($data['command']) ?>"  style="width:200px;" />
							<input type="hidden" name="orig_command" value="<?php print($data['command']) ?>" />
						</td>
						<td>
							<input type="text" name="entity_commands|error_text" value="<?php print($data['error_text']) ?>"  style="width:50px;" />
							<input type="hidden" name="orig_error_text" value="<?php print($data['error_text']) ?>" />
						</td>
						<td>
							<input type="text" name="entity_commands|cast_time" value="<?php print($data['cast_time']) ?>"  style="width:50px;" />
							<input type="hidden" name="orig_cast_time" value="<?php print($data['cast_time']) ?>" />
						</td>
						<td>
							<input type="text" name="entity_commands|spell_visual" value="<?php print($data['spell_visual']) ?>"  style="width:50px;" />
							<input type="hidden" name="orig_spell_visual" value="<?php print($data['spell_visual']) ?>" />
						</td>
						<?php if($eq2->CheckAccess(G_DEVELOPER)) : ?>
						<td><input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" /></td>
						<td><input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" /></td>
						<?php else : ?>
						<td colspan="2"></td>
						<?php endif; ?>
					</tr>
					<input type="hidden" name="table_name" value="<?= $table ?>" />
					</form>
				<?php endforeach; ?>
				</table>
				</fieldset>
			</td>
		</tr>
	</table>
<?php
}


function conditionals() {
	global $vgo,$objectName,$link;

?>
	<table border="0" cellpadding="5">
		<tr>
			<td colspan="3">
				<span class="heading">Editing: Conditionals</span><br />This editor turns on/off things like Halloween, Frostfell, Bristlebane Day, etc. (Coming Soon!)
			</td>
		</tr>
	</table>
<?php
}


function emotes() {
	global $vgo,$objectName,$link;

?>
	<table border="0" cellpadding="5">
		<tr>
			<td colspan="8">
				<span class="heading">Editing: Emotes</span><br />This editor modifies server emotes.
			</td>
		</tr>
		<tr>
			<td>id</td>
			<td>name</td>
			<td>visual_state_id</td>
			<td>message</td>
			<td>targeted_message</td>
			<td>self_message</td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<?php 
		$where = ( isset($_GET['id']) ) ? sprintf(" WHERE id = %d", $_GET['id']) : " ORDER BY message";
		$query = sprintf("SELECT id, name, message FROM emotes%s", $where);
		if( !$result = $eq2->db->sql_query($query) )
			die("SQL Error in " . $query);
		while( $row = $eq2->db->sql_fetchrow($result) )
		{
		}
		?>
	</table>
<?php
}


function spawn_npc_equipment() {
	global $vgo,$objectName,$link;

	$table="spawn_npc_equipment";
?>
	<table border="0" cellpadding="5">
		<tr>
			<td width="880" valign="top">
				<fieldset><legend>General</legend>
				<table width="100%" cellpadding="0" border="0">
					<tr>
						<td width="50">id</td>
						<td width="105">equipment_list_id</td>
						<td width="555">item_id</td>

						<td colspan="2">&nbsp;</td>
					</tr>

						<?php
						$query=sprintf("select * from %s order by equipment_list_id, item_id",$table);
						$result=$eq2->db->sql_query($query);
						while($data=$eq2->db->sql_fetchrow($result)) 
						{
							$equipment_list_id = intval($data['equipment_list_id']);
							$next_equipment_list_id = ( $equipment_list_id > 0 ) ? $equipment_list_id++ : 1;
						?>
					<form method="post" name="multiForm|<?php print($data['id']); ?>" />
					<tr>
						<td>
							<input type="text" name="spawn_npc_equipment|id" value="<?php print($data['id']) ?>" style="width:45px;  background-color:#ddd;" readonly />
							<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
						</td>

						<td>
							<input type="text" name="spawn_npc_equipment|equipment_list_id" value="<?php print($data['equipment_list_id']) ?>" style="width:105px;" />
							<input type="hidden" name="orig_equipment_list_id" value="<?php print($data['equipment_list_id']) ?>" />
						</td>
						<td>
							<input type="text" name="spawn_npc_equipment|item_id" value="<?php print($data['item_id']) ?>" style="width:105px; cursor:pointer;" title="<?= $eq2->getItemName($data['item_id']) ?>" />&nbsp;<em><?= $eq2->getItemName($data['item_id']) ?></em>
							<input type="hidden" name="orig_item_id" value="<?php print($data['item_id']) ?>" />
						</td>
						<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" /><?php } ?>&nbsp;</td>
						<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" /><?php } ?>&nbsp;</td>
					</tr>
					<input type="hidden" name="objectName" value="<?= $objectName ?>" />
					<input type="hidden" name="tableName" value="<?= $table ?>" />
					</form>
				<?php
				}
				?>
				<?php if($eq2->CheckAccess(G_DEVELOPER)) { ?>
					<form method="post" name="sdForm|new" />
					<tr>

						<td align="center"><strong>new</strong></td>
						<td>
							<input type="text" name="spawn_npc_equipment|equipment_list_id|new" value="<?= $next_equipment_list_id ?>" style="width:105px;" />
						</td>
						<td>
							<input type="text" name="spawn_npc_equipment|item_id|new" value="1" style="width:105px;" />
						</td>
						<td>

							<input type="submit" name="cmd" value="Insert" style="font-size:10px; width:60px" />
						</td>
					</tr>
					<input type="hidden" name="tableName" value="<?= $table ?>" />
					</form>
					<tr>
						<td colspan="4"><br />
							<strong>Note:</strong> Items are too many to list, so manually type the item ID when creating a new entry
						</td>
					</tr>
				<?php } ?>
				</table>
				</fieldset>
			</td>
		</tr>
	</table>
<?php
}


function spawn_npc_skills() {
	global $vgo,$objectName,$link;

	$table="spawn_npc_skills";
?>
	<table border="0" cellpadding="5">
		<tr>
			<td><strong>Note:</strong> Currently this editor is only setup to build the 40 generic class skill sets. Eventually you will be able to add new skill_list_id's to assign to spawn_npcs, but for now, just focus on generic skills.</td>
		</tr>
		<tr>
			<td>
				<select name="class_id" onchange="dosub(this.options[this.selectedIndex].value)" style="width:300px;">
					<option value="">Pick a Class</option>
					<?php 
					foreach($eq2->eq2Classes as $key=>$val) 
					{
						if( $val != "ALL" )
						{
							if( isset($_GET['c']) && $key == $_GET['c'] )
							{
								$selected = " selected";
								$class_name = $val;
							}
							else
							{
								$selected = "";
								$class_name = "";
							}
							
							printf("<option value=\"server.php?p=spawn_npc_skills&c=%s\"%s>%s (%s)</option>\n",
								$key, $selected, $val, $key);
						}
					}
					?>
				</select>
				<input type="hidden" name="orig_class_" value="<?php print($data['class_']); ?>" />
			</td>
		</tr>
	</table>
	<?php
	if( isset($_GET['c']) )
	{
	?>
	<table border="0" cellpadding="5">
		<tr>
			<td width="680" valign="top">
				<fieldset><legend>General</legend>
				<table width="100%" cellpadding="0" border="0">
					<tr>
						<td width="55">id</td>
						<td width="75">skill_list_id</td>
						<td width="305">skill_id</td>
						<td width="105">starting_value</td>
						<td colspan="2">&nbsp;</td>
					</tr>

						<?php
						$query=sprintf("select * from %s where skill_list_id = %d order by skill_list_id, skill_id", $table, $_GET['c']);
						$result=$eq2->db->sql_query($query);
						while($data=$eq2->db->sql_fetchrow($result)) 
						{
							$skill_list_id = intval($data['skill_list_id']);
							//$next_skill_list_id = ( $skill_list_id > 0 ) ? $skill_list_id++ : 1;
						?>
					<form method="post" name="multiForm|<?php print($data['id']); ?>" />
					<tr>
						<td>
							<input type="text" name="spawn_npc_skills|id" value="<?php print($data['id']) ?>" style="width:45px;  background-color:#ddd;" readonly />
							<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
						</td>
						<td>
							<input type="text" name="spawn_npc_skills|skill_list_id" value="<?php print($data['skill_list_id']) ?>" style="width:65px; background-color:#ddd;" readonly />
							<input type="hidden" name="orig_skill_list_id" value="<?php print($data['skill_list_id']) ?>" />
						</td>
						<td>
							<select name="spawn_npc_skills|skill_id" style="width:250px;">
								<?= $eq2->getClassSkills($data['skill_id']) ?>
							</select>
							<input type="hidden" name="orig_skill_id" value="<?php print($data['skill_id']) ?>" />

						</td>
						<td>
							<input type="text" name="spawn_npc_skills|starting_value" value="<?php print($data['starting_value']) ?>" style="width:75px;" />
							<input type="hidden" name="orig_starting_value" value="<?php print($data['starting_value']) ?>" />
						</td>
						<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" /><?php } ?></td>
						<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" /><?php } ?></td>
					</tr>

					<input type="hidden" name="objectName" value="<?= $objectName ?>" />
					<input type="hidden" name="tableName" value="<?= $table ?>" />
					</form>
				<?php
				}
				?>
				<?php if($eq2->CheckAccess(G_DEVELOPER)) { ?>
					<form method="post" name="sdForm|new" />
					<tr>
						<td align="center"><strong>new</strong></td>
						<td>

							<input type="text" name="spawn_npc_skills|skill_list_id|new" value="<?= $skill_list_id ?>" style="width:65px; background-color:#ddd;" readonly />
						</td>
						<td>
							<select name="spawn_npc_skills|skill_id|new" style="width:250px;">
								<?= $eq2->getClassSkills(0) ?>
							</select>
						</td>
						<td>
							<input type="text" name="spawn_npc_skills|starting_value|new" value="25" style="width:75px;" />
						</td>
						<td>

							<input type="submit" name="cmd" value="Insert" style="font-size:10px; width:60px" />
						</td>
					</tr>
					<input type="hidden" name="tableName" value="<?= $table ?>" />
					</form>
				<?php } ?>
				</table>
				</fieldset>
			</td>
		</tr>
	</table>
	<?php
	}
}


function npc_spells() {
	global $eq2;

	$list = $_GET['list'] ?? NULL;
	$listName = NULL;
	$cat = $_GET['cat'] ?? NULL;
?>
<fieldset>
	<legend>Spell Lists</legend>
	<table border="0" cellpadding="5">
		<tr>
			<td>
				<select name="spell_category" onchange="dosub(this.options[this.selectedIndex].value)" style="width:300px;">
					<option value="">Pick a category</option>
					<option <?php if ($list == "new") echo "selected "?>value="server.php?page=npc_spells&list=new">Add new</option>
					<option value="server.php?page=npc_spells&cat"<?php if ($cat === "") echo " selected" ?>>No Category</option>
					<?php $data = $eq2->RunQueryMulti("SELECT DISTINCT `category` FROM ".DEV_DB.".spawn_npc_spell_lists WHERE LENGTH(`category`) ORDER BY `category`"); ?>
					<?php foreach($data as $row) : ?>
						<?php $bSelect = $cat == $row['category']; ?>
						<option <?php if ($bSelect) echo "selected "?>value="server.php?page=npc_spells&cat=<?php echo $row['category']?>"><?php printf("%s", $row['category']) ?></option>
					<?php endforeach; ?>
				</select>
			</td>
			<?php if ($cat !== NULL) : ?>
			<td>
				<select name="spell_list" onchange="dosub(this.options[this.selectedIndex].value)" style="width:300px;">
					<option value="">Pick a spell list</option>
					<option <?php if ($list == "new") echo "selected "?>value="server.php?page=npc_spells&cat=<?php echo $cat ?>&list=new">Add new</option>
					<?php $data = $eq2->RunQueryMulti("SELECT `id`, `description` FROM ".DEV_DB.".spawn_npc_spell_lists WHERE category='".$eq2->SQLEscape($cat)."' ORDER BY `description`"); ?>
					<?php foreach($data as $row) : ?>
						<?php 
						$bSelect = $list == $row['id'];
						if ($bSelect) $listName = $row['description'];
						?>
						<option <?php if ($bSelect) echo "selected "?>value="server.php?page=npc_spells&cat=<?php echo $cat ?>&list=<?php echo $row['id']?>"><?php printf("%s (%s)", $row['description'], $row['id']) ?></option>
					<?php endforeach; ?>
				</select>
			</td>
			<?php endif; ?>
		</tr>
	</table>
	<?php

	if ($list == NULL) return;

	if ($list == "new") {
		?>
		<form method="post" name="SpellListForm">
		</br>
		<fieldset>
		<legend>New Spell List</legend>
		<table>
			<tr>
				<td align="center">
				<strong style="font-size:large">Insert a new spell list</strong>
				</td>
			</tr>
			<tr>
				<td align="right">
				<span>category:</span>
				<input type="text" width="300px" name="new_spell_list_cat" value="<?php echo $cat ?>"></input>
				</td>
			</tr>
			<tr>
				<td>
				<span>description:</span>
				<input type="text" width="300px" name="new_spell_list_desc"></input>
				</td>
			</tr>
			<tr>
				<td align="center">
				<input type="submit" value="Insert" name="cmd"></input>
				</td>
			</tr>
		</table>
		</fieldset>
		</form>
		<?php
		return;
	}

	//Invalid or deleted list id set
	if ($listName == NULL) return;

	?>
	</br>
	<fieldset>
	<legend>List Entry</legend>
	<form method="post" name="SpellListDescForm">
		<table>
		<tr>
		<td>
		<strong>Category:</strong>
		<input type="text" name="spawn_npc_spell_lists|category" value="<?php echo $cat ?>"/>
		<input type="hidden" name="orig_category" value="<?php echo $cat ?>"/>
		<input type="hidden" name="orig_id" value="<?php echo $list ?>"/>
		<input type="hidden" name="table_name" value="spawn_npc_spell_lists"/>
		&nbsp;
		</td>
		<td>
		<strong>Description:</strong>
		<input type="text" name="spawn_npc_spell_lists|description" value="<?php echo $listName ?>"/>
		<input type="hidden" name="orig_description" value="<?php echo $listName ?>"/>
		&nbsp;
		</td>
		<td>
		<input type="submit" name="cmd" value="Update" style="font-size:10px;width:60px;"/>
		</td>
		<td>
		<input type="submit" id="DeleteButton" name="cmd" value="Delete" style="font-size:10px;width:60px;"/>
		</td>
		<td>
		<?php $eq2->GenerateBlueCheckbox("EnableDelete", false, "EnableDelete") ?>
		</td>
		</tr>
		</table>
		<script>ElementToggleCheckbox("EnableDelete", "DeleteButton");</script>
	</form>
	</fieldset>
	</br>
	<fieldset>
		<legend>Spells</legend>
		<table class="ContrastTable" cellpadding="5" id="EditorTable">
			<tr align="center">
			<th>icon</th>
			<th>name</th>
			<th>spell_id</th>
			<th>tier</th>
			<th>cast</th>
			<th colspan="2"></th>
			</tr>
			<?php
			$query = sprintf("SELECT s.`name`,s.`icon`,s.`icon_backdrop`,l.`id`,l.`spell_id`,l.`spell_tier`,l.`on_spawn_cast`,l.`on_aggro_cast` FROM %s.spawn_npc_spells l INNER JOIN %s.spells s ON l.spell_id = s.id WHERE `spell_list_id` = %s", DEV_DB, DEV_DB, $list);
			$res = $eq2->RunQueryMulti($query);

			//Trying a new way for a new inserted row template without a bunch of extra html code
			if (!is_array($res)) $res = array();
			$newRow = array();
			$newRow['ISNEWROW'] = true;
			$newRow['spell_id'] = "";
			$newRow['spell_tier'] = 1;
			$newRow['category'] = $cat;
			$newRow['on_aggro_cast'] = 0;
			$newRow['on_spawn_cast'] = 0;
			array_unshift($res, $newRow);

			foreach ($res as $data) :
				$bNew = $data['ISNEWROW'] ?? false;
			?>
			<tr align="center">
			<form method="post" name="multiForm|<?php echo ($data['id'] ?? "new") ?>">
			<?php if ($bNew) : ?>
				<td colspan="2" align="center"><strong>New Entry</strong></td>
			<?php else : ?>
				<td>
					<input type="hidden" name="orig_id" value="<?php echo $data['id'] ?>"/>
					<img src="eq2Icon.php?type=spell&<?php printf("id=%s&backdrop=%s", $data['icon'], $data['icon_backdrop']) ?>">
				</td>
				<td>
					<?php printf("<a href=\"spells.php?id=%s\">%s</a>", $data['spell_id'], $data['name'])?>
				</td>
			<?php endif; ?>
			<td>
				<input type="hidden" name="table_name" value="spawn_npc_spells"/>
				<input type="hidden" name="spawn_npc_spells|spell_list_id" value="<?php echo $list ?>"/>
				<input type="hidden" name="orig_spell_list_id" value="<?php echo $list ?>"/>

				<input type="text" name="spawn_npc_spells|spell_id" value="<?php echo $data['spell_id'] ?>" />
				<input type="hidden" name="orig_spell_id" value="<?php echo $data['spell_id'] ?>" />
			</td>
			<td>
				<input type="text" name="spawn_npc_spells|spell_tier" value="<?php echo $data['spell_tier'] ?>" />
				<input type="hidden" name="orig_spell_tier" value="<?php echo $data['spell_tier'] ?>" />
			</td>
			<td>
				<table>
					<tr>
						<td>
							<table><tr><td>on_spawn:</td><td><?php $eq2->GenerateBlueCheckbox("spawn_npc_spells|on_spawn_cast", $data['on_spawn_cast']) ?></td></tr></table>
						</td>
						<td>
							<table><tr><td>on_aggro:</td><td><?php $eq2->GenerateBlueCheckbox("spawn_npc_spells|on_aggro_cast", $data['on_aggro_cast']) ?></td></tr></table>
						</td>
					</tr>
				</table>
				<input type="hidden" name="orig_on_spawn_cast" value="<?php echo $data['on_spawn_cast'] ?>" />
				<input type="hidden" name="orig_on_aggro_cast" value="<?php echo $data['on_aggro_cast'] ?>" />
			</td>
			<?php if ($bNew) : ?>
				<td colspan="2">
					<input type="submit" name="cmd" value="Insert" style="font-size:10px;width:60px;"/>
				</td>
			<?php else : ?>
				<td>
					<input type="submit" name="cmd" value="Update" style="font-size:10px;width:60px;"/>
				</td>
				<td>
					<input type="submit" name="cmd" value="Delete" style="font-size:10px;width:60px;"/>
				</td>
			<?php endif; ?>
			</form>
			</tr>
			<?php endforeach; ?>
		</table>
	</fieldset>
</fieldset>
	<?php
}


function starting_zones() {
	global $vgo,$objectName,$link;

	$table="starting_zones";
?>
			<table border="0" cellpadding="5">
				<tr>
					<td valign="top">
						<fieldset><legend>General</legend>
						<table>
							<tr style="font-weight:bold">
								<td width="55">id</td>
								<td width="155">class_id</td>
								<td width="150">race_id</td>
								<td width="150">zone_id</td>
								<td colspan="2">&nbsp;</td>
							</tr>
						</table>

						<?php
						$query=sprintf("select * from starting_zones");
						$result=$eq2->db->sql_query($query);
						while($data=$eq2->db->sql_fetchrow($result)) {
						?>
						<table>
							<form method="post" name="multiForm|<?php print($data['id']); ?>" />
							<tr>
								<td>
									<input type="text" name="starting_zones|id" value="<?php print($data['id']) ?>"  style="width:50px;  background-color:#ddd;" readonly />
									<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
								</td>
								<td>
									<select name="starting_zones|class_id" style="width:150px;">
										<?= $eq2->GetClasses($data['class_id']) ?>
									</select>
									<input type="hidden" name="orig_class_id" value="<?php print($data['class_id']) ?>" />
								</td>
								<td>
									<select name="starting_zones|race_id" style="width:150px;">
										<?= $eq2->getPlayerRaceOptions($data['race_id']) ?>
									</select>
									<input type="hidden" name="orig_race_id" value="<?php print($data['race_id']) ?>" />
								</td>
								<td>
									<select name="starting_zones|zone_id" style="width:150px;">
										<?= $eq2->GetChunkOptionsByID($data['zone_id']) ?>
									</select>
									<input type="hidden" name="orig_zone_id" value="<?php print($data['zone_id']) ?>" />
								</td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" /><?php } ?></td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" /><?php } ?></td>
							</tr>
							<input type="hidden" name="orig_object" value="starting zone" />
							<input type="hidden" name="tableName" value="<?= $table ?>" />
							</form>
						</table>
						<?php
						}
						?>
						<?php if($eq2->CheckAccess(G_DEVELOPER)) { ?>
						<table>
							<form method="post" name="sdForm|new" />
							<tr align="center">
								<td width="55"><strong>new</strong></td>
								<td>
									<select name="starting_zones|class_id|new" style="width:150px;">
										<?= $eq2->GetClasses(255) ?>
									</select>
								</td>
								<td>
									<select name="starting_zones|race_id|new" style="width:150px;">
										<?= $eq2->getPlayerRaceOptions(255) ?>
									</select>
								</td>
								<td>
									<select name="starting_zones|zone_id|new" style="width:150px;">
										<?= $eq2->GetChunkOptionsByID(253) ?>
									</select>
								</td>
								<td>
									<input type="submit" name="cmd" value="Insert" style="font-size:10px; width:60px" />
								</td>
							</tr>
							<input type="hidden" name="orig_object" value="new entry" />
							<input type="hidden" name="tableName" value="<?= $table ?>" />
							</form>
						</table>
						<?php } ?>
						</fieldset>
					</td>
				</tr>
			</table>
		<?php
}


function starting_spells() {
	global $eq2;

	$table="starting_spells";
?>
			<table border="0" cellpadding="5">
				<tr>
					<td valign="top">
						<fieldset><legend>General</legend>
						<table class="ContrastTable">
							<tr>
								<th width="55">id</th>
								<th width="150">race_id</th>
								<th width="150">class_id</th>
								<th width="55">spell_id</th>
								<th width="45">tier</th>
								<th width="105">knowledge_slot</th>
								<th colspan="2"></th>
							</tr>
						<?php
						$query=sprintf("select * from `%s`.starting_spells", DEV_DB);
						$result=$eq2->db->sql_query($query);
						while($data=$eq2->db->sql_fetchrow($result)) {
						?>
							<form method="post" name="multiForm|<?php print($data['id']); ?>">
							<tr>
								<td>
									<input type="text" name="starting_spells|id" value="<?php print($data['id']) ?>"  style="width:50px;  background-color:#ddd;" readonly />
									<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
								</td>
								<td>
									<select name="starting_spells|race_id" style="width:150px;">
										<?= $eq2->getPlayerRaceOptions($data['race_id']) ?>
									</select>
									<input type="hidden" name="orig_race_id" value="<?php print($data['race_id']) ?>" />
								</td>
								<td>
									<select name="starting_spells|class_id" style="width:150px;">
										<?= $eq2->GetClasses($data['class_id']) ?>
									</select>
									<input type="hidden" name="orig_class_id" value="<?php print($data['class_id']) ?>" />
								</td>
								<td>
									<input type="text" name="starting_spells|spell_id" value="<?php print($data['spell_id']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_spell_id" value="<?php print($data['spell_id']) ?>" />
								</td>
								<td>
									<input type="text" name="starting_spells|tier" value="<?php print($data['tier']) ?>" />
									<input type="hidden" name="orig_tier" value="<?php print($data['tier']) ?>" />
								</td>
								<td>
									<input type="hidden" name="orig_object" value="starting item" />
									<input type="hidden" name="table_name" value="<?= $table ?>" />
									<input type="text" name="starting_spells|knowledge_slot" value="<?php print($data['knowledge_slot']) ?>"  style="width:100px;" />
									<input type="hidden" name="orig_knowledge_slot" value="<?php print($data['knowledge_slot']) ?>" />
								</td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" /><?php } ?></td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" /><?php } ?></td>
							</tr>
							</form>
						<?php
						}
						?>
						<?php if($eq2->CheckAccess(G_DEVELOPER)) { ?>
							<form method="post" name="sdForm|new" />
							<tr align="center">
								<td width="55"><strong>new</strong></td>
								<td>
									<select name="starting_spells|race_id|new" style="width:150px;">
										<?= $eq2->getPlayerRaceOptions(255) ?>
									</select>
								</td>
								<td>
									<select name="starting_spells|class_id|new" style="width:150px;">
										<?= $eq2->GetClasses(255) ?>
									</select>
								</td>
								<td>
									<input type="text" name="starting_spells|spell_id|new" value="0"  style="width:50px;" />
								</td>
								<td>
									<input type="text" name="starting_spells|knowledge_slot|new" value="0"  style="width:100px;" />
								</td>
								<td>
									<input type="submit" name="cmd" value="Insert" style="font-size:10px; width:60px" />
								</td>
							</tr>
							<input type="hidden" name="orig_object" value="new entry" />
							<input type="hidden" name="tableName" value="<?= $table ?>" />
							</form>
						</table>
						<?php } ?>
						</fieldset>
					</td>
				</tr>
			</table>
		<?php
}


function starting_skills() {
	global $vgo,$objectName,$link;

	$table="starting_skills";

	// pick list of classes to shorten the list of spells
	foreach($eq2->eq2Classes as $key=>$val) 
	{
		if( isset($_GET['c']) && $_GET['c'] != 'all' )
		{
			$selected = ( $_GET['c'] == $key ) ? " selected" : "";
		}
		$classOptions.=sprintf("<option value=\"server.php?p=starting_skills&c=%s\"%s>%s</option>\n", $key, $selected, ucfirst(strtolower($val)));
	}
?>
	<table>
		<tr>
			<td valign="top" height="20">
				<select name="class_id" onchange="dosub(this.options[this.selectedIndex].value)" style="width:150px;">
				<option>Pick a Class</option>
				<?= $classOptions ?>
				<option value="server.php?p=starting_skills&c=show_all"<?php if( $_GET['c'] == 'show_all' ) print(" selected") ?>>All Starting Skills</option>
				</select>
			</td>
		</tr>
	</table>
<?php
if( isset($_GET['c']) ) 
{
?>
			<table border="0" cellpadding="5">
				<tr>
					<td valign="top">
						<fieldset><legend>General</legend>
						<table>
							<tr style="font-weight:bold">
								<td width="35">id</td>
								<td width="155">class_id</td>
								<td width="155">race_id</td>
								<td width="155">skill_id</td>
								<td width="75">current_val</td>
								<td width="75">max_val</td>
								<td width="75">progress</td>
								<td colspan="2">&nbsp;</td>
							</tr>
						<?php
						if( $_GET['c'] == "show_all" )
						{
							$query=sprintf("select * from starting_skills");
						}
						else
						{
							$query=sprintf("select starting_skills.* from starting_skills, skills where starting_skills.skill_id = skills.id and class_id = %d order by skills.name", $_GET['c']);
						}
						$result=$eq2->db->sql_query($query);
						while($data=$eq2->db->sql_fetchrow($result)) {
						?>
							<form method="post" name="multiForm|<?php print($data['id']); ?>" />
							<tr>
								<td>
									<input type="text" name="starting_skills|id" value="<?php print($data['id']) ?>"  style="width:30px; background-color:#ddd;" readonly />
									<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
								</td>
								<td>
									<select name="starting_skills|class_id" style="width:150px;">
										<?= $eq2->GetClasses($data['class_id']) ?>
									</select>
									<input type="hidden" name="orig_class_id" value="<?php print($data['class_id']) ?>" />
								</td>
								<td>
									<select name="starting_skills|race_id" style="width:150px;">
										<?= $eq2->getPlayerRaceOptions($data['race_id']) ?>
									</select>
									<input type="hidden" name="orig_race_id" value="<?php print($data['race_id']) ?>" />
								</td>
								<td>
									<select name="starting_skills|skill_id" style="width:150px;">
										<?= $eq2->getClassSkills($data['skill_id']) ?>
									</select>
									<input type="hidden" name="orig_skill_id" value="<?php print($data['skill_id']) ?>" />
								</td>
								<td>
									<input type="text" name="starting_skills|current_val" value="<?php print($data['current_val']) ?>"  style="width:70px;" />
									<input type="hidden" name="orig_current_val" value="<?php print($data['current_val']) ?>" />
								</td>
								<td>
									<input type="text" name="starting_skills|max_val" value="<?php print($data['max_val']) ?>"  style="width:70px;" />
									<input type="hidden" name="orig_max_val" value="<?php print($data['max_val']) ?>" />
								</td>
								<td>
									<input type="text" name="starting_skills|progress" value="<?php print($data['progress']) ?>"  style="width:70px;" />
									<input type="hidden" name="orig_progress" value="<?php print($data['progress']) ?>" />
								</td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" /><?php } ?></td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" /><?php } ?></td>
							</tr>
							<input type="hidden" name="orig_object" value="starting skill" />
							<input type="hidden" name="tableName" value="<?= $table ?>" />
							</form>
						<?php
						}
						?>
						<?php 
						if($eq2->CheckAccess(G_DEVELOPER))
						{ 
							$class_id = ( isset($_GET['c']) && $_GET['c'] != 'all' ) ? $_GET['c'] : 255;
						?>
							<form method="post" name="sdForm|new" />
							<tr align="center">
								<td><strong>new</strong></td>
								<td>
									<select name="starting_skills|class_id|new" style="width:150px;">
										<?= $eq2->GetClasses($class_id) ?>
									</select>
								</td>
								<td>
									<select name="starting_skills|race_id|new" style="width:150px;">
										<?= $eq2->getPlayerRaceOptions(255) ?>
									</select>
								</td>
								<td>
									<select name="starting_skills|skill_id|new" style="width:150px;">
										<?= $eq2->getClassSkills(0) ?>
									</select>
								</td>
								<td>
									<input type="text" name="starting_skills|current_val|new" value="1"  style="width:70px;" />
								</td>
								<td>
									<input type="text" name="starting_skills|max_val|new" value="1"  style="width:70px;" />
								</td>
								<td>
									<input type="text" name="starting_skills|progress|new" value="0"  style="width:70px;" />
								</td>
								<td>
									<input type="submit" name="cmd" value="Insert" style="font-size:10px; width:60px" />
								</td>
							</tr>
							<input type="hidden" name="orig_object" value="new entry" />
							<input type="hidden" name="tableName" value="<?= $table ?>" />
							</form>
						</table>
						<?php } ?>
						</fieldset>
					</td>
				</tr>
			</table>
			<table>
				<tr>
					<td><p>Unassigned Skills:</p>
		<?php
		$query = "select * from skills where id > 2 and id not in (select skill_id from starting_skills) order by name;";
		$result=$eq2->db->sql_query($query);
		while($data=$eq2->db->sql_fetchrow($result)) {
			echo $data['name'] . "<br />";
		}
		?>
					</td>
				</tr>
			</table>
		<?php
	}
}


function starting_skillbar() {
	global $vgo,$objectName,$link;

	$table="starting_skillbar";
?>
			<table border="0" cellpadding="5">
				<tr>
					<td valign="top">
						<fieldset><legend>General</legend>
						<table>
							<tr style="font-weight:bold">
								<td width="55">id</td>
								<td width="155">race_id</td>
								<td width="150">class_id</td>
								<td width="55">type</td>
								<td width="55">hotbar</td>
								<td width="55">spell_id</td>
								<td width="55">slot</td>
								<td width="155">text_val</td>
								<td colspan="2">&nbsp;</td>
							</tr>
						</table>
						<?php
						$query=sprintf("select * from starting_skillbar");
						$result=$eq2->db->sql_query($query);
						while($data=$eq2->db->sql_fetchrow($result)) {
							$nextSlot = $data['slot'];
							$nextHotBar = ( isset($data['hotbar']) ) ? $data['hotbar'] : 0;
						?>
						<table>
							<form method="post" name="multiForm|<?php print($data['id']); ?>" />
							<tr>
								<td>
									<input type="text" name="starting_skillbar|id" value="<?php print($data['id']) ?>"  style="width:50px;  background-color:#ddd;" readonly />
									<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
								</td>
								<td>
									<select name="starting_skillbar|race_id" style="width:150px;">
										<?= $eq2->getPlayerRaceOptions($data['race_id']) ?>
									</select>
									<input type="hidden" name="orig_race_id" value="<?php print($data['race_id']) ?>" />
								</td>
								<td>
									<select name="starting_skillbar|class_id" style="width:150px;">
										<?= $eq2->GetClasses($data['class_id']) ?>
									</select>
									<input type="hidden" name="orig_class_id" value="<?php print($data['class_id']) ?>" />
								</td>
								<td>
									<input type="text" name="starting_skillbar|type" value="<?php print($data['type']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_type" value="<?php print($data['type']) ?>" />
								</td>
								<td>
									<input type="text" name="starting_skillbar|hotbar" value="<?php print($data['hotbar']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_hotbar" value="<?php print($data['hotbar']) ?>" />
								</td>
								<td>
									<input type="text" name="starting_skillbar|spell_id" value="<?php print($data['spell_id']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_spell_id" value="<?php print($data['spell_id']) ?>" />
								</td>
								<td>
									<input type="text" name="starting_skillbar|slot" value="<?php print($data['slot']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_slot" value="<?php print($data['slot']) ?>" />
								</td>
								<td>
									<input type="text" name="starting_skillbar|text_val" value="<?php print($data['text_val']) ?>"  style="width:150px;" />
									<input type="hidden" name="orig_text_val" value="<?php print($data['text_val']) ?>" />
								</td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" /><?php } ?></td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" /><?php } ?></td>
							</tr>
							<input type="hidden" name="orig_object" value="starting hotbar" />
							<input type="hidden" name="tableName" value="<?= $table ?>" />
							</form>
						</table>
						<?php
						}
						?>
						<?php 
						if($eq2->CheckAccess(G_DEVELOPER)) { 
							$nextSlot++;
							if( $nextSlot>=12 ) {
								$nextSlot=1;
								$nextHotBar++;
							}
						?>
						<table>
							<form method="post" name="sdForm|new" />
							<tr align="center">
								<td width="55"><strong>new</strong></td>
								<td>
									<select name="starting_skillbar|race_id|new" style="width:150px;">
										<?= $eq2->getPlayerRaceOptions(255) ?>
									</select>
								</td>
								<td>
									<select name="starting_skillbar|class_id|new" style="width:150px;">
										<?= $eq2->GetClasses(255) ?>
									</select>
								</td>
								<td>
									<input type="text" name="starting_skillbar|type|new" value="0"  style="width:50px;" />
								</td>
								<td>
									<input type="text" name="starting_skillbar|hotbar|new" value="<?= $nextHotBar ?>"  style="width:50px;" />
								</td>
								<td>
									<input type="text" name="starting_skillbar|spell_id|new" value="1"  style="width:50px;" />
								</td>
								<td>
									<input type="text" name="starting_skillbar|slot|new" value="<?= $nextSlot ?>"  style="width:50px;" />
								</td>
								<td>
									<input type="text" name="starting_skillbar|text_val|new" value=""  style="width:150px;" />
								</td>
								<td>
									<input type="submit" name="cmd" value="Insert" style="font-size:10px; width:60px" />
								</td>
							</tr>
							<input type="hidden" name="orig_object" value="new entry" />
							<input type="hidden" name="tableName" value="<?= $table ?>" />
							</form>
						</table>
						<?php } ?>
						</fieldset>
					</td>
				</tr>
			</table>
		<?php
}


function starting_items() {
	global $vgo,$objectName,$link;

	$table="starting_items";
?>
			<table border="0" cellpadding="5">
				<tr>
					<td valign="top">
						<fieldset><legend>General</legend>
						<table>
							<tr style="font-weight:bold;">
								<td width="45">id</td>
								<td width="100">class_id</td>
								<td width="100">race_id</td>
								<td width="100">type</td>
								<td width="200">item_id (item name)</td>
								<td width="75">condition_</td>
								<td width="55">attuned</td>
								<td width="55">count</td>
								<td width="120" colspan="2">&nbsp;</td>
							</tr>
						<?php
						$query=sprintf("select * from starting_items order by class_id,race_id,item_id,count");
						$result=$eq2->db->sql_query($query);
						while($data=$eq2->db->sql_fetchrow($result)) {
						?>
							<form method="post" name="multiForm|<?php print($data['id']); ?>" />
							<tr>
								<td>
			            <input type="text" name="starting_items|id" value="<?= $data['id'] ?>" readonly style="width:40px; background-color:#ddd;" />
									<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
								</td>
								<td>
									<select name="starting_items|class_id" style="width:100px;">
										<?= $eq2->GetClasses($data['class_id']) ?>
									</select>
									<input type="hidden" name="orig_class_id" value="<?php print($data['class_id']) ?>" />
								</td>
								<td>
									<select name="starting_items|race_id" style="width:100px;">
										<?= $eq2->getPlayerRaceOptions($data['race_id']) ?>
									</select>
									<input type="hidden" name="orig_race_id" value="<?php print($data['race_id']) ?>" />
								</td>
								<td>
									<select name="starting_items|type" style="width:100px;">
										<option<?php if( $data['type']=="EQUIPPED" ) echo " selected" ?>>EQUIPPED</option>
										<option<?php if( $data['type']=="NOT-EQUIPPED" ) echo " selected" ?>>NOT-EQUIPPED</option>
									</select>
									<input type="hidden" name="orig_type" value="<?php print($data['type']) ?>" />
								</td>
								<td width="200" nowrap="nowrap">
									<input type="text" name="starting_items|item_id" value="<?php print($data['item_id']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_item_id" value="<?php print($data['item_id']) ?>" />
									&nbsp;<a href="items.php?show=items&id=<?php print($data['item_id']) ?>" target="_blank"><u><?= $eq2->getItemName($data['item_id']) ?></u></a>&nbsp;
								</td>
								<td>
									<input type="text" name="starting_items|condition_" value="<?php print($data['condition_']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_condition_" value="<?php print($data['condition_']) ?>" />
								</td>
								<td width="75" align="center">
									<input type="checkbox" name="starting_items|attuned" value="1"<?= ( $data['attuned'] ) ? " checked" : "" ?> />
									<input type="hidden" name="orig_attuned" value="<?php print($data['attuned']) ?>" />
								</td>
								<td width="50">
									<input type="text" name="starting_items|count" value="<?php print($data['count']) ?>"  style="width:30px;" />
									<input type="hidden" name="orig_count" value="<?php print($data['count']) ?>" />
								</td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" /><?php } ?></td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" /><?php } ?></td>
							</tr>
							<input type="hidden" name="orig_object" value="starting item" />
							<input type="hidden" name="tableName" value="<?= $table ?>" />
							</form>
						<?php
						}
						?>
						</table>
						<?php if($eq2->CheckAccess(G_DEVELOPER)) { ?>
						<table>
							<form method="post" name="sdForm|new" />
							<tr align="center">
								<td width="45"><strong>new</strong></td>
								<td>
									<select name="starting_items|class_id|new" style="width:100px;">
										<?= $eq2->GetClasses(255) ?>
									</select>
								</td>
								<td>
									<select name="starting_items|race_id|new" style="width:100px;">
										<?= $eq2->getPlayerRaceOptions(255) ?>
									</select>
								</td>
								<td>
									<select name="starting_items|type|new" style="width:100px;">
										<option>NOT-EQUIPPED</option>
										<option>EQUIPPED</option>
									</select>
								</td>
								<td>
									<input type="text" name="starting_items|item_id|new" value="0"  style="width:50px;" />
									&nbsp;<a href="items.php" title="Lookup Item" target="_blank"><u>?</u></a>&nbsp;
								</td>
								<td>
									<input type="text" name="starting_items|creator|new" value=""  style="width:100px;" />
								</td>
								<td>
									<input type="text" name="starting_items|condition_|new" value="100"  style="width:50px;" />
								</td>
								<td width="75">
									<input type="checkbox" name="starting_items|attuned|new" value="1"  />
								</td>
								<td width="50" align="left">
									<input type="text" name="starting_items|count|new" value="1"  style="width:30px;" />
								</td>
								<td>
									<input type="submit" name="cmd" value="Insert" style="font-size:10px; width:60px" />
								</td>
							</tr>
							<input type="hidden" name="orig_object" value="starting item" />
							<input type="hidden" name="tableName" value="<?= $table ?>" />
							</form>
						</table>
						<?php } ?>
						</fieldset>
					</td>
				</tr>
			</table>
		<?php
}

function recipes() {
	global $eq2, $s;
	$id = $_GET['id'] ?? null;

	$search = isset($_GET['search']);
	$filterName = $_GET['name'] ?? null;
	$filterClass = $_GET['cls'] ?? -1;
	$filterAllProducts = $_GET['ap'] ?? 1;
	$filterMissingComp = $_GET['mc'] ?? 0;
	$filterMinLvl = $_GET['minl'] ?? null;
	$filterMaxLvl = $_GET['maxl'] ?? null;
	?>
	<a href="server.php?page=recipes&new">Add New</a>
	</br></br>
	<fieldset>
		<legend>Lookup</legend>
		<form method="post" name="FormRecipeFilters">
		<div id="RecipeSearchFilters">
			<div><label>name: </label><input type="text" name="searchFilter|Name" value="<?=$filterName?>" style="width:200px"/></div>
			<div><label>lvlMin: </label><input type="text" name="searchFilter|MinLvl" value="<?php if (isset($_GET['minl'])) echo $filterMinLvl ?>" style="width:25px"/></div>
			<div><label>lvlMax: </label><input type="text" name="searchFilter|MaxLvl" value="<?php if (isset($_GET['maxl'])) echo $filterMaxLvl ?>" style="width:25px"/></div>
			<div>
				<label>class: </label>
				<select name="searchFilter|Class">
					<option value="-1">Any</option>
					<?php foreach ($eq2->eq2ArchetypeSortedTSClasses as $v) {
						foreach ($v as $classID=>$className) {
							printf('<option value="%s"%s>%s</option>', $classID, $filterClass == $classID ? " selected" : "", $className);
						}
					} ?>
				</select>
			</div>
			<div>
				<table>
					<tr>
						<td>bAllProductsSet: </td>
						<td><?php $eq2->GenerateBlueCheckbox("searchFilter|ap", $filterAllProducts)?></div></td>
					</tr>
				</table>
			</div>
			<div>
				<table>
					<tr>
						<td>MissingComponentsOnly: </td>
						<td><?php $eq2->GenerateBlueCheckbox("searchFilter|mc", $filterMissingComp)?></div></td>
					</tr>
				</table>
			</div>
			<input type="submit" name="cmd" value="Search"/>
		</form>
	</fieldset>
	</br>
	<?php if (isset($_GET['new'])) : ?>
		<?php $s->PrintNewRecipeForm() ?>
	<?php elseif ($id != null) : ?>
		<?php $s->PrintRecipeForm($id) ?>
	<?php endif; ?>
	<?php if ($search) : 
		//Apply pre query filters
		$filters = array();
		if ($filterMinLvl !== null || $filterMaxLvl !== null) {
			if ($filterMinLvl === null) $filterMinLvl = 0;

			if ($filterMaxLvl !== null) {
				$filters[] = sprintf("level BETWEEN %s AND %s", $eq2->SQLEscape($filterMinLvl), $eq2->SQLEscape($filterMaxLvl));			
			}
			else {
				$filters[] = sprintf("level >= %s", $eq2->SQLEscape($filterMinLvl));
			}
		}
		if ($filterClass != -1) {
			$filters[] = sprintf("ts_classes & %s", 1 << intval($filterClass));
		}
		$filters[] = "bHaveAllProducts = ".$filterAllProducts;
		if ($filterName !== null) {
			$filters[] = sprintf("`name` rlike '%s'", $eq2->SQLEscape($filterName));
		}
		//Make the base query
		$query = sprintf("SELECT id, level, icon, name, stage4_id FROM %s.recipe", DEV_DB);
		//Add filters
		if (count($filters) != 0) {
			$query .= " WHERE";
			$bFirst = true;
			foreach ($filters as $f) {
				if ($bFirst == true) $bFirst = false;
				else $query .= " AND";
				$query .= " " . $f;
			}
		}
		$query .= " ORDER BY level, name";
		$rows = $eq2->RunQueryMulti($query);

		//Post query filtering
		if ($filterMissingComp) {
			$query = sprintf("SELECT id FROM %s.recipe r WHERE id NOT IN
			(
			SELECT DISTINCT r.id FROM %s.recipe_comp_list rcl
			INNER JOIN %s.recipe r ON rcl.bEmpty AND r.primary_comp_list = rcl.id
			UNION
			SELECT DISTINCT r.id FROM %s.recipe_comp_list rcl
			INNER JOIN %s.recipe r ON rcl.bEmpty AND r.fuel_comp_list = rcl.id
			UNION
			SELECT DISTINCT rsc.recipe_id as id FROM %s.recipe_secondary_comp rsc
			INNER JOIN %s.recipe_comp_list rcl ON rcl.bEmpty AND rcl.id = rsc.comp_list
			)
			ORDER BY id",
			DEV_DB, DEV_DB, DEV_DB, DEV_DB, DEV_DB, DEV_DB, DEV_DB);
			$res = $eq2->RunQueryMulti($query);

			$ind = 0;
			$filterIDS = new SplFixedArray(count($res));
			foreach ($res as $d) {
				$filterIDS[$ind] = intval($d['id']);
				$ind++;
			}
		}
	?>
	<fieldset>
		<legend>Search Results</legend>
		<table class="ContrastTable">
			<tr>
				<th>recipe</th>
				<th>level</th>
				<th colspan="2">item</th>
			</tr>
			<?php foreach($rows as $data) : ?>
				<?php if ($filterMissingComp) {
					$bFound = false;
					$tmpid = intval($data['id']);
					foreach ($filterIDS as $f) {
						if ($f == $tmpid) {
							$bFound = true;
							break;
						}
					}
					if ($bFound) continue;
				}  
				?>
				<tr>
					<td>
						<a href="<?php printf("server.php?page=recipes&id=%s", $data['id']) ?>"><?php echo $data['id']?></a>
					</td>
					<td align="center"><?=$data['level']?></td>
					<td>
						<img src="<?php printf("eq2Icon.php?type=item&id=%s", $data['icon'])?>" />
					</td>
					<td>
					<?php if ($data['stage4_id']) : ?><a href="<?php printf("items.php?id=%s", $data['stage4_id']) ?>"><?php endif; echo $data['name']?><?php if ($data['stage4_id']) echo "</a>" ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
	</fieldset>
	<?php endif;
}

function merchants() {
	global $vgo,$objectName,$link;

	$table="merchants";
	//if( empty($_GET['t']) ) {
	//} else {
	?>
	<table border="0" cellpadding="5">
		<tr>
			<td width="900" valign="top">
				<fieldset><legend>General</legend>
				<table width="100%" cellpadding="0" border="0">
					<tr>
						<td colspan="3">
							<span class="heading">Editing: <?= $objectName ?></span><br />&nbsp;
						</td>
					</tr>
					<tr>
						<td width="55">id</td>
						<td width="100">merchant_id</td>
						<td width="100">inventory_id</td>
						<td width="400">description</td>
						<td colspan="2">&nbsp;</td>
					</tr>

						<?php
						$query=sprintf("select * from %s order by description",$table);
						$result=$eq2->db->sql_query($query);
						while($data=$eq2->db->sql_fetchrow($result)) {
						?>
					<form method="post" name="multiForm|<?php print($data['id']); ?>" />
					<tr>
						<td>
							<input type="text" name="merchants|id" value="<?php print($data['id']) ?>" style="width:45px;  background-color:#ddd;" readonly />
							<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
						</td>
						<td>
							<input type="text" name="merchants|merchant_id" value="<?php print($data['merchant_id']) ?>" style="width:100px;" />
							<input type="hidden" name="orig_merchant_id" value="<?php print($data['merchant_id']) ?>" />
						</td>
						<td>
							<input type="text" name="merchants|inventory_id" value="<?php print($data['inventory_id']) ?>" style="width:100px;" />
							<input type="hidden" name="orig_inventory_id" value="<?php print($data['inventory_id']) ?>" />
						</td>
						<td>
							<input type="text" name="merchants|description" value="<?php print($data['description']) ?>" style="width:400px;" />
							<input type="hidden" name="orig_description" value="<?php print($data['description']) ?>" />
						</td>
						<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" /><?php } ?></td>
						<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" /><?php } ?></td>
					</tr>
					<input type="hidden" name="objectName" value="<?= $objectName ?>" />
					<input type="hidden" name="tableName" value="<?= $table ?>" />
					</form>
				<?php
				}
				?>
				<?php if($eq2->CheckAccess(G_DEVELOPER)) { ?>
					<form method="post" name="sdForm|new" />
					<tr>
						<td align="center"><strong>new</strong></td>
						<td>
							<input type="text" name="merchants|merchant_id|new" value="<?= $eq2->getNextPK('merchants','merchant_id') ?>" style="width:100px;" />
						</td>
						<td>
							<input type="text" name="merchants|inventory_id|new" value="<?= $eq2->getNextPK('merchant_inventory','inventory_id') ?>" style="width:100px;" />
						</td>
						<td>
							<input type="text" name="merchants|description|new" value="" style="width:400px;" />
						</td>
						<td>
							<input type="submit" name="cmd" value="Insert" style="font-size:10px; width:60px" />
						</td>
					</tr>
					<input type="hidden" name="tableName" value="<?= $table ?>" />
					</form>
				<?php } ?>
				</table>
				</fieldset>
			</td>
		</tr>
	</table>
	<?php
	//}
}


function faction_alliances() {
	global $vgo,$objectName,$link;

	$table="faction_alliances";
?>
			<table border="0" cellpadding="5">
				<tr>
					<td valign="top">
						<fieldset><legend>General</legend>
						<table>
							<tr>
								<td colspan="3">
									<span class="heading">Editing: <?= $objectName ?></span><br />&nbsp;
								</td>
							</tr>
							<tr>
								<td width="55">id</td>
								<td width="200">faction_id</td>
								<td width="200">friend_faction</td>
								<td width="200">hostile_faction</td>
								<td colspan="2">&nbsp;</td>
							</tr>
						</table>

						<?php
						$query=sprintf("select * from faction_alliances");
						$result=$eq2->db->sql_query($query);
						while($data=$eq2->db->sql_fetchrow($result)) {
						?>
						<table>
							<form method="post" name="multiForm|<?php print($data['id']); ?>" />
							<tr>
								<td>
									<input type="text" name="faction_alliances|id" value="<?php print($data['id']) ?>"  style="width:50px;  background-color:#ddd;" readonly />
									<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
								</td>
								<td>
									<select name="faction_alliances|faction_id" style="width:200px">
									<?= $eq2->getFactions($data['faction_id']) ?>
									</select>
									<input type="hidden" name="orig_faction_id" value="<?php print($data['faction_id']) ?>" />
								</td>
								<td>
									<select name="faction_alliances|friend_faction" style="width:200px">
									<?= $eq2->getFactions($data['friend_faction']) ?>
									</select>
									<input type="hidden" name="orig_friend_faction" value="<?php print($data['friend_faction']) ?>" />
								</td>
								<td>
									<select name="faction_alliances|hostile_faction" style="width:200px">
									<?= $eq2->getFactions($data['hostile_faction']) ?>
									</select>
									<input type="hidden" name="orig_hostile_faction" value="<?php print($data['hostile_faction']) ?>" />
								</td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" /><?php } ?></td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" /><?php } ?></td>
							</tr>
							<input type="hidden" name="objectName" value="<?= $objectName ?>" />
							<input type="hidden" name="tableName" value="<?= $table ?>" />
							</form>
						</table>
						<?php
						}
						?>
						<?php if($eq2->CheckAccess(G_DEVELOPER)) { ?>
						<table>
							<form method="post" name="sdForm|new" />
							<tr align="center">
								<td width="55"><strong>new</strong></td>
								<td>
									<select name="faction_alliances|faction_id|new" style="width:200px">
									<?= $eq2->getFactions($data['faction_id']) ?>
									</select>
								</td>
								<td>
									<select name="faction_alliances|friend_faction|new" style="width:200px">
									<?= $eq2->getFactions($data['friend_faction']) ?>
									</select>
								</td>
								<td>
									<select name="faction_alliances|hostile_faction|new" style="width:200px">
									<?= $eq2->getFactions($data['hostile_faction']) ?>
									</select>
								</td>
								<td>
									<input type="submit" name="cmd" value="Insert" style="font-size:10px; width:60px" />
								</td>
							</tr>
							<input type="hidden" name="tableName" value="<?= $table ?>" />
							</form>
						</table>
						<?php } ?>
						</fieldset>
					</td>
				</tr>
			</table>
		<?php
 }


function factions() {
	global $vgo,$objectName,$link;

	$table="factions";
?>
			<table border="0" cellpadding="5">
				<tr>
					<td valign="top">
						<fieldset><legend>General</legend>
						<table>
							<tr>
								<td width="35">id</td>
								<td width="205">name</td>
								<td width="75">type</td>
								<td width="350">description</td>
								<td width="55" align="center">default level</td>
								<td width="55" align="center">negative change</td>
								<td width="55" align="center">positive change</td>
								<td colspan="2">&nbsp;</td>
							</tr>
						</table>

						<?php
						$query=sprintf("select * from factions order by id");
						$result=$eq2->db->sql_query($query);
						while($data=$eq2->db->sql_fetchrow($result)) {
						?>
						<table>
							<form method="post" name="multiForm|<?php print($data['id']); ?>" />
							<tr valign="top">
								<td>
									<input type="text" name="factions|id" value="<?php print($data['id']) ?>"  style="width:30px;" />
									<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
								</td>
								<td>
									<input type="text" name="factions|name" value="<?php print($data['name']) ?>"  style="width:200px;" />
									<input type="hidden" name="orig_name" value="<?php print($data['name']) ?>" />
								</td>
								<td>
									<input type="text" name="factions|type" value="<?php print($data['type']) ?>"  style="width:70px;" />
									<input type="hidden" name="orig_type" value="<?php print($data['type']) ?>" />
								</td>
								<td>
									<textarea name="factions|description" style="width:350px; height:60px;"><?php print($data['description']) ?></textarea>
									<input type="hidden" name="orig_description" value="<?php print($data['description']) ?>" />
								</td>
								<td>
									<input type="text" name="factions|default_level" value="<?php print($data['default_level']) ?>"  style="width:40px;" />
									<input type="hidden" name="orig_default_level" value="<?php print($data['default_level']) ?>" />
								</td>
								<td>
									<input type="text" name="factions|negative_change" value="<?php print($data['negative_change']) ?>"  style="width:40px;" />
									<input type="hidden" name="orig_negative_change" value="<?php print($data['negative_change']) ?>" />
								</td>
								<td>
									<input type="text" name="factions|positive_change" value="<?php print($data['positive_change']) ?>"  style="width:40px;" />
									<input type="hidden" name="orig_positive_change" value="<?php print($data['positive_change']) ?>" />
								</td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" /><?php } ?></td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" /><?php } ?></td>
							</tr>
							<input type="hidden" name="objectName" value="<?= $objectName ?>" />
							<input type="hidden" name="tableName" value="<?= $table ?>" />
							</form>
						</table>
						<?php
						}
						?>
						<?php if($eq2->CheckAccess(G_DEVELOPER)) { // currently off for all ?>
						<table>
							<form method="post" name="sdForm|new" />
							<tr valign="top">
								<td width="35"><strong>new</strong></td>
								<td>
									<input type="text" name="factions|name|new" value=""  style="width:200px;" />
								</td>
								<td>
									<input type="text" name="factions|type|new" value=""  style="width:70px;" />
								</td>
								<td>
									<textarea name="factions|description|new" style="width:350px; height:60px;"></textarea>
								</td>
								<td>
									<input type="text" name="factions|default_level|new" value=""  style="width:40px;" />
								</td>
								<td>
									<input type="text" name="factions|negative_change|new" value=""  style="width:40px;" />
								</td>
								<td>
									<input type="text" name="factions|positive_change|new" value=""  style="width:40px;" />
								</td>
								<td>
									<input type="submit" name="cmd" value="Insert" style="font-size:10px; width:60px" />
								</td>
							</tr>
							<input type="hidden" name="tableName" value="<?= $table ?>" />
							</form>
						</table>
						<?php } ?>
						</fieldset>
					</td>
				</tr>
			</table>
		<?php
}

function rules() {
	global $vgo,$objectName,$link;
?>
	<table border="0" cellpadding="5">
		<tr>
			<td colspan="3">
				<span class="heading">Editing: Rules</span><br />This editor configures server ruleset overrides.
			</td>
		</tr>
	</table>
<?php
}

function titles() {
	global $vgo,$objectName,$link;
?>
	<table border="0" cellpadding="5">
		<tr>
			<td colspan="3">
				<span class="heading">Editing: Titles</span><br />This editor configures server available titles.
			</td>
		</tr>
	</table>
<?php
}

function transporters() {
	global $eq2,$objectName;

	$table="transporters";
?>
	<table border="0" cellpadding="5">
		<tr>
			<td colspan="3">
				<span class="heading">Editing: Transporters</span><br />This editor is a little dodgey, sorry... I'll try improving it.
			</td>
		</tr>
<?php
	$query = sprintf("select * from %s.%s order by transport_id;", DEV_DB, $table);
	$result=$eq2->db->sql_query($query);
	while($data=$eq2->db->sql_fetchrow($result)) {
?>
		<tr>
			<td valign="top">
				<fieldset><legend>Transporter to: <?php print($data['display_name']) ?></legend>
				<table border="0" cellpadding="0" cellspacing="4">
				<form method="post" name="multiForm|<?php print($data['id']); ?>" />
					<tr>
						<td align="right">id:</td>
						<td>
							<input type="text" name="transporters|id" value="<?php print($data['id']) ?>"  style="width:50px;  background-color:#ddd;" readonly />
							<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
						</td>
						<td align="right">transport_id:</td>
						<td>
							<input type="text" name="transporters|transport_id" value="<?php print($data['transport_id']) ?>"  style="width:50px;" />
							<input type="hidden" name="orig_transport_id" value="<?php print($data['transport_id']) ?>" />
						</td>
						<td align="right">display_name:</td>
						<td colspan="3">
							<input type="text" name="transporters|display_name" value="<?php print($data['display_name']) ?>"  style="width:125px;" />
							<input type="hidden" name="orig_display_name" value="<?php print($data['display_name']) ?>" />
						</td>
					</tr>
					<tr>
						<td align="right">cost</td>
						<td>
							<input type="text" name="transporters|cost" value="<?php print($data['cost']) ?>"  style="width:50px;" />
							<input type="hidden" name="orig_cost" value="<?php print($data['cost']) ?>" />
						</td>
						<td align="right">transport_type</td>
						<td>
							<select name="transporters|transport_type" style="width:125px;">
								<option<?php if($data['transport_type']=='Generic Transporter') print(' selected'); ?>>Generic Transporter</option>
								<option<?php if($data['transport_type']=='Location') print(' selected'); ?>>Location</option>
								<option<?php if($data['transport_type']=='Zone') print(' selected'); ?>>Zone</option>
							</select>
							<input type="hidden" name="orig_transport_type" value="<?php print($data['transport_type']) ?>" />
						</td>
						<td align="right">message:</td>
						<td colspan="3">
							<input type="text" name="transporters|message" value="<?php print($data['message']) ?>"  style="width:315px;" />
							<input type="hidden" name="orig_message" value="<?php print($data['message']) ?>" />
						</td>
					</tr>
					<tr>
						<td align="right">destination_zone_id</td>
						<td colspan="3">
							<select name="transporters|destination_zone_id" style="width:290px;">
								<?= $eq2->GetChunkOptionsByID($data['destination_zone_id']) ?>
							</select>
							<input type="hidden" name="orig_destination_zone_id" value="<?php print($data['destination_zone_id']) ?>" />
						</td>
						<td align="right">trigger_location_zone_id</td>
						<td colspan="3">
							<select name="transporters|trigger_location_zone_id" style="width:290px;">
								<?= $eq2->GetChunkOptionsByID($data['trigger_location_zone_id']) ?>
							</select>
							<input type="hidden" name="orig_trigger_location_zone_id" value="<?php print($data['trigger_location_zone_id']) ?>" />
						</td>
					</tr>
					<tr>
						<td align="right">destination_x</td>
						<td>
							<input type="text" name="transporters|destination_x" value="<?php print($data['destination_x']) ?>"  style="width:50px;" />
							<input type="hidden" name="orig_destination_x" value="<?php print($data['destination_x']) ?>" />
						</td>
						<td align="right">destination_y</td>
						<td>
							<input type="text" name="transporters|destination_y" value="<?php print($data['destination_y']) ?>"  style="width:50px;" />
							<input type="hidden" name="orig_destination_y" value="<?php print($data['destination_y']) ?>" />
						</td>
						<td align="right">destination_z</td>
						<td>
							<input type="text" name="transporters|destination_z" value="<?php print($data['destination_z']) ?>"  style="width:50px;" />
							<input type="hidden" name="orig_destination_z" value="<?php print($data['destination_z']) ?>" />
						</td>
						<td align="right">destination_heading</td>
						<td>
							<input type="text" name="transporters|destination_heading" value="<?php print($data['destination_heading']) ?>"  style="width:50px;" />
							<input type="hidden" name="orig_destination_heading" value="<?php print($data['destination_heading']) ?>" />
						</td>
					</tr>
					<tr>
						<td align="right">trigger_location_x</td>
						<td>
							<input type="text" name="transporters|trigger_location_x" value="<?php print($data['trigger_location_x']) ?>"  style="width:50px;" />
							<input type="hidden" name="orig_trigger_location_x" value="<?php print($data['trigger_location_x']) ?>" />
						</td>
						<td align="right">trigger_location_y</td>
						<td>
							<input type="text" name="transporters|trigger_location_y" value="<?php print($data['trigger_location_y']) ?>"  style="width:50px;" />
							<input type="hidden" name="orig_trigger_location_y" value="<?php print($data['trigger_location_y']) ?>" />
						</td>
						<td align="right">trigger_location_z</td>
						<td>
							<input type="text" name="transporters|trigger_location_z" value="<?php print($data['trigger_location_z']) ?>"  style="width:50px;" />
							<input type="hidden" name="orig_trigger_location_z" value="<?php print($data['trigger_location_z']) ?>" />
						</td>
						<td align="right">trigger_radius</td>
						<td>
							<input type="text" name="transporters|trigger_radius" value="<?php print($data['trigger_radius']) ?>"  style="width:50px;" />
							<input type="hidden" name="orig_trigger_radius" value="<?php print($data['trigger_radius']) ?>" />
						</td>
					</tr>
					<?php if($eq2->CheckAccess(G_DEVELOPER)) { ?>
					<tr>
						<td align="center" colspan="8">
							<input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" />
							<input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" />
							<input type="hidden" name="objectName" value="<?= $objectName ?>" />
							<input type="hidden" name="tableName" value="<?= $table ?>" />
						</td>
					</tr>
					<?php } ?>
				</form>
				</table>
				</fieldset>
			</td>
		</tr>
	<?php
	} // while...loop
	?>	
	</table>
<?php
}


function skills() {
	global $vgo,$objectName,$link;

	$table="skills";
?>
	<table border="0" cellpadding="5">
		<tr>
			<td valign="top"><p><b>Note:</b> Only the Server Admin can add new skills</p>
				<fieldset><legend>General</legend>
				<table width="100%" cellpadding="0" border="0">
					<tr>
						<td width="100">id</td>
						<td width="120">short_name</td>
						<td width="120">name</td>
						<td width="65">skill_type</td>
						<td width="65">display</td>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td colspan="6">description</td>
					</tr>
						<?php
						$query=sprintf("select * from %s order by `name`",$table);
						$result=$eq2->db->sql_query($query);
						while($data=$eq2->db->sql_fetchrow($result)) {
							$objectName = $data['short_name'];
						?>
					<form method="post" name="multiForm|<?php print($data['id']); ?>" />
					<tr>
						<td>
							<input type="text" name="skills|id" value="<?php print($data['id']) ?>" style="width:95px;  background-color:#ddd;" readonly />
							<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
						</td>
						<td>
							<input type="text" name="skills|short_name" value="<?php print($data['short_name']) ?>" style="width:115px;" />
							<input type="hidden" name="orig_short_name" value="<?php print($data['short_name']) ?>" />
						</td>
						<td>
							<input type="text" name="skills|name" value="<?php print($data['name']) ?>" style="width:115px;" />
							<input type="hidden" name="orig_name" value="<?php print($data['name']) ?>" />
						</td>
						<td>
							<input type="text" name="skills|skill_type" value="<?php print($data['skill_type']) ?>" style="width:60px;" />
							<input type="hidden" name="orig_skill_type" value="<?php print($data['skill_type']) ?>" />
						</td>
						<td>
							<input type="text" name="skills|display" value="<?php print($data['display']) ?>" style="width:60px;" />
							<input type="hidden" name="orig_display" value="<?php print($data['display']) ?>" />
						</td>
						<td rowspan="2"><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" /><?php } ?></td>
						<td rowspan="2"><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" /><?php } ?></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td colspan="4">
							<textarea name="skills|description" style="width:374px; height:40px;"><?php print($data['description']) ?></textarea>
							<input type="hidden" name="orig_description" value="<?php print($data['description']) ?>" />
						</td>
					</tr>
					<input type="hidden" name="objectName" value="<?= $objectName ?>" />
					<input type="hidden" name="tableName" value="<?= $table ?>" />
					</form>
					<tr>
						<td colspan="7" height="10"></td>
					</tr>
				<?php
				}
				?>
				<?php if($eq2->CheckAccess(G_DEVELOPER)) { ?>
					<form method="post" name="sdForm|new" />
					<tr>
						<td align="center"><strong>new</strong></td>
						<td>
							<input type="text" name="skills|short_name|new" value="" style="width:115px;" />
						</td>
						<td>
							<input type="text" name="skills|name|new" value="" style="width:115px;" />
						</td>
						<td>
							<input type="text" name="skills|skill_type|new" value="0" style="width:60px;" />
						</td>
						<td>
							<input type="text" name="skills|display|new" value="0" style="width:60px;" />
						</td>
						<td rowspan="2">
							<input type="submit" name="cmd" value="Insert" style="font-size:10px; width:60px"<?php if( $eq2->CheckAccess(G_DEVELOPER) ) echo " disabled" ?> />
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td colspan="5">
							<textarea name="skills|description|new" style="width:374px; height:40px;"><?php print($data['description']) ?></textarea>
						</td>
					</tr>
					<input type="hidden" name="tableName" value="<?= $table ?>" />
					</form>
				<?php } ?>
				</table>
				</fieldset>
			</td>
		</tr>
	</table>
<?php
}

function table_versions() {
	global $vgo,$objectName,$link;

	$table="table_versions";
?>
			<table border="0" cellpadding="5">
				<tr>
					<td valign="top">
						<fieldset><legend>General</legend>
						<table>
							<tr style="font-weight:bold;">
								<td width="55">id</td>
								<td width="255">name</td>
								<td width="55">version</td>
								<td width="105">download_version</td>
								<td colspan="2">&nbsp;</td>
							</tr>
						</table>
						<?php
						$query=sprintf("select * from table_versions order by name",$id);
						$result=$eq2->db->sql_query($query);
						while($data=$eq2->db->sql_fetchrow($result)) {
						?>
						<table>
							<form method="post" name="multiForm|<?php print($data['id']); ?>" />
							<tr>
								<td>
									<input type="text" name="table_versions|id" value="<?php print($data['id']) ?>"  style="width:50px;  background-color:#ddd;" readonly />
									<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
								</td>
								<td>
									<input type="text" name="table_versions|name" value="<?php print($data['name']) ?>"  style="width:250px;" />
									<input type="hidden" name="orig_name" value="<?php print($data['name']) ?>" />
								</td>
								<td>
									<input type="text" name="table_versions|version" value="<?php print($data['version']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_version" value="<?php print($data['version']) ?>" />
								</td>
								<td width="105">
									<input type="text" name="table_versions|download_version" value="<?php print($data['download_version']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_download_version" value="<?php print($data['download_version']) ?>" />
								</td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" /><?php } ?></td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" /><?php } ?></td>
							</tr>
							<input type="hidden" name="orig_object" value="<?= $data['name'] ?>" />
							<input type="hidden" name="tableName" value="<?= $table ?>" />
							</form>
						</table>
						<?php
						}
						?>
						</fieldset>
					</td>
				</tr>
			</table>
		<?php
}


function name_filter() {
	global $vgo,$objectName,$link;

	$table="name_filter";
?>
			<table border="0" cellpadding="5">
				<tr>
					<td valign="top">
						<fieldset><legend>General</legend>
						<table>
							<tr>
								<td>name</td>
								<td colspan="2">&nbsp;</td>
							</tr>
						</table>
						<?php
						$query=sprintf("select * from name_filter order by name");
						$result=$eq2->db->sql_query($query);
						while($data=$eq2->db->sql_fetchrow($result)) {
						?>
						<table>
							<form method="post" name="multiForm|<?php print($data['id']); ?>" />
							<tr>
								<td>
									<input type="text" name="name_filter|name" value="<?php print($data['name']) ?>"  style="width:150px;" />
									<input type="hidden" name="orig_name" value="<?php print($data['name']) ?>" />
								</td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="namefilter" value="Update" style="font-size:10px; width:60px" /><?php } ?></td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="namefilter" value="Delete" style="font-size:10px; width:60px" /><?php } ?></td>
							</tr>
							<input type="hidden" name="orig_object" value="<?= $data['name'] ?>" />
							<input type="hidden" name="tableName" value="<?= $table ?>" />
							</form>
						</table>
						<?php
						}
						?>
						<?php if($eq2->CheckAccess(G_DEVELOPER)) { ?>
						<table>
							<form method="post" name="sdForm|new" />
							<tr align="center">
								<td><input type="text" name="name_filter|name|new" value=""  style="width:150px;" /></td>
								<td>
									<input type="submit" name="namefilter" value="Insert" style="font-size:10px; width:60px" />
								</td>
							</tr>
							<input type="hidden" name="orig_object" value="new entry" />
							<input type="hidden" name="tableName" value="<?= $table ?>" />
							</form>
						</table>
						<?php } ?>
						</fieldset>
					</td>
				</tr>
			</table>
		<?php
}

function commands() {
	global $eq2;

	$table="commands";
?>
			<fieldset><legend>Commands</legend>
			<table cellpadding="5" id="EditorTable">
						<?php
						$i = 0;
						$query=sprintf("select * from %s.commands order by command,subcommand;", DEV_DB);
						foreach ($eq2->RunQueryMulti($query) as $data) {
						?>
						<?php if ($i++ % 10 == 0) : ?>
							<tr style="font-weight:bold">
								<th width="60">id</th>
								<th width="55">type</th>
								<th width="155">command</th>
								<th width="105">subcommand</th>
								<th width="55">handler</th>
								<th width="105">required_status</th>
								<th colspan="2">&nbsp;</th>
							</tr>
						<?php endif ?>
							<form method="post" name="multiForm|<?php print($data['id']); ?>" >
							<tr>
								<td align="center">
									<span><?php print($data['id']) ?></span>
									<input type="hidden" name="table_name" value="<?= $table ?>" />
									<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
								</td>
								<td>
									<input type="text" name="commands|type" value="<?php print($data['type']) ?>" style="width:50px;" />
									<input type="hidden" name="orig_type" value="<?php print($data['type']) ?>" />
								</td>
								<td>
									<input type="text" name="commands|command" value="<?php print($data['command']) ?>"  style="width:150px;" />
									<input type="hidden" name="orig_command" value="<?php print($data['command']) ?>" />
								</td>
								<td>
									<input type="text" name="commands|subcommand" value="<?php print($data['subcommand']) ?>"  style="width:100px;" />
									<input type="hidden" name="orig_subcommand" value="<?php print($data['subcommand']) ?>" />
								</td>
								<td>
									<input type="text" name="commands|handler" value="<?php print($data['handler']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_handler" value="<?php print($data['handler']) ?>" />
								</td>
								<td width="100">
									<input type="text" name="commands|required_status" value="<?php print($data['required_status']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_required_status" value="<?php print($data['required_status']) ?>" />
								</td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" /><?php } ?></td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" /><?php } ?></td>
							</tr>
							</form>
						<?php
						}
						?>
						<?php if($eq2->CheckAccess(G_DEVELOPER)) { ?>
							<tr style="font-weight:bold">
								<th width="60">id</th>
								<th width="55">type</th>
								<th width="155">command</th>
								<th width="105">subcommand</th>
								<th width="55">handler</th>
								<th width="105">required_status</th>
								<th colspan="2">&nbsp;</th>
							</tr>
							<form method="post" name="sdForm|new" >
							<tr align="center">
								<td width="55"><strong>new</strong></td>
								<td>
									<input type="text" name="commands|type|new" value=""  style="width:50px;" />
								</td>
								<td>
									<input type="text" name="commands|command|new" value=""  style="width:150px;" />
								</td>
								<td>
									<input type="text" name="commands|subcommand|new" value=""  style="width:100px;" />
								</td>
								<td>
									<input type="text" name="commands|handler|new" value=""  style="width:50px;" />
								</td>
								<td width="105" align="left">
									<input type="text" name="commands|required_status|new" value="0"  style="width:50px;" />
								</td>
								<td>
									<input type="submit" name="cmd" value="Insert" style="font-size:10px; width:60px" />
								</td>
								<td>&nbsp;</td>
							</tr>
							<input type="hidden" name="table_name" value="<?= $table ?>" />
							</form>
						<?php } ?>
			</table>
			</fieldset>
		<?php
 }

 function starting_factions() {
	global $s;
	?>

	<table cellpadding="5" id="EditorTable">
		<tr>
			<td>
			<?php $s->PrintStartingFactions(); ?>
			</td>
		</tr>
 	</table>
	 <?php
 }

 function recipe_comp() {
	global $eq2, $s;
	$id = $_GET['id'] ?? null;
	$search = $_GET['search'] ?? null;
	$compname = $_GET['compname'] ?? null;
	$compitems = explode(' ',$compname);
	$count = count($compitems);
	?>
	<a href="server.php?page=recipe_comp&new">Add New</a>
	</br></br>
	<fieldset>
		<legend>Search</legend>
		<form method="post" name="FormRecipeCompSearch">
		<label>name:</label>
		<input type="text" name="searchFilter|Name" value="<?=$search?>" style="width:200px"/>
		<input type="submit" name="cmd" value="Search"/>
 		</form>
	</fieldset>
 	</br>
	<?php
	if (isset($_GET['retid'])) {
		printf('<a href=server.php?page=recipes&id=%s>%s</a></br></br>', $_GET['retid'], "Return to Recipe");
	}
	$data = null;
	if ($id != null) {
		$query = sprintf("SELECT * FROM %s.recipe_comp_list_item WHERE comp_list = %s", DEV_DB, $id);
		$data = $eq2->RunQueryMulti($query);

		$query = sprintf("SELECT name FROM %s.recipe_comp_list WHERE id = %s", DEV_DB, $id);
		$name = $eq2->RunQuerySingle($query)['name'];

		$query =sprintf("SELECT t1.item_id, t9.name, t9.icon FROM %s.item_classifications t1 JOIN %s.items t9 ON t1.item_id = t9.id",DEV_DB, DEV_DB);

		for($x = 1; $x <= $count-1; $x++) {
			$y = $x+1;
			$query = $query.sprintf(" join %s.item_classifications t$y on t1.item_id = t$y.item_id and t$y.classification = '%s'", DEV_DB,$compitems[$x]);
		}
		$query = $query.sprintf(" WHERE t1.classification = '%s'",$compitems[0]);
		$comps = $eq2->RunQueryMulti($query);
	}

	if ($id != null) : ?>
		<fieldset>
			<legend><?php printf("%s (%s)", $name, $id) ?></legend>
			<table style="background:#eee">
				<tr>
					<td colspan="2" align="center">
						<?php //Overview ?>
						<table>
							<tr>
							<td>

							</td>
							<td><strong style="font-size:200%"><?php echo $r['name'] ?></strong></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<?php //Components ?>
	
					<fieldset>
						<legend>Custom List</legend>
			
							<table class="ContrastTable">
							
				
								<tr>
					<th>item id</th>
					<th>icon</th>
					<th>name</th>
					<th></th>
				</tr>
				<?php foreach($data as $item) : ?>
					<?php $s->PrintCompListRow($item) ?>
				<?php endforeach; ?>
				<?php 
					$item = array("new"=>true, "id"=>"0", "comp_list"=>$id);
					$s->PrintCompListRow($item);
				?>
				
			</table>
				
		</fieldset>
						</td>
					<td>
			<fieldset			style="white-space:nowrap">
			<legend>Classification Based List</legend>
			
			<table class="ContrastTable">
			
				
				<tr>
					<th>item id</th>
					<th>icon</th>
					<th>name</th>
					<th></th>
				</tr>
				<?php foreach($comps as $item) : ?>
					<?php $s->PrintCompListRowClass($item) ?>
				<?php endforeach; ?>
				<?php 
					$item = array("new"=>true, "id"=>"0", "comp_list"=>$id);
					$s->PrintCompListRow($item, false);
				?>
				
			</table>
				
		</fieldset>
	<?php
	elseif ($search != null) : 
		$query = sprintf("SELECT id, name FROM %s.recipe_comp_list WHERE name rlike '%s' ORDER BY name", DEV_DB, $eq2->SQLEscape($search));
		$rows = $eq2->RunQueryMulti($query);

		$compname = $_GET['compname'] ?? null;
		$compitems = explode(' ',$search);
		$count = count($compitems);


		$query =sprintf("SELECT t1.item_id, t9.name, t9.icon FROM %s.item_classifications t1 JOIN %s.items t9 ON t1.item_id = t9.id",DEV_DB, DEV_DB);

		for($x = 1; $x <= $count-1; $x++) {
			$y = $x+1;
			$query = $query.sprintf(" join %s.item_classifications t$y on t1.item_id = t$y.item_id and t$y.classification = '%s'", DEV_DB,$compitems[$x]);
		}
		$query = $query.sprintf(" WHERE t1.classification = '%s'",$compitems[0]);
		$comps = $eq2->RunQueryMulti($query);
		echo  $query ;
	?>
						
	<fieldset>
		<legend>Search Results - Component List</legend>
		<table class="ContrastTable">
			<tr>
				<th>id</th>
				<th>name</th>
			</tr>
			<?php foreach($rows as $data) : ?>		
				<tr>
					<td>
						<?=$data['id']?>
					</td>
					<td>
					<?php printf("<a href=\"server.php?page=recipe_comp&id=%s&compname=%s\">%s</a>", $data['id'],  $data['name'], $data['name']);?>
						
					</td>
				</tr>			
			<?php endforeach; ?>
		</table>
	</fieldset>
	<?php elseif (isset($_GET['new'])) :
		$s->PrintNewRecipeCompForm();
	endif;
 }

 function loot_table() {
	global $eq2, $s;
	$id = $_GET['id'] ?? null;
	$search = $_GET['search'] ?? null;
	?>

	<a href="server.php?page=loot_table&id=new">Add New</a>
	</br></br>
	<fieldset>
		<legend>Search</legend>
		<form method="post" name="FormLootTableSearch">
		<label>name:</label>
		<input id="lt_txtSearch" type="text" value="<?=$search?>" style="width:200px" autocomplete="off" onkeyup="LootTableLookupAJAX();"/>
		<input type="submit" name="cmd" value="Search"/>
		<div id="lt_search_suggest"></div>
 		</form>
	</fieldset>
 	</br>
	<?php
	$data = null;
	if ($id != null && $id != "new") {
		$query = sprintf("SELECT * FROM %s.loottable WHERE id = %s", DEV_DB, $id);
		$data = $eq2->RunQuerySingle($query);
	}

	if ($data != null) : ?>
		<fieldset>
			<legend><? printf("loottable (%s)", $id) ?></legend>
			<table class="ContrastTable">
				<tr>
					<th>item id</th>
					<th>icon</th>
					<th>name</th>
					<th>max loot items</th>
					<th>item prob</th>
					<th>coin prob</th>
					<th colspan="4">min coin</th>
					<th colspan="4">max coin</th>
					<th></th>
				</tr>
				<?php $s->PrintLootTableRow($data) ?>
			</table>
		</fieldset>
		<?php if ($id != "new") : ?>
		</br>
		<?php 
		$query = sprintf("SELECT lt.*, i.name as item_name, i.icon, i.tier, i.crafted FROM %s.lootdrop lt LEFT JOIN %s.items i ON i.id = lt.item_id WHERE loot_table_id = %s", DEV_DB, DEV_DB, $id);
		$data = $eq2->RunQueryMulti($query);
		?>
		<fieldset>
		<legend><? printf("lootdrop (%s)", $id) ?></legend>
		<table class="ContrastTable">
			<tr>
				<th>item id</th>
				<th>item</th>
				<th>probability</th>
				<th>charges</th>
				<th>equip item</th>
				<th>no drop</br>quest completed</th>
				<th></th>
			</tr>
			<?php
			$newRow = array();
			$newRow['id'] = "new";
			$newRow['item_id'] = "";
			$newRow['probability'] = 0;
			$newRow['equip_item'] = 0;
			$newRow['item_charges'] = 0;
			$newRow['no_drop_quest_completed'] = 0;
			$newRow['new'] = true;
			$s->PrintLootDropRow($newRow);
			?>
			<?php foreach ($data as $ld) {
				$s->PrintLootDropRow($ld);
			}
			?>
		</table>
		</fieldset>
		<?php endif; ?>
	<?php
	elseif ($search != null) : 
		$query = sprintf("SELECT id, name FROM %s.loottable WHERE name rlike '%s' ORDER BY name", DEV_DB, $eq2->SQLEscape($search));
		$rows = $eq2->RunQueryMulti($query);
	?>
	<fieldset>
		<legend>Search Results</legend>
		<table class="ContrastTable">
			<tr>
				<th>id</th>
				<th>name</th>
			</tr>
			<?php foreach($rows as $data) : ?>		
				<tr>
					<td>
						<?=$data['id']?>
					</td>
					<td>
						<a href="server.php?page=loot_table&id=<?=$data['id']?>"><?=$data['name']?></a>
					</td>
				</tr>			
			<?php endforeach; ?>
		</table>
	</fieldset>
	<?php elseif ($id == "new") : ?>
		<fieldset style="width:max-content">
			<legend>New Loot Table</legend>
			<form name="FormNewLootTable" method="post">
			<table>
				<tr>
					<td align="center">
						<strong style="font-size:large">Add A New Loot Table</strong>
					</td>
				</tr>
				<tr>
					<td>
						<label>name:</label>
						<input type="text" name="loottable|name"/>
						<input type="hidden" name="table_name" value="loottable"/>
					</td>
				</tr>
				<tr>
					<td align="center">
						<input type="submit" name="cmd" value="Insert"/>
					</td>
				</tr>
			</table>
			</form>
		</fieldset>
	<?php endif;
 }

 function loot_global() {
	global $eq2, $s;
	?>

	<?php
	$query = sprintf("SELECT * FROM %s.loot_global ORDER BY `type`, `value1`", DEV_DB);
	$data = $eq2->RunQueryMulti($query);
	?>
	<fieldset>
		<legend>Loot Global</legend>
		<table class="ContrastTable">
			<tr>
				<th>type</th>
				<th>value1</th>
				<th>loot table</th>
				<th>value2</th>
				<th>value3</th>
				<th>value4</th>
				<th></th>
			</tr>
			<?php 
				$item = array("new"=>true, "type"=>"Racial", "id"=>"new", "loot_table"=>"", "value1"=>0, "value2"=>0, "value3"=>0, "value4"=>0);
				$s->PrintLootGlobalRow($item);
			?>
			<?php foreach($data as $row) : ?>
				<?php $s->PrintLootGlobalRow($row) ?>
			<?php endforeach; ?>
		</table>
	</fieldset>
	<?php
 }
 
?>
