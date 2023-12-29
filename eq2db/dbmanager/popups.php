<?php 
include("header_short.php"); 

$Type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "";

?>
<div id="dbmanager-body">
<?php
if( isset($Type) ) {

	switch( $Type ) {
		case "spawn" 			: ShowSpawnDetails(); break;
		case "appearance"	: ShowSpawnAppearances(); break;
		case "alldata"		: CompareAllData(); break;
		case "merge"			: Merge(); break;
	}
}
?>
</div>
<?
include_once("footer.php"); 
exit;

function Merge()
{
	global $eq2;

	$_SESSION['merging'] == 1;
	$from_id 	= isset($_SESSION['combine_from']) ? $_SESSION['combine_from'] : 0;
	$to_id 		= isset($_SESSION['combine_to']) ? $_SESSION['combine_to'] : 0;

	//$mergeOK	= isset($_POST['doMerge']) ? 1 : 0;
	//print($mergeOK);
	$mergeOK	= 1;

	
	unset($_SESSION['merging']);

}


function CompareAllData()
{
	global $eq2;
	
	if( (isset($_SESSION['combine_from']) && isset($_SESSION['combine_to'])) && ($_SESSION['combine_from'] != $_SESSION['combine_to']) )
	{
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
											
												spawn_npcs.spawn_id as npc_spawn_id, 	
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
												
												spawn_objects.spawn_id as object_spawn_id, 	
											
												spawn_signs.spawn_id as sign_spawn_id, 
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
											
												spawn_widgets.spawn_id as widget_spawn_id, 
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
											
												spawn_ground.spawn_id as ground_spawn_id, 
												spawn_ground.number_harvests, 
												spawn_ground.num_attempts_per_harvest, 
												spawn_ground.groundspawn_id, 
												spawn_ground.collection_skill
												
											FROM `".DEV_DB."`.spawn 
											LEFT JOIN `".DEV_DB."`.spawn_npcs ON spawn.id = spawn_npcs.spawn_id
											LEFT JOIN `".DEV_DB."`.spawn_objects ON spawn.id = spawn_objects.spawn_id
											LEFT JOIN `".DEV_DB."`.spawn_signs ON spawn.id = spawn_signs.spawn_id
											LEFT JOIN `".DEV_DB."`.spawn_widgets ON spawn.id = spawn_widgets.spawn_id
											LEFT JOIN `".DEV_DB."`.spawn_ground ON spawn.id = spawn_ground.spawn_id
											WHERE 
												spawn.id = %lu", $_SESSION['combine_from']);

		if( !$result = $eq2->db->sql_query($query) ) 
			die('SQL Error: Line ' . __LINE__);
		else 
			while( $data = $eq2->db->sql_fetchrow($result) ) 
				$spawnFrom = $data;

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
											
												spawn_npcs.spawn_id as npc_spawn_id, 	
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
												
												spawn_objects.spawn_id as object_spawn_id, 	
											
												spawn_signs.spawn_id as sign_spawn_id, 
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
											
												spawn_widgets.spawn_id as widget_spawn_id, 
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
											
												spawn_ground.spawn_id as ground_spawn_id, 
												spawn_ground.number_harvests, 
												spawn_ground.num_attempts_per_harvest, 
												spawn_ground.groundspawn_id, 
												spawn_ground.collection_skill
												
											FROM `".DEV_DB."`.spawn 
											LEFT JOIN `".DEV_DB."`.spawn_npcs ON spawn.id = spawn_npcs.spawn_id
											LEFT JOIN `".DEV_DB."`.spawn_objects ON spawn.id = spawn_objects.spawn_id
											LEFT JOIN `".DEV_DB."`.spawn_signs ON spawn.id = spawn_signs.spawn_id
											LEFT JOIN `".DEV_DB."`.spawn_widgets ON spawn.id = spawn_widgets.spawn_id
											LEFT JOIN `".DEV_DB."`.spawn_ground ON spawn.id = spawn_ground.spawn_id
											WHERE 
												spawn.id = %lu", $_SESSION['combine_to']);
		if( !$result2 = $eq2->db->sql_query($query) ) 
			die('SQL Error: Line ' . __LINE__);
		else 
			while( $data2 = $eq2->db->sql_fetchrow($result2) ) 
				$spawnTo = $data2;

		// Appearance
		$query = sprintf("SELECT signed_value, type, red, green, blue FROM npc_appearance WHERE spawn_id = %lu", $_SESSION['combine_from']);
		if( !$result3 = $eq2->db->sql_query($query) ) 
			die('SQL Error: Line ' . __LINE__);
		else 
			while( $data3 = $eq2->db->sql_fetchrow($result3) ) 
				$spawnFrom['appearance'][] = $data3;

		$query = sprintf("SELECT signed_value, type, red, green, blue FROM npc_appearance WHERE spawn_id = %lu", $_SESSION['combine_to']);
		if( !$result4 = $eq2->db->sql_query($query) ) 
			die('SQL Error: Line ' . __LINE__);
		else 
			while( $data4 = $eq2->db->sql_fetchrow($result4) ) 
				$spawnTo['appearance'][] = $data4;


		// Equipment
		$query = sprintf("SELECT slot_id, equip_type, red, green, blue, highlight_red, highlight_green, highlight_blue FROM npc_appearance_equip WHERE spawn_id = %lu ORDER BY slot_id", $_SESSION['combine_from']);
		if( !$result5 = $eq2->db->sql_query($query) ) 
			die('SQL Error: Line ' . __LINE__);
		else 
			while( $data5 = $eq2->db->sql_fetchrow($result5) ) 
				$spawnFrom['equipment'][] = $data5;

		$query = sprintf("SELECT slot_id, equip_type, red, green, blue, highlight_red, highlight_green, highlight_blue FROM npc_appearance_equip WHERE spawn_id = %lu ORDER BY slot_id", $_SESSION['combine_to']);
		if( !$result6 = $eq2->db->sql_query($query) ) 
			die('SQL Error: Line ' . __LINE__);
		else 
			while( $data6 = $eq2->db->sql_fetchrow($result6) ) 
				$spawnTo['equipment'][] = $data6;

		print('<table align="center">');
		print('<tr><td height="30" valign="bottom" align="center" style="font-size:13px;" colspan="2"><strong>PREVIEW: All Spawn Data</strong></td></tr>');
		print('<tr><td width="50%" valign="top">');
		print('<table width="100%" border="1">');
		printf('<tr style="font-weight:bold;"><td>Field</td><td>Spawn 1 (%s)</td><td>Spawn 2 (%s)</td></tr>', $_SESSION['combine_from'], $_SESSION['combine_to']);
		
		$randomize = 0;
		foreach($spawnFrom as $field=>$value)
		{
			if( strlen($value)>0 && strlen($spawnTo[$field])>0 ) 
			{
				if( $value != $spawnTo[$field] )
				{
					$field1bg = ' style="background-color:#abc"';
					$field2bg = ' style="background-color:#cba"';
					
					// calculate recommended /randomize value
					$randomize = AddRandomize($field, $randomize);
					if( $field != 'id' && !strpos($field, 'spawn_id') && !strstr($field, 'soga') )
						printf('<tr><td>%s</td><td%s>%s</td><td%s>%s</td></tr>', $field, $field1bg, $value, $field2bg, $spawnTo[$field]);
				}
				else
				{
					$field1bg = '';
					$field2bg = '';
				}
			}
			
			if( $field == "appearance" )
			{
				print('<tr><td colspan="3"><table width="100%" border="1"><tr><td width="30%">Name</td><td colspan="5">Appearances 1</td><td colspan="4">Appearances 2</td></tr>');

				$appearanceChanged = false;
				$i = 0;
				foreach($spawnFrom['appearance'] as $appearance)
				{
					if( strstr($appearance['type'], "soga") ) {
						$i++;
						continue;
					}
																 
					$rowColor = ( $i % 2 ) ? "#eee;" : "#fff";
					
					if( $appearance['red'] != $spawnTo['appearance'][$i]['red'] ) {
						$bg_red =  ' style="background-color:#fb8"';
						$appearanceRed = true;
					} else {
						$bg_red =  '';
						$appearanceRed = false;
					}

					if( $appearance['green'] != $spawnTo['appearance'][$i]['green'] ) {
						$bg_green =  ' style="background-color:#bf8"';
						$appearanceGreen = true;
					} else {
						$bg_green =  '';
						$appearanceGreen = false;
					}

					if( $appearance['blue'] != $spawnTo['appearance'][$i]['blue'] ) {
						$bg_blue =  ' style="background-color:#8bf"';
						$appearanceBlue = true;
					} else {
						$bg_blue =  '';
						$appearanceBlue = false;
					}
					
					if( $appearanceRed || $appearanceGreen || $appearanceBlue )
					{
						//printf('Before: %s + %s<br />', $appearance['type'], $randomize);
						$randomize = AddRandomize($appearance['type'], $randomize);
					}
					
					printf('<tr style="background-color:%s"><td>%s</td><td>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td><td>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td></tr>', $rowColor, 
								 $appearance['type'], $appearance['signed_value'], $bg_red, $appearance['red'], $bg_green, $appearance['green'], $bg_blue, $appearance['blue'],
								 $spawnTo['appearance'][$i]['signed_value'], $bg_red, $spawnTo['appearance'][$i]['red'], $bg_green, $spawnTo['appearance'][$i]['green'], $bg_blue, $spawnTo['appearance'][$i]['blue']);
					$i++;
				}
				print('</table></td></tr>');
			}
			// see function SpareCode(), where I hid all this crappy equipment code!
		} // foreach spawnFrom
		print('<tr><td colspan="3">If Equipment arrays show color, means they are different. You should check them out before merging.</td></tr>');
		print('</table>');
	}
	else
	{
		printf('Invalid From/To selected.');
		die();
	}
	if( $_SESSION['merging'] == 1 )
		return $randomize;
	else
		printf('Recommended Randomize Value: %s', $randomize);
		
}

