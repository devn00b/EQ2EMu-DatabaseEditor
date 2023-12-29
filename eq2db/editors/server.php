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
include("../class/eq2.icon.php");
$eq2Icons = new eq2Icons();

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
		case "delete": 
            $eq2->ProcessDeletes();
			$s->PostDeletes(); 
            break;
		case "multiinsert":
			$eq2->ProcessMultiInsert();
			break;
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
							//case "locations"			: $s->StartingLocations(); break;
							//case "opcodes"			: $s->OpcodeEditor(); break;
							case "variables"			: $s->ServerVariables(); break;
							case "groundspawns"			: groundspawns(); break;
							case "groundspawn_items"	: groundspawn_items(); break;
							case "entity_commands"		: entity_commands(); break;
							case "commands"				: commands(); break;
							case "collections"			: collections(); break;
							case "starting_spells"		: starting_spells(); break;
							case "starting_factions"	: starting_factions(); break;
							case "npc_spells"			: npc_spells(); break;
							case "recipes"				: recipes(); break;
							case "recipe_comp"			: recipe_comp(); break;
							case "loot_table"			: loot_table(); break;
							case "loot_global"			: loot_global(); break;
                            case "editor_lists"			: editor_lists(); break;
							case "static_values"		: static_values(); break;
							case "heroic_ops"			: heroic_ops(); break;
							case "misc_scripts"			: misc_scripts(); break;
							case "icons"				: icons(); break;
							case "lua_blocks"			: lua_blocks(); break;


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
					<?php $data = $eq2->RunQueryMulti("SELECT DISTINCT `category` FROM `".DEV_DB."`.spawn_npc_spell_lists WHERE LENGTH(`category`) ORDER BY `category`"); ?>
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
					<?php $data = $eq2->RunQueryMulti("SELECT `id`, `description` FROM `".DEV_DB."`.spawn_npc_spell_lists WHERE category='".$eq2->SQLEscape($cat)."' ORDER BY `description`"); ?>
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
    $searchtype = $_GET['searchtype'] ?? null;
    $strOffset = str_repeat("\x20",22);

    $strHTML = "";
	$strHTML .= $strOffset . "<a href='server.php?page=loot_table&id=new'>Add New</a>\n";
	$strHTML .= $strOffset . "</br></br>\n";

    //ALLOW USER TO SELECT SEARCH TYPE
    $strHTML .= $strOffset . "<fieldset>\n";
    $strHTML .= $strOffset . "  <legend>Select Search Type</legend>\n";
    $strHTML .= $strOffset . "  <form method='post' name='FormLootTableSearch'>\n";
    $strHTML .= $strOffset . "    <input type='radio' id='name' name='searchtype' value='server.php?page=loot_table&searchtype=name' onchange='dosub(this.value)'>By Name\n";
    $strHTML .= $strOffset . "    <input type='radio' id='zone' name='searchtype' value='server.php?page=loot_table&searchtype=zone' onchange='dosub(this.value)'>By Zone\n";
    $strHTML .= $strOffset . "    <input type='radio' id='list' name='searchtype' value='server.php?page=loot_table&searchtype=list' onchange='dosub(this.value)'>By List\n";
    $strHTML .= $strOffset . "    <input type='radio' id='all' name='searchtype' value='server.php?page=loot_table&searchtype=all&row_offset=0&row_size=20' onchange='dosub(this.value)'>All Loot Tables\n";
    $strHTML .= $strOffset . "  </form>\n";
    $strHTML .= $strOffset . "</fieldset>\n";

    //SEARCHTYPE CAN BE NAME, ZONE, LIST, ALL
    ////SEARCH BY NAME
    if($searchtype == 'name'){
        $strHTML .= $strOffset . "<fieldset>\n";
        $strHTML .= $strOffset . "  <legend>Search By Name</legend>\n";
        $strHTML .= $strOffset . "  <form method='post' name='FormLootTableSearch'>\n";
        $strHTML .= $strOffset . "    <label>name:</label>\n";
        $strHTML .= $strOffset . "    <input id='lt_txtSearch' type='text' value='" . $search . "' style='width:200px' autocomplete='off' onkeyup='LootTableLookupAJAX();'/>\n";
        $strHTML .= $strOffset . "    <input type='submit' name='cmd' value='Search'/>\n";
        $strHTML .= $strOffset . "    <div id='lt_search_suggest'></div>\n";
        $strHTML .= $strOffset . "  </form>\n";
        $strHTML .= $strOffset . "</fieldset>\n";

    ////SEARCH BY ZONE
    }elseif($searchtype == 'zone'){
        $strHTML .= $strOffset . "<!-- SEARCH BY ZONE -->\n";
		$strHTML .= $strOffset . "<fieldset>\n";
		$strHTML .= $strOffset . "  <legend>Search By Zone:</legend>\n";
		$strHTML .= $strOffset . "    <select name='loottable_zone' onchange='dosub(this.options[this.selectedIndex].value)' style='width:300px;'>\n";
		$strHTML .= $strOffset . "      <option value=''>Pick a Zone</option>\n";
				
        ////GRAB A LIST OF ZONES THAT HAVE SPAWNS WITH LOOTLISTS ATTACHED
        $lt_Query = "SELECT DISTINCT(z.name) AS zNAME, ";
        $lt_Query .= "  z.id AS zID ";
        $lt_Query .= "FROM `".DEV_DB."`.zones AS z ";
        $lt_Query .= "  JOIN `".DEV_DB."`.spawn_location_placement AS s_place ";
        $lt_Query .= "    ON z.id = s_place.zone_id ";
        $lt_Query .= "  LEFT JOIN `".DEV_DB."`.spawn_location_entry AS s_loc ";
        $lt_Query .= "    ON s_loc.spawn_location_id = s_place.spawn_location_id ";
        $lt_Query .= "  LEFT JOIN `".DEV_DB."`.spawn_loot AS s_loot ";
        $lt_Query .= "    ON s_loot.spawn_id = s_loc.spawn_id ";
        $lt_Query .= "  LEFT JOIN `".DEV_DB."`.loottable AS lt ";
        $lt_Query .= "    ON lt.id = s_loot.loottable_id ";
        $lt_Query .= "WHERE lt.id IS NOT NULL ";
        $lt_Query .= "ORDER BY zName ASC; ";
        $data = $eq2->RunQueryMulti($lt_Query);
        foreach($data as $row){
            $isSelected = "";
            if(isset($_GET['z_id'])){
                if($_GET['z_id'] == $row['zID']){
                    $isSelected = "selected ";
                }
            }
            $strHTML .= $strOffset . "      <option " . $isSelected . "value='server.php?page=loot_table&searchtype=zone&z_id=" . $row['zID'] . "'>" . $row['zNAME'] . "</option>\n";
        }
        $strHTML .= $strOffset . "        </select>\n";


		////IF WE KNOW THE ZONE ID, BUILD AT LIST OF LOOTTABLES FOR THAT ZONE
		if($_GET['z_id'] !== NULL){
			$strHTML .= $strOffset . "        <select name='loottable_list' onchange='dosub(this.options[this.selectedIndex].value)' style='width:300px;'>\n";
			$strHTML .= $strOffset . "          <option value=''>Pick a Loot Table</option>\n";
			$strHTML .= $strOffset . "          <option value='server.php?page=loot_table&id=new'>Add new loot table</option>\n";


		    ////GRAB A LIST OF LOOTTABLES FOR A SPECIFIC ZONE
			$lt_Query = "SELECT DISTINCT(lt.id) AS LOOTTABLE_ID, ";
			$lt_Query .= "      lt.name AS LOOTTABLE_NAME ";
			$lt_Query .= "FROM `".DEV_DB."`.spawn s ";
			$lt_Query .= "	JOIN `".DEV_DB."`.spawn_npcs AS s_npc ";
			$lt_Query .= "    ON s.id = s_npc.spawn_id ";
			$lt_Query .= "	LEFT JOIN `".DEV_DB."`.spawn_location_entry AS s_loc ";
			$lt_Query .= "    ON s.id = s_loc.spawn_id ";
			$lt_Query .= "	LEFT JOIN `".DEV_DB."`.spawn_location_placement AS s_place ";
			$lt_Query .= "    ON s_loc.spawn_location_id = s_place.spawn_location_id ";
			$lt_Query .= "	LEFT JOIN `".DEV_DB."`.spawn_loot AS s_loot ";
			$lt_Query .= "    ON s_npc.spawn_id = s_loot.spawn_id ";
			$lt_Query .= "	LEFT JOIN `".DEV_DB."`.loottable AS lt ";
			$lt_Query .= "    ON s_loot.loottable_id = lt.id ";
			$lt_Query .= "WHERE s_place.zone_id = " . $_GET['z_id'] . " ";
			$lt_Query .= "  AND s_loot.loottable_id IS NOT NULL";
			$data = $eq2->RunQueryMulti($lt_Query);
			foreach($data as $row){
                $isSelected = '';
				if($_GET['id'] == $row['LOOTTABLE_ID']){
					$isSelected = 'selected';
				}
				$strHTML .= $strOffset . "          <option " . $isSelected . " value='server.php?page=loot_table&searchtype=zone&z_id=" . $_GET['z_id'] . "&id=" . $row['LOOTTABLE_ID'] . "'>" . $row['LOOTTABLE_NAME'] . "</option>\n";
			}
			$strHTML .= $strOffset . "        </select>\n";
		}
		$strHTML .= $strOffset . "</frameset>\n";

    ////SEARCH BY LIST
    }elseif($searchtype == 'list'){
        $strHTML .= $strOffset . "<fieldset>\n";
        $strHTML .= $strOffset . "  <legend>Search By List</legend>\n";
        $strHTML .= $strOffset . "  <form method='post' name='FormLootTableSearch'>\n";
        $strHTML .= $strOffset . $s->PrintAvailableLists('select', $_GET['page'], '', '');
        $strHTML .= $strOffset . "  </form>\n";
        $strHTML .= $strOffset . "</fieldset>\n";

    ////SHOW ALL LOOT TABLES
    }elseif($searchtype == 'all'){
        $row_offset = (isSet($_GET['row_offset']))? intval($_GET['row_offset']) : 0;
        $row_count = (isSet($_GET['row_count']))? intval($_GET['row_count']) : 25;

        $strHTML .= $strOffset . "<fieldset>\n";

        ////WE NEED TO KNOW HOW MANY RECORDS THERE ARE FOR PAGINATION
        $count_query = "SELECT COUNT(*) as COUNT FROM " . DEV_DB .".loottable";
		$data = $eq2->RunQuerySingle($count_query);
        
        ////PAGINATION DATA/OPTIONS
        $strHTML .= $strOffset . "  <legend>Loot Tables(ROWS " . intval($row_offset) . " - " .  intval($row_offset) + intval($row_count) . " of " . $data['COUNT'] . ")\n";
        $strHTML .= $strOffset . "    <select name='loottable_list' onchange='dosub(this.options[this.selectedIndex].value)'>\n";
        $isSelect50 = ($row_count == 50)? "selected" : "";
        $isSelect75 = ($row_count == 75)? "selected" : "";
        $isSelect100 = ($row_count == 100)? "selected" : "";
        $strHTML .= $strOffset . "      <option " . $isSelect50 . " value='server.php?page=loot_table&searchtype=all&row_offset=0&row_count=50'>50</option>\n";
        $strHTML .= $strOffset . "      <option " . $isSelect75 . " value='server.php?page=loot_table&searchtype=all&row_offset=0&row_count=75'>75</option>\n";
        $strHTML .= $strOffset . "      <option " . $isSelect100 . " value='server.php?page=loot_table&searchtype=all&row_offset=0&row_count=100'>100</option>\n";
        $strHTML .= $strOffset . "    <select>\n";
        $strHTML .= $strOffset . "  </legend>\n";

        ////SHOW THE PAGES OF DATA TO AID NAVIGATION
        $strHTML .= $strOffset . "<br>";
        $page_count = $data['COUNT'] / $row_count;
        for($x=0; $x<= $page_count; $x++)
        {
            $strHTML .= "[<a href='server.php?page=loot_table&searchtype=all&row_offset=" . $x*$row_count . "&row_count=" . $row_count . "'>" . $x . "</a>]\n";
        }

        ////MASSIVE FORM TO ENABLE LIST MANAGEMENT AT SCALE
        $strHTML .= $strOffset . "  <form method='post' name='FormAllLootTables'>\n";
        
        ////LETS GET A LIST OF LISTS THAT WE CAN ADD LOOTTABLES TO
        ////THIS IS DUPLICATED BELOW THE LIST AND JAVASCRIPT KEEPS THEM IN SYNC
        $strHTML .= $strOffset . $s->PrintAvailableLists('select', $_GET['page'], 'assignMulti', '');
        $strHTML .= $strOffset . "  <input type='hidden' name='list_values|id' value='MultiAdd'>\n";
        $strHTML .= $strOffset . "  <input type='submit' name='cmd' value='MultiInsert'>\n";
        
        $strHTML .= $strOffset . "  <table class='ContrastTable'>\n";
        $strHTML .= $strOffset . "      <tr>\n";
        $strHTML .= $strOffset . "        <th>InList</th>\n";
		$strHTML .= $strOffset . "        <th>show loot</th>\n";
		$strHTML .= $strOffset . "        <th>Loot Table ID</th>\n";
		$strHTML .= $strOffset . "        <th>name</th>\n";
		$strHTML .= $strOffset . "        <th>max loot items</th>\n";
		$strHTML .= $strOffset . "        <th>item prob</th>\n";
		$strHTML .= $strOffset . "        <th>coin prob</th>\n";
		$strHTML .= $strOffset . "        <th colspan='4'>min coin</th>\n";
		$strHTML .= $strOffset . "        <th colspan='4'>max coin</th>\n";
		$strHTML .= $strOffset . "      </tr>\n";

        ////EACH LIST SHOULD BE READ ONLY EXCEPT FOR LIST CHECKBOX
        $lt_Query = "SELECT * FROM `" . DEV_DB . "`.`loottable` ORDER BY id ASC LIMIT " . intval($row_offset) . "," . intval($row_count) .";";
		$data = $eq2->RunQueryMulti($lt_Query);
		$rowcnt=0;
        foreach($data as $row)
        {
			$rowcnt ++;
            $strHTML .= $s->PrintLootTableRow($row, 'simple', $rowcnt) . "\n";
        }

        $strHTML .= $strOffset . "  </table>\n";
        ////LETS GET A LIST OF LISTS THAT WE CAN ADD LOOTTABLES TO
        ////THIS IS DUPLICATED BELOW THE LIST AND JAVASCRIPT KEEPS THEM IN SYNC
        $strHTML .= $strOffset . $s->PrintAvailableLists('select', $_GET['page'], 'assignMulti', '');
		$strHTML .= $strOffset . "  <input type='hidden' name='numrows' value='" . $rowcnt . "'>\n";
        $strHTML .= $strOffset . "  <input type='hidden' name='index' value='list_values|id'>\n";
		$strHTML .= $strOffset . "  <input type='hidden' name='table_name' value='list_values'>\n";
        $strHTML .= $strOffset . "  <input type='submit' name='cmd' value='MultiInsert'>\n";

        $strHTML .= $strOffset . "  </form>\n";
        $strHTML .= $strOffset . "</fieldset>\n";
    }

    $data = null;

	//if ($id != null && $id != "new") {
	//	$query = sprintf("SELECT * FROM %s.loottable WHERE id = %s", DEV_DB, $id);
	//	$data = $eq2->RunQuerySingle($query);
	//}
    
    //GET A LIST OF LISTS
	if(intval($_GET['e_list'])>0){
		$query = "SELECT list_values.value AS listitem ";
		$query .= "FROM lists ";
		$query .= "  JOIN list_values ";
		$query .= "    ON lists.id = list_values.list_id ";
		$query .= "WHERE lists.id=" . $_GET['e_list'];
		$data = $eq2->RunQueryMulti($query);
		//this data will be used in the section below
	}elseif ($id != null && $id != "new") {
		$query = sprintf("SELECT * FROM %s.loottable WHERE id = %s", DEV_DB, $id);
		$data = $eq2->RunQuerySingle($query);
		//this data will be used in the section below

	}

