<?php 
/*

	Reminder: Left off editing Migrator at Validate (Step 2). Trying to decide to automate fixing data or manually change with DB Editor...

*/
include("header.php"); 
if( $eq2->mLev < 200 ) { print("Options Not Available Yet!"); exit; }

$Page = isset($_GET['page']) ? $_GET['page'] : "";
$Type = isset($_GET['type']) ? $_GET['type'] : "";

switch($_POST['cmd'])
{
	case "convert": 
		foreach($_POST as $key=>$val)
		{
			if( substr($key, 0, 4) == "conv" )
				$myArr = explode("|", $key);
			if( $key == "new_id" )
				$new_id = $val;				
		}
		// printf("Re-indexing %s to %s<br>", $myArr[1], $new_id);
		$query = sprintf("update %s.spawn set id = %s where id = %s;", $db_name, $new_id, $myArr[1]); 
		//echo $query; exit;
	
		$eq2->runQuery($query);
		$eq2->logQuery($query);
		$p = array('spawn',$_POST['orig_object'],$query); // print_r($p);
		$eq2->dbEditorLog($p);
		break;

	case "reindex": 
		foreach($_POST as $key=>$val)
		{
			if( substr($key, 0, 5) == "reidx" )
				$myArr = explode("|", $key);
			if( $key == "new_id" )
				$new_id = $val;				
		}
		// printf("Re-indexing %s to %s<br>", $myArr[1], $new_id);
		$query = sprintf("update %s.spawn_location_entry set spawn_id = %s where spawn_id = %s;", $db_name, $new_id, $myArr[1]);
		//echo $query; exit;

		$eq2->runQuery($query);
		$eq2->logQuery($query);
		$p = array('zonespawnentry',$_POST['orig_object'],$query); // print_r($p);
		$eq2->dbEditorLog($p);
		
		$query = sprintf("delete from %s.spawn where id = %s;", $db_name, $myArr[1]); // echo $query;
		$eq2->runQuery($query);
		$eq2->logQuery($query);
		$p = array('spawn',$_POST['orig_object'],$query); // print_r($p);
		$eq2->dbEditorLog($p);
		break;
}

?>
<div id="sub-menu1">
	<table cellspacing="0">
		<tr>
			<td align="right" width="100px"><strong>AutoTools:</strong> </td>
			<td>&nbsp;
				[ <a href="<?php print($link) ?>?page=convert">Converter</a> ] &bull;
				[ <a href="<?php print($link) ?>?page=cleanup">Cleanup Spawns</a> ] &bull;
				[ <a href="<?php print($link) ?>?page=migrate">Data Migration</a> ] 
			</td>
		</tr>
<?php
switch( $Page ) 
{
	case "convert":
		?>
		<tr>
			<td align="right" width="100px"><strong>Convert:</strong> </td>
			<td>&nbsp;
				[ <a href="<?php print($link) ?>?page=convert&type=harvest">Harvest Nodes</a> ]
			</td>
		</tr>
		<?php
		break;
		
	case "cleanup": 
		?>
		<tr>
			<td align="right" width="100px"><strong>Type:</strong> </td>
			<td>&nbsp;
				[ <a href="<?php print($link) ?>?page=cleanup&type=npcs">NPCs</a> ] &bull;
				[ <a href="<?php print($link) ?>?page=cleanup&type=objects">Objects</a> ] &bull;
				[ <a href="<?php print($link) ?>?page=cleanup&type=signs">Signs</a> ] &bull;
				[ <a href="<?php print($link) ?>?page=cleanup&type=widgets">Widgets</a> ] &bull;
				[ <a href="<?php print($link) ?>?page=cleanup&type=ground">Ground Spawns</a> ] &bull;
				[ <a href="<?php print($link) ?>?page=cleanup&type=dupes">SpawnPoints</a> ]
			</td>
		</tr>
		<?php
		break;
	
	case "migrate":
		?>
		<tr>
			<td align="right" width="100px"><strong>Migrate:</strong> </td>
			<td>&nbsp;
				[ <a href="<?php print($link) ?>?page=migrate&type=core">Core Data</a> ] &bull;
				[ <a href="<?php print($link) ?>?page=migrate&type=factions">Factions</a> ] &bull;
				[ <a href="<?php print($link) ?>?page=migrate&type=items">Items</a> ] &bull;
				[ <a href="<?php print($link) ?>?page=migrate&type=merchants">Merchants</a> ] &bull;
				[ <a href="<?php print($link) ?>?page=migrate&type=revive">Revive Points</a> ] &bull;
				[ <a href="<?php print($link) ?>?page=migrate&type=spells">Spells</a> ] &bull;
				[ <a href="<?php print($link) ?>?page=migrate&type=transporters">Transporters</a> ] &bull;
				[ <a href="<?php print($link) ?>?page=migrate&type=zones">Zone</a> ]
			</td>
		</tr>
		<?php
		break;
}
?>
	</table>