function AddRandomize($field, $randomize)
{
	switch($field)
	{
		case "gender"						: $randomize += 1; break;
		case "race"							: $randomize += 2; break;
		case "model_type"				: $randomize += 4; break;

		case "face_hair_type_id": $randomize += 8; break;
		case "hair_type_id"			: $randomize += 16; break;
		case "wing_type_id"			: $randomize += 64; break;

		case "cheek_type"				: $randomize += 128; break;
		case "chin_type"				: $randomize += 256; break;
		case "ear_type"					: $randomize += 512; break;
		case "eye_brow_type"		: $randomize += 1024; break;
		case "eye_type"					: $randomize += 2048; break;
		case "lip_type"					: $randomize += 4096; break;
		case "nose_type"				: $randomize += 8192; break;

		case "eye_color"				: $randomize += 16384; break;
		case "hair_color1"			: $randomize += 32768; break;
		case "hair_color2"			: $randomize += 65536; break;
		case "hair_highlight"		: $randomize += 131072; break;
		case "hair_face_color"	: $randomize += 262144; break;
		case "hair_face_highlight_color": $randomize += 524288; break;
		case "hair_type_color"	: $randomize += 1048576; break;
		case "hair_type_highlight_color": $randomize += 2097152; break;
		case "skin_color"				: $randomize += 4194304; break;
		case "wing_color1"			: $randomize += 8388608; break;
		case "wing_color2"			: $randomize += 16777216; break;
		default: break;
	}
	//printf('After: %s + %s<br />', $field, $randomize);
	return $randomize;
}