//SHOW LOOTTABLE(S)
	if ($data != null){
		$strHTML .= $strOffset . "<!-- SHOW DROPS TABLE -->\n";
		$strHTML .= $strOffset . "<form method='post' name='LootTableForm'>\n";
		$strHTML .= $strOffset . "  <fieldset>\n";
		$strHTML .= $strOffset . "    <legend>loottable(s)</legend>\n";
		$strHTML .= $strOffset . "    <table class='ContrastTable'>\n";
		$strHTML .= $strOffset . "      <tr>\n";
		$strHTML .= $strOffset . "        <th>show loot</th>\n";
		$strHTML .= $strOffset . "        <th>item id</th>\n";
		$strHTML .= $strOffset . "        <th>name</th>\n";
		$strHTML .= $strOffset . "        <th>max loot items</th>\n";
		$strHTML .= $strOffset . "        <th>item prob</th>\n";
		$strHTML .= $strOffset . "        <th>coin prob</th>\n";
		$strHTML .= $strOffset . "        <th colspan='4'>min coin</th>\n";
		$strHTML .= $strOffset . "        <th colspan='4'>max coin</th>\n";
		$strHTML .= $strOffset . "        <th>Loot Table Options</th>\n";
        $strHTML .= $strOffset . "        <th>List Options</th>\n";
		$strHTML .= $strOffset . "      </tr>\n";
		if($_GET['e_list']>0)
		{
			$strHTML .= $strOffset . $s->PrintListLootTable($data);
		}else{
			$strHTML .= $strOffset . $s->PrintLootTableRow($data, '', '');
		}
		$strHTML .= $strOffset . "    </table>\n";
		$strHTML .= $strOffset . "  </fieldset>\n";

	    if ($id && $id != "new"){
            $strHTML .= $strOffset . "</br>\n";
            $strHTML .= $strOffset . "<!-- ID FOUND && NOT NEW -->\n";
            $strHTML .= $strOffset . "<fieldset>\n";
            $strHTML .= $strOffset . "  <legend>lootdrop (" . $id . ")</legend>\n";
            $strHTML .= $strOffset . "  <table class='ContrastTable'>\n";
            $strHTML .= $strOffset . "    <tr>\n";
            $strHTML .= $strOffset . "      <th>item id</th>\n";
            $strHTML .= $strOffset . "      <th>item</th>\n";
            $strHTML .= $strOffset . "      <th>probability</th>\n";
            $strHTML .= $strOffset . "      <th>charges</th>\n";
            $strHTML .= $strOffset . "      <th>equip item</th>\n";
            $strHTML .= $strOffset . "      <th>no drop</br>quest completed</th>\n";
            $strHTML .= $strOffset . "      <th></th>\n";
            $strHTML .= $strOffset . "    </tr>\n";

			$newRow = array(
				"id"=>"new",
				"item_id"=>"",
				"probability"=> 0,
				"equip_item"=>0,
				"item_charges"=>0,
				"no_drop_quest_completed"=>0,
				"new"=>true
			);

			$query = "SELECT lt.*, i.name as item_name, i.icon, i.tier, i.crafted FROM `" . DEV_DB . "`.`lootdrop` lt LEFT JOIN `" . DEV_DB . "`.`items` i ON i.id = lt.item_id WHERE loot_table_id = " . $id;
			$data = $eq2->RunQueryMulti($query);
			$strHTML .= $s->PrintLootDropRow($newRow);
            
            foreach ($data as $ld) {
				$strHTML .= $strOffset . $s->PrintLootDropRow($ld);
			}
            $strHTML .= $strOffset . "  </table>\n";
            $strHTML .= $strOffset . "</fieldset>\n";
        }
    }elseif($search != null){ 
        $query = sprintf("SELECT id, name FROM %s.loottable WHERE name rlike '%s' ORDER BY name", DEV_DB, $eq2->SQLEscape($search));
        $rows = $eq2->RunQueryMulti($query);

        $strHTML .= $strOffset . "<fieldset>\n";
        $strHTML .= $strOffset . "  <legend>Search Results</legend>\n";
        $strHTML .= $strOffset . "  <table class='ContrastTable'>\n";
        $strHTML .= $strOffset . "    <tr>\n";
        $strHTML .= $strOffset . "      <th>id</th>\n";
        $strHTML .= $strOffset . "      <th>name</th>\n";
        $strHTML .= $strOffset . "    </tr>\n";
        foreach($rows as $data){
            $strHTML .= $strOffset . "    <tr>\n";
            $strHTML .= $strOffset . "      <td>\n";
            $strHTML .= $strOffset . $data['id'];
            $strHTML .= $strOffset . "      </td>\n";
            $strHTML .= $strOffset . "      <td>\n";
            $strHTML .= $strOffset . "        <a href='server.php?page=loot_table&id=" . $data['id'] . "'>" . $data['name'] . "</a>\n";
            $strHTML .= $strOffset . "      </td>\n";
            $strHTML .= $strOffset . "    </tr>\n";		
        }
        $strHTML .= $strOffset . "  </table>\n";
        $strHTML .= $strOffset . "</fieldset>\n";
    }elseif ($id == "new"){
        $strHTML .= $strOffset . "<fieldset style='width:max-content'>\n";
        $strHTML .= $strOffset . "  <legend>New Loot Table</legend>\n";
        $strHTML .= $strOffset . "  <form name='FormNewLootTable' method='post'>\n";
        $strHTML .= $strOffset . "    <table>\n";
        $strHTML .= $strOffset . "      <tr>\n";
        $strHTML .= $strOffset . "        <td align='center'>\n";
        $strHTML .= $strOffset . "          <strong style='font-size:large'>Add A New Loot Table</strong>\n";
        $strHTML .= $strOffset . "        </td>\n";
        $strHTML .= $strOffset . "      </tr>\n";
        $strHTML .= $strOffset . "      <tr>\n";
        $strHTML .= $strOffset . "        <td>\n";
        $strHTML .= $strOffset . "          <label>name:</label>\n";
        $strHTML .= $strOffset . "          <input type='text' name='loottable|name'/>\n";
        $strHTML .= $strOffset . "          <input type='hidden' name='table_name' value='loottable'/>\n";
        $strHTML .= $strOffset . "        </td>\n";
        $strHTML .= $strOffset . "      </tr>\n";
        $strHTML .= $strOffset . "      <tr>\n";
        $strHTML .= $strOffset . "        <td align='center'>\n";
        $strHTML .= $strOffset . "          <input type='submit' name='cmd' value='Insert'/>\n";
        $strHTML .= $strOffset . "        </td>\n";
        $strHTML .= $strOffset . "      </tr>\n";
        $strHTML .= $strOffset . "    </table>\n";
        $strHTML .= $strOffset . "  </form>\n";
        $strHTML .= $strOffset . "</fieldset>\n";
    }
    print($strHTML);
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
 
 function editor_lists(){
    global $eq2, $s;
	//LLAMA NOTE:
    //IM WRITTING THIS WITH A FOCUS ON LOOTTABLES. I HOPE
	//TO REWRITE IT TO MAKE IT MORE GENERIC FOR ALL LIST TYPES.
	
	$strOffset = str_repeat("\x20",22);

	//ACTION VALUES ARE 'NEW', 'ADD', 'REMOVE'
	if($_GET['action'] == 'new'){
			//DISPLAY THE NEW LIST FORM
			$strHTML = "\n";
			$strHTML .= $strOffset . "<fieldset>\n";
			$strHTML .= $strOffset . "  <legend>New Lists:</legend>\n";
			$strHTML .= $strOffset . "  <form method='post' name='multiForm|addLootTableToNewList'>\n";
			$strHTML .= $strOffset . "    <table cellpadding='5' id='EditorTable'>\n";
			$strHTML .= $strOffset . "	    <tr>\n";
			$strHTML .= $strOffset . "	      <td colspan='4' align='center'>Adding " . $_GET['id'] . " to a new list.</td>\n";
			$strHTML .= $strOffset . "      </tr>\n";
			$strHTML .= $strOffset . "      <tr style='font-weight:bold'>\n";
			$strHTML .= $strOffset . "        <th width='100'>Name</th>\n";
			$strHTML .= $strOffset . "        <th width='20'>Shared</th>\n";
			$strHTML .= $strOffset . "        <th width='20'>Type</th>\n";
			$strHTML .= $strOffset . "        <th width='20'>Action</th>\n";
			$strHTML .= $strOffset . "      </tr>\n";
			$strHTML .= $strOffset . "	    <tr>\n";
			$strHTML .= $strOffset . "	      <td><input type='text' name='lists|name'></td>\n";
			$strHTML .= $strOffset . "	      <td>\n";
			$strHTML .= $strOffset . "	        <select name='lists|shared'>\n";
			$strHTML .= $strOffset . "	          <option value='0'>No</option>\n";
			$strHTML .= $strOffset . "	          <option value='1'>Yes</option>\n";
			$strHTML .= $strOffset . "	        <select>\n";
			$strHTML .= $strOffset . "	      <td>\n";
			$strHTML .= $strOffset . "	        <select name='lists|list_type'>\n";
			$query = "SELECT id, type_name, type_value FROM list_types;";
			$data = $eq2->RunQueryMulti($query);
            foreach ($data as $row) {
			        $strHTML .= $strOffset . "	        <option value='" . $row['id'] . "'>" . $row['type_name'] . "</option>\n";
            }
            $strHTML .= $strOffset . "	        </select>\n";
            $strHTML .= $strOffset . "	        <input type='hidden' name='lists|user_id' value='" . $eq2->userdata['id'] . "'>\n";
            $strHTML .= $strOffset . "          <input type='hidden' name='idx_name' value='id' />\n";
            $strHTML .= $strOffset . "          <input type='hidden' name='table_name' value='lists' />\n";
			$strHTML .= $strOffset . "	      </td>\n";
			$strHTML .= $strOffset . "	      <td><input type='submit' name='cmd' value='insert'></td>\n";
			$strHTML .= $strOffset . "	    </tr>\n";
			$strHTML .= $strOffset . "	  </table>\n";
			$strHTML .= $strOffset . "	</form>\n";
			$strHTML .= $strOffset . "</fieldset>\n";
	}elseif($_GET['action'] == 'add'){
		//ADD A LOOTTABLE TO AN EXISING TABLE
			$strHTML = "\n";
			$strHTML .= $strOffset . "<fieldset>\n";
			$strHTML .= $strOffset . "  <legend>Confirm:</legend>\n";
			$strHTML .= $strOffset . "  <form method='post' name='multiForm|addLootTableToExistingList'>\n";
			$strHTML .= $strOffset . "    <table cellpadding='5' id='EditorTable'>\n";
			$strHTML .= $strOffset . "      <tr style='font-weight:bold'>\n";
			$strHTML .= $strOffset . "        <th width='150'>List</th>\n";
			$strHTML .= $strOffset . "        <th width='100'>Loottable</th>\n";
			$strHTML .= $strOffset . "        <th width='50'>Action</th>\n";
			$strHTML .= $strOffset . "      </tr>\n";
			$strHTML .= $strOffset . "	    <tr>\n";
			$queryA = "SELECT name FROM lists WHERE id='" . $_GET['e_list'] . "';";
			$dataA = $eq2->RunQuerySingle($queryA);
			$queryB = "SELECT name FROM `" . DEV_DB . "`.`loottable` WHERE id='" . $_GET['id'] . "';";
			$dataB = $eq2->RunQuerySingle($queryB);
			$strHTML .= $strOffset . "        <td>" . $dataA['name'] . "</td>\n";
			$strHTML .= $strOffset . "        <td>" . $dataB['name'] . "</td>\n";
			$strHTML .= $strOffset . "        <td>\n";
			$strHTML .= $strOffset . "          <input type='hidden' name='list_values|value' value='" . $_GET['id'] . "'/>\n";
			$strHTML .= $strOffset . "          <input type='hidden' name='list_values|list_id' value='" . $_GET['e_list'] . "'/>\n";
            $strHTML .= $strOffset . "          <input type='hidden' name='idx_name' value='id' />\n";
            $strHTML .= $strOffset . "          <input type='hidden' name='table_name' value='list_values' />\n";
			$strHTML .= $strOffset . "          <input type='submit' name='cmd' value='insert'/>\n";
			$strHTML .= $strOffset . "         </td>\n";
			$strHTML .= $strOffset . "        <td>\n";
			$strHTML .= $strOffset . "	    </tr>\n";
			$strHTML .= $strOffset . "	  </table>\n";
			$strHTML .= $strOffset . "	</form>\n";
			$strHTML .= $strOffset . "</fieldset>\n";
	}elseif($_GET['action'] == 'view'){

			$strHTML = "\n";
			$strHTML .= $strOffset . "<fieldset>\n";
			$strHTML .= $strOffset . "  <legend>Edit List(" . $_GET['e_list'] . "):</legend>\n";
			$strHTML .= $strOffset . "  <table cellpadding='5' id='EditorTable'>\n";
			$strHTML .= $s->PrintAvailableLists('editform',$_GET['page'], 'list', 'edit_single');
			$strHTML .= $strOffset . "	</table>\n";
			$strHTML .= $strOffset . "</fieldset>\n";

	}else{
		$strHTML = "\n";
		$strHTML .= $strOffset . "<fieldset>\n";
        $strHTML .= $strOffset . "<a href='server.php?page=editor_lists&action=new'>New List</a>\n";
		$strHTML .= $strOffset . "  <legend>Your Lists:</legend>\n";
		$strHTML .= $s->PrintAvailableLists('table',$_GET['page'], 'list', 'owner_only');
		$strHTML .= $strOffset . "</fieldset>\n";
		$strHTML .= $strOffset . "<fieldset>\n";
		$strHTML .= $strOffset . "  <legend>Shared Lists:</legend>\n";
		$strHTML .= $s->PrintAvailableLists('table',$_GET['page'], 'list', 'shared_only');
		$strHTML .= $strOffset . "</fieldset>\n";
	}
	print($strHTML);
 }

 function static_values(){
    print("static_values()");
 }