</div>
<div id="stats-body">
<?php
if( isset($Type) ) {

	switch( $Type ) {
		case "harvest"	: 
			ConvertHarvestingNodes(100, 199, 'Shinies');
			ConvertHarvestingNodes(200, 399, 'Flora');
			ConvertHarvestingNodes(400, 499, 'Animal Dens');
			ConvertHarvestingNodes(500, 699, 'Rocks');
			ConvertHarvestingNodes(500, 699, 'Rubble');
			ConvertHarvestingNodes(700, 799, 'Logs');
			ConvertHarvestingNodes(800, 899, 'Fish');
			break;
		
		case "dupes":
			CleanupSpawnPointDupes();
			break;
			
		case "npcs":
			CleanupNPCs();
			break;
			
		case "objects":
			CleanupObjects();
			break;
			
		case "signs":
			CleanupSigns();
			break;
			
		case "widgets":
			CleanupWidgets();
			break;
			
		case "ground":
			CleanupGroundSpawns();
			break;
		
		case "zones":
		
			$source_db = ( $_GET['page'] == "raw" ) ? "eq2raw" : "eq2dev";
			$destination_db = ( $source_db == "eq2raw" ) ? "eq2dev" : "eq2migrate";

			print('<div id="zone-select">');
			$eq2->ZoneSelector(1);
			printf('&nbsp;<a href="?%s">Reload Page</a>', $_SERVER['QUERY_STRING']);

			switch($_GET['step'])
			{
			
				case 2:
					break;
					
				case 1:
					if( !ValidateZoneData() )
					{
						print("<p>Failed Validation, cannot continue til the spawn data is cleaned up!</p>");
					}
					else
					{
						?><input type="button" value="Step2 - Purge Destination" style="width:160px; font-size:13px;" onclick="javascript:window.open('<?= $link ?>?page=migrate&type=zones&zone=<?= $_GET['zone'] ?>&step=2', target='_self');" /><?
					}
					break;
					
				default:
					PreviewZoneData();
					break;
					
			}
			break;
	}
}
?>
</div>
<?
include_once("footer.php"); 
exit;


