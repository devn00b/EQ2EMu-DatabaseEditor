<?php include("header.php"); 
if( $eq2->mLev < 250 ) { print("Options Not Available Yet!"); exit; }

$Page = isset($_GET['page']) ? $_GET['page'] : "";
$Type = isset($_GET['type']) ? $_GET['type'] : "";
?>
<div id="sub-menu1">
	<table cellspacing="0">
		<tr>
			<td align="right" width="100px"><strong>Zones:</strong> </td>
			<td>&nbsp;
				[ <a href="<?php print($link) ?>?page=raw">Raw To Dev</a> ] &bull;
				[ <a href="<?php print($link) ?>?page=dev">Dev To Live</a> ]
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><br />Note: Active Database selector has no effect using these functions.</td>
		</tr>
<?php
// sub-menu from above selection
switch( $Page ) {
	case "raw": 
	case "dev": $Type = "migrate"; break;
}
?>
	</table>
</div>
<div id="dbmanager-body">
<?php
if( isset($Type) ) {

	switch( $Type ) {
		case "migrate" : MigrateData(); break;
	}
}
?>
</div>
<?
include_once("footer.php"); 
exit;


function MigrateData() {
	global $eq2;
	
	$source_db = $_GET['page'] == "raw" ? "eq2raw" : "eq2dev";
	$destination_db = $source_db == "eq2raw" ? "eq2dev" : "eq2migrate";
	
	print('<div id="zone-select">');
	$eq2->ZoneSelector(1);
	printf('&nbsp;<a href="?%s">Reload Page</a>', $_SERVER['QUERY_STRING']);

	//$show_comparison = true;
	
	switch( $_POST['cmd'] ) 
	{
		case "Migrate"			: PurgeZone($_GET['zone']); break;
		case "Complete"			: CompleteZonePurge($_GET['zone']); break;
		case "Step 2"				: UpdateGroundSpawns($_GET['zone']); break;
		case "Step 3"				: InsertSpawns($_GET['zone']); break;
		case "Step 4"				: UpdateSpawnData($_GET['zone']); break;
		case "Step 5"				: UpdateAuxZoneData($_GET['zone']); break;
		case "Delete These" : 
			//$show_comparison = false; 
			DeleteSpawns(); 
			break;
		case "Spawn These" 	: 
			//$show_comparison = false; 
			PlaceSpawns(); 
			break;
	}
	
	CompareZones();
	
}	

