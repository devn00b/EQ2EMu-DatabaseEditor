<?php 
if( !isset($_GET['z']) || !isset($_GET['s']) || !isset($_GET['t']) ) die("Invalid parameters passed.");

include("../config.php");

$zone_id = $_GET['z'];
$spawn_id = $_GET['s'];
$spawn_type = $_GET['t'];

$spawn = $eq2->runScalarQuery("select * from spawn where id = ".$spawn_id);
$spawn_npcs = $eq2->runScalarQuery("select * from spawn_npcs where spawn_id = ".$spawn_id);
$entity_commands = $eq2->runScalarQuery("select entity_commands.* from entity_commands, spawn where command_primary = command_list_id and spawn.id = ".$spawn_id);
$npc_appearance = '';
$npc_appearance_equip = '';
$spawn_loot = '';
$zonespawngroup = '';
$zonespawnentry = '';
$zonespawns = '';

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
</head>

<body>
-- MySQL Dump of Spawn: <?= $spawn['name']; ?><br />
-- Date: <?= date("Y/m/d h:m a",time()); ?><br />
-- Server Data from: TessEQ2<br />
-- <br />
-- Instructions: Copy/Paste this screen into your query window an execute. Note that any autoincrement IDs are those used by this server for consistency.<br />
-- <br />
<p>
-- table: spawn (primary record, must be inserted first)<br />
<?php buildSingleInserts("spawn",$spawn) ?>
</p>

<p>-- table: spawn_npcs<br />
<?php buildSingleInserts("spawn_npcs",$spawn_npcs) ?>
</p>

<p>-- table: entity_commands<br />
<?php buildSingleInserts("entity_commands",$entity_commands) ?>
</p>




</body>
</html>
<?php
function buildSingleInserts($table,$data) {
	foreach($data as $key=>$val) {
		if( empty($fields) ) :
			$fields.=$key;
			$values.="'".addslashes($val)."'";
		else :
			$fields.=", ".$key;
			$values.=",'".addslashes($val)."'";
		endif;
	}
	if( !empty($fields) ) {
		printf("insert into %s (%s) values (%s);<br>",$table,$fields,$values);
	}
}
?>