function ShowSpawnAppearances()
{
	global $eq2;

	if( isset($_SESSION['combine_from']) && isset($_SESSION['combine_to']) )
	{
		print('<table width="100%" align="center">');
		print('<tr><td height="30" valign="bottom" align="center" style="font-size:13px;" colspan="2"><strong>PREVIEW: Spawn Appearances</strong></td></tr>');
		print('<tr><td width="50%" valign="top">');

		$query = sprintf("SELECT * FROM npc_appearance WHERE spawn_id = %lu", $_SESSION['combine_from']);
		if( !$result = $eq2->db->sql_query($query) ) 
		{
			$error = $eq2->db->sql_error();
			die($error['message']);
		} 
		else 
		{
			print('<table width="100%" border="1">');
			while( $data = $eq2->db->sql_fetchrow($result) ) 
			{
				printf('<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>', $data['type'], $data['red'], $data['green'], $data['blue']);
			}
			print('</table>');
		}

		print('</td><td width="50%" valign="top">');
		$query = sprintf("SELECT * FROM npc_appearance WHERE spawn_id = %lu", $_SESSION['combine_to']);
		if( !$result = $eq2->db->sql_query($query) ) 
		{
			$error = $eq2->db->sql_error();
			die($error['message']);
		} 
		else 
		{
			print('<table width="100%" border="1">');
			while( $data = $eq2->db->sql_fetchrow($result) ) 
			{
				printf('<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>', $data['type'], $data['red'], $data['green'], $data['blue']);
			}
			print('</table>');
		}
		print('</td></tr></table>');
	}
}


