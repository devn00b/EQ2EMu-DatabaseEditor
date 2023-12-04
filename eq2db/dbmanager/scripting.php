<?php 
include("header.php"); 
if( $eq2->mLev < 250 ) { print("Options Not Available Yet!"); exit; }
?>
<div id="sub-menu1">
	<table cellspacing="0">
		<tr>
			<td align="right" width="100px"><strong>Scripting:</strong></td>
			<td>
				[ <a href="<?php print($link) ?>?p=dialogs">Build Dialogs</a> ] &bull;
				[ <a href="<?php print($link) ?>?p=conversations">Build Conversations</a> ] &bull;
				[ <a href="<?php print($link) ?>?p=movement">Build Movement</a> ] &bull;
				[ <a href="<?php print($link) ?>?p=voiceovers">List Voiceovers</a> ] &bull;
				[ <a href="<?php print($link) ?>?p=link">(Re)link Scripts</a> ] &bull;
				[ <a href="<?php print($link) ?>?p=scripts">Validate Scripts</a> ]
			</td>
		</tr> 	
	</table>
</div>

<?php

switch($w)
{
	default						: 
		/*<div id="sub-menu1">
			<a href="spawnscripts.php">Link SpawnScripts</a>
		</div>
		This script will (re)link SpawnScripts for re-populated zones*/
		LinkSpawnScripts(); 
		break;
}

exit;

function GetZoneDescriptionByID($id) {
	$query="select description from zones where id = $id";
	$result=$this->db->sql_query($query);
	$data=$this->db->sql_fetchrow($result);
	if( !empty($data['description']) )
		return $data['description'];
	else
		die("Cannot fine zone description for zone id: " . $id);
}	


function LinkSpawnScripts() {
	global $config;

	if( $_POST['cmd'] )
		$this->ProcessLinkSpawnScripts();
	
	print('<div id="zone-select">');
	$this->ZoneSelector(1);

	printf('&nbsp;<a href="?%s">Reload Page</a>', $_SERVER['QUERY_STRING']);
	$query = sprintf("select distinct spawn.id, spawn.name
										from eq2_rawdata.raw_zones
										join eq2_rawdata.raw_spawns on raw_zones.id = raw_spawns.zone_id
										join eq2_rawdata.raw_spawn_info on raw_spawns.spawn_id = raw_spawn_info.id
										join eq2_rawdata.raw_dialogs on raw_spawn_info.id = raw_dialogs.spawn_id
										join %s.spawn on raw_spawn_info.name = spawn.name
										where 
											zone_desc = '%s' and
											spawn.id like '%d____'
										order by spawn.name;", 
										$config['dev_db'],
										addslashes($this->GetZoneDescriptionByID($_GET['z'])), 
										$_GET['z']);
	if( !$result = $this->db->sql_query($query) ) {
		$error = $this->db->sql_error();
		die($error['message']);
	} else {
		while( $row = $this->db->sql_fetchrow($result) ) {
			//printf("%s %s<br>", $row['id'], $row['name']);
			$raw_scripts[$row['id']] = $row['name'];
		}
	}
	$this->db->sql_freeresult($result);

	$query = sprintf("select distinct spawn.id, spawn.name
										from eq2_rawdata.raw_zones
										join eq2_rawdata.raw_spawns on raw_zones.id = raw_spawns.zone_id
										join eq2_rawdata.raw_spawn_info on raw_spawns.spawn_id = raw_spawn_info.id
										join eq2_rawdata.raw_conversations on raw_spawn_info.id = raw_conversations.spawn_id
										join %s.spawn on raw_spawn_info.name = spawn.name
										where 
											zone_desc = '%s' and
											spawn.id like '%d____'
										order by spawn.name;", 
										$config['dev_db'],
										addslashes($this->GetZoneDescriptionByID($_GET['z'])), 
										$_GET['z']);
	if( !$result = $this->db->sql_query($query) ) {
		$error = $this->db->sql_error();
		die($error['message']);
	} else {
		while( $row = $this->db->sql_fetchrow($result) ) {
			$raw_scripts[$row['id']] = $row['name'];
		}
	}
	$this->db->sql_freeresult($result);

	ksort($raw_scripts);
	$zone_name = $this->GetZoneNameByID($_GET['z']);

	// build side-by-side table comparisons
	print('<table width="100%" align="center"><tr><td width="50%" valign="top"><form method="post">');
	print('<table width="100%" cellpadding="2" cellspacing="2" border="1" align="center">');
	print('<tr><td colspan="5" align="center"><strong>eq2_rawdata</strong></td></tr>');
	print('<tr><td>script path</td></tr>');
	
	foreach($raw_scripts as $key=>$val) {
		$pattern[0]="/ /";
		$pattern[1]="/'/";
		$pattern[2]="/`/";
		$pattern[3]="/\"/";
		$spawnName=preg_replace($pattern,"",$val);
		$scriptName = sprintf("SpawnScripts/%s/%s.lua", $zone_name, $spawnName);
	
		printf('<tr><td>&nbsp;%s <input type="hidden" name="raw_%s" value="%s"></td></tr>',
			$scriptName, $key, $scriptName);
	}
	print('<tr><td align="center"><input type="submit" name="cmd" value="Link Scripts to Spawns"></td></tr>');
	print('</table>');
	print('</td><td width="50%" valign="top">');
	print('<table width="100%" cellpadding="2" cellspacing="2" border="1" align="center">');
	print('<tr><td colspan="5" align="center"><strong>SpawnScript</strong></td></tr>');
	print('<tr><td>script exists</td></tr>');
	foreach($raw_scripts as $key=>$val) {
		$pattern[0]="/ /";
		$pattern[1]="/'/";
		$pattern[2]="/`/";
		$pattern[3]="/\"/";
		$spawnName=preg_replace($pattern,"",$val);

		$script = sprintf("SpawnScripts/%s/%s.lua", $zone_name, $spawnName);

		if( $this->CheckScriptExists($script) )
			printf('<tr><td>&nbsp;%s</td></tr>', $script);
		else
			print('<tr><td>&nbsp;Not Found</td></tr>');
		
	}
	print('</table></form>');
	print('</td></tr></table>');

	print('</div>');
}


function ProcessLinkSpawnScripts() {
	global $config;

	foreach($_POST as $key=>$val) {
		$myArray = explode("_", $key);
		if( $myArray[0] == "raw" ) {
			$query = sprintf("insert ignore into %s.spawn_scripts (spawn_id, lua_script) values ('%s', '%s');", $config['dev_db'], $myArray[1], $val);
			// printf("%s<br>", $query);
			$this->db->sql_query($query);
		}
	}
}

?>