function heroic_ops(){
	global $eq2, $s, $eq2Icons;
	$strOffset = str_repeat("\x20",22);
	//LLAMA NOTE:
	//WE NEEDED A EASY WAY TO MANAGE HEROIC OPPORTUNITIES
	//THIS IS MY FIRAT ATTEMPT UNTIL I UNDERSTAND MORE ABOUT
	//HOW HO WORKS
	$strHTML = "\n";
	$strHTML .= $strOffset . "<fieldset>\n";
	$strHTML .= $strOffset . "  <legend>Sort/Search:</legend>\n";
	$strHTML .= $strOffset . "  <form method='post' name='heroic_ops|searchtype'>\n";
    $strHTML .= $strOffset . "    <input type='radio' id='name' name='searchtype' value='server.php?page=heroic_ops&searchtype=starter' onchange='dosub(this.value)'>All (By Starter ID)\n";
	//$strHTML .= $strOffset . "    <input type='radio' id='name' name='searchtype' value='server.php?page=heroic_ops&searchtype=class' onchange='dosub(this.value)'>By Class\n";
	//$strHTML .= $strOffset . "    <input type='radio' id='name' name='searchtype' value='server.php?page=heroic_ops&searchtype=ability' onchange='dosub(this.value)'>By Ability\n";
	$strHTML .= $strOffset . "  </form>";
	$strHTML .= $strOffset . "\n";
	$strHTML .= $strOffset . "</fieldset>\n";

	//STARTERS
	if($_GET['searchtype'] == 'starter'){
		$strHTML .= $strOffset . "\n";
		$strHTML .= $strOffset . "<fieldset>\n";
		$strHTML .= $strOffset . "  <legend>By Starter</legend>\n";
		$strHTML .= $strOffset . "  <table class='ContrastTable'>\n";
		$strHTML .= $strOffset . "    <tr>\n";
		$strHTML .= $strOffset . "      <th colspan='3' align='left'><button onclick='history.back()'> <<-BACK</button></th>\n";
		$strHTML .= $strOffset . "      <th colspan='11'>Followup Symbol(s)</th>\n";
		$strHTML .= $strOffset . "      <th colspan='2' align='right'></th>\n";
		$strHTML .= $strOffset . "    </tr>\n";
		$strHTML .= $strOffset . "    <tr>\n";
		$strHTML .= $strOffset . "      <th>ID</th>\n";
		$strHTML .= $strOffset . "      <th>Starter</th>\n";
		$strHTML .= $strOffset . "      <th>Party</th>\n";
		$strHTML .= $strOffset . "      <th>Ability1</th>\n";
		$strHTML .= $strOffset . "      <th colspan='2'>Ability2</th>\n";
		$strHTML .= $strOffset . "      <th colspan='2'>Ability3</th>\n";
		$strHTML .= $strOffset . "      <th colspan='2'>Ability4</th>\n";
		$strHTML .= $strOffset . "      <th colspan='2'>Ability5</th>\n";
		$strHTML .= $strOffset . "      <th colspan='2'>Ability6</th>\n";
		$strHTML .= $strOffset . "      <th>Possible Outcomes</th>\n";
		$strHTML .= $strOffset . "      <th>Actions</th>\n";
		$strHTML .= $strOffset . "    </tr>\n";
		$strHTML .= $strOffset . "    <script>\n";
		$strHTML .= $strOffset . "      function update_icon(row){\n";
		$strHTML .= $strOffset . "        console.log(document.getElementById('classIcon_select_' + row).value);\n";
		$strHTML .= $strOffset . "        switch(parseInt(document.getElementById('classIcon_select_' + row).value)){\n";
		$strHTML .= $strOffset . "          case 1:\n";
		$strHTML .= $strOffset . "            document.getElementById('classIcon_hidden_' + row).value = 1;\n";
		$strHTML .= $strOffset . "          break;\n";
		$strHTML .= $strOffset . "          case 11:\n";
		$strHTML .= $strOffset . "            document.getElementById('classIcon_hidden_' + row).value = 17;\n";
		$strHTML .= $strOffset . "          break;\n";
		$strHTML .= $strOffset . "          case 21:\n";
		$strHTML .= $strOffset . "            document.getElementById('classIcon_hidden_' + row).value = 26;\n";
		$strHTML .= $strOffset . "          break;\n";
		$strHTML .= $strOffset . "          case 31:\n";
		$strHTML .= $strOffset . "            document.getElementById('classIcon_hidden_' + row).value = 39;\n";
		$strHTML .= $strOffset . "          break;\n";
		$strHTML .= $strOffset . "          }\n";
		$strHTML .= $strOffset . "        console.log(document.getElementById('classIcon_hidden_' + row).value);\n";
		$strHTML .= $strOffset . "        }\n";
		$strHTML .= $strOffset . "      </script>\n";

		//TESTING ICON STANDIZATION
		$IconType = 'ho';
		$HO_Icons = $eq2Icons->GetIcons($IconType);
		//var_dump($HO_Icons);
				
		$query = "SELECT * FROM `" . DEV_DB . "`.`heroic_ops` WHERE starter_link_id = 0 ORDER BY starter_class, enhancer_class;";
		$data = $eq2->RunQueryMulti($query);
		foreach($data as $row){
			$strHTML1 = $strOffset . "  <form method='post' name='heroic_ops|staters" . $row['id'] . "' />\n";
			$strHTML1 .= "<!-- HTML1 START ROW -->\n";
			$strHTML1 .= $strOffset . "    <tr>\n";
			$strHTML2 = "<!-- HTML2 START ROW -->\n";
			$strHTML2 .= $strOffset . "    <tr>\n";
			$strHTML1 .= "<!-- HTML1 ROW_ID -->\n";
			$strHTML1 .= $strOffset . "      <td><a href='server.php?page=heroic_ops&searchtype=chains&starter=" . $row['id'] . "'><h2>" . $row['id'] . "</h2></a></td>\n";
			$strHTML2 .= "<!-- HTML2 ROW_ID -->\n";
			$strHTML2 .= $strOffset . "      <td><input type='hidden' name='orig_id' value='" . $row['id'] . "'></td>\n";
			$strHTML1 .= "<!-- HTML1 SPELL_ID -->\n";
			$strHTML1 .= $strOffset . "      <td align='center'><img src='" . $HO_Icons[$row['starter_icon']]['src'] . "' alt='" . $HO_Icons[$row['starter_icon']]['name'] . "' style='width: 40px; height: 40px; object-fit: none; object-position: " . $HO_Icons[$row['starter_icon']]['posX'] . "% " . $HO_Icons[$row['starter_icon']]['posY'] . "%'/></td>\n";
			
			//CLASS DROPDOWN
			$isSelected = "";
			$strHTML2 .= "<!-- HTML2 SPELL_ID -->\n";
			$strHTML2 .= $strOffset . "      <td align='center'>\n";
			$strHTML2 .= $strOffset . "        <select id='classIcon_select_" . $row['id'] . "' name='heroic_ops|starter_class' onchange=\"update_icon('" . $row['id'] . "');\">\n";
			$allHOStarterClasses_query = "SELECT class_name, class_id, class_map FROM `eq2classes` WHERE ho_class = 1 AND class_id != 0 AND class_map != 0";
			$allHOStarterClasses = $eq2->RunQueryMulti($allHOStarterClasses_query);
			foreach($allHOStarterClasses as $HOStaterClass)
			{
				if(intval($HOStaterClass['class_id']) == intval($row['starter_class']))
				{
					$isSelected = "selected";
				}
				if($row['starter_class']==0)
				{
					$strHTML2 .= $strOffset . "          <option value='" . $row['starter_class'] . "' " . $isSelected . ">" . $classtype_data['class_name'] . "</option>\n";
				}else{
					$strHTML2 .= $strOffset . "          <option value='" . $HOStaterClass['class_id'] . "' " . $isSelected . ">" . $HOStaterClass['class_name'] . "</option>\n";
				}
				$isSelected = "";
			}
			$strHTML2 .= $strOffset . "        </select>\n";
			$strHTML2 .= $strOffset . "        <input type='hidden' name='orig_starter_class' value='" . $row['starter_class'] . "'>\n";
			$strHTML2 .= $strOffset . "        <input type='hidden' id='classIcon_hidden_" . $row['id'] . "' name='heroic_ops|starter_icon' value='" . $row['starter_icon'] . "'>\n";
			$strHTML2 .= $strOffset . "        <input type='hidden' name='orig_starter_icon' value='" . $row['starter_icon'] . "'>\n";
			$strHTML2 .= $strOffset . "      </td>\n";

			//REACH BACK AND GET THE ENHANCER CLASS(ES)
			$enhancer_class_query = "SELECT class_name, class_id, class_map FROM `eq2classes` WHERE ho_class = 1 AND class_map =" . $row['enhancer_class'];
			$enhancer_class_data = $eq2->RunQuerySingle($enhancer_class_query);
			$strHTML1 .= "<!-- HTML1 ENHANCER/PARTY -->\n";
			$strHTML1 .= $strOffset . "      <td align='center'>" . $enhancer_class_data['class_name'] . "</td>\n";

			$strHTML2 .= "<!-- HTML2 ENHANCER/PARTY -->\n";
			$isSelected = "";
			$strHTML2 .= $strOffset . "      <td>\n";
			$strHTML2 .= $strOffset . "        <select name='heroic_ops|enhancer_class'>\n";
			$allHOStarterClasses_query = "SELECT class_name, class_id, class_map FROM `eq2classes` WHERE ho_class = 1 ORDER BY class_map ASC";
			$allHOStarterClasses = $eq2->RunQueryMulti($allHOStarterClasses_query);
			foreach($allHOStarterClasses as $HOStaterClass)
			{
				if(intval($HOStaterClass['class_map']) == intval($row['enhancer_class']))
				{
					$isSelected = "selected";
				}
				$strHTML2 .= $strOffset . "          <option value='" . $HOStaterClass['class_map'] . "' " . $isSelected . ">" . $HOStaterClass['class_name'] . "</option>\n";
				$isSelected = "";
			
			}
			$strHTML2 .= $strOffset . "        <select>\n";
			$strHTML2 .= $strOffset . "        <input type='hidden' name='orig_enhancer_class' value='" . $row['enhancer_class'] . "'>\n";
			$strHTML2 .= $strOffset . "      </td>\n";

			//SETUP CHAIN ORDER ICON FOR USE BELOW
			if($row['chain_order'] == 1)
			{
				$chain_order = "<i class='fa fa-arrow-right' aria-hidden='true'></i>";
			}else{
				$chain_order = "<i class='fa fa-plus' aria-hidden='true'></i>";
			}


			if($row['ability1'] != '65535'){
				$strHTML1 .= "<!-- HTML1 ABILITY1 -->\n";
				$strHTML1 .= $strOffset . "      <td><img src='" . $HO_Icons[$row['ability1']]['src'] . "' alt='" . $HO_Icons[$row['ability1']]['name'] . "' style='width: 40px; height: 40px; object-fit: none; object-position: " . $HO_Icons[$row['ability1']]['posX'] . "% " . $HO_Icons[$row['ability1']]['posY'] . "%'/></td>\n";
				$strHTML2 .= "<!-- HTML2 ABILITY1 -->\n";
				$strHTML2 .= $strOffset . "      <td>\n";
				$strHTML2 .= $strOffset . "        <input type='text' name='heroic_ops|ability1' value='". $row['ability1'] . "' size='3'/>\n";
				$strHTML2 .= $strOffset . "        <input type='hidden' name='orig_ability1' value='". $row['ability1'] . "' size='3'/>\n";
				$strHTML2 .= $strOffset . "      </td>\n";
			}else{
				$strHTML1 .= "<!-- HTML1 ABILITY1 -->\n";
				$strHTML1 .= $strOffset . "      <td></td>\n";
				$strHTML2 .= "<!-- HTML2 ABILITY1 -->\n";
				$strHTML2 .= $strOffset . "      <td>\n";
				$strHTML2 .= $strOffset . "        <input type='text' name='heroic_ops|ability1' value='". $row['ability1'] . "' size='3'/>\n";
				$strHTML2 .= $strOffset . "        <input type='hidden' name='orig_ability1' value='". $row['ability1'] . "' size='3'/>\n";
				$strHTML2 .= $strOffset . "      </td>\n";

			}

			if($row['ability2'] != '65535'){
				$strHTML1 .= "<!-- HTML1 ABILITY2 -->\n";
				$strHTML1 .= $strOffset . "      <td>" . $chain_order . "</td>\n";
				$strHTML1 .= $strOffset . "      <td><img src='" . $HO_Icons[$row['ability2']]['src'] . "' alt='" . $HO_Icons[$row['ability2']]['name'] . "' style='width: 40px; height: 40px; object-fit: none; object-position: " . $HO_Icons[$row['ability2']]['posX'] . "% " . $HO_Icons[$row['ability2']]['posY'] . "%'/></td>\n";
				$strHTML2 .= "<!-- HTML2 ABILITY2 -->\n";
				$strHTML2 .= $strOffset . "      <td></td>\n";
				$strHTML2 .= $strOffset . "      <td>\n";
				$strHTML2 .= $strOffset . "        <input type='text' name='heroic_ops|ability2' value='". $row['ability2'] . "' size='3'/>\n";
				$strHTML2 .= $strOffset . "        <input type='hidden' name='orig_ability2' value='". $row['ability2'] . "' size='3'/>\n";
				$strHTML2 .= $strOffset . "      </td>\n";
			}else{
				$strHTML1 .= "<!-- HTML1 ABILITY2 -->\n";
				$strHTML1 .= $strOffset . "      <td></td>\n";
				$strHTML1 .= $strOffset . "      <td></td>\n";
				$strHTML2 .= "<!-- HTML2 ABILITY2 -->\n";
				$strHTML2 .= $strOffset . "      <td></td>\n";
				$strHTML2 .= $strOffset . "      <td>\n";
				$strHTML2 .= $strOffset . "        <input type='text' name='heroic_ops|ability2' value='". $row['ability2'] . "' size='3'/>\n";
				$strHTML2 .= $strOffset . "        <input type='hidden' name='orig_ability2' value='". $row['ability2'] . "' size='3'/>\n";
				$strHTML2 .= $strOffset . "      </td>\n";

			}

			if($row['ability3'] != '65535'){
				$strHTML1 .= "<!-- HTML1 ABILITY3 -->\n";
				$strHTML1 .= $strOffset . "      <td>" . $chain_order . "</td>\n";
				$strHTML1 .= $strOffset . "      <td><img src='" . $HO_Icons[$row['ability3']]['src'] . "' alt='" . $HO_Icons[$row['ability3']]['name'] . "' style='width: 40px; height: 40px; object-fit: none; object-position: " . $HO_Icons[$row['ability3']]['posX'] . "% " . $HO_Icons[$row['ability3']]['posY'] . "%'/></td>\n";
				$strHTML2 .= "<!-- HTML2 ABILITY3 -->\n";
				$strHTML2 .= $strOffset . "      <td></td>\n";
				$strHTML2 .= $strOffset . "      <td>\n";
				$strHTML2 .= $strOffset . "        <input type='text' name='heroic_ops|ability3' value='". $row['ability3'] . "' size='3'/>\n";
				$strHTML2 .= $strOffset . "        <input type='hidden' name='orig_ability3' value='". $row['ability3'] . "' size='3'/>\n";
				$strHTML2 .= $strOffset . "      </td>\n";
			}else{
				$strHTML1 .= "<!-- HTML1 ABILITY3 -->\n";
				$strHTML1 .= $strOffset . "      <td></td>\n";
				$strHTML1 .= $strOffset . "      <td></td>\n";
				$strHTML2 .= "<!-- HTML2 ABILITY3 -->\n";
				$strHTML2 .= $strOffset . "      <td></td>\n";
				$strHTML2 .= $strOffset . "      <td>\n";
				$strHTML2 .= $strOffset . "        <input type='text' name='heroic_ops|ability3' value='". $row['ability3'] . "' size='3'/>\n";
				$strHTML2 .= $strOffset . "        <input type='hidden' name='orig_ability3' value='". $row['ability3'] . "' size='3'/>\n";
				$strHTML2 .= $strOffset . "      </td>\n";

			}

			if($row['ability4'] != '65535'){
				$strHTML1 .= "<!-- HTML1 ABILITY4 -->\n";
				$strHTML1 .= $strOffset . "      <td>" . $chain_order . "</td>\n";
				$strHTML1 .= $strOffset . "      <td><img src='" . $HO_Icons[$row['ability4']]['src'] . "' alt='" . $HO_Icons[$row['ability4']]['name'] . "' style='width: 40px; height: 40px; object-fit: none; object-position: " . $HO_Icons[$row['ability4']]['posX'] . "% " . $HO_Icons[$row['ability4']]['posY'] . "%'/></td>\n";
				$strHTML2 .= "<!-- HTML2 ABILITY4 -->\n";
				$strHTML2 .= $strOffset . "      <td></td>\n";
				$strHTML2 .= $strOffset . "      <td>\n";
				$strHTML2 .= $strOffset . "        <input type='text' name='heroic_ops|ability4' value='". $row['ability4'] . "' size='3'/>\n";
				$strHTML2 .= $strOffset . "        <input type='hidden' name='orig_ability4' value='". $row['ability4'] . "' size='3'/>\n";
				$strHTML2 .= $strOffset . "      </td>\n";
			}else{
				$strHTML1 .= "<!-- HTML1 ABILITY4 -->\n";
				$strHTML1 .= $strOffset . "      <td></td>\n";
				$strHTML1 .= $strOffset . "      <td></td>\n";
				$strHTML2 .= "<!-- HTML2 ABILITY4 -->\n";
				$strHTML2 .= $strOffset . "      <td></td>\n";
				$strHTML2 .= $strOffset . "      <td>\n";
				$strHTML2 .= $strOffset . "        <input type='text' name='heroic_ops|ability4' value='". $row['ability4'] . "' size='3'/>\n";
				$strHTML2 .= $strOffset . "        <input type='hidden' name='orig_ability4' value='". $row['ability4'] . "' size='3'/>\n";
				$strHTML2 .= $strOffset . "      </td>\n";

			}

			if($row['ability5'] != '65535'){
				$strHTML1 .= "<!-- HTML1 ABILITY5 -->\n";
				$strHTML1 .= $strOffset . "      <td>" . $chain_order . "</td>\n";
				$strHTML1 .= $strOffset . "      <td><img src='" . $HO_Icons[$row['ability5']]['src'] . "' alt='" . $HO_Icons[$row['ability5']]['name'] . "' style='width: 40px; height: 40px; object-fit: none; object-position: " . $HO_Icons[$row['ability5']]['posX'] . "% " . $HO_Icons[$row['ability5']]['posY'] . "%'/></td>\n";
				$strHTML2 .= "<!-- HTML2 ABILITY5 -->\n";
				$strHTML2 .= $strOffset . "      <td></td>\n";
				$strHTML2 .= $strOffset . "      <td>\n";
				$strHTML2 .= $strOffset . "        <input type='text' name='heroic_ops|ability5' value='". $row['ability5'] . "' size='3'/>\n";
				$strHTML2 .= $strOffset . "        <input type='hidden' name='orig_ability5' value='". $row['ability5'] . "' size='3'/>\n";
				$strHTML2 .= $strOffset . "      </td>\n";
			}else{
				$strHTML1 .= "<!-- HTML1 ABILITY5 -->\n";
				$strHTML1 .= $strOffset . "      <td></td>\n";
				$strHTML1 .= $strOffset . "      <td></td>\n";
				$strHTML2 .= "<!-- HTML2 ABILITY5 -->\n";
				$strHTML2 .= $strOffset . "      <td></td>\n";
				$strHTML2 .= $strOffset . "      <td>\n";
				$strHTML2 .= $strOffset . "        <input type='text' name='heroic_ops|ability5' value='". $row['ability5'] . "' size='3'/>\n";
				$strHTML2 .= $strOffset . "        <input type='hidden' name='orig_ability5' value='". $row['ability5'] . "' size='3'/>\n";
				$strHTML2 .= $strOffset . "      </td>\n";

			}
			
			if($row['ability6'] != '65535'){
				$strHTML1 .= "<!-- HTML1 ABILITY6 -->\n";
				$strHTML1 .= $strOffset . "      <td>" . $chain_order . "</td>\n";
				$strHTML1 .= $strOffset . "      <td><img src='" . $HO_Icons[$row['ability6']]['src'] . "' alt='" . $HO_Icons[$row['ability6']]['name'] . "' style='width: 40px; height: 40px; object-fit: none; object-position: " . $HO_Icons[$row['ability6']]['posX'] . "% " . $HO_Icons[$row['ability6']]['posY'] . "%'/></td>\n";
				$strHTML2 .= "<!-- HTML2 ABILITY6 -->\n";
				$strHTML2 .= $strOffset . "      <td></td>\n";
				$strHTML2 .= $strOffset . "      <td>\n";
				$strHTML2 .= $strOffset . "        <input type='text' name='heroic_ops|ability6' value='". $row['ability5'] . "' size='3'/>\n";
				$strHTML2 .= $strOffset . "        <input type='hidden' name='orig_ability6' value='". $row['ability5'] . "' size='3'/>\n";
				$strHTML2 .= $strOffset . "      </td>\n";
			}else{
				$strHTML1 .= "<!-- HTML1 ABILITY6 -->\n";
				$strHTML1 .= $strOffset . "      <td></td>\n";
				$strHTML1 .= $strOffset . "      <td></td>\n";
				$strHTML2 .= "<!-- HTML2 ABILITY6 -->\n";
				$strHTML2 .= $strOffset . "      <td></td>\n";
				$strHTML2 .= $strOffset . "      <td>\n";
				$strHTML2 .= $strOffset . "        <input type='text' name='heroic_ops|ability6' value='". $row['ability6'] . "' size='3'/>\n";
				$strHTML2 .= $strOffset . "        <input type='hidden' name='orig_ability6' value='". $row['ability6'] . "' size='3'/>\n";
				$strHTML2 .= $strOffset . "      </td>\n";

			}
			
			//LIST OF POSSIBLE OUTCOMES
			$strHTML1 .= "<!-- HTML1 POSSIBLE_OUTCOMES -->\n";
			$strHTML1 .= $strOffset . "      <td>\n";
			$strHTML1 .= $strOffset . "        <ul>\n";
			$outcomes_setup_query = "SELECT spell_id FROM `" . DEV_DB . "`.`heroic_ops` WHERE starter_link_id = " . $row['id'];
			$outcomes_setup_data = $eq2->RunQueryMulti($outcomes_setup_query);
			foreach ($outcomes_setup_data as $outcome_setup)
			{
				$outcome_query = "SELECT name FROM `" . DEV_DB . "`.`spells` WHERE id=" . $outcome_setup['spell_id'];
				$outcome_data = $eq2->RunQuerySingle($outcome_query);
				$strHTML1 .= $strOffset . "          <li>" . $outcome_data['name'] . "</li>\n";
			}
			$strHTML1 .= $strOffset . "        </ul>\n";
			$strHTML1 .= $strOffset . "      </td>\n";
			$strHTML2 .= "<!-- HTML2 POSSIBLE_OUTCOMES -->\n";
			$strHTML2 .= $strOffset . "      <td></td>\n";

			//ACTIONS
			$strHTML1 .= "<!-- HTML1 FORM COMMANDS -->\n";
			$strHTML1 .= $strOffset . "      <td>\n";
			$strHTML1 .= $strOffset . "      </td>\n";
			$strHTML2 .= "<!-- HTML2 FORM COMMANDS -->\n";
			$strHTML2 .= $strOffset . "      <td>\n";
			$strHTML2 .= $strOffset . "        <input type='hidden' name='idx_name' value='id' />\n";
            $strHTML2 .= $strOffset . "        <input type='hidden' name='table_name' value='heroic_ops' />\n";
			$strHTML2 .= $strOffset . "        <input type='submit' name='cmd' value='update'/>\n";
			$strHTML2 .= $strOffset . "        <input type='submit' name='cmd' value='delete'/>\n";
			$strHTML2 .= $strOffset . "      </td>\n";

			$strHTML1 .= "<!-- HTML1 END ROW -->\n";
			$strHTML1 .= $strOffset . "    </tr>\n";
			$strHTML2 .= "<!-- HTML2 END ROW -->\n";
			$strHTML2 .= $strOffset . "    </tr>\n";
			$strHTML2 .= $strOffset . "    <tr>\n";
			//NOT SURE WHY THIS IS NEEDED, BUT IT'S HERE TO CORRECT THE EVEN/ODD STYLESHEET COLORS
			$strHTML2 .= $strOffset . "    <td colspan='16'>&nbsp;</td>\n";
			$strHTML2 .= $strOffset . "    </tr>\n";
			$strHTML2 .= $strOffset . "  </form>\n";
			$strHTML .= $strHTML1;
			$strHTML .= $strHTML2;
		}
		//ONE LAST ROW TO ALLOW A NEW STARTER CHAIN FROM THIS INTERFACE
		$strHTML .= $strOffset . "  <form method='post' name='heroic_ops|newStater'>\n";
		$strHTML .= $strOffset . "    <tr>\n";
		$strHTML .= $strOffset . "    <td><h3>New<h3></td>\n";
		//STARTER_CLASS
		$strHTML .= $strOffset . "    <td>\n";
		$strHTML .= $strOffset . "      <select name='heroic_ops|starter_class'>\n";
		$allHOStarterClasses_query = "SELECT class_name, class_id, class_map FROM `eq2classes` WHERE ho_class = 1 AND class_id != 0 AND class_map != 0";
		$allHOStarterClasses = $eq2->RunQueryMulti($allHOStarterClasses_query);
		foreach($allHOStarterClasses as $HOStaterClass)
		{
			$strHTML .= $strOffset . "          <option value='" . $HOStaterClass['class_id'] . "' " . $isSelected . ">" . $HOStaterClass['class_name'] . "</option>\n";
		}
		$strHTML .= $strOffset . "        </select>\n";
		$strHTML .= $strOffset . "      </select>\n";
		$strHTML .= $strOffset . "    </td>\n";
		//ENHANCER_CLASS
		$strHTML .= $strOffset . "    <td>\n";
		$strHTML .= $strOffset . "        <select name='heroic_ops|enhancer_class'>\n";
		$allHOStarterClasses_query = "SELECT class_name, class_id, class_map FROM `eq2classes` WHERE ho_class = 1 ORDER BY class_map ASC";
		$allHOStarterClasses = $eq2->RunQueryMulti($allHOStarterClasses_query);
		foreach($allHOStarterClasses as $HOStaterClass)
		{
			$strHTML .= $strOffset . "          <option value='" . $HOStaterClass['class_map'] . "' " . $isSelected . ">" . $HOStaterClass['class_name'] . "</option>\n";
		
		}
		$strHTML .= $strOffset . "      </select>\n";
		$strHTML .= $strOffset . "    </td>\n";
		//ABILITY1
		$strHTML .= $strOffset . "    <td>\n";
		$strHTML .= $strOffset . "      <input type='text' size='3' name='heroic_ops|ability1' value='65535'>\n";
		$strHTML .= $strOffset . "    </td>\n";
		//ABILITY2
		$strHTML .= $strOffset . "    <td></td>\n";
		$strHTML .= $strOffset . "    <td>\n";
		$strHTML .= $strOffset . "      <input type='text' size='3' name='heroic_ops|ability2' value='65535'>\n";
		$strHTML .= $strOffset . "    </td>\n";
		//ABILITY3
		$strHTML .= $strOffset . "    <td></td>\n";
		$strHTML .= $strOffset . "    <td>\n";
		$strHTML .= $strOffset . "      <input type='text' size='3' name='heroic_ops|ability3' value='65535'>\n";
		$strHTML .= $strOffset . "    </td>\n";
		//ABILITY4
		$strHTML .= $strOffset . "    <td></td>\n";
		$strHTML .= $strOffset . "    <td>\n";
		$strHTML .= $strOffset . "      <input type='text' size='3' name='heroic_ops|ability4' value='65535'>\n";
		$strHTML .= $strOffset . "    </td>\n";
		//ABILITY5
		$strHTML .= $strOffset . "    <td></td>\n";
		$strHTML .= $strOffset . "    <td>\n";
		$strHTML .= $strOffset . "      <input type='text' size='3' name='heroic_ops|ability5' value='65535'>\n";
		$strHTML .= $strOffset . "      <input type='hidden'>\n";
		$strHTML .= $strOffset . "    </td>\n";
		//ABILITY6
		$strHTML .= $strOffset . "    <td></td>\n";
		$strHTML .= $strOffset . "    <td>\n";
		$strHTML .= $strOffset . "      <input type='text' size='3' name='heroic_ops|ability6' value='65535'>\n";
		$strHTML .= $strOffset . "      <input type='hidden'>\n";
		$strHTML .= $strOffset . "    </td>\n";
		//EMPTY SECTION FOR OUTCOMES
		$strHTML .= $strOffset . "    <td></td>\n";
		//COMMAND
		$strHTML .= $strOffset . "    <td>\n";
		$strHTML .= $strOffset . "      <input type='hidden' name='heroic_ops|ho_type' value='Starter'/>\n";
		$strHTML .= $strOffset . "      <input type='hidden' name='heroic_ops|starter_link_id' value='0'/>\n";
		$strHTML .= $strOffset . "      <input type='hidden' name='heroic_ops|chain_order' value='0'/>\n";
		$strHTML .= $strOffset . "      <input type='hidden' name='heroic_ops|shift_icon' value='0'/>\n";
		$strHTML .= $strOffset . "      <input type='hidden' name='heroic_ops|spell_id' value='0'/>\n";
		$strHTML .= $strOffset . "      <input type='hidden' name='heroic_ops|chance' value='0'/>\n";
		$strHTML .= $strOffset . "      <input type='hidden' name='idx_name' value='id' />\n";
		$strHTML .= $strOffset . "      <input type='hidden' name='table_name' value='heroic_ops' />\n";
		$strHTML .= $strOffset . "      <input type='submit' name='cmd' value='insert'>\n";
		$strHTML .= $strOffset . "    </td>\n";
		$strHTML .= $strOffset . "  </form>\n";
		$strHTML .= $strOffset . "    </tr>\n";
		$strHTML .= $strOffset . "  </table>\n";
		$strHTML .= $strOffset . "</fieldset>\n";

	//CHAINS
	}elseif($_GET['searchtype'] == 'chains'){
		$strHTML .= $strOffset . "<fieldset>\n";
		$strHTML .= $strOffset . "  <legend>By Chain(" . $_GET['starter'] . ")</legend>\n";
		$strHTML .= $strOffset . "  <table class='ContrastTable'>\n";
		$strHTML .= $strOffset . "    <tr>\n";
		$strHTML .= $strOffset . "      <th>ID</th>\n";
		$strHTML .= $strOffset . "      <th>Icon</th>\n";
		$strHTML .= $strOffset . "      <th>Result Name</th>\n";
		$strHTML .= $strOffset . "      <th>Starter</th>\n";
		$strHTML .= $strOffset . "      <th>Enhancer(s)</th>\n";
		$strHTML .= $strOffset . "      <th>Chance</th>\n";
		$strHTML .= $strOffset . "      <th>Order<br>Required</th>\n";
		$strHTML .= $strOffset . "      <th>Shift<br>Icon</th>\n";
		$strHTML .= $strOffset . "      <th>Ability1</th>\n";
		$strHTML .= $strOffset . "      <th colspan='2'>Ability2</th>\n";
		$strHTML .= $strOffset . "      <th colspan='2'>Ability3</th>\n";
		$strHTML .= $strOffset . "      <th colspan='2'>Ability4</th>\n";
		$strHTML .= $strOffset . "      <th colspan='2'>Ability5</th>\n";
		$strHTML .= $strOffset . "      <th colspan='2'>Ability6</th>\n";
		$strHTML .= $strOffset . "      <th colspan='2'>Actions</th>\n";
		$strHTML .= $strOffset . "    </tr>\n";
		//TESTING ICON STANDIZATION
		$IconType = 'ho';
		$HO_Icons = $eq2Icons->GetIcons($IconType);
		$Spell_Icons = $eq2Icons->GetIcons('spells');
		
		$query = "SELECT * FROM `" . DEV_DB . "`.`heroic_ops` WHERE starter_link_id = " . $_GET['starter'] . " ORDER BY id ASC;";
		$data = $eq2->RunQueryMulti($query);
		foreach($data as $row){
			$strHTML1 = "<!-- CHAIN ID -->\n";
			$strHTML2 = "<!-- CHAIN ID -->\n";
			
			$isSelected = "";
			$strHTML1 .= $strOffset . "   <form method='post' name='heroic_ops|chains" . $row['id'] . ">\n";
			$strHTML1 .= $strOffset . "    <tr>\n";
			$strHTML1 .= $strOffset . "      <td>\n";
			$strHTML1 .= $strOffset . "        <input type='hidden' name='heroic_ops|id' value='" . $row['id'] . "'>\n";
			$strHTML1 .= $strOffset . "        <input type='hidden' name='orig_id' value='" . $row['id'] . "'>\n";
			$strHTML1 .= $strOffset . "        </td>\n";
			$strHTML2 .= $strOffset . "     <tr>\n";
			$strHTML2 .= $strOffset . "      <td>" . $row['id'] . "</td>\n";

			//LETS GRAB SOME SPELL DATA
			$strHTML1 .= "<!-- RESULT SPELL -->\n";
			$strHTML2 .= "<!-- RESULT SPELL -->\n";
			$strHTML1 .= $strOffset . "      <td></td>\n";
			$spell_query = "SELECT * FROM `" . DEV_DB . "`.`spells` WHERE id = " . $row['spell_id'] . ";";
			$spell_data = $eq2->RunQuerySingle($spell_query);
			$strHTML1 .= $strOffset . "      <td><img src='" . $Spell_Icons[$row['ability1']]['src'] . "' alt='" . $Spell_Icons[$row['ability1']]['name'] . "' style='width: 40px; height: 40px; object-fit: none; object-position: " . $Spell_Icons[$row['ability1']]['posX'] . "% " . $Spell_Icons[$row['ability1']]['posY'] . "%'/></td>\n";
			$strHTML1 .= $strOffset . "      <td>" . $spell_data['name'] . "</td>\n";
			$strHTML2 .= $strOffset . "      <td><input type='hidden' name='orig_spell_id' value='" . $row['spell_id'] . "'></td>\n";
			$strHTML2 .= $strOffset . "      <td><input type='text' name='heroic_ops|spell_id' value='" . $row['spell_id'] . "' size='5'/></td>\n";
			
			//REACH BACK AND GET THE START CLASS NAME
			$strHTML1 .= "<!-- STARTER CLASS -->\n";
			$strHTML2 .= "<!-- STARTER CLASS -->\n";
			$classtype_query = "SELECT class_name FROM `eq2classes` WHERE class_id = (SELECT starter_class FROM `" . DEV_DB . "`.`heroic_ops` WHERE id=" . $row['starter_link_id'] . ");";
			$classtype_data = $eq2->RunQuerySingle($classtype_query);
			$isSelected = "";
			$strHTML1 .= $strOffset . "      <td>" . $classtype_data['class_name'] . "</td>\n";
			$strHTML2 .= $strOffset . "      <td></td>\n";

			//REACH BACK AND GET THE ENHANCER CLASS(ES)
			$strHTML1 .= "<!-- ENCHANCER -->\n";

			$classtype_query = "SELECT class_name FROM `eq2classes` WHERE class_map = " . $row['enhancer_class'] . ";";
			$classtype_data = $eq2->RunQuerySingle($classtype_query);
			$strHTML1 .= $strOffset . "      <td>" . $classtype_data['class_name'] . "</td>\n";
			$strHTML2 .= $strOffset . "      <td>\n";
			$strHTML2 .= $strOffset . "        <select name='heroic_ops|enhancer_class'>\n";
			$enhancer_class_query = "SELECT class_name, class_id, class_map FROM `eq2classes` WHERE ho_class = 1 ORDER BY class_map ASC";
			$enhancer_class_data = $eq2->RunQueryMulti($enhancer_class_query);
			foreach($enhancer_class_data as $enhancer_class)
			{
					$strHTML2 .= $strOffset . "          <option value='" . $enhancer_class['class_map'] . "'>" . $enhancer_class['class_name'] . "</option>\n";
			}
			$strHTML2 .= $strOffset . "        </select>\n";
			$strHTML2 .= $strOffset . "        <input type='hidden' name='orig_enhancer_class' value='" . $row['enhancer_class'] . "'>\n";
			$strHTML2 .= $strOffset . "      </td>\n";

			//CHANCE
			$strHTML1 .= "<!-- CHANCE -->\n";
			$strHTML2 .= "<!-- CHANCE -->\n";
			$strHTML1 .= $strOffset . "      <td></td>\n";
			$strHTML2 .= $strOffset . "      <td>\n";
			$strHTML2 .= $strOffset . "        <input type='text' name='heroic_ops|chance' value='" . $row['chance'] . "' size='3'/>\n";
			$strHTML2 .= $strOffset . "        <input type='hidden' name='orig_chance' value='" . $row['chance'] . "'>\n";
			$strHTML2 .= $strOffset . "      </td>\n";

			//CHAIN ORDER
			$strHTML1 .= "<!-- CHAIN ORDER -->\n";
			$strHTML2 .= "<!-- CHAIN ORDER -->\n";
			$strHTML1 .= $strOffset . "      <td></td>\n";
			$strHTML2 .= $strOffset . "      <td>\n";
			$strHTML2 .= $strOffset . "        <select name='heroic_ops|chain_order'>\n";
			$strHTML2 .= $strOffset . "          <option value='0'>0 - False</option>\n";
			$strHTML2 .= $strOffset . "          <option value='1' " . (intval($row['chain_order']) == 1?'selected':'') . ">1 - True</option>\n";
			$strHTML2 .= $strOffset . "        <select>\n";
			$strHTML2 .= $strOffset . "        <input type='hidden' name='orig_chain_order' value='" . $row['chain_order'] . "'>\n";
			$strHTML2 .= $strOffset . "      </td>\n";
			if($row['chain_order'] == 1)
			{
				$chain_order = "<i class='fa fa-arrow-right' aria-hidden='true'></i>";
			}else{
				$chain_order = "<i class='fa fa-plus' aria-hidden='true'></i></td>";
			}

			//SHIFT ICON
			$strHTML1 .= "<!-- SHIFT ICON -->\n";
			$strHTML2 .= "<!-- SHIFT ICON -->\n";
			$strHTML1 .= $strOffset . "      <td><img src='eq2Icon.php?type=ho&id=" . $row['shift_icon'] . "'/></td> \n";
			$strHTML2 .= $strOffset . "      <td>\n";
			$strHTML2 .= $strOffset . "        <input type='text' name='heroic_ops|shift_icon' value='" . $row['shift_icon'] . "' size='3'/>\n";
			$strHTML2 .= $strOffset . "        <input type='hidden' name='orig_shift_icon' value='" . $row['shift_icon'] . "' size='3'/>\n";
			$strHTML2 .= $strOffset . "      </td>\n";

			//ABILITIES
			$strHTML1 .= "<!-- ABILITIES 1 -->\n";
			$strHTML2 .= "<!-- ABILITIES 1 -->\n";
			if($row['ability1'] != '65535'){
				$strHTML1 .= $strOffset . "      <td><img src='eq2Icon.php?type=ho&id=" . $row['ability1'] . "'/></td> \n";
			}else{
				$strHTML1 .= $strOffset . "      <td></td>\n";
			}
			$strHTML2 .= $strOffset . "      <td>\n";
			$strHTML2 .= $strOffset . "        <input type='text' name='heroic_ops|ability1' value='" . $row['ability1'] . "' size='3'/>\n";
			$strHTML2 .= $strOffset . "        <input type='hidden' name='orig_ability1' value='" . $row['ability1'] . "' size='3'/>\n";
			$strHTML2 .= $strOffset . "      </td>\n";

			$strHTML1 .= "<!-- ABILITIES 2 -->\n";
			$strHTML2 .= "<!-- ABILITIES 2 -->\n";
			if($row['ability2'] != '65535'){
				$strHTML1 .= $strOffset . "      <td>" . $chain_order . "</td>\n";
				$strHTML1 .= $strOffset . "      <td><img src='eq2Icon.php?type=ho&id=" . $row['ability2'] . "'/></td> \n";
			}else{
				$strHTML1 .= $strOffset . "      <td></td><td></td>\n";

			}
			$strHTML2 .= $strOffset . "      <td></td>\n";			
			$strHTML2 .= $strOffset . "      <td>\n";
			$strHTML2 .= $strOffset . "        <input type='text' name='heroic_ops|ability2' value='" . $row['ability2'] . "' size='3'/>\n";
			$strHTML2 .= $strOffset . "        <input type='hidden' name='orig_ability2' value='" . $row['ability2'] . "' size='3'/>\n";
			$strHTML2 .= $strOffset . "      <t/d>\n";

			$strHTML1 .= "<!-- ABILITIES 3 -->\n";
			$strHTML2 .= "<!-- ABILITIES 3 -->\n";
			if($row['ability3'] != '65535'){
				$strHTML1 .= $strOffset . "      <td>" . $chain_order . "</td>\n";
				$strHTML1 .= $strOffset . "      <td><img src='eq2Icon.php?type=ho&id=" . $row['ability3'] . "'/></td> \n";
			}else{
				$strHTML1 .= $strOffset . "      <td></td><td></td>\n";
			}
			$strHTML2 .= $strOffset . "      <td></td>\n";
			$strHTML2 .= $strOffset . "      <td>\n";
			$strHTML2 .= $strOffset . "        <input type='text' name='heroic_ops|ability3' value='" . $row['ability3'] . "' size='3'/>\n";
			$strHTML2 .= $strOffset . "        <input type='hidden' name=orig_ability3' value='" . $row['ability3'] . "' size='3'/>\n";
			$strHTML2 .= $strOffset . "      </td> \n";

			$strHTML1 .= "<!-- ABILITIES 4 -->\n";
			$strHTML2 .= "<!-- ABILITIES 4 -->\n";
			if($row['ability4'] != '65535'){
				$strHTML1 .= $strOffset . "      <td>" . $chain_order . "</td>\n";
				$strHTML1 .= $strOffset . "      <td><img src='eq2Icon.php?type=ho&id=" . $row['ability4'] . "'/></td> \n";
			}else{
				$strHTML1 .= $strOffset . "      <td></td><td></td>\n";
			}
			$strHTML2 .= $strOffset . "      <td></td>\n";
			$strHTML2 .= $strOffset . "      <td>\n";
			$strHTML2 .= $strOffset . "        <input type='text' name='heroic_ops|ability4' value='" . $row['ability4'] . "' size='3'/>\n";
			$strHTML2 .= $strOffset . "        <input type='hidden' name='orig_ability4' value='" . $row['ability4'] . "' size='3'/>\n";
			$strHTML2 .= $strOffset . "      </td> \n";

			$strHTML1 .= "<!-- ABILITIES 5 -->\n";
			$strHTML2 .= "<!-- ABILITIES 5 -->\n";
			if($row['ability5'] != '65535'){
				$strHTML1 .= $strOffset . "      <td>" . $chain_order . "</td>\n";
				$strHTML1 .= $strOffset . "      <td><img src='eq2Icon.php?type=ho&id=" . $row['ability5'] . "'/></td> \n";
			}else{
				$strHTML1 .= $strOffset . "      <td></td><td></td>\n";
			}
			$strHTML2 .= $strOffset . "      <td></td>\n";
			$strHTML2 .= $strOffset . "      <td>\n";
			$strHTML2 .= $strOffset . "        <input type='text' name='heroic_ops|ability5' value='" . $row['ability5'] . "' size='3'/>\n";
			$strHTML2 .= $strOffset . "        <input type='hidden' name='orig_ability5' value='" . $row['ability5'] . "' size='3'/>\n";
			$strHTML2 .= $strOffset . "      </td> \n";

			$strHTML1 .= "<!-- ABILITIES 6 -->\n";
			$strHTML2 .= "<!-- ABILITIES 6 -->\n";
			if($row['ability6'] != '65535'){
				$strHTML1 .= $strOffset . "      <td>" . $chain_order . "</td>\n";
				$strHTML1 .= $strOffset . "      <td><img src='eq2Icon.php?type=ho&id=" . $row['ability6'] . "'/></td> \n";
			}else{
				$strHTML1 .= $strOffset . "      <td></td><td></td>\n";
			}
			$strHTML2 .= $strOffset . "      <td></td>\n";
			$strHTML2 .= $strOffset . "      <td>\n";
			$strHTML2 .= $strOffset . "        <input type='text' name='heroic_ops|ability6' value='" . $row['ability6'] . "' size='3'/>\n";
			$strHTML2 .= $strOffset . "        <input type='hidden' name='orig_ability6' value='" . $row['ability6'] . "' size='3'/>\n";
			$strHTML2 .= $strOffset . "      </td> \n";

			//ACTIONS
			$strHTML1 .= $strOffset . "      <td></td>\n";

			$strHTML2 .= $strOffset . "      <td>\n";
			$strHTML2 .= $strOffset . "        <input type='hidden' name='idx_name' value='id' />\n";
            $strHTML2 .= $strOffset . "        <input type='hidden' name='table_name' value='heroic_ops' />\n";
			$strHTML2 .= $strOffset . "        <input type='submit' name='cmd' value='update'/>\n";
			$strHTML2 .= $strOffset . "        <input type='submit' name='cmd' value='delete'/>\n";
			$strHTML2 .= $strOffset . "      </td>\n";

			$strHTML1 .= $strOffset . "    </tr>\n";
			$strHTML2 .= $strOffset . "    </tr>\n";
			$strHTML2 .= $strOffset . "    </form>\n";
			$strHTML .= $strHTML1;
			$strHTML .= $strHTML2;
		}

		//ONE MORE LINE TO ALLOW USERS TO ADD NEW HO CHAINS
		$strHTML .= $strOffset . "    <form method='post' name='heroic_ops|newChain'>\n";
		$strHTML .= $strOffset . "      <tr></tr>\n";
		$strHTML .= "<!-- CHAIN ID -->\n";
		$strHTML .= $strOffset . "      <tr>\n";
		$strHTML .= $strOffset . "        <td>\n";
		$strHTML .= $strOffset . "          New\n";
		$strHTML .= $strOffset . "        </td>\n";

		$strHTML .= "<!-- ICON -->\n";
		$strHTML .= $strOffset . "        <td>\n";
		$strHTML .= $strOffset . "        </td>\n";

		$strHTML .= "<!-- SPELL -->\n";
		$strHTML .= $strOffset . "         <td>\n";
		$strHTML .= $strOffset . "           <input type='text' name='heroic_ops|spell_id' size='5'>\n";
		$strHTML .= $strOffset . "         </td>\n";

		$strHTML .= "<!-- STARTER -->\n";
		$strHTML .= $strOffset . "        <td>\n";
		$strHTML .= $strOffset . "          <i>AUTOMATIC</i>\n";
		$strHTML .= $strOffset . "        </td>\n";

		$strHTML .= "<!-- ENHANCER -->\n";
		$strHTML .= $strOffset . "        <td>\n";
		$strHTML .= $strOffset . "          <select name='heroic_ops|enhancer_class'>\n";
		$enhancer_class_query = "SELECT class_name, class_id, class_map FROM `eq2classes` WHERE ho_class = 1 ORDER BY class_map ASC";
		$enhancer_class_data = $eq2->RunQueryMulti($enhancer_class_query);
		foreach($enhancer_class_data as $enhancer_class)
		{
				$strHTML .= $strOffset . "            <option value='" . $enhancer_class['class_map'] . "'>" . $enhancer_class['class_name'] . "</option>\n";
		}
		$strHTML .= $strOffset . "          </select>\n";
		$strHTML .= $strOffset . "        </td>\n";

		$strHTML .= "<!-- CHANCE -->\n";
		$strHTML .= $strOffset . "        <td>\n";
		$strHTML .= $strOffset . "          <input type='text' name='heroic_ops|chance'  value='" . $row['chance'] . "' size='3'/>\n";
		$strHTML .= $strOffset . "      </td>\n";

		$strHTML .= "<!-- ORDER REQ -->\n";
		$strHTML .= $strOffset . "    <td>\n";
		$strHTML .= $strOffset . "        <select name='heroic_ops|chain_order'>\n";
		$strHTML .= $strOffset . "          <option value='0'>0 - False</option>\n";
		$strHTML .= $strOffset . "          <option value='1'>1 - True</option>\n";
		$strHTML .= $strOffset . "        <select>\n";
		$strHTML .= $strOffset . "    </td>\n";

		$strHTML .= "<!-- SHIFT ICON -->\n";
		$strHTML .= $strOffset . "    <td>\n";
		$strHTML .= $strOffset . "      <input type='text' name='heroic_ops|shift_icon' size='3'>\n";
		$strHTML .= $strOffset . "    </td>\n";

		$strHTML .= "<!-- ABILITY1 -->\n";
		$strHTML .= $strOffset . "    <td>\n";
		$strHTML .= $strOffset . "      <input type='text' name='heroic_ops|ability1' size='3'>\n";
		$strHTML .= $strOffset . "    </td>\n";

		$strHTML .= "<!-- ABILITY2 -->\n";
		$strHTML .= $strOffset . "    <td>\n";
		$strHTML .= $strOffset . "    </td>\n";
		$strHTML .= $strOffset . "    <td>\n";
		$strHTML .= $strOffset . "      <input type='text' name='heroic_ops|ability2' size='3'>\n";
		$strHTML .= $strOffset . "    </td>\n";

		$strHTML .= "<!-- ABILITY3 -->\n";
		$strHTML .= $strOffset . "    <td>\n";
		$strHTML .= $strOffset . "    </td>\n";
		$strHTML .= $strOffset . "    <td>\n";
		$strHTML .= $strOffset . "      <input type='text' name='heroic_ops|ability3' size='3'>\n";
		$strHTML .= $strOffset . "    </td>\n";

		$strHTML .= "<!-- ABILITY4 -->\n";
		$strHTML .= $strOffset . "    <td>\n";
		$strHTML .= $strOffset . "    </td>\n";
		$strHTML .= $strOffset . "    <td>\n";
		$strHTML .= $strOffset . "      <input type='text' name='heroic_ops|ability4' size='3'>\n";
		$strHTML .= $strOffset . "    </td>\n";

		$strHTML .= "<!-- ABILITY5 -->\n";
		$strHTML .= $strOffset . "    <td>\n";
		$strHTML .= $strOffset . "    </td>\n";
		$strHTML .= $strOffset . "    <td>\n";
		$strHTML .= $strOffset . "      <input type='text' name='heroic_ops|ability5' size='3'>\n";
		$strHTML .= $strOffset . "    </td>\n";

		$strHTML .= "<!-- ABILITY6 -->\n";
		$strHTML .= $strOffset . "    <td>\n";
		$strHTML .= $strOffset . "    </td>\n";
		$strHTML .= $strOffset . "    <td>\n";
		$strHTML .= $strOffset . "      <input type='text' name='heroic_ops|ability6' size='3'>\n";
		$strHTML .= $strOffset . "    </td>\n";

		$strHTML .= "<!-- FORM ACTIONS -->\n";
		$strHTML .= $strOffset . "    <td>\n";
		$strHTML .= $strOffset . "      <input type='hidden' name='heroic_ops|starter_link_id' value='" . $_GET['starter'] . "'>\n";
		$strHTML .= $strOffset . "      <input type='hidden' name='idx_name' value='id' />\n";
		$strHTML .= $strOffset . "      <input type='hidden' name='table_name' value='heroic_ops' />\n";
		$strHTML .= $strOffset . "      <input type='submit' name='cmd' value='insert'/>\n";
		$strHTML .= $strOffset . "    </td>\n";
		$strHTML .= $strOffset . "    </tr>\n";
		$strHTML .= $strOffset . "    </form>\n";
		$strHTML .= $strOffset . "  </table>\n";
		$strHTML .= $strOffset . "</fieldset>\n";
		
	}
	print($strHTML);

}
function icons()
{
	global $eq2, $s, $eq2Icons;
	$strOffset = str_repeat("\x20",22);
	$strHTML = "\n";
	$strHTML .= $strOffset . "<fieldset>\n";
	$strHTML .= $strOffset . "  <legend>Icon Type:</legend>\n";
	$strHTML .= $strOffset . "  <form method='post' name='FormLootTableSearch'>\n";
    $strHTML .= $strOffset . "    <input type='radio' id='name' name='searchtype' value='server.php?page=icons&searchtype=heroic_ops' onchange='dosub(this.value)'>Heroic Ops\n";
	$strHTML .= $strOffset . "    <input type='radio' id='name' name='searchtype' value='server.php?page=icons&searchtype=spells' onchange='dosub(this.value)'>Spells\n";
	$strHTML .= $strOffset . "  </form>";
	$strHTML .= $strOffset . "\n";
	$strHTML .= $strOffset . "</fieldset>\n";

	if($_GET['searchtype'] == 'spells')
	{
		$IconType = "spells";
	}else
	{
		$IconType = 'ho';
	}

	$Icons = $eq2Icons->GetIcons($IconType);

	$strHTML .= $strOffset . "<table class='ContrastTable'>";
	$cntA = 0;
	while($cntA <= count($Icons))
	{
		$tblRow = 1;
		$strHTML .= $strOffset . "<tr>";
		for($tblCol = 0; $tblCol <= 9; $tblCol++)
		{
			if($cntA <= count($Icons))
			{
				$strHTML .= $strOffset . "<td>";
				$strHTML .= $strOffset . "<span>" . $cntA . "</span><br>";
				$strHTML .= $strOffset . "<span class='iconBG'>";
				$strHTML .= $strOffset . "<img src='" . $Icons[$cntA]['src'] . "' alt='" . $Icons[$cntA]['name'] . "' style='width: 40px; height: 40px; object-fit: none; object-position: " . $Icons[$cntA]['posX'] . "% " . $Icons[$cntA]['posY'] . "%'/>\n";
				$strHTML .= $strOffset . "</span>\n";
				$strHTML .= $strOffset . "</div>";
				$strHTML .= $strOffset . "</td>";
				$cntA++;
			}else{
				$strHTML .= $strOffset . "<td></td>";
			}

		}
		$strHTML .= $strOffset . "</tr>";
	}
	$strHTML .= $strOffset . "</table>";
	print($strHTML);
}