function ShowSpawnDetails()
{
	global $eq2;

	if( isset($_SESSION['combine_from']) )
	{
		print('<table width="100%" align="center">');
		print('<tr><td height="30" valign="bottom" align="center" style="font-size:13px;"><strong>PREVIEW: Spawn Details</strong></td></tr>');
		print('<tr><td valign="top">');
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
												spawn_ground.groundspawn_id, 
												spawn_ground.collection_skill
												
											FROM `.DEV_DB."`.spawn 
											LEFT JOIN `".DEV_DB."`.spawn_npcs ON spawn.id = spawn_npcs.spawn_id
											LEFT JOIN `".DEV_DB."`.spawn_objects ON spawn.id = spawn_objects.spawn_id
											LEFT JOIN `".DEV_DB."`.spawn_signs ON spawn.id = spawn_signs.spawn_id
											LEFT JOIN `".DEV_DB."`.spawn_widgets ON spawn.id = spawn_widgets.spawn_id
											LEFT JOIN `".DEV_DB."`.spawn_ground ON spawn.id = spawn_ground.spawn_id
											WHERE 
												spawn.id = %lu", $_SESSION['combine_from']);
		//echo $query;
		if( !$result = $eq2->db->sql_query($query) ) {
			$error = $eq2->db->sql_error();
			die($error['message']);
		} else {
			$field_count = $eq2->db->sql_numfields($result);
			print('<table width="100%" border="1">');
			while( $data = $eq2->db->sql_fetchrow($result) ) {
				$fields = '';
				$values = '';
				$break_count = 0;
				for( $i = 0; $i < $field_count; $i++ )
				{
					$field_name = $eq2->db->sql_fieldname($i);
					
					if( !isset($data[$field_name]) ) 
						continue;

					if( $current_table != $eq2->db->sql_fieldtable($i, $result) )
					{
						$current_table = $eq2->db->sql_fieldtable($i, $result);
						$break_count = 0;
						printf('<tr>%s</tr>', $fields);
						printf('<tr>%s</tr>', $values);
						printf('<tr><td colspan="8" style="font-size:13px; font-weight:bold; color:#0000ff">&nbsp;%s</td></tr>', $current_table);
						$fields = '';
						$values = '';
						
					}

					if( $break_count == 8 ) {
						$break_count = 0;
						printf('<tr>%s</tr>', $fields);
						printf('<tr>%s</tr>', $values);
						$fields = '';
						$values = '';
					}
					else
					{
						$break_count++;
						$fields .= sprintf('<td style="font-size:11px; font-weight:bold; text-align:center;">%s</td>', $field_name);
						$values .= sprintf('<td style="font-size:11px; background-color:#ddd" nowrap>&nbsp;%s</td>', $data[$field_name]);
					}
				}
				printf('<tr>%s</tr>', $fields);
				printf('<tr>%s</tr>', $values);
				printf('<tr><td bgcolor="#999999" colspan="%d">&nbsp;</td></tr>', $field_count);
			}
			print('</table><br />');
		}
		$eq2->db->sql_freeresult($result);

		print('</td></tr>');
		print('</table>');
	}
}