function ValidateZoneData()
{
	global $eq2, $link, $source_db, $destination_db;

	if( isset($_GET['zone']) )
	{
		$gtg = true;
		
		$the_data = array();
		// validate attackable spawns have correct flags
		$query = sprintf("SELECT DISTINCT s.* 
											FROM ".$source_db.".spawn s 
											JOIN ".$source_db.".spawn_location_entry sle ON s.id = sle.spawn_id 
											JOIN ".$source_db.".spawn_location_placement slp ON sle.spawn_location_id = slp.spawn_location_id 
											WHERE 
												slp.zone_id = %d AND
												command_primary IN (SELECT command_list_id FROM ".$source_db.".entity_commands WHERE command_text = 'attack') AND
												(targetable = 0 OR show_name = 0 OR show_level = 0 OR attackable = 0)
											ORDER BY s.id", $_GET['zone']);
		//echo $query;
		if( !$result = $eq2->db->sql_query($query) ) {
			$error = $eq2->db->sql_error();
			die($error['message']);
		} 
		else 
		{
			if( $eq2->db->sql_numrows($result) )
			{
				while( $data = $eq2->db->sql_fetchrow($result) )
				{
					$the_data[] = $data;
				}
				if( is_array($the_data) ) 
				{
					$gtg = false;
					print('<p>ATTACK SPAWNS</p>');
					DisplayValidationData($the_data);
				}
			}
		}
		
		$the_data = array();
		// validate non-attackable spawns have correct flags
		$query = sprintf("SELECT DISTINCT s.* 
											FROM ".$source_db.".spawn s 
											JOIN ".$source_db.".spawn_location_entry sle ON s.id = sle.spawn_id 
											JOIN ".$source_db.".spawn_location_placement slp ON sle.spawn_location_id = slp.spawn_location_id 
											WHERE 
												slp.zone_id = %d AND
												command_primary IN (SELECT command_list_id FROM ".$source_db.".entity_commands WHERE command_text <> 'attack' and command_text <> 'find npc') AND
												(targetable = 0 OR show_name = 0 OR show_level = 1 OR attackable = 1)
											ORDER BY s.id", $_GET['zone']);
		if( !$result = $eq2->db->sql_query($query) ) {
			$error = $eq2->db->sql_error();
			die($error['message']);
		} 
		else 
		{
			if( $eq2->db->sql_numrows($result) )
			{
				while( $data = $eq2->db->sql_fetchrow($result) )
				{
					$the_data[] = $data;
				}
				if( is_array($the_data) ) 
				{
					$gtg = false;
					print('<p>NON-ATTACK SPAWNS</p>');
					DisplayValidationData($the_data);
				}
			}
		}
											
		$the_data = array();
		// validate spawn objects have correct flags
		$query = sprintf("SELECT DISTINCT s.* 
											FROM ".$source_db.".spawn s 
											JOIN ".$source_db.".spawn_objects so ON s.id = so.spawn_id
											JOIN ".$source_db.".spawn_location_entry sle ON s.id = sle.spawn_id 
											JOIN ".$source_db.".spawn_location_placement slp ON sle.spawn_location_id = slp.spawn_location_id 
											WHERE 
												slp.zone_id = %d AND
												(targetable = 1 OR show_name = 1 OR show_level = 1 OR attackable = 1)
											ORDER BY s.id", $_GET['zone']);
		if( !$result = $eq2->db->sql_query($query) ) {
			$error = $eq2->db->sql_error();
			die($error['message']);
		} 
		else 
		{
			if( $eq2->db->sql_numrows($result) )
			{
				while( $data = $eq2->db->sql_fetchrow($result) )
				{
					$the_data[] = $data;
				}
				if( is_array($the_data) ) 
				{
					$gtg = false;
					print('<p>SPAWN OBJECTS</p>');
					DisplayValidationData($the_data);
				}
			}
		}

		$the_data = array();
		// validate spawn objects have correct flags
		$query = sprintf("SELECT DISTINCT s.* 
											FROM ".$source_db.".spawn s
											WHERE 
												s.id not in (select spawn_id from ".$source_db.".spawn_location_entry) AND
												s.id LIKE '%d____'
											ORDER BY s.id", $_GET['zone']);
		if( !$result = $eq2->db->sql_query($query) ) {
			$error = $eq2->db->sql_error();
			die($error['message']);
		} 
		else 
		{
			if( $eq2->db->sql_numrows($result) )
			{
				while( $data = $eq2->db->sql_fetchrow($result) )
				{
					$the_data[] = $data;
				}
				if( is_array($the_data) ) 
				{
					$gtg = false;
					print('<p>NOT SPAWNED (will be ignored during migration!)</p>');
					DisplayValidationData($the_data);
				}
			}
		}
	}
	else
	{
		$gtg = false;
	}
}

function PreviewZoneData()
{
	global $eq2, $link, $source_db, $destination_db;

	if( isset($_GET['zone']) )
	{
		$query = sprintf("SELECT DISTINCT s.* 
											FROM ".$source_db.".spawn s
											JOIN ".$source_db.".spawn_location_entry sle ON s.id = sle.spawn_id
											JOIN ".$source_db.".spawn_location_placement slp ON sle.spawn_location_id = slp.spawn_location_id
											WHERE 
												slp.zone_id = %d
											ORDER BY s.id", $_GET['zone']);
		//echo $query;
		if( !$result = $eq2->db->sql_query($query) ) {
			$error = $eq2->db->sql_error();
			die($error['message']);
		} else {
			while( $row = $eq2->db->sql_fetchrow($result) ) {
				$dev_data[] = $row;
			}
		}
		$eq2->db->sql_freeresult($result);
	
		$query = sprintf("SELECT DISTINCT s.* 
											FROM ".$destination_db.".spawn s
											JOIN ".$destination_db.".spawn_location_entry sle ON s.id = sle.spawn_id
											JOIN ".$destination_db.".spawn_location_placement slp ON sle.spawn_location_id = slp.spawn_location_id
											WHERE 
												slp.zone_id = %d
											ORDER BY s.id", $_GET['zone']);
		if( !$result = $eq2->db->sql_query($query) ) {
			$error = $eq2->db->sql_error();
			die($error['message']);
		} else {
			while( $row = $eq2->db->sql_fetchrow($result) ) {
				$live_data[] = $row;
			}
		}
		$eq2->db->sql_freeresult($result);

		// spawns without spawn placements
		$query = sprintf("SELECT DISTINCT s.* 
											FROM ".$source_db.".spawn s
											WHERE 
												s.id not in (select spawn_id from ".$source_db.".spawn_location_entry) AND
												s.id LIKE '%d____'
											ORDER BY s.id", $_GET['zone']);
		//echo $query;
		if( !$result = $eq2->db->sql_query($query) ) {
			$error = $eq2->db->sql_error();
			die($error['message']);
		} else {
			while( $row = $eq2->db->sql_fetchrow($result) ) {
				$dev_data2[] = $row;
			}
		}
		$eq2->db->sql_freeresult($result);

		// spawns without spawn placements
		$query = sprintf("SELECT DISTINCT s.* 
											FROM ".$destination_db.".spawn s
											WHERE 
												s.id not in (select spawn_id from ".$destination_db.".spawn_location_entry) AND
												s.id LIKE '%d____'
											ORDER BY s.id", $_GET['zone']);
		//echo $query;
		if( !$result = $eq2->db->sql_query($query) ) {
			$error = $eq2->db->sql_error();
			die($error['message']);
		} else {
			while( $row = $eq2->db->sql_fetchrow($result) ) {
				$live_data2[] = $row;
			}
		}
		$eq2->db->sql_freeresult($result);
	
		// build side-by-side table comparisons
		?>
		<table width="100%" align="center">
			<tr>
				<td colspan="2" align="center" style="font-size:24px; color:#f00;"><strong>PREVIEW</strong></td>
			</tr>
			<tr>
				<td colspan="2" align="center" style="font-size:13px;"><strong>PREVIEW: Differences Between Dev and Live Zone</strong></td>
			</tr>
			<tr>
				<td colspan="2" style="font-size:11px;">
					<ol>
						<li>Using the 2 tables below, compare Dev to Live data to be sure this is the data you wish to migrate</li>
						<li>Clean up (delete or spawn) any spawns that do not have placements (bottom left table)</li>
						<li>If you are keeping the non-placed spawns, be sure to combine the non-placed spawn with any currently placed spawn and adjust size/level offsets in existing spawn placement</li>
					</ol>
				</td>
			</tr>
			<tr>
				<!-- source -->
				<td width="50%" valign="top">
					<? DisplayComparisonData($dev_data, $source_db); ?>
				</td>
				<!-- destination -->
				<td width="50%" valign="top">
					<? DisplayComparisonData($live_data, $destination_db); ?>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="font-size:13px; font-weight:bold; text-align:center;">PREVIEW: Spawns Without Spawn Placements</td>
			</tr>
			<tr>
				<!-- source -->
				<td width="50%" valign="top">
					<? DisplayComparisonData($dev_data2, $source_db); ?>
				</td>
				<!-- destination -->
				<td width="50%" valign="top">
					<? DisplayComparisonData($live_data2, $destination_db); ?>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="font-size:12px"><br />
					<strong>BEFORE YOU MIGRATE:</strong> Be sure any spawns without placements are deleted or get placements with a 0 spawnpercentage <strong>or they will not be migrated</strong>.
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<input type="button" value="Step 1 - Validate" style="width:160px; font-size:13px;" onclick="javascript:window.open('<?= $link ?>?page=migrate&type=zones&zone=<?= $_GET['zone'] ?>&step=1', target='_self');" />
				</td>
			</tr>
		</table>
	<?php } ?>
	</div>
<?php
}


function DisplayValidationData($data)
{
	?>
	<table width="100%" cellpadding="2" cellspacing="2" border="1" align="center">
		<tr>
			<td>id</td>
			<td>name</td>
			<td>primary</td>
			<td>secondary</td>
			<td>targetable</td>
			<td>attackable</td>
			<td>show_name</td>
			<td>show_level</td>
			<td>command_icon</td>
			<td>hand_icon</td>
		</tr>
		<?
		if( !empty($data) ) 
		{
			$count = 0;
			foreach($data as $key) 
			{
		?>
		<tr>
			<td>&nbsp;<?= $key['id'] ?></td>
			<td>&nbsp;<?= $key['name'] ?></td>
			<td>&nbsp;<?= $key['command_primary'] ?></td>
			<td>&nbsp;<?= $key['command_secondary'] ?></td>
			<td>&nbsp;<?= $key['targetable'] ?></td>
			<td>&nbsp;<?= $key['attackable'] ?></td>
			<td>&nbsp;<?= $key['show_name'] ?></td>
			<td>&nbsp;<?= $key['show_level'] ?></td>
			<td>&nbsp;<?= $key['show_command_icon'] ?></td>
			<td>&nbsp;<?= $key['display_hand_icon'] ?></td>
		</tr>
		<?
				$count++;
			}
		}
		?>
		<tr>
			<td colspan="10" style="font-weight:bold; text-align:right;"><?= $count ?> records found&nbsp;</td>
		</tr>
	</table>
	<?
}

function DisplayComparisonData($data, $db)
{
?>
					<table width="100%" cellpadding="2" cellspacing="2" border="1" align="center">
						<tr>
							<td colspan="5" align="center"><strong><?= $db ?></strong></td>
						</tr>
						<tr>
							<td>id</td>
							<td>name</td>
							<td>sub_title</td>
							<td>mode_type</td>
							<td>size</td>
						</tr>
						<?
						if( !empty($data) ) 
						{
							$count = 0;
							foreach($data as $key) 
							{
						?>
						<tr>
							<td>&nbsp;<?= $key['id'] ?></td>
							<td>&nbsp;<?= $key['name'] ?></td>
							<td>&nbsp;<?= $key['sub_title'] ?></td>
							<td>&nbsp;<?= $key['model_type'] ?></td>
							<td>&nbsp;<?= $key['size'] ?></td>
						</tr>
						<?
								$count++;
							}
						}
						?>
						<tr>
							<td colspan="5" style="font-weight:bold; text-align:right;"><?= $count ?> records found&nbsp;</td>
						</tr>
		      </table>
<?
}


function CleanupSpawnPointDupes()
{
	global $eq2, $db_name;

	print('<div id="zone-select">');
	$eq2->ZoneSelector(1);
	printf('&nbsp;<a href="?%s">Reload Page</a>', $_SERVER['QUERY_STRING']);
	print('</div><br>&nbsp;');

	if( isset($_GET['zone']) )
	{
	
	}
}


function CleanupNPCs() 
{
	global $eq2;
	
	print('<div id="zone-select">');
	$eq2->ZoneSelector(1);
	printf('&nbsp;<a href="?%s">Reload Page</a>', $_SERVER['QUERY_STRING']);
	print('</div><br>&nbsp;');

	if( isset($_GET['zone']) )
	{
		switch($_POST['cmd'])
		{
			case "Compare":
				if( isset($_POST['combine_from']) && isset($_POST['combine_to']) )
				{
					$_SESSION['combine_from'] = $_POST['combine_from'];
					$_SESSION['combine_to'] = $_POST['combine_to'];
					printf("<script language='javascript'>window.open('popups.php?type=alldata', 'npc', 'resizable,width=1152,left=50,top=75,scrollbars=yes', target='_blank')</script>");
				}
				break;
				
			case "Details":
				if( isset($_POST['combine_from']) )
					$_SESSION['combine_from'] = $_POST['combine_from'];
					$_SESSION['combine_to'] = $_POST['combine_to'];
					printf("<script language='javascript'>window.open('popups.php?type=spawn', 'npc', 'resizable,width=1152,left=50,top=75,scrollbars=yes', target='_blank')</script>");
				break;
				
			case "newMerge":
				if( isset($_POST['combine_from']) && isset($_POST['combine_to']) )
					$_SESSION['combine_from'] = $_POST['combine_from'];
					$_SESSION['combine_to'] = $_POST['combine_to'];
					printf("<script language='javascript'>window.open('popups.php?type=merge', 'npc', 'resizable,width=1152,left=50,top=75,scrollbars=yes', target='_blank')</script>");
				break;
				
			case "Merge":
				// both must be set to work
				if( ($from_id && $to_id) && ($from_id != $to_id) ) 
				{
					// Step 0: Validate the two spawns "appearances" are identical, or abort.
					$query = sprintf("SELECT spawn_id, type, red, green, blue FROM npc_appearance WHERE spawn_id = %lu", $from_id);
					//printf("%s<br />", $query);
				
					if( !$result = $eq2->db->sql_query($query) )
					{
						$error = $eq2->db->sql_error();
						die($error['message']."<br />".$query);
					}
					if( $eq2->db->sql_numrows($result) )
					{
						while( $data = $eq2->db->sql_fetchrow($result) )
						{
							$from_npc_appearance[] = $data;
						}
						
					}
				
					$query = sprintf("SELECT spawn_id, type, red, green, blue FROM npc_appearance WHERE spawn_id = %lu", $to_id);
					//printf("%s<br />", $query);
				
					if( !$result = $eq2->db->sql_query($query) )
					{
						$error = $eq2->db->sql_error();
						die($error['message']."<br />".$query);
					}
					if( $eq2->db->sql_numrows($result) )
					{
						while( $data = $eq2->db->sql_fetchrow($result) )
						{
							$to_npc_appearance[] = $data;
						}
						
					}
				
					//print_r($to_npc_appearance);
					
					if( is_array($from_npc_appearance) && is_array($to_npc_appearance) )
					{
						//print_r($from_npc_appearance); 
						//print_r($to_npc_appearance);
				
						$is_unique = false;
						foreach($from_npc_appearance as $from)
						{
							if( $is_unique )
								continue;
							foreach($to_npc_appearance as $to)
							{
								if( $is_unique )
									continue;
									
								if( $from['type'] == $to['type'] ) 
								{
									if ( $from['red'] == $to['red'] && $from['green'] == $to['green'] && $from['blue'] == $to['blue'] )
									{
										$is_unique = false;
										//printf("Dupe From %s: %s %s %s!<br>", $from['type'], $from['red'], $from['green'], $from['blue']);
										//printf("Dupe To %s: %s %s %s!<br>", $to['type'], $to['red'], $to['green'], $to['blue']);
									}
									else
									{
										$is_unique = true;
										//printf("Unique From %s: %s %s %s!<br>", $from['type'], $from['red'], $from['green'], $from['blue']);
										//printf("Unique To %s: %s %s %s!<br>", $to['type'], $to['red'], $to['green'], $to['blue']);
									}
								}
							}
						}
					}
					if( $is_unique )
					{
						die('<span style="color:#f00; font-weight:bold;">Cannot merge these two; appearances are unique!</span>');
						break;
					}
						
					// Step 1: Load spawn.id data for comparisons used in Merge (only 2 spawn_id's)
					$query = sprintf("SELECT * FROM ".$db_name.".spawn, ".$db_name.".spawn_npcs WHERE spawn.id = spawn_npcs.spawn_id AND spawn.id IN (%lu, %lu)", $to_id, $from_id);
					//printf("<br />Step 1: %s<br />", $query);
					if( !$result = $eq2->db->sql_query($query) )
					{
						$error = $eq2->db->sql_error();
						die($error['message']."<br />".$query);
					}
					while( $data = $eq2->db->sql_fetchrow($result) )
					{
						if( $data['spawn_id'] == $from_id )
						{
							$from_array[$from_id] = $data;
						}
						else if( $data['spawn_id'] == $to_id )
						{
							$to_array[$to_id] = $data;
						}
					}
					$eq2->db->sql_freeresult($result);
					// print($to_array[$to_id]['size']); exit;
				
					// Step 2: Move spawn_location_entry to new spawn_id
					$query = sprintf("UPDATE %s.spawn_location_entry SET spawn_id = %lu WHERE spawn_id = %lu", $db_name, $to_id, $from_id);
					//printf("Step 2: %s<br />", $query);
				
					if( !$result = $eq2->db->sql_query($query) )
					{
						$error = $eq2->db->sql_error();
						die($error['message']."<br />".$query);
					}
				
					// Step 3: Update new spawn_npcs.spawn_id min/max/size offsets to include old spawn_id ranges
					$min_level1 = $_POST["merge|min_level|".$from_id];
					$min_level2 = $_POST["merge|min_level|".$to_id];
					$max_level1 = $_POST["merge|max_level|".$from_id];
					$max_level2 = $_POST["merge|max_level|".$to_id];
					$size1 			= $_POST["merge|size|".$from_id];
					$size2 			= $_POST["merge|size|".$to_id];
					
					// calculate min_level
					$min = ( $min_level1 < $min_level2 ) ? $min_level1 : $min_level2;
					// calculate max_level
					$max = ( $max_level1 < $max_level2 ) ? $max_level2 : $max_level1;
					// calculate new size
					$size = ( $size1 == $size2 ) ? $size1 : round(($size1 + $size2) / 2);
					// calculate new size_offset
					if( $size1 <= $size2 )
						$size_offset = round(($size2 - $size1) / 2);
					else
						$size_offset = round(($size1 - $size2) / 2);
					
					$query = sprintf("UPDATE %s.spawn_npcs, %s.spawn SET min_level = %d, max_level = %d, size = %d, size_offset = %d WHERE spawn.id = spawn_npcs.spawn_id AND spawn_id = %lu", $db_name, $db_name, $min, $max, $size, $size_offset, $to_id);
					//printf("Step 3: %s<br />", $query);
				
					if( !$result = $eq2->db->sql_query($query) )
					{
						$error = $eq2->db->sql_error();
						die($error['message']."<br />".$query);
					}
				
					
					// Step 4: Move spawn_npcs.spawn_id to new spawn_id
					$query = sprintf("DELETE FROM %s.spawn_npcs WHERE spawn_id = %lu", $db_name, $from_id);
					//printf("Step 4: %s<br />", $query);
				
					if( !$result = $eq2->db->sql_query($query) )
					{
						$error = $eq2->db->sql_error();
						die($error['message']."<br />".$query);
					}
					
					
					// Step 5: Move npc_appearance/equip to new spawn_id
					$query = sprintf("DELETE FROM %s.npc_appearance WHERE spawn_id = %lu", $db_name, $from_id);
					//printf("Step 5a: %s<br />", $query);
					
					if( !$result = $eq2->db->sql_query($query) )
					{
						$error = $eq2->db->sql_error();
						die($error['message']."<br />".$query);
					}
					
					$query = sprintf("DELETE FROM %s.npc_appearance_equip WHERE spawn_id = %lu", $db_name, $from_id);
					//printf("Step 5b: %s<br />", $query);
					
					if( !$result = $eq2->db->sql_query($query) )
					{
						$error = $eq2->db->sql_error();
						die($error['message']."<br />".$query);
					}
				
					// Step 6: Move spawn_loot to new spawn_id
					$query = sprintf("DELETE FROM %s.spawn_loot WHERE spawn_id = %lu", $db_name, $from_id);
					//printf("Step 6: %s<br />", $query);
					
					if( !$result = $eq2->db->sql_query($query) )
					{
						$error = $eq2->db->sql_error();
						die($error['message']."<br />".$query);
					}
				
					// Step 7: Move spawn_ground to new spawn_id
					$query = sprintf("UPDATE %s.spawn_ground SET spawn_id = %lu WHERE spawn_id = %lu", $db_name, $to_id, $from_id);
					//printf("Step 7: %s<br />", $query);
					
					if( !$result = $eq2->db->sql_query($query) )
					{
						$error = $eq2->db->sql_error();
						die($error['message']."<br />".$query);
					}
				
					// Step 8: Validate spawn settings are correct for spawn type (command_primary = targetable/attackable 1, etc)
					$query = sprintf("UPDATE %s.spawn SET targetable = 1, attackable = 1, show_name = 1, show_level = 1 WHERE id = %lu AND command_primary = 3", $db_name, $to_id, $from_id);
					//printf("Step 8: %s<br />", $query);
					
					if( !$result = $eq2->db->sql_query($query) )
					{
						$error = $eq2->db->sql_error();
						die($error['message']."<br />".$query);
					}
				
					// Step 9: Delete old spawn.id
					$query = sprintf("DELETE FROM %s.spawn WHERE id = %lu", $db_name, $from_id);
					//printf("Step 9: %s<br />", $query);
					
					if( !$result = $eq2->db->sql_query($query) )
					{
						$error = $eq2->db->sql_error();
						die($error['message']."<br />".$query);
					}
					
					// Step 10: Hide the spawn from the Migrate Data wizard
					$query = sprintf("UPDATE eq2raw.spawn SET processed = 1 WHERE id = %lu", $from_id);
					//printf("Step 9: %s<br />", $query);
					
					if( !$result = $eq2->db->sql_query($query) )
					{
						$error = $eq2->db->sql_error();
						die($error['message']."<br />".$query);
					}
					
					$log_text = sprintf("Merged Spawn %lu to %lu", $from_id, $to_id);
					$p = array($log_text,$from_id,$query); // print_r($p);
					$eq2->dbEditorLog($p);
				}
				else
				{
					printf("Invalid From (%lu) and To (%lu) values.<br />", $from_id, $to_id);
					die();
				}
				break;		

			case "Change":
				echo $_POST['cmd'];
				/*
					Step 1: Move spawn_location_entry to new spawn_id in this zone
				*/
				break;		

			case "Despawn":
				echo $_POST['cmd'];
				/*
					Step 1: Delete spawn_location_entry for this spawn_id in this zone
				*/
				break;		

			case "Delete":
					// Step 1: Delete spawn.id from database
					$query = sprintf("DELETE FROM %s.spawn WHERE id = %lu", $db_name, $_POST['combine_from']);
					printf("Step 1: %s<br />", $query);
					/*if( !$result = $eq2->db->sql_query($query) )
					{
						$error = $eq2->db->sql_error();
						die($error['message']."<br />".$query);
					}*/
					
				break;		

		}
	
		// first, get list of spawn_id's that belong to a specific zone
		/*$query = sprintf("SELECT DISTINCT spawn_id
											FROM ".DEV_DB.".spawn_location_entry sle
											JOIN ".DEV_DB.".spawn_location_placement slp ON sle.spawn_location_id = slp.spawn_location_id
											WHERE zone_id = %d", $_GET['zone']);*/
		
		// 2011.08.07 - let's try a cleaner lookup...
		$query = sprintf("SELECT id FROM ".DEV_DB.".spawn WHERE id LIKE '%d____';", $_GET['zone']);
		//printf("%s<br />", $query);
		if( !$result = $eq2->db->sql_query($query) ) 
		{
			$error = $eq2->db->sql_error();
			die($error['message']);
		} else {
			while( $data = $eq2->db->sql_fetchrow($result) ) 
			{
				$spawn_id_list .= ( empty($spawn_id_list) ) ? $data['id'] : ", ".$data['id'];
			}
		}
	
		//print_r($spawn_id_list); exit;
	
		// now we group by all the common features to see which are duplicated, building a dupes list
		$query = sprintf("SELECT 
												name
											FROM ".DEV_DB.".spawn 
											RIGHT JOIN ".DEV_DB.".spawn_npcs ON spawn.id = spawn_npcs.spawn_id 
											WHERE 
												spawn.id IN (%s)
											GROUP BY 
												spawn.name, spawn.sub_title, spawn.race, spawn.model_type, spawn_npcs.class_, /* spawn_npcs.gender,*/ spawn_npcs.hair_type_id, spawn_npcs.facial_hair_type_id, spawn_npcs.wing_type_id, spawn_npcs.chest_type_id, spawn_npcs.legs_type_id, spawn_npcs.soga_hair_type_id, spawn_npcs.soga_facial_hair_type_id, spawn_npcs.soga_model_type 
												HAVING COUNT(*) > 1", $spawn_id_list);
		//printf("%s<br />", $query);
		if( !$result = $eq2->db->sql_query($query) ) 
		{
			$error = $eq2->db->sql_error();
			die($error['message']);
		} else {
			while( $data = $eq2->db->sql_fetchrow($result) ) 
			{
				$dupe_spawn_list .= ( empty($dupe_spawn_list) ) ? "'".addslashes($data['name'])."'" : ", '".addslashes($data['name'])."'";
			}
		}

		//print_r($dupe_spawn_list);
		if( isset($dupe_spawn_list) )
		{
			$query = sprintf("SELECT DISTINCT
												spawn.id, 
												spawn.name, 
												spawn.sub_title, 
												spawn.race, 
												spawn.model_type, 
												spawn.size, 
												spawn.size_offset, 

												spawn_npcs.min_level, 
												spawn_npcs.max_level, 
												spawn_npcs.enc_level, 
												spawn_npcs.class_, 
												spawn_npcs.gender, 
												spawn_npcs.heroic_flag
												
											FROM ".DEV_DB.".spawn 
											LEFT JOIN ".DEV_DB.".spawn_npcs ON spawn.id = spawn_npcs.spawn_id
											WHERE 
												spawn.name IN (%s)
												AND spawn.id in (%s)
											ORDER BY name, model_type, min_level, spawn.id", $dupe_spawn_list, $spawn_id_list);

/*											WHERE 
												spawn.name IN (%s)
											ORDER BY name, race, model_type, enc_level, min_level", $dupe_spawn_list);*/


	
			//printf("<br>%s<br />", $query);
			//exit;
			if( !$result = $eq2->db->sql_query($query) ) 
			{
				$error = $eq2->db->sql_error();
				die($error['message']."<br />".$query);
			} else {
				?>
				<table cellpadding="4" cellspacing="0" border="1">
					<form method="post">
					<tr>
						<td colspan="27">
							<ul>
								<li>Use <strong>Compare</strong> to popup NPC Appearances for difference comparison</li>
								<li>Use <strong>Merge</strong> to set min/max/size offset in FROM spawn to TO spawn, leaving only 1 spawn_id</li>
								<li>Use <strong>Change</strong> to switch the spawn_location_entry.spawn_id to the TO spawn_id (used if the wrong spawn_id range is showing up in a zone)</li>
								<li>Use <strong>Despawn</strong> to remove spawn_location_entry's from this zone (does not delete spawn record)</li>
								<li>Use <strong>Delete</strong> to remove record from `spawn` table competely (usually because it is a dupe - MERGE FIRST!)</li>
							</ul>
						</td>
					</tr>
				<?
				while( $data = $eq2->db->sql_fetchrow($result) ) 
				{
					$RowClass = ( $i % 2 ) ? " bgcolor=#eeeeee" : " bgcolor=#dddddd";
					
					if( substr($data['id'],0, strlen($_GET['zone'])) == $_GET['zone'] )
						$spawn_id = $data['id'];
					else 
						$spawn_id = sprintf('<span style="font-weight:bold; color:#f00;">%lu</span>', $data['id']);
						
					if( $currName != $data['name'] )
					{
						$currName = $data['name'];
						?>
						<tr>
							<td colspan="27">
								&nbsp;<input type="submit" name="cmd" value="Compare" style="width:70px; font-size:11px" />
								&nbsp;<input type="submit" name="cmd" value="Details" style="width:70px; font-size:11px" />
								&nbsp;<input type="submit" name="cmd" value="Merge" style="width:70px; font-size:11px" />
								&nbsp;<input type="submit" name="cmd" value="Change" style="width:70px; font-size:11px" />
								&nbsp;<input type="submit" name="cmd" value="Despawn" style="width:70px; font-size:11px" />
								&nbsp;<input type="submit" name="cmd" value="Delete" style="width:70px; font-size:11px" />
							</td>
						</tr>
						<tr style="font-weight:bold">
							<td colspan="2">&nbsp;</td>
							<td>spawn_id</td>
							<td>name</td>
							<td>title</td>
							<td>race</td>
							<td>model</td>
							<td>size</td>
							<td>offset</td>
							<td>min</td>
							<td>max</td>
							<td>enc</td>
							<td>heroic</td>
							<td>class_</td>
							<td>gender</td>
						</tr>
						<?
					}
					?>
					
					<tr<?= $RowClass ?> id="<?= $i ?>">
						<td>&nbsp;<input type="radio" name="combine_from" value="<?= $data['id']?>" /></td>
						<td>&nbsp;<input type="radio" name="combine_to" value="<?= $data['id']?>" /></td>
						<td>&nbsp;<?= $spawn_id ?></td>
						<td nowrap>&nbsp;<?= $data['name'] ?></td>
						<td nowrap>&nbsp;<?= preg_replace("/<(.*?)>/i", "&lt;$1&gt;", $data['sub_title']) ?></td>
						<td>&nbsp;<?= $data['race'] ?></td>
						<td>&nbsp;<?= $data['model_type'] ?></td>
						<td>&nbsp;<?= $data['size'] ?><input type="hidden" name="merge|size|<?= $data['id']?>" value="<?= $data['size']?>" /></td>
						<td>&nbsp;<?= $data['size_offset'] ?><input type="hidden" name="merge|size_offset|<?= $data['id']?>" value="<?= $data['size_offset']?>" /></td>
						<td>&nbsp;<?= $data['min_level'] ?><input type="hidden" name="merge|min_level|<?= $data['id']?>" value="<?= $data['min_level']?>" /></td>
						<td>&nbsp;<?= $data['max_level'] ?><input type="hidden" name="merge|max_level|<?= $data['id']?>" value="<?= $data['max_level']?>" /></td>
						<td>&nbsp;<?= $data['enc_level'] ?></td>
						<td>&nbsp;<?= $data['heroic_flag'] ?></td>
						<td>&nbsp;<?= $data['class_'] ?></td>
						<td>&nbsp;<?= $data['gender'] ?></td>
					</tr>
					<?
					$i++;			
				}
				?>
				</form>
			</table>
			<?
			}
		}
		else
		{
			print("No duplicates detected! Congrats!");
		}
	}
	$eq2->db->sql_freeresult($result);
}


function ConvertHarvestingNodes($low, $high, $subcategory) {
	global $eq2, $db_name;

	$model_id_list = getModelIDs($subcategory);
	// fetch all existing converted node ID's
	$sql1 = sprintf("select id, name, model_type from %s.spawn where model_type in (%s) and id <= 10000 order by model_type, name, id", $db_name, $model_id_list); 
	if( SHOW_DEBUG ) 
		$eq2->dbgQuery($sql1);
	if( !$result1 = $eq2->db->sql_query($sql1) ) 
		$eq2->sqlError($sql1);
	while($data = $eq2->db->sql_fetchrow($result1)) 
		$reindex_nodes[] = $data;

	// fetch all node ID's needing conversion/re-indexing
	$sql2 = sprintf("select id, name, model_type from %s.spawn where model_type in (%s) and id >= 10000 order by model_type, name, id", $db_name, $model_id_list); 
	if( SHOW_DEBUG ) 
		$eq2->dbgQuery($sql2);
	if( !$result2 = $eq2->db->sql_query($sql2) )
		$eq2->sqlError($sql2);
	while($data = $eq2->db->sql_fetchrow($result2)) 
		$convert_nodes[] = $data;
?>
	<fieldset><legend><?= $subcategory ?> Nodes</legend>
	<table width="100%">
		<tr>
			<td>
				<table width="100%" border="1">
					<tr>
						<td colspan="4" align="center"><strong>Source</strong></td>
						<td colspan="3" align="center"><strong>Destination</strong></td>
					</tr>
					<tr>
						<td width="10%">&nbsp;<strong>ID</strong></td>
						<td width="25%">&nbsp;<strong>Node Name</strong></td>
						<td width="10%">&nbsp;<strong>Model</strong></td>
						<td width="5%" align="center">
						<td width="10%">&nbsp;<strong>ID</strong></td>
						<td width="30%">&nbsp;<strong>Node Name</strong></td>
						<td width="10%">&nbsp;<strong>Model</strong></td>
					</tr>
					<?
					if( is_array($convert_nodes) )
					{
						foreach($convert_nodes as $nodes)
						{
							$reindex_it = false;
							$convert_it = false;
							
							if( ($current_model_type != $nodes['model_type'] || $current_model_name != $nodes['name']) )
							{
								// new model/name
								$current_model_type = $nodes['model_type'];
								$current_model_name = $nodes['name'];
								foreach($reindex_nodes as $reindex)
								{
									if( $current_model_type == $reindex['model_type'] && $current_model_name == $reindex['name'] )
									{
										$reindex_it = true;
										$convert_it = false;
										$new_model_id = $reindex['id'];
										$new_model_type = $reindex['model_type'];
										$new_model_name = $reindex['name'];
									}
									else
									{
										$reindex_it = false;
										$convert_it = true;
									}
								}
							}
							else
							{
								$reindex_it = true;
								$convert_it = false;
							}

							if( $reindex_it )
							{
								// re-index
								printf('<tr><td title="%s" style="cursor:pointer">%s</td><td>%s</td><td>%s</td>', 
									$eq2->getZoneNameByID(round($nodes['id'] / 10000)), $nodes['id'], $nodes['name'], $nodes['model_type']);
								printf('<form method="post" name="form_%s">', $nodes['id']);
								printf('<td>&nbsp;<input type="submit" name="reidx|%s" value="Re-Index" style="font-size:9px; width:60px;" />', 
									$nodes['id']);
								printf('<input type="hidden" name="new_id" value="%s" /><input type="hidden" name="orig_object" value="%s" /><input type="hidden" name="cmd" value="reindex" /></td>', 
									$new_model_id, addslashes($new_model_name));
								printf('<td>%s</td><td>%s</td><td>%s</td></form>',
									$new_model_id, addslashes($new_model_name), $new_model_type);
							}
							
							if( $convert_it )
							{
								// convert
								$next_node_id = GetNextNodeIDX('spawn', $low/100);
								printf('<tr><td title="%s" style="cursor:pointer">%s</td><td>%s</td><td>%s</td>', 
									$eq2->getZoneNameBySpawnID($row['id']), $nodes['id'], $nodes['name'], $nodes['model_type']);
								printf('<form method="post" name="form_%s">', $nodes['id']);
								printf('<td>&nbsp;<input type="submit" name="conv|%s" value="Convert" style="font-size:9px; width:60px;" />', 
									$nodes['id']);
								printf('<input type="hidden" name="new_id" value="%s" /><input type="hidden" name="orig_object" value="%s" /><input type="hidden" name="cmd" value="convert" />', 
									$next_node_id, addslashes($nodes['name']));
								printf('</td></form>');
								printf('<td>&nbsp;%s</td><td colspan="2">&nbsp;</td></tr>',$next_node_id);
							}
						}
					}
					?>
				</table>
			</td>
		</tr>
	</table>
	</fieldset>
	<br />
<?php
}


function GetModelIDs($var) {
	global $eq2, $db_name;
	
	$query = sprintf("select model_type from %s.eq2models where subcategory = '%s' order by model_type;", $db_name, $var); //echo $query;
	if( !$result = $eq2->db->sql_query($query) ) {
		$error = $eq2->db->sql_error();
		die($error['message']);
	} else {
		while($row = $eq2->db->sql_fetchrow($result)) {
			if( empty($row_data) ) {
				$row_data = $row['model_type'];
			} else {
				$row_data .= ",".$row['model_type'];
			}
		}
	}
	return $row_data;
}


function GetNextNodeIDX($table,$id) {
	global $eq2, $db_name;

	$query=sprintf("select max(id)+1 as nextID from %s.%s where id like \"%d__\";", $db_name, $table, $id);
	$result=$eq2->db->sql_query($query);
	$data=$eq2->db->sql_fetchrow($result);
	return isset($data['nextID']) ? $data['nextID'] : $id.'00';
}

?>