function lua_blocks()
{
	global $eq2, $s;
	$strOffset = str_repeat("\x20",22);
	$strHTML = "\n";
	
	switch($_GET['action'])
	{
		case "add_category":
			$strHTML .= $strOffset . "<table class='SubPanel ContrastTable'>\n";
			$strHTML .= $strOffset . "   <tr>\n";
			$strHTML .= $strOffset . "     <td>\n";
			$strHTML .= $strOffset . "       <fieldset>\n";
			$strHTML .= $strOffset . "         <legend>Add Lua Blocks Category:</legend>\n";
			$strHTML .= $strOffset . "         <button onclick='history.back()'> <<-BACK</button>\n";
			$strHTML .= $strOffset . "         <form method='post' name='multiform|addNewCategory'>\n";
			$strHTML .= $strOffset . "           <table>\n";
			$strHTML .= $strOffset . "             <tr>\n";
			$strHTML .= $strOffset . "               <th>Name</th>\n";
			$strHTML .= $strOffset . "               <th>Type</th>\n";
			$strHTML .= $strOffset . "               <th>Action</th>\n";
			$strHTML .= $strOffset . "             </tr>\n";
			$strHTML .= $strOffset . "             <tr>\n";
			$strHTML .= $strOffset . "               <td><input type='text' name='eq2lua_categories|name'></td>\n";
			$strHTML .= $strOffset . "               <td>\n";
			$strHTML .= $strOffset . "                 <select name='eq2lua_categories|type'>\n";
			$cat_query = "SELECT * from `eq2lua_types`";
			$cat_data=$eq2->RunQueryMulti($cat_query);
			foreach($cat_data as $cat)
			{
					$strHTML .= $strOffset . "                 <option value='" . $cat['id'] . "'>" . $cat['name'] . "</option>\n";
			}
			$strHTML .= $strOffset . "                 </select>\n";
			$strHTML .= $strOffset . "               </td>\n";
			$strHTML .= $strOffset . "               <td>\n";
			$strHTML .= $strOffset . "	               <input type='hidden' name='eq2lua_categories|user_id' value='" . $eq2->userdata['id'] . "'>\n";
			$strHTML .= $strOffset . "                 <input type='hidden' name='idx_name' value='id' />\n";
            $strHTML .= $strOffset . "                 <input type='hidden' name='table_name' value='eq2lua_categories' />\n";
			$strHTML .= $strOffset . "                 <input type='submit' name='cmd' value='insert'>\n";
			$strHTML .= $strOffset . "               </td>\n";
			$strHTML .= $strOffset . "             </tr>\n";
			$strHTML .= $strOffset . "           </table>\n";
			$strHTML .= $strOffset . "         </form>\n";
			$strHTML .= $strOffset . "       </fieldset>\n";

			break;
		case "edit_category":
			$strHTML .= $strOffset . "<table class='SubPanel ContrastTable'>\n";
			$strHTML .= $strOffset . "   <tr>\n";
			$strHTML .= $strOffset . "     <td>\n";
			$strHTML .= $strOffset . "       <fieldset>\n";
			$strHTML .= $strOffset . "         <legend>Lua Blocks Category:</legend>\n";
			$strHTML .= $strOffset . "         <button onclick='history.back()'> <<-BACK</button>\n";
			$strHTML .= $strOffset . "         <form method='post' name='lua_blocks|edit_category'>\n";
			$strHTML .= $strOffset . "           <table>\n";
			$strHTML .= $strOffset . "             <tr>\n";
			$strHTML .= $strOffset . "               <th>ID</th>\n";
			$strHTML .= $strOffset . "               <th>Name</th>\n";
			$strHTML .= $strOffset . "               <th>Type</th>\n";
			$strHTML .= $strOffset . "               <th>User</th>\n";
			$strHTML .= $strOffset . "               <th>Action</th>\n";
			$strHTML .= $strOffset . "             </tr>\n";
			$item_query = "SELECT * from `eq2lua_categories` WHERE id=" . $_GET['id'];
			$item_data=$eq2->RunQuerySingle($item_query);
			$strHTML .= $strOffset . "             <tr>\n";
			$strHTML .= $strOffset . "               <td>\n";
			$strHTML .= $strOffset . "                 " . $item_data['id'] . "\n";
			$strHTML .= $strOffset . "                 <input type='hidden' name='eq2lua_categories|id' value='" . $item_data['id'] ."' />\n";
			$strHTML .= $strOffset . "                 <input type='hidden' name='orig_id' value='" . $item_data['id'] ."' />\n";
			$strHTML .= $strOffset . "               </td>\n";
			$strHTML .= $strOffset . "               <td>\n";
			$strHTML .= $strOffset . "                 <input type='text' name='eq2lua_categories|name' value='" . $item_data['name'] ."'>\n";
			$strHTML .= $strOffset . "                 <input type='hidden' name='orig_name' value='" . $item_data['name'] ."' />\n";
			$strHTML .= $strOffset . "               </td>\n";
			$strHTML .= $strOffset . "               <td>\n";
			$strHTML .= $strOffset . "                 <select name='eq2lua_categories|type'>\n";
			$cat_query = "SELECT * from `eq2lua_types`";
			$cat_data=$eq2->RunQueryMulti($cat_query);
			foreach($cat_data as $cat)
			{
				$isSelected = ($cat['id'] == $item_data['id']?' selected ':'');
					$strHTML .= $strOffset . "                 <option value='" . $cat['id'] . "' " . $isSelected . ">" . $cat['name'] . "</option>\n";
			}
			$strHTML .= $strOffset . "                 </select>\n";
			$strHTML .= $strOffset . "                 <input type='hidden' name='orig_name' value='" . $item_data['name'] ."' />\n";
			$strHTML .= $strOffset . "               </td>\n";
			$user_query = "SELECT id,displayname from `users` WHERE id=" . $item_data['user_id'];
			$user_data=$eq2->RunQuerySingle($user_query);
			$strHTML .= $strOffset . "               <td>" . $user_data['displayname'] . "</td>\n";
			$strHTML .= $strOffset . "               <td>\n";
			$strHTML .= $strOffset . "	               <input type='hidden' name='eq2lua_categories|user_id' value='" . $user_data['id'] . "'>\n";
			$strHTML .= $strOffset . "                 <input type='hidden' name='idx_name' value='id' />\n";
            $strHTML .= $strOffset . "                 <input type='hidden' name='table_name' value='eq2lua_categories' />\n";
			if($item_data['user_id'] == $eq2->userdata['id'])
			{
				$strHTML .= $strOffset . "                 <input type='submit' name='cmd' value='update'>\n";
				$strHTML .= $strOffset . "                 <input type='submit' name='cmd' value='delete'>\n";
			}
			$strHTML .= $strOffset . "               </td>\n";
			$strHTML .= $strOffset . "             </tr>\n";
			$strHTML .= $strOffset . "           </table>\n";
			$strHTML .= $strOffset . "         </form>\n";

			$strHTML .= $strOffset . "       </fieldset>\n";
			break;
		case "add_block":
			$strHTML .= $strOffset . "<table class='SubPanel ContrastTable'>\n";
			$strHTML .= $strOffset . "   <tr>\n";
			$strHTML .= $strOffset . "     <td>\n";
			$strHTML .= $strOffset . "       <fieldset>\n";
			$strHTML .= $strOffset . "         <legend>Lua Blocks Editor:</legend>\n";
			$strHTML .= $strOffset . "         <button onclick='history.back()'> <<-BACK</button>\n";
			$strHTML .= $strOffset . "         <form method='post' id='blockForm' name='lua_blocks|add_block'>\n";
			$strHTML .= $strOffset . "           <table>\n";
			$strHTML .= $strOffset . "             <tr>\n";
			$strHTML .= $strOffset . "               <th>Name</th>\n";
			$strHTML .= $strOffset . "               <th>Category</th>\n";
			$strHTML .= $strOffset . "               <th>Type</th>\n";
			$strHTML .= $strOffset . "               <th>Shared</th>\n";
			$strHTML .= $strOffset . "             </tr>\n";
			$strHTML .= $strOffset . "             <tr>\n";
			$strHTML .= $strOffset . "               <td>\n";
			$strHTML .= $strOffset . "                 <input type='text' name='eq2lua_blocks|name'></td>\n";
			$strHTML .= $strOffset . "               <td>\n";
			$strHTML .= $strOffset . "                 <select name='eq2lua_blocks|category'>\n";
			$cat_query = "SELECT * from `eq2lua_categories`";
			$cat_data=$eq2->RunQueryMulti($cat_query);
			foreach($cat_data as $cat)
			{
				$strHTML .= $strOffset . "                 <option value='" . $cat['id'] . "'>" . $cat['name'] . "</option>\n";
			}
			$strHTML .= $strOffset . "                 </select>\n";
			$strHTML .= $strOffset . "               </td>\n";
			$strHTML .= $strOffset . "               <td>\n";
			$strHTML .= $strOffset . "                 <select name='eq2lua_blocks|type'>\n";
			$type_query = "SELECT * from `eq2lua_types`";
			$type_data=$eq2->RunQueryMulti($type_query);
			foreach($type_data as $type)
			{
					$strHTML .= $strOffset . "                 <option value='" . $type['id'] . "'>" . $type['name'] . "</option>\n";
			}
			$strHTML .= $strOffset . "                 </select>\n";
			$strHTML .= $strOffset . "               </td>\n";
			$strHTML .= $strOffset . "               <td>\n";
			$strHTML .= $strOffset . "                 <select name='eq2lua_blocks|shared'>\n";
			$strHTML .= $strOffset . "                   <option value='0'>No</option>\n";
			$strHTML .= $strOffset . "                   <option value='1'>Yes</option>\n";
			$strHTML .= $strOffset . "                 </select>\n";
			$strHTML .= $strOffset . "               </td>\n";
			$strHTML .= $strOffset . "             </tr>\n";
			$strHTML .= $strOffset . "             <tr>\n";
			$strHTML .= $strOffset . "                 <td colspan='5'>\n";
			$strHTML .= $strOffset . "                   Description\n";
			$strHTML .= $strOffset . "                   <textarea  id='src_desc' name='eq2lua_blocks|description'></textarea>\n";
			$strHTML .= $strOffset . "                 </td>\n";
			$strHTML .= $strOffset . "             </tr>\n";
			$strHTML .= $strOffset . "             <tr>\n";
			$strHTML .= $strOffset . "               <td colspan='5'>\n";
			$strHTML .= $strOffset . "                 Code Block\n";
			$strHTML .= $strOffset . "                 <textarea id='src_text' name='eq2lua_blocks|text'></textarea>\n";
			$strHTML .= $strOffset . "               </td>\n";
			$strHTML .= $strOffset . "             </tr>\n";
			$strHTML .= $strOffset . "             <tr>\n";
			$strHTML .= $strOffset . "               <td colspan='4' align='center'>\n";
			$strHTML .= $strOffset . "	               <input type='hidden' name='eq2lua_blocks|user_id' value='" . $eq2->userdata['id'] . "'>\n";
			$strHTML .= $strOffset . "                 <input type='hidden' name='idx_name' value='id' />\n";
			$strHTML .= $strOffset . "                 <input type='hidden' name='table_name' value='eq2lua_blocks' />\n";
			$strHTML .= $strOffset . "                 <input type='submit' name='cmd' value='insert'>\n";
			$strHTML .= $strOffset . "               </td>\n";
			$strHTML .= $strOffset . "             </tr>\n";
			$strHTML .= $strOffset . "           </table>\n";
			$strHTML .= $strOffset . "         </form>\n";
			$strHTML .= $strOffset . "       </fieldset>\n";
			break;
		case "edit_block":
			$strHTML .= $strOffset . "<table class='SubPanel ContrastTable'>\n";
			$strHTML .= $strOffset . "   <tr>\n";
			$strHTML .= $strOffset . "     <td>\n";
			$strHTML .= $strOffset . "       <fieldset>\n";
			$strHTML .= $strOffset . "         <legend>Lua Blocks Editor:</legend>\n";
			$strHTML .= $strOffset . "         <button onclick='history.back()'> <<-BACK</button>\n";
			$strHTML .= $strOffset . "         <form method='post' name='lua_blocks|edit_block'>\n";
			$strHTML .= $strOffset . "           <table>\n";
			$strHTML .= $strOffset . "             <tr>\n";
			$strHTML .= $strOffset . "               <th>ID</th>\n";
			$strHTML .= $strOffset . "               <th>Name</th>\n";
			$strHTML .= $strOffset . "               <th>Category</th>\n";
			$strHTML .= $strOffset . "               <th>Type</th>\n";
			$strHTML .= $strOffset . "               <th>Shared</th>\n";
			$strHTML .= $strOffset . "             </tr>\n";
			$block_query = "SELECT * from `eq2lua_blocks` WHERE id=" . $_GET['id'];
			$block_data=$eq2->RunQuerySingle($block_query);
			$strHTML .= $strOffset . "             <tr>\n";
			$strHTML .= $strOffset . "               <td>" . $block_data['id'] ."\n";
			$strHTML .= $strOffset . "                 <input type='hidden' name='eq2lua_blocks|id' value='" . $block_data['id'] ."'>\n";
			$strHTML .= $strOffset . "                 <input type='hidden' name='orig_id' value='" . $block_data['id'] ."'>\n";
			$strHTML .= $strOffset . "               </td>\n";
			$strHTML .= $strOffset . "               <td>\n";
			$strHTML .= $strOffset . "                 <input type='text' name='eq2lua_blocks|name' value='" . $block_data['name'] ."'></td>\n";
			$strHTML .= $strOffset . "                 <input type='hidden' name='orig_name' value='" . $block_data['name'] ."'></td>\n";
			$strHTML .= $strOffset . "               <td>\n";
			$strHTML .= $strOffset . "                 <select name='eq2lua_blocks|category'>\n";
			$cat_query = "SELECT * from `eq2lua_categories`";
			$cat_data=$eq2->RunQueryMulti($cat_query);
			foreach($cat_data as $cat)
			{
				$isSelected = ($cat['id'] == $block_data['category']?' selected ':'');
					$strHTML .= $strOffset . "                 <option value='" . $cat['id'] . "' " . $isSelected . ">" . $cat['name'] . "</option>\n";
			}
			$strHTML .= $strOffset . "                 </select>\n";
			$strHTML .= $strOffset . "                 <input type='hidden' name='orig_category' value='" . $block_data['category'] ."'></td>\n";
			$strHTML .= $strOffset . "               </td>\n";
			$strHTML .= $strOffset . "               <td>\n";
			$strHTML .= $strOffset . "                 <select name='eq2lua_blocks|type'>\n";
			$type_query = "SELECT * from `eq2lua_types`";
			$type_data=$eq2->RunQueryMulti($type_query);
			foreach($type_data as $type)
			{
				$isSelected = ($type['id'] == $block_data['type']?' selected ':'');
					$strHTML .= $strOffset . "                 <option value='" . $type['id'] . "' " . $isSelected . ">" . $type['name'] . "</option>\n";
			}
			$strHTML .= $strOffset . "                 </select>\n";
			$strHTML .= $strOffset . "                 <input type='hidden' name='orig_type' value='" . $block_data['type'] ."'></td>\n";
			$strHTML .= $strOffset . "               </td>\n";
			$strHTML .= $strOffset . "               <td>\n";
			$strHTML .= $strOffset . "                 <select name='eq2lua_blocks|shared'>\n";
			$strHTML .= $strOffset . "                   <option value='0'>No</option>\n";
			$strHTML .= $strOffset . "                   <option value='1' " . ($block_data['shared'] == 1?'selected':'') . ">Yes</option>\n";
			$strHTML .= $strOffset . "                 </select>\n";
			$strHTML .= $strOffset . "                 <input type='hidden' name='orig_shared' value='" . $block_data['type'] ."'></td>\n";
			$strHTML .= $strOffset . "               </td>\n";
			$strHTML .= $strOffset . "             </tr>\n";
			$strHTML .= $strOffset . "             <tr>\n";
			$strHTML .= $strOffset . "               <td colspan='5'>\n";
			$strHTML .= $strOffset . "                 <textarea name='eq2lua_blocks|description'>" . $block_data['description'] . "</textarea>\n";
			$strHTML .= $strOffset . "                 <input type='hidden' name='orig_description' value='" . $block_data['description'] . "'>\n";
			$strHTML .= $strOffset . "               </td>\n";
			$strHTML .= $strOffset . "             </tr>\n";
			$strHTML .= $strOffset . "             <tr>\n";
			$strHTML .= $strOffset . "               <td colspan='5'>\n";
			$strHTML .= $strOffset . "                 <textarea name='eq2lua_blocks|text'>" . $block_data['text'] . "</textarea>\n";
			$strHTML .= $strOffset . "                 <input type='hidden' name='orig_text' value='" . $block_data['text'] . "'>\n";
			$strHTML .= $strOffset . "               </td>\n";
			$strHTML .= $strOffset . "             </tr>\n";
			$strHTML .= $strOffset . "             <tr>\n";
			$strHTML .= $strOffset . "               <td colspan='5' align='center'>\n";			$strHTML .= $strOffset . "	               <input type='hidden' name='eq2lua_blocks|user_id' value='" . $eq2->userdata['id'] . "'>\n";
			$strHTML .= $strOffset . "                 <input type='hidden' name='idx_name' value='id' />\n";
            $strHTML .= $strOffset . "                 <input type='hidden' name='table_name' value='eq2lua_blocks' />\n";
			$strHTML .= $strOffset . "                 <input type='submit' name='cmd' value='update'>\n";
			$strHTML .= $strOffset . "                 <input type='submit' name='cmd' value='delete'>\n";
			$strHTML .= $strOffset . "               </td>\n";
			$strHTML .= $strOffset . "             </tr>\n";
			$strHTML .= $strOffset . "           </table>\n";
			$strHTML .= $strOffset . "         </form>\n";
			$strHTML .= $strOffset . "       </fieldset>\n";
			break;
		default:
			$strHTML .= $strOffset . "<script>\n";
			$strHTML .= $strOffset . "  function AddTextToEditor(element) {\n";
			$strHTML .= $strOffset . "    editor.insert(element.getAttribute('myFuncText'));\n";
			$strHTML .= $strOffset . "  }\n";
			$strHTML .= $strOffset . "</script>\n";			
			$strHTML .= $strOffset . "<table>\n";
			$strHTML .= $strOffset . "   <tr>\n";
			$strHTML .= $strOffset . "     <td>\n";
			$strHTML .= $strOffset . "       <fieldset>\n";
			$strHTML .= $strOffset . "         <legend>Lua Blocks Editor:</legend>\n";
			$strHTML .= $strOffset . "         <form method='post' name='lua_blocks|EditorActions'>\n";
			$strHTML .= $strOffset . "           <select name='new' onchange='dosub(this.options[this.selectedIndex].value)'>\n";
			$strHTML .= $strOffset . "             <option value=''>Select Action</option>\n";
			$strHTML .= $strOffset . "             <option value='./server.php?page=lua_blocks&action=add_category'>New Category</option>\n";
			$strHTML .= $strOffset . "             <option value='./server.php?page=lua_blocks&action=add_block'>New Block</option>\n";
			$strHTML .= $strOffset . "           </select>\n";
			$strHTML .= $strOffset . "           <table class='ContrastTable'>\n";
			$strHTML .= $strOffset . "             <!-- START LUA BLOCKS --> \n";
			$strHTML .= $strOffset . $eq2->GetLuaBlocks('showList');
			$strHTML .= $strOffset . "             <!-- END LUA BLOCKS --> \n";
			$strHTML .= $strOffset . "           </table>\n";
			$strHTML .= $strOffset . "         </form>\n";
			$strHTML .= $strOffset . "       </fieldset>\n";
			$strHTML .= $strOffset . "     </td>\n";
			/*
			$strHTML .= $strOffset . "     <td width='80%'>\n";
			$strHTML .= $strOffset . "       <form method='post' name='ScriptForm'>\n";
			$strHTML .= $strOffset . "         <table class='SectionMain' cellspacing='0' border='0' style='width:100%;'>\n";
			$strHTML .= $strOffset . "           <tr>\n";
			$strHTML .= $strOffset . "             <td class='SectionTitle' align='center'>Script Editor</td>\n";
			$strHTML .= $strOffset . "           </tr>\n";
			$strHTML .= $strOffset . "           <tr>\n";
			$strHTML .= $strOffset . "             <td id='ScriptToolbar'>\n";
			$strHTML .= $strOffset . "           </tr>\n";
			$strHTML .= $strOffset . "           <tr>\n";
			$strHTML .= $strOffset . "             <td height='480px'> \n";
			//$scriptText = $this->LoadLUAScript($scriptPath);
			$strHTML .= $strOffset . "               <!-- START EDITOR --> \n";
			$strHTML .= $strOffset . "               <div id='scripteditor' style='margin: 0; width: 100%; height: 100%'>" . $scriptText . "</div>\n";
			$strHTML .= $strOffset . "               <script src='../ace/src-noconflict/ace.js' charset='utf-8'></script>\n";
			$strHTML .= $strOffset . "               <script src='../ace/src-noconflict/ext-language_tools.js'></script>\n";
			$strHTML .= $strOffset . "               <script>\n";
			$strHTML .= $strOffset . "                 var lang_tools = ace.require('../ace/ext/language_tools');\n";
			$strHTML .= $strOffset . "                 var editor = ace.edit('scripteditor');\n";
			$strHTML .= $strOffset . "                 editor.setTheme('../ace/theme/textmate');\n";
			$strHTML .= $strOffset . "                 editor.session.setMode('../ace/mode/lua'); \n";
			$strHTML .= $strOffset . "                 lang_tools.setCompleters([lang_tools.snippetCompleter, lang_tools.keyWordCompleter]);\n";
			$strHTML .= $strOffset . "                 editor.setOptions({\n";
			$strHTML .= $strOffset . "                   enableLiveAutocompletion: true\n";
			$strHTML .= $strOffset . "                 });\n";
			$strHTML .= $strOffset . "                 editor.on('change', function() {updateCachedScript();});\n";
			$strHTML .= $strOffset . "               </script>\n";
			$strHTML .= $strOffset . "             </td>\n";
			$strHTML .= $strOffset . "           </tr>\n";
		//if( $this->CheckAccess(G_DEVELOPER) )
		//{
			$strHTML .= $strOffset . "           <tr>\n";
			$strHTML .= $strOffset . "             <td align='center'>\n";
			$strHTML .= $strOffset . "               <input type='submit' align='center' name='cmd' value='" . ($script_exists ? 'Save' : 'Create') . "' class='submit' id='savescript' />\n";
			$strHTML .= $strOffset . "               <!--<input type='submit' name='cmd' value='Rebuild' class='submit' title='Rebuilds the script from scratch (overwrite old one).' />-->\n";
			$strHTML .= $strOffset . "               <button id='scriptRevert' type='button'>Revert</button>\n";
			$strHTML .= $strOffset . "               <input type='hidden' id='script_name' name='script_name' value='" . $scriptPath . "' />\n";
			$strHTML .= $strOffset . "               <input type='hidden' name='script_path' value='" . substr($scriptPath, 0, strrpos($scriptPath, '/')) . "' />\n";
			$strHTML .= $strOffset . "               <input type='hidden' name='table_name' value='" . $table . "' />\n";
			$strHTML .= $strOffset . "               <input type='hidden' name='object_id' value='" . $objectID . "' />\n";
			$strHTML .= $strOffset . "               <input type='hidden' name='script_text' id='LuaScript' />\n";
			$strHTML .= $strOffset . "               <script>\n";
			$strHTML .= $strOffset . "                 document.getElementById('savescript').onclick = \n";
			$strHTML .= $strOffset . "                 function() {\n";
			$strHTML .= $strOffset . "                   document.getElementById('LuaScript').value = editor.getValue();\n";
			$strHTML .= $strOffset . "                 };\n";
			$strHTML .= $strOffset . "                 document.getElementById('scriptRevert').onclick = \n";
			$strHTML .= $strOffset . "                 function() {\n";
			$strHTML .= $strOffset . "                   if (confirm('Are you sure you want to revert? You will lose your local changes.')) {\n";
			$strHTML .= $strOffset . "                     editor.setValue(original_lua_script_text, 1);\n";
			$strHTML .= $strOffset . "                     clearCachedScript();\n";
			$strHTML .= $strOffset . "                   }\n";
			$strHTML .= $strOffset . "                 };\n";
			$strHTML .= $strOffset . "                 checkForCachedScript();\n";
			$strHTML .= $strOffset . "               </script>\n";
			$strHTML .= $strOffset . "             </td>\n";
			$strHTML .= $strOffset . "           </tr>\n";
		//}
			$strHTML .= $strOffset . "         </table>\n";
			$strHTML .= $strOffset . "       </form>\n";
			$strHTML .= $strOffset . "     </td>\n";
			*/
			$strHTML .= $strOffset . "   </tr>\n";
			$strHTML .= $strOffset . " </table>\n";
			break;
	}
	print($strHTML);
}

