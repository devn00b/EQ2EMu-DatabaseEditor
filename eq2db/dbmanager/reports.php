<?php 
include("header.php"); 
// if( $eq2->mLev < 250 ) { print("Options Not Available Yet!"); exit; }

$Page = isset($_GET['page']) ? $_GET['page'] : "";
$Type = isset($_GET['type']) ? $_GET['type'] : "";

?>
<div id="sub-menu1">
	<table cellspacing="0">
		<tr>
			<td align="right" width="100px"><strong>Data:</strong> </td>
			<td>&nbsp;
				[ <a href="<?php print($link) ?>?page=zone">Zone Data</a> ]
			</td>
		</tr>
<?php
switch( $Page ) {
	case "zone":
		?>
		<tr>
			<td align="right" width="100px"><strong>Zone:</strong> </td>
			<td>&nbsp;
				[ <a href="<?php print($link) ?>?page=zone&type=npc">NPCs</a> ]
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
		
		case "npc":
			DisplaySpawns();
			break;
	}
}
?>
</div>
<?
include_once("footer.php"); 
exit;


function CompareZones() {
	global $config;
	
	// TODO: taken from sandbox, cleanup $this-> and $config vars to make work with Reports
	print('<div id="zone-select">');
	$this->ZoneSelector(1);
	printf('&nbsp;<a href="?%s">Reload Page</a>', $_SERVER['QUERY_STRING']);
	$query = sprintf("SELECT id, name, sub_title, model_type, size FROM %s.spawn WHERE id LIKE '%d____' ORDER BY id;", $config['raw_db'], $_GET['z']);
	if( !$result = $this->db->sql_query($query) ) {
		$error = $this->db->sql_error();
		die($error['message']);
	} else {
		while( $row = $this->db->sql_fetchrow($result) ) {
			$raw_data[] = $row;
		}
	}
	$this->db->sql_freeresult($result);

	$query = sprintf("SELECT id, name, sub_title, model_type, size FROM %s.spawn WHERE id LIKE '%d____' ORDER BY id;", $config['dev_db'], $_GET['z']);
	if( !$result = $this->db->sql_query($query) ) {
		$error = $this->db->sql_error();
		die($error['message']);
	} else {
		while( $row = $this->db->sql_fetchrow($result) ) {
			$live_data[] = $row;
		}
	}
	$this->db->sql_freeresult($result);
	
	// build side-by-side table comparisons
	print('<table width="100%" align="center"><tr><td width="50%" valign="top">');
	print('<table width="100%" cellpadding="2" cellspacing="2" border="1" align="center">');
	printf('<tr><td colspan="5" align="center"><strong>%s</strong></td></tr>', $config['raw_db']);
	print('<tr><td>id</td><td>name</td><td>sub_title</td><td>mode_type</td><td>size</td></tr>');
	foreach($raw_data as $key) {
		printf('<tr><td>&nbsp;%s</td><td>&nbsp;%s</td><td>&nbsp;%s</td><td>&nbsp;%s</td><td>&nbsp;%s</td></tr>',
			$key['id'], $key['name'], $key['sub_title'], $key['model_type'], $key['size']);
	}
	print('</table>');
	print('</td><td width="50%" valign="top">');
	print('<table width="100%" cellpadding="2" cellspacing="2" border="1" align="center">');
	printf('<tr><td colspan="5" align="center"><strong>%s</strong></td></tr>', $config['raw_db']);
	print('<tr><td>id</td><td>name</td><td>sub_title</td><td>mode_type</td><td>size</td></tr>');
	foreach($live_data as $key) {
		printf('<tr><td>&nbsp;%s</td><td>&nbsp;%s</td><td>&nbsp;%s</td><td>&nbsp;%s</td><td>&nbsp;%s</td></tr>',
			$key['id'], $key['name'], $key['sub_title'], $key['model_type'], $key['size']);
	}
	print('</table>');
	print('</td></tr></table>');
}