function CompareZones() 
{
	global $eq2;

	if( isset($_GET['zone']) )
	{
		$query = sprintf("SELECT DISTINCT s.* 
											FROM `".DEV_DB."`.spawn s
											JOIN `".DEV_DB."`.spawn_location_entry sle ON s.id = sle.spawn_id
											JOIN `".DEV_DB."`.spawn_location_placement slp ON sle.spawn_location_id = slp.spawn_location_id
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
											FROM `".LIVE_DB."`.spawn s
											JOIN `".LIVE_DB."`.spawn_location_entry sle ON s.id = sle.spawn_id
											JOIN `".LIVE_DB."`.spawn_location_placement slp ON sle.spawn_location_id = slp.spawn_location_id
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
											FROM `".DEV_DB."`.spawn s
											WHERE 
												s.id not in (select spawn_id from `".DEV_DB."`.spawn_location_entry) AND
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
											FROM `".LIVE_DB."`.spawn s
											WHERE 
												s.id not in (select spawn_id from `".LIVE_DB."`.spawn_location_entry) AND
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
		print('<table width="100%" align="center">');
		print('<tr><td colspan="2" align="center" style="font-size:24px; color:#f00;"><strong>STEP 1</strong></td></tr>');
		print('<tr><td colspan="2" align="center" style="font-size:13px;"><strong>PREVIEW: Differences Between Dev and Live Zone</strong></td></tr>');
		print('<tr><td colspan="2" style="font-size:11px;"><ol><li>Using the 2 tables below, compare Dev to Live data to be sure this is the data you wish to migrate</li>');
		print('<li>Clean up (delete or spawn) any spawns that do not have placements (bottom left table)</li>');
		print('<li>If you are keeping the non-placed spawns, be sure to combine the non-placed spawn with any currently placed spawn and adjust size/level offsets in existing spawn placement</li>');
		print('</td></tr>');
		print('<tr><td width="50%" valign="top">');
		print('<table width="100%" cellpadding="2" cellspacing="2" border="1" align="center">');
		printf('<tr><td colspan="5" align="center"><strong>%s</strong></td></tr>', DEV_DB);
		print('<tr><td>id</td><td>name</td><td>sub_title</td><td>mode_type</td><td>size</td></tr>');
		if( !empty($dev_data) ) {
			$count = 0;
			foreach($dev_data as $key) {
				printf('<tr><td>&nbsp;%s</td><td>&nbsp;%s</td><td>&nbsp;%s</td><td>&nbsp;%s</td><td>&nbsp;%s</td></tr>',
					$key['id'], $key['name'], $key['sub_title'], $key['model_type'], $key['size']);
				$count++;
			}
		}
		printf('<tr><td colspan="5" style="font-weight:bold; text-align:right;">%d records found&nbsp;</td></tr>', $count);
		print('</table>');
		print('</td><td width="50%" valign="top">');
		print('<table width="100%" cellpadding="2" cellspacing="2" border="1" align="center">');
		printf('<tr><td colspan="5" align="center"><strong>%s</strong></td></tr>', LIVE_DB);
		print('<tr><td>id</td><td>name</td><td>sub_title</td><td>mode_type</td><td>size</td></tr>');
		if( !empty($live_data) ) {
			$count = 0;
			foreach($live_data as $key) {
				printf('<tr><td>&nbsp;%s</td><td>&nbsp;%s</td><td>&nbsp;%s</td><td>&nbsp;%s</td><td>&nbsp;%s</td></tr>',
					$key['id'], $key['name'], $key['sub_title'], $key['model_type'], $key['size']);
				$count++;
			}
		}
		printf('<tr><td colspan="5" style="font-weight:bold; text-align:right;">%d records found&nbsp;</td></tr>', $count);
		print('</table>');
		print('</td></tr>');

		print('<tr><td colspan="2" style="font-size:13px; font-weight:bold; text-align:center;">PREVIEW: Spawns Without Spawn Placements</td></tr>');
		print('<tr><td width="50%" valign="top">');
		print('<table width="100%" cellpadding="2" cellspacing="2" border="1" align="center">');
		printf('<tr><td colspan="5" align="center"><strong>%s</strong></td></tr>', DEV_DB);
		print('<tr><td>id</td><td>name</td><td>sub_title</td><td>mode_type</td><td>size</td></tr>');
		if( !empty($dev_data2) ) {
			$count = 0;
			foreach($dev_data2 as $key) {
				printf('<tr><td>&nbsp;%s</td><td>&nbsp;%s</td><td>&nbsp;%s</td><td>&nbsp;%s</td><td>&nbsp;%s</td></tr>',
					$key['id'], $key['name'], $key['sub_title'], $key['model_type'], $key['size']);
				$count++;
				// build hidden fields to handle these non-placed spawns
				$cleanup_ids .= sprintf('<input type="hidden" name="clean_%d" value="%s">', $count, $key['id']);
			}
		}
		printf('<form method="post" name="Cleanup"><tr><td colspan="2">&nbsp;<input type="submit" name="cmd" value="Delete These" style="width:90px; font-size:10px;">&nbsp;<input type="submit" name="cmd" value="Spawn These" style="width:90px; font-size:10px;">%s</td><td colspan="3" style="font-weight:bold; text-align:right;">%d records found&nbsp;</td></tr></form>', $cleanup_ids, $count);
		print('</table>');
		print('</td><td width="50%" valign="top">');
		print('<table width="100%" cellpadding="2" cellspacing="2" border="1" align="center">');
		printf('<tr><td colspan="5" align="center"><strong>%s</strong></td></tr>', LIVE_DB);
		print('<tr><td>id</td><td>name</td><td>sub_title</td><td>mode_type</td><td>size</td></tr>');
		if( !empty($live_data2) ) {
			$count = 0;
			foreach($live_data2 as $key) {
				printf('<tr><td>&nbsp;%s</td><td>&nbsp;%s</td><td>&nbsp;%s</td><td>&nbsp;%s</td><td>&nbsp;%s</td></tr>',
					$key['id'], $key['name'], $key['sub_title'], $key['model_type'], $key['size']);
				$count++;
			}
		}
		printf('<tr><td colspan="5" style="font-weight:bold; text-align:right;">%d records found&nbsp;</td></tr>', $count);
		print('</table>');
		print('</td></tr>');

		print('<tr><td colspan="2" style="font-size:12px"><br /><strong>BEFORE YOU MIGRATE:</strong> Be sure any spawns without placements are deleted, or get placements, or they will not be migrated.</td></tr>');
		print('<tr><td colspan="2"><form method="post" name="MigrateZone"><tr><td colspan="2" align="center"><input type="submit" name="cmd" value="Migrate" style="width:90px; font-size:13px;" /></td></tr></form></td></tr>');
		print('</table>');
	}
	print('</div>');
}


function DeleteSpawns()
{
	global $eq2;

	$ok_clean = false;
	foreach($_POST as $key=>$val)
	{
		$tmp = explode("_", $key);
		if( $tmp[0] == "clean" )
		{
			$ok_clean = true;
			$spawn_ids .= ( isset($spawn_ids) ) ? ", " . $val : $val;
		}
	}
	
	if( isset($spawn_ids) )
	{
		print('<table width="100%" border="1" align="center">');
		print('<tr><td colspan="10" height="30" valign="bottom" align="center" style="font-size:13px;"><strong>PREVIEW: <font color="red">Delete From Dev</font></strong></td></tr>');
		print('<tr><td colspan="5" width="50%" align="center"><strong>To Be Deleted</strong></td><td colspan="5" width="50%" align="center"><strong>Matched In DB</strong></td></tr>');
		print('<tr><td>id</td><td>name</td><td>sub_title</td><td>mode_type</td><td style="border-right:2px solid #000;">size</td><td>id</td><td>name</td><td>sub_title</td><td>mode_type</td><td>size</td></tr>');

		$query = sprintf("SELECT 
												spawn.id, 
												spawn.name, 
												spawn.sub_title, 
												spawn.race, 
												spawn.model_type, 
												spawn.size, 
												spawn.size_offset, 
												spawn.targetable, 
												spawn.show_name, 
												spawn.command_primary, 
												spawn.command_secondary, 
												spawn.visual_state, 
												spawn.attackable, 
												spawn.show_level, 
												spawn.show_command_icon, 
												spawn.display_hand_icon, 
												spawn.faction_id, 
												spawn.collision_radius, 
												spawn.hp, 
												spawn.power, 
												spawn.merchant_id, 
												spawn.transport_id, 
												spawn.merchant_type,  
											
												spawn_npcs.spawn_id, 	
												spawn_npcs.min_level, 
												spawn_npcs.max_level, 
												spawn_npcs.enc_level, 
												spawn_npcs.class_, 
												spawn_npcs.gender, 
												spawn_npcs.min_group_size, 
												spawn_npcs.max_group_size, 
												spawn_npcs.hair_type_id, 
												spawn_npcs.facial_hair_type_id, 
												spawn_npcs.wing_type_id, 
												spawn_npcs.chest_type_id, 
												spawn_npcs.legs_type_id, 
												spawn_npcs.soga_hair_type_id, 
												spawn_npcs.soga_facial_hair_type_id, 
												spawn_npcs.soga_model_type, 
												spawn_npcs.heroic_flag, 
												spawn_npcs.action_state, 
												spawn_npcs.mood_state, 
												spawn_npcs.initial_state, 
												spawn_npcs.activity_status, 
												
												spawn_objects.spawn_id, 	
											
												spawn_signs.spawn_id, 
												spawn_signs.type, 
												spawn_signs.zone_id, 
												spawn_signs.widget_id, 
												spawn_signs.title, 
												spawn_signs.widget_x, 
												spawn_signs.widget_y, 
												spawn_signs.widget_z, 
												spawn_signs.icon, 
												spawn_signs.description, 
												spawn_signs.sign_distance, 
												spawn_signs.zone_x, 
												spawn_signs.zone_y, 
												spawn_signs.zone_z, 
												spawn_signs.zone_heading, 
												spawn_signs.include_heading, 
												spawn_signs.include_location, 
											
												spawn_widgets.spawn_id, 
												spawn_widgets.widget_id, 
												spawn_widgets.widget_x, 
												spawn_widgets.widget_y, 
												spawn_widgets.widget_z, 
												spawn_widgets.include_heading, 
												spawn_widgets.include_location, 
												spawn_widgets.icon, 
												spawn_widgets.type, 
												spawn_widgets.open_heading, 
												spawn_widgets.closed_heading, 
												spawn_widgets.open_y, 
												spawn_widgets.action_spawn_id, 
												spawn_widgets.open_sound_file, 
												spawn_widgets.close_sound_file, 
												spawn_widgets.open_duration, 
												spawn_widgets.close_y, 
												spawn_widgets.linked_spawn_id, 
											
												spawn_ground.spawn_id, 
												spawn_ground.number_harvests, 
												spawn_ground.num_attempts_per_harvest, 
												spawn_ground.groundspawn_entry_id, 
												spawn_ground.collection_skill
												
											FROM `".DEV_DB."`.spawn 
											LEFT JOIN `".DEV_DB."`.spawn_npcs ON spawn.id = spawn_npcs.spawn_id
											LEFT JOIN `".DEV_DB."`.spawn_objects ON spawn.id = spawn_objects.spawn_id
											LEFT JOIN `".DEV_DB."`.spawn_signs ON spawn.id = spawn_signs.spawn_id
											LEFT JOIN `".DEV_DB."`.spawn_widgets ON spawn.id = spawn_widgets.spawn_id
											LEFT JOIN `".DEV_DB."`.spawn_ground ON spawn.id = spawn_ground.spawn_id
											WHERE 
												spawn.id IN (%s)", $spawn_ids);
												
		if( !$resultZ = $eq2->db->sql_query($query) ) {
			$error = $eq2->db->sql_error();
			die($error['message']);
		} else {
			while( $data = $eq2->db->sql_fetchrow($resultZ) ) {
			
				//$id_link = sprintf('<a href="../editors/spawns.php?z=%d&t=%s&s=%lu" target="_self">%lu</a>', $eq2->GetZoneIDBySpawnID($data['id']), $eq2->GetSpawnTypeBySpawnID($data['id']), $data['id'], $data['id']);
				printf('<tr><td>&nbsp;%s</td><td>&nbsp;%s</td><td>&nbsp;%s</td><td>&nbsp;%s</td><td style="border-right:2px solid #000;">&nbsp;%s</td>',
					$id_link, $data['name'], $data['sub_title'], $data['model_type'], $data['size']);

				$field_count = $eq2->db->sql_numfields($resultZ);

				for( $i = 0; $i < $field_count; $i++ )
				{
					$field_name = $eq2->db->sql_fieldname($i);
					
					if( !isset($data[$field_name]) || !strcmp($field_name, "id") ) 
						continue;

					$current_table = $eq2->db->sql_fieldtable($i, $resultZ);
					$compare_data .= sprintf("(%s.%s = '%s')", $current_table, $field_name, $data[$field_name]);
				}

				$compare_data = preg_replace("/\)\(/", ") AND (", $compare_data);
				$query2 = sprintf("SELECT 
														spawn.id, 
														spawn.name, 
														spawn.sub_title, 
														spawn.race, 
														spawn.model_type, 
														spawn.size, 
														spawn.size_offset, 
														spawn.targetable, 
														spawn.show_name, 
														spawn.command_primary, 
														spawn.command_secondary, 
														spawn.visual_state, 
														spawn.attackable, 
														spawn.show_level, 
														spawn.show_command_icon, 
														spawn.display_hand_icon, 
														spawn.faction_id, 
														spawn.collision_radius, 
														spawn.hp, 
														spawn.power, 
														spawn.merchant_id, 
														spawn.transport_id, 
														spawn.merchant_type,  
													
														spawn_npcs.spawn_id, 	
														spawn_npcs.min_level, 
														spawn_npcs.max_level, 
														spawn_npcs.enc_level, 
														spawn_npcs.class_, 
														spawn_npcs.gender, 
														spawn_npcs.min_group_size, 
														spawn_npcs.max_group_size, 
														spawn_npcs.hair_type_id, 
														spawn_npcs.facial_hair_type_id, 
														spawn_npcs.wing_type_id, 
														spawn_npcs.chest_type_id, 
														spawn_npcs.legs_type_id, 
														spawn_npcs.soga_hair_type_id, 
														spawn_npcs.soga_facial_hair_type_id, 
														spawn_npcs.soga_model_type, 
														spawn_npcs.heroic_flag, 
														spawn_npcs.action_state, 
														spawn_npcs.mood_state, 
														spawn_npcs.initial_state, 
														spawn_npcs.activity_status, 
														
														spawn_objects.spawn_id, 	
													
														spawn_signs.spawn_id, 
														spawn_signs.type, 
														spawn_signs.zone_id, 
														spawn_signs.widget_id, 
														spawn_signs.title, 
														spawn_signs.widget_x, 
														spawn_signs.widget_y, 
														spawn_signs.widget_z, 
														spawn_signs.icon, 
														spawn_signs.description, 
														spawn_signs.sign_distance, 
														spawn_signs.zone_x, 
														spawn_signs.zone_y, 
														spawn_signs.zone_z, 
														spawn_signs.zone_heading, 
														spawn_signs.include_heading, 
														spawn_signs.include_location, 
													
														spawn_widgets.spawn_id, 
														spawn_widgets.widget_id, 
														spawn_widgets.widget_x, 
														spawn_widgets.widget_y, 
														spawn_widgets.widget_z, 
														spawn_widgets.include_heading, 
														spawn_widgets.include_location, 
														spawn_widgets.icon, 
														spawn_widgets.type, 
														spawn_widgets.open_heading, 
														spawn_widgets.closed_heading, 
														spawn_widgets.open_y, 
														spawn_widgets.action_spawn_id, 
														spawn_widgets.open_sound_file, 
														spawn_widgets.close_sound_file, 
														spawn_widgets.open_duration, 
														spawn_widgets.close_y, 
														spawn_widgets.linked_spawn_id, 
													
														spawn_ground.spawn_id, 
														spawn_ground.number_harvests, 
														spawn_ground.num_attempts_per_harvest, 
														spawn_ground.groundspawn_entry_id, 
														spawn_ground.collection_skill
														
													FROM `".DEV_DB."`.spawn 
													LEFT JOIN `".DEV_DB."`.spawn_npcs ON spawn.id = spawn_npcs.spawn_id
													LEFT JOIN `".DEV_DB."`.spawn_objects ON spawn.id = spawn_objects.spawn_id
													LEFT JOIN `".DEV_DB."`.spawn_signs ON spawn.id = spawn_signs.spawn_id
													LEFT JOIN `".DEV_DB."`.spawn_widgets ON spawn.id = spawn_widgets.spawn_id
													LEFT JOIN `".DEV_DB."`.spawn_ground ON spawn.id = spawn_ground.spawn_id
													WHERE 
														%s AND spawn.id != %lu", $compare_data, $data['id']);
				//echo $query2; exit;
				if( !$result2 = $eq2->db->sql_query($query2) ) {
					$error = $eq2->db->sql_error();
					die($error['message']);
				} else {
					if( $eq2->db->sql_numrows($result2) > 0 )
					{
						while( $data2 = $eq2->db->sql_fetchrow($result2) ) {
							printf('<td>&nbsp;%s</td><td>&nbsp;%s</td><td>&nbsp;%s</td><td>&nbsp;%s</td><td>&nbsp;%s</td>',
								$data2['id'], $data2['name'], $data2['sub_title'], $data2['model_type'], $data2['size']);
						}
					}
					else
					{
						printf('<td colspan="5">&nbsp;No Match Found</td>');
					}
					// $eq2->db->sql_freeresult($result2);
				}
			}
		}
		//$eq2->db->sql_freeresult($result);

exit;
		printf('<form method="post" name="Cleanup"><tr><td colspan="2">&nbsp;<input type="submit" name="cmd" value="Delete These" style="width:90px; font-size:10px;">&nbsp;<input type="submit" name="cmd" value="Spawn These" style="width:90px; font-size:10px;">%s</td><td colspan="3" style="font-weight:bold; text-align:right;">%d records found&nbsp;</td></tr></form>', $cleanup_ids, $count);
		print('</table>');
		print('</td><td width="50%" valign="top">');
		print('<table width="100%" cellpadding="2" cellspacing="2" border="1" align="center">');
		printf('<tr><td colspan="5" align="center"><strong>%s</strong></td></tr>', $destination_db);
		print('<tr><td>id</td><td>name</td><td>sub_title</td><td>mode_type</td><td>size</td></tr>');
		if( !empty($live_data2) ) {
			$count = 0;
			foreach($live_data2 as $key) {
				printf('<tr><td>&nbsp;%s</td><td>&nbsp;%s</td><td>&nbsp;%s</td><td>&nbsp;%s</td><td>&nbsp;%s</td></tr>',
					$key['id'], $key['name'], $key['sub_title'], $key['model_type'], $key['size']);
				$count++;
			}
		}
		printf('<tr><td colspan="5" style="font-weight:bold; text-align:right;">%d records found&nbsp;</td></tr>', $count);
		print('</table>');
		print('</td></tr>');

		print('<tr><td colspan="2" style="font-size:12px"><br /><strong>BEFORE YOU MIGRATE:</strong> Be sure any spawns without placements are deleted, or get placements, or they will not be migrated.</td></tr>');
		print('<tr><td colspan="2"><form method="post" name="MigrateZone"><tr><td colspan="2" align="center"><input type="submit" name="cmd" value="Migrate" style="width:90px; font-size:13px;" /></td></tr></form></td></tr>');
		print('</table>');
	


	}
}


function PlaceSpawns()
{
	global $eq2;

	$ok_clean = false;
	foreach($_POST as $key=>$val)
	{
		$tmp = explode("_", $key);
		if( $tmp[0] == "clean" )
		{
			$ok_clean = true;
			$spawn_ids .= ( isset($spawn_ids) ) ? ", " . $val : $val;
		}
	}
	$query = sprintf("SELECT * FROM spawn WHERE id in (%s)", $spawn_ids); echo $query;
	
}


function PurgeZone($id)
{
	global $eq2;

	// Step 1: Build list of spawn_id's to purge from destination DB
	$query = sprintf("DELETE FROM spawn WHERE id LIKE '%d____' ORDER BY id;");

}


function ProcessMigrateZone() 
{
	
	$source_db = $_GET['page'] == "raw" ? "eq2raw" : "eq2dev";
	$destination_db = $source_db == "eq2raw" ? "eq2dev" : "eq2live";
	
	if( isset($_GET['zone']) ) {
		// first, purge destination info
		$query = sprintf("delete from %s.spawn where id like '%d____';", $destination_db, $_GET['zone']);
		printf("%s<br>", $query);
		$query = sprintf("delete from %s.spawn_location_entry where spawn_id like '%d____';", $destination_db, $_GET['zone']);
		printf("%s<br>", $query);
		$query = sprintf("delete from %s.spawn_location_name where id not in (select spawngroup_id from spawn_location_entry);", $destination_db);
		printf("%s<br>", $query);

		// next, push new zone data from the dev server to live
		$query = sprintf("insert into %s.spawn s1 SELECT * FROM %s.spawn s2 WHERE s2.id IN (SELECT spawn_id FROM %s.spawn_location_entry z1, %s.spawn_location_placement z2 WHERE z1.spawn_location_id = z2.spawngroup_id AND zone_id = '%d____');", $destination_db, $source_db, $source_db, $source_db, $_GET['zone']);
		printf("%s<br>", $query);
		$query = sprintf("insert into %s.spawn_npcs s1 select * from %s.spawn_npcs s2 where s2.spawn_id like '%d____';", $destination_db, $source_db, $_GET['zone']);
		printf("%s<br>", $query);
		$query = sprintf("insert into %s.spawn_objects s1 select * from %s.spawn_objects s2 where s2.spawn_id like '%d____';", $destination_db, $source_db, $_GET['zone']);
		printf("%s<br>", $query);
		$query = sprintf("insert into %s.spawn_signs s1 select * from %s.spawn_signs s2 where s2.spawn_id like '%d____';", $destination_db, $source_db, $_GET['zone']);
		printf("%s<br>", $query);
		$query = sprintf("insert into %s.spawn_widgets s1 select * from %s.spawn_widgets s2 where s2.spawn_id like '%d____';", $destination_db, $source_db, $_GET['zone']);
		printf("%s<br>", $query);
		$query = sprintf("insert into %s.spawn_ground s1 select * from %s.spawn_ground s2 where s2.spawn_id like '%d____';", $destination_db, $source_db, $_GET['zone']);
		printf("%s<br>", $query);

		$query = sprintf("insert into %s.npc_appearance s1 select * from %s.npc_appearance s2 where s2.spawn_id like '%d____';", $destination_db, $source_db, $_GET['zone']);
		printf("%s<br>", $query);
		$query = sprintf("insert into %s.npc_appearance_equip s1 select * from %s.npc_appearance_equip s2 where s2.spawn_id like '%d____';", $destination_db, $source_db, $_GET['zone']);
		printf("%s<br>", $query);

		$query = sprintf("insert into %s.spawn_location_name s1 select * from %s.spawn_location_name s2 where s2.id in (select spawngroup_id from %s.spawn_location_placement where zone_id = '%d');", $destination_db, $source_db, $source_db, $_GET['zone']);
		printf("%s<br>", $query);
		$query = sprintf("insert into %s.spawn_location_entry s1 select * from %s.spawn_location_entry s2 where s2.spawn_location_id in (select spawngroup_id from %s.spawn_location_placement where zone_id = '%d');", $destination_db, $source_db, $source_db, $_GET['zone']);
		printf("%s<br>", $query);
		
		
	} else {
		die("No zone selected. How did you get here?");
	}
}

?>