<?php 
define('IN_EDITOR', true);
include("header.php"); 

if ( !$eq2->CheckAccess(M_SCRIPTS) )
	die("ACCESS: Denied!");

?>
<div id="sub-menu1">
	<a href="scripts.php?t=spawn_scripts">Spawn Script Editor</a> | 
	<a href="scripts.php?t=quests">Quest Script Editor</a> | 
	<a href="scripts.php?t=spells">Spell Script Editor</a> | 
	<a href="scripts.php?cl=history">Scripts Changelog</a>
</div>
<?php
if( isset($_GET['cl']) ) {
	?>
	<table>
		<tr>
			<td>
				<select name="tableName" onChange="dosub(this.options[this.selectedIndex].value)">
					<option>Pick a table</option>
					<option value="spawns.php?cl=history&t=spawn"<?php if( $_GET['t']=="spawn" ) echo " selected" ?>>spawn</option> 
					<option value="spawns.php?cl=history&t=spawn_npcs"<?php if( $_GET['t']=="spawn_npcs" ) echo " selected" ?>>spawn_npcs</option> 
					<option value="spawns.php?cl=history&t=npc_appearance"<?php if( $_GET['t']=="npc_appearance" ) echo " selected" ?>>npc_appearance</option> 
					<option value="spawns.php?cl=history&t=npc_appearance_equip"<?php if( $_GET['t']=="npc_appearance_equip" ) echo " selected" ?>>npc_appearance_equip</option> 
					<option value="spawns.php?cl=history&t=spawn_loot"<?php if( $_GET['t']=="spawn_loot" ) echo " selected" ?>>spawn_loot</option> 
					<option value="spawns.php?cl=history&t=spawn_objects"<?php if( $_GET['t']=="spawn_objects" ) echo " selected" ?>>spawn_objects</option>
					<option value="spawns.php?cl=history&t=spawn_signs"<?php if( $_GET['t']=="spawn_signs" ) echo " selected" ?>>spawn_signs</option>
					<option value="spawns.php?cl=history&t=spawn_widgets"<?php if( $_GET['t']=="spawn_widgets" ) echo " selected" ?>>spawn_widgets</option>
					<option value="spawns.php?cl=history&t=zonespawngroup"<?php if( $_GET['t']=="zonespawngroup" ) echo " selected" ?>>zonespawngroup</option>
					<option value="spawns.php?cl=history&t=zonespawnentry"<?php if( $_GET['t']=="zonespawnentry" ) echo " selected" ?>>zonespawnentry</option> 
					<option value="spawns.php?cl=history&t=zonespawns"<?php if( $_GET['t']=="zonespawns" ) echo " selected" ?>>zonespawns</option> 
				</select>
			</td>
			<?php 
			if( isset($_GET['t']) ) 
			{ 
				$table = ( isset($_GET['t']) ) ? $_GET['t'] : "";
				$editor_id = ( isset($_GET['c']) ) ? $_GET['c'] : 0;
			?>
			<td>Limit by user:&nbsp;
				<select name="char_id" onChange="dosub(this.options[this.selectedIndex].value)">
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
}

include("footer.php");
?>