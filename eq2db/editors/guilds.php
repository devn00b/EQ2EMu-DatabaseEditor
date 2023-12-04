<?php 
define('IN_EDITOR', true);
include("header.php");

if ( !$eq2->CheckAccess(M_GUILDS) )
	die("Access denied!");

?>
<div id="sub-menu1"><a href="guilds.php">Guilds Editor</a> | <a href="guilds.php?cl=history">Guilds Changelog</a></div>
<?php
if( isset($_GET['cl']) ) {
	?>
	<table>
		<tr>
			<td>
				<select name="tableName" onchange="dosub(this.options[this.selectedIndex].value)">
					<option>Pick a table</option>
					<option value="guilds.php?cl=history&t=guilds"<?php if( $_GET['t']=="guilds" ) echo " selected" ?>>guilds</option> 
				</select>
			</td>
			<?php 
			if( isset($_GET['t']) ) 
			{ 
				$table = $_GET['t'] ?? "";
				$editor_id = $_GET['c'] ?? 0;
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

$query=sprintf("select id,name,level from %s.guilds order by name", DEV_DB);
$result=$eq2->db->sql_query($query);
while($data=$eq2->db->sql_fetchrow($result)) {
	$selected=( $_GET['g'] == $data['id'] ) ? " selected" : "";
	$guildOptions.='<option value="?g='.$data['id'].'"'.$selected.'>'.$data['name'].' ('.$data['level'].')</option>\n';
}
?>
<table>
	<tr>
		<td valign="top">
			<select name="guildID" onchange="dosub(this.options[this.selectedIndex].value)">
			<option>Pick a Guild</option>
			<?= $guildOptions ?>
			</select> <a href="guilds.php?<?= $_SERVER['QUERY_STRING'] ?>">Reload Page</a>
		</td>
	</tr>
</table>
<?php

include("footer.php");

?>