function spareCode()
{
			/*if( $field == "equipment" )
			{
				print('<tr><td colspan="3"><table width="100%" border="1"><tr><td colspan="2"><strong>From:</strong></td><td colspan="6"><strong>Equipment 1</strong></td><td colspan="6"><strong>Equipment 2</strong></td></tr>');
				
				// first, check to see if slot+equip match between two arrays
				foreach( $spawnFrom['equipment'] as $equipFrom )
				{
					print('spawnFrom: starting.<br />');
					$compared = false;
					
					foreach( $spawnTo['equipment'] as $equipTo )
					{
						if( $compared )
							continue;
						print('spawnTo: starting.<br />');
							
						if( $equipFrom['slot_id'] == $equipTo['slot_id'] )
						{
							printf('Slot ID: %s found.<br />', $equipFrom['slot_id']);
							
							if( $equipFrom['equip_type'] == $equipTo['equip_type'] )
							{
								printf('Equip Type: %s match.<br />', $equipFrom['equip_type']);
								
								printf('Red: %s<br />', ( $equipFrom['red'] == $equipTo['red'] ) ? 'matched' : 'mismatched');
								printf('Green: %s<br />', ( $equipFrom['green'] == $equipTo['green'] ) ? 'matched' : 'mismatched');
								printf('Blue: %s<br />', ( $equipFrom['blue'] == $equipTo['blue'] ) ? 'matched' : 'mismatched');
								printf('Red HL: %s<br />', ( $equipFrom['highlight_red'] == $equipTo['highlight_red'] ) ? 'matched' : 'mismatched');
								printf('Green HL: %s<br />', ( $equipFrom['highlight_green'] == $equipTo['highlight_green'] ) ? 'matched' : 'mismatched');
								printf('Blue HL: %s<br />', ( $equipFrom['highlight_blue'] == $equipTo['highlight_blue'] ) ? 'matched' : 'mismatched');
								
								$compared = true;

							}
							else
							{
								printf('Equip Type From: %s, To: %s mismatch.<br />', $equipFrom['equip_type'], $equipTo['equip_type']);
							}
							
							print('spawnTo: ending.<br />');
						}
						else
						{
							printf('Slot ID From: %s, To: %s found.<br />', $equipFrom['slot_id'], $equipTo['slot_id']);
						}
						
						print('spawnFrom: ending.<br /><br />');
					}
				}
				print('</table>');
			}*/



		// Equipment
		if( is_array($spawnFrom['equipment']) &&  is_array($spawnTo['equipment']) )
		{
			/*
			//print('<tr><td colspan="3"><table width="100%" border="1"><tr><td colspan="2"><strong>From:</strong></td><td colspan="6"><strong>Equipment 1</strong></td><td colspan="6"><strong>Equipment 2</strong></td></tr>');
			//print('<tr><td>slot</td><td>equip_id</td><td>red</td><td>green</td><td>blue</td><td>redhl</td><td>greenhl</td><td>bluehl</td><td>red</td><td>green</td><td>blue</td><td>redhl</td><td>greenhl</td><td>bluehl</td></tr>');

			$mismatch = ' style="background-color:#990"';
			$from_row = '';
			$to_row = '';
			
			// loop through spawnFrom equipment, comparing to every spawnTo for mismatches
			foreach($spawnFrom['equipment'] as $equipFrom)
			{
				//print_r($equipFrom);
				foreach($spawnTo['equipment'] as $equipTo)
				{
					//print_r($equipTo);
					//exit;
					// compare equipment in the same slot
					if( $equipTo['slot_id'] == $equipFrom['slot_id'])
					{
						$from_row .= sprintf('<td>%s</td>', $equipFrom['slot_id']);
						$slotFound = true; // flag that the From slot is used in the To spawn, otherwise it is a unique slot to From
						
						// if it is the same, flag it match
						if( $equipTo['equip_type'] == $equipFrom['equip_type'] && $slotFound )
						{
							$equipFound = true;
							
							$bg_red 		= ( $equipTo['red'] 						!= $equipFrom['red'] ) 							? ' style="background-color:#fb8"' : '';
							$bg_green 	= ( $equipTo['green'] 					!= $equipFrom['green'] ) 						? ' style="background-color:#bf8"' : '';
							$bg_blue 		= ( $equipTo['blue'] 						!= $equipFrom['blue'] ) 						? ' style="background-color:#8bf"' : '';
							$bg_redh 		= ( $equipTo['highlight_red'] 	!= $equipFrom['highlight_red'] ) 		? ' style="background-color:#fb8"' : '';
							$bg_greenh 	= ( $equipTo['highlight_green'] != $equipFrom['highlight_green'] ) 	? ' style="background-color:#bf8"' : '';
							$bg_blueh 	= ( $equipTo['highlight_blue'] 	!= $equipFrom['highlight_blue'] ) 	? ' style="background-color:#8bf"' : '';

							$from_row .= sprintf('<td>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td>', 
																		$equipFrom['equip_type'], $bg_red, $equipFrom['red'], $bg_green, $equipFrom['green'], $bg_blue, $equipFrom['blue'], 
																		$bg_redh, $equipFrom['highlight_red'], $bg_greenh, $equipFrom['highlight_green'], $bg_blueh, $equipFrom['highlight_blue']);

							$from_row .= sprintf('<td%s>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td></tr>', 
																		$bg_red, $equipTo['red'], $bg_green, $equipTo['green'], $bg_blue, $equipTo['blue'], 
																		$bg_redh, $equipTo['highlight_red'], $bg_greenh, $equipTo['highlight_green'], $bg_blueh, $equipTo['highlight_blue']);
						}
						else
						{
							 // flag it mismatch equipment
							$equipFound = false;
							$from_row .= sprintf('<td>%s</td><td colspan="6">', 
																		$equipFrom['equip_type']);
							$from_row .= sprintf('<td%s>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td></tr>', 
																		$mismatch, $equipFrom['red'], $mismatch, $equipTo['green'], $mismatch, $equipFrom['blue'], 
																		$mismatch, $equipFrom['highlight_red'], $mismatch, $equipFrom['highlight_green'], $mismatch, $equipFrom['highlight_blue']);
						} // end equip_type

					}
					else
					{
						$slotFound = false; // slot in To was not configured at all in From spawn - display!
						$from_row .= sprintf('<td>%s</td><td colspan="7"></td></tr>', $equipFrom['slot_id']);
						
						continue; // slot didn't match, do not continue looking through To array
					} // end slot_id
					
				} // end spawnTo foreach
			} // end spawnFrom foreach

			// Now, do the exact opposite, see if there is anything in the To spawn that is NOT on the From spawn... ugh.

			// loop through spawnTo equipment, comparing to every spawnTo for mismatches
			foreach($spawnTo['equipment'] as $equipTo)
			{
				foreach($spawnFrom['equipment'] as $equipFrom)
				{
					// compare equipment in the same slot
					if( $equipFrom['slot_id'] == $equipTo['slot_id'])
					{
						$to_row .= sprintf('<td>%s</td>', $equipTo['slot_id']);
						$slotFound = true; // flag that the From slot is used in the To spawn, otherwise it is a unique slot to From
						
						// if it is the same, flag it match
						if( $equipFrom['equip_type'] == $equipTo['equip_type'] && $slotFound )
						{
							$equipFound = true;
							
							$bg_red 		= ( $equipTo['red'] 						!= $equipFrom['red'] ) 							? ' style="background-color:#fb8"' : '';
							$bg_green 	= ( $equipTo['green'] 					!= $equipFrom['green'] ) 						? ' style="background-color:#bf8"' : '';
							$bg_blue 		= ( $equipTo['blue'] 						!= $equipFrom['blue'] ) 						? ' style="background-color:#8bf"' : '';
							$bg_redh 		= ( $equipTo['highlight_red'] 	!= $equipFrom['highlight_red'] ) 		? ' style="background-color:#fb8"' : '';
							$bg_greenh 	= ( $equipTo['highlight_green'] != $equipFrom['highlight_green'] ) 	? ' style="background-color:#bf8"' : '';
							$bg_blueh 	= ( $equipTo['highlight_blue'] 	!= $equipFrom['highlight_blue'] ) 	? ' style="background-color:#8bf"' : '';

							$to_row .= sprintf('<td>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td>', 
																		$equipTo['equip_type'], $bg_red, $equipFrom['red'], $bg_green, $equipFrom['green'], $bg_blue, $equipFrom['blue'], 
																		$bg_redh, $equipFrom['highlight_red'], $bg_greenh, $equipFrom['highlight_green'], $bg_blueh, $equipFrom['highlight_blue']);

							$to_row .= sprintf('<td%s>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td></tr>', 
																		$bg_red, $equipTo['red'], $bg_green, $equipTo['green'], $bg_blue, $equipTo['blue'], 
																		$bg_redh, $equipTo['highlight_red'], $bg_greenh, $equipTo['highlight_green'], $bg_blueh, $equipTo['highlight_blue']);

							continue; // found a match, do not continue looking through the To array
							
						} // end equip_type
						else
						{
							// flag it mismatch
							$equipFound = false;
							
							$mismatch = ' style="background-color:#990"';
							$to_row .= sprintf('<td>%s</td><td colspan="6">', 
																		$equipTo['equip_type']);
		
							$to_row .= sprintf('<td%s>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td></tr>', 
																		$$mismatch, $equipTo['red'], $$mismatch, $equipTo['green'], $$mismatch, $equipTo['blue'], 
																		$$mismatch, $equipTo['highlight_red'], $$mismatch, $equipTo['highlight_green'], $$mismatch, $equipTo['highlight_blue']);
		
						} // end equip_type
					}
					else
					{
						$slotFound = false; // slot in From was not configured at all in To spawn - display!
						continue; // slot didn't match, do not continue looking through From array
					} // end slot_id

				} // end spawnTo foreach
			} // end spawnFrom foreach


			$rowColor = ( $i % 2 ) ? "#eee;" : "#fff";

			print('<tr><td colspan="3"><table width="100%" border="1"><tr><td colspan="2"><strong>From:</strong></td><td colspan="6"><strong>Equipment 1</strong></td><td colspan="6"><strong>Equipment 2</strong></td></tr>');
			print('<tr><td>slot</td><td>equip_id</td><td>red</td><td>green</td><td>blue</td><td>redhl</td><td>greenhl</td><td>bluehl</td><td>red</td><td>green</td><td>blue</td><td>redhl</td><td>greenhl</td><td>bluehl</td></tr>');
			printf('<tr style="background-color:%s">%s', $rowColor, $from_row);
			
			print('<tr><td colspan="2"><strong>To:</strong></td><td colspan="6"><strong>Equipment 1</strong></td><td colspan="6"><strong>Equipment 2</strong></td></tr>');
			print('<tr><td>slot</td><td>equip_id</td><td>red</td><td>green</td><td>blue</td><td>redhl</td><td>greenhl</td><td>bluehl</td><td>red</td><td>green</td><td>blue</td><td>redhl</td><td>greenhl</td><td>bluehl</td></tr>');
			printf('<tr style="background-color:%s">%s', $rowColor, $to_row);


				$bg_red = ( $equipment['red'] != $spawnTo['equipment'][$i]['red'] ) ? ' style="background-color:#fb8"' : '';
				$bg_green = ( $equipment['green'] != $spawnTo['equipment'][$i]['green'] ) ? ' style="background-color:#bf8"' : '';
				$bg_blue = ( $equipment['blue'] != $spawnTo['equipment'][$i]['blue'] ) ? ' style="background-color:#8bf"' : '';
				$bg_redh = ( $equipment['red'] != $spawnTo['equipment'][$i]['red'] ) ? ' style="background-color:#fb8"' : '';
				$bg_greenh = ( $equipment['green'] != $spawnTo['equipment'][$i]['green'] ) ? ' style="background-color:#bf8"' : '';
				$bg_blueh = ( $equipment['blue'] != $spawnTo['equipment'][$i]['blue'] ) ? ' style="background-color:#8bf"' : '';

				printf('<tr style="background-color:%s"><td>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td><td%s>%s</td></tr>', $rowColor, 
							 $equipment['equip_type'], 
							 $bg_red, $equipment['red'], $bg_green, $equipment['green'], $bg_blue, $equipment['blue'], $bg_redh, $equipment['highlight_red'], $bg_greenh, $equipment['highlight_green'], $bg_blueh, $equipment['highlight_blue'],
							 $bg_red, $spawnTo['equipment'][$i]['red'], $bg_green, $spawnTo['equipment'][$i]['green'], $bg_blue, $spawnTo['equipment'][$i]['blue'], $bg_redh, $spawnTo['equipment'][$i]['highlight_red'], $bg_greenh, $spawnTo['equipment'][$i]['highlight_green'], $bg_blueh, $spawnTo['equipment'][$i]['highlight_blue']);
				$i++;
			}*/
			//print('</table></td></tr>');
		}
}

?>