function misc_scripts()
{
	global $eq2, $s;
	$strOffset = str_repeat("\x20",22);
	$strHTML = "\n";

	switch($_GET['script_type'])
	{
		case "open":
			break;
		case "region":
			$strHTML .= $strOffset . "<fieldset>\n";
			$strHTML .= $strOffset . "  <legend>Select a Script Type:</legend>";
			$strHTML .= $strOffset . "  <table class='SectionMainFloat' style='width:100%'>\n";
			$strHTML .= $strOffset . "    <tr>\n";
			$strHTML .= $strOffset . "<!-- LEFT COL -->\n";
			$strHTML .= $strOffset . "      <td valign='top'>\n";
			$strHTML .= $strOffset . "      <div style='width:300px;height:640px;overflow-y:scroll;overflow-x:scroll'>\n";
			$strHTML .= $strOffset . "        <table class='ContrastTable'>\n";
			$scriptDir = "RegionScripts/";
			$fileSet = $s->GenerateFileList($scriptDir);
			//$strHTML .= $strOffset . "[>" . var_dump($fileSet);
			foreach($fileSet as $fileItem)
			{
				$strHTML .= $strOffset . "<tr>\n";
				$strHTML .= $strOffset . "  <td><i class='fa fa-folder'></i></td><td colspan='2'>" . $fileItem['Directory'] . "</td>\n";
				$strHTML .= $strOffset . "</tr>\n";
				$files = $fileItem['Files'];
				foreach($files as $file)
				{
					$strHTML .= $strOffset . "<tr>\n";
					$strHTML .= $strOffset . "  <td></td>\n";
					$strHTML .= $strOffset . "  <td><i class='fa fa-file'></i></td>\n";
					$strPathAndFile = $scriptDir . $fileItem['Directory'] . "/" . $file;
					$strHTML .= $strOffset . "  <td><a href='server.php?page=misc_scripts&script_type=region&file=" . base64_encode($strPathAndFile) . "'>" . $file . "</td>\n";
					$strHTML .= $strOffset . "</tr>\n";
				}
				
				
			}

			$strHTML .= $strOffset . "        </table>\n";
			$strHTML .= $strOffset . "        </div>\n";
			$strHTML .= $strOffset . "      </td>\n";
			$strHTML .= $strOffset . "<!-- RIGHT COL -->\n";
			$strHTML .= $strOffset . "      <td valign='top' width='100%'>\n";
			if(isset($_GET['file']))
			{
				$strHTML .= $eq2->DisplayScriptEditor(base64_decode($_GET['file']), '[Llama: Replace this Text]',null,"spells", $finalTemplates);
			}
			$strHTML .= $strOffset . "      </td>\n";
			$strHTML .= $strOffset . "    </tr>\n";
			$strHTML .= $strOffset . "  </table>\n";
			$strHTML .= $strOffset . "</div>\n";
			$strHTML .= $strOffset . "</fieldset>\n";
			break;
		default:
			$strHTML .= $strOffset . "<a href='server.php?page=misc_scripts&script_type=region'>Region</a>";
			break;
	}

	print($strHTML);
}
?>