function DisplaySpawns() {
	global $eq2, $db_name;
	
	print('<div id="zone-select">');
	$eq2->ZoneSelector(1);
	printf('&nbsp;<a href="?%s">Reload Page</a>', $_SERVER['QUERY_STRING']);
	print('</div><br />');

	if( isset($_GET['zone']) ) {
		print('<table width="100%" cellpadding="2" cellspacing="1" border="1" align="center">');
		print('<tr style="font-weight:bold"><td>spawn_id</td><td>name</td><td>sub_title</td><td>race</td><td>model_type</td><td>size</td><td>visual_state</td><td>min_level</td><td>max_level</td><td>enc_level</td><td>class_</td><td>gender</td><td>hair_type_id</td><td>facial_hair_type_id</td><td>wing_type_id</td><td>chest_type_id</td><td>legs_type_id</td><td>soga_hair_type_id</td><td>soga_facial_hair_type_id</td><td>soga_model_type</td><td>action_state</td><td>mood_state</td><td>initial_state</td><td>activity_status</td></tr>');
	
		$query = sprintf("SELECT s1.`id` as spawn_id, `name`, `sub_title`, `race`, `model_type`, `size`, `visual_state`, `min_level`, `max_level`, `enc_level`, `class_`, `gender`, `hair_type_id`, `facial_hair_type_id`, `wing_type_id`, `chest_type_id`, `legs_type_id`, `soga_hair_type_id`, `soga_facial_hair_type_id`, `soga_model_type`, `action_state`, `mood_state`, `initial_state`, `activity_status` 
											FROM %s.spawn s1
											LEFT JOIN %s.spawn_npcs s2 ON s1.id = s2.spawn_id
											WHERE spawn_id LIKE '%d____'
											ORDER BY `name`, model_type, enc_level", 
											$db_name, $db_name, $_GET['zone']);
		//echo $query;
		if( !$result = $eq2->db->sql_query($query) ) {
			$error = $eq2->db->sql_error();
			die($error['message']);
		} else {
			$i = 0;
			while( $data = $eq2->db->sql_fetchrow($result) ) {
				$row_class = ( $i % 2 ) ? " bgcolor=#eee" : " bgcolor=#ddd";
				printf('<tr%s>
									<td>&nbsp;%s</td>
									<td nowrap>&nbsp;%s</td>
									<td>&nbsp;%s</td>
									<td>&nbsp;%s</td>
									<td>&nbsp;%s</td>
									<td>&nbsp;%s</td>
									<td>&nbsp;%s</td>
									<td>&nbsp;%s</td>
									<td>&nbsp;%s</td>
									<td>&nbsp;%s</td>
									<td>&nbsp;%s</td>
									<td>&nbsp;%s</td>
									<td>&nbsp;%s</td>
									<td>&nbsp;%s</td>
									<td>&nbsp;%s</td>
									<td>&nbsp;%s</td>
									<td>&nbsp;%s</td>
									<td>&nbsp;%s</td>
									<td>&nbsp;%s</td>
									<td>&nbsp;%s</td>
									<td>&nbsp;%s</td>
									<td>&nbsp;%s</td>
									<td>&nbsp;%s</td>
									<td>&nbsp;%s</td>
								</tr>',
								$row_class,
								$data['spawn_id'], 
								$data['name'], 
								$data['sub_title'], 
								$data['race'], 
								$data['model_type'], 
								$data['size'], 
								$data['visual_state'], 
								$data['min_level'], 
								$data['max_level'], 
								$data['enc_level'], 
								$data['class_'], 
								$data['gender'], 
								$data['hair_type_id'], 
								$data['facial_hair_type_id'],
								$data['wing_type_id'], 
								$data['chest_type_id'], 
								$data['legs_type_id'], 
								$data['soga_hair_type_id'], 
								$data['soga_facial_hair_type_id'], 
								$data['soga_model_type'], 
								$data['action_state'], 
								$data['mood_state'], 
								$data['initial_state'], 
								$data['activity_status']
								);
				$i++;
			}
		}
		$eq2->db->sql_freeresult($result);
		print('</table>');

	}
	
}

?>