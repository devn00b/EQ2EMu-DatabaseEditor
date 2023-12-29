<?php 
include_once("header.php"); 

$Page = isset($_GET['page']) ? $_GET['page'] : "";
$Type = isset($_GET['type']) ? $_GET['type'] : "";

?>
<div id="sub-menu1">
	<table cellspacing="0">
		<tr>
			<td align="right" width="100px"><strong>Project:</strong> </td>
			<td>&nbsp;
				[ <a href="<?php print($link) ?>?page=stats">Stats</a> ] &bull;
				[ <a href="http://www.eq2emulator.net/index.php?page=19" target="_blank">Bug List</a> ] &bull;
				[ <a href="<?php print($link) ?>?page=tasks">Task List</a> ]
			</td>
		</tr>
<?php
// sub-menu from above selection
switch( $Page ) {
	case "stats":
		?>
		<tr>
			<td align="right" width="100px"><strong>Stats:</strong> </td>
			<td>&nbsp;
				[ <a href="<?php print($link) ?>?page=stats&type=server">Server</a> ] &bull;
				[ <a href="<?php print($link) ?>?page=stats&type=player">Player</a> ] &bull;
				[ <a href="<?php print($link) ?>?page=stats&type=project">Project</a> ]
			</td>
		</tr>
		<?php
		break;

	case "bugs": $Type = "bugs"; break;

	case "tasks": 
		?>
		<tr>
			<td align="right" width="100px"><strong>Tasks:</strong> </td>
			<td>&nbsp;
				[ <a href="<?php print($link) ?>?page=tasks&type=mine">My Tasks</a> ] &bull;
				[ <a href="<?php print($link) ?>?page=tasks&type=open">Open Tasks</a> ] &bull;
				[ <a href="<?php print($link) ?>?page=tasks&type=closed">Closed Tasks</a> ]
			</td>
		</tr>
		<?php
		break;
}
?>
	</table>
</div>
<div id="dbmanager-body">
<?php
if( isset($Type) ) {

	switch( $Type ) {
		case "server"		: ShowServerStats(); break;
		case "player"		: ShowPlayerStats(); break;
		case "project"	: ShowProjectStats(); break;
		case "bugs"			: ShowBugList(); break;
		case "mine"			: 
		case "open"			: 
		case "closed"		: 
		case "new"			: 
			ShowTasks($Type); 
			break;
	}
}
?>
</div>
<?
include_once("footer.php"); 
exit;


function ShowTasks($type) {
	global $eq2, $db_name;

	$user_id = $eq2->userdata['id'];

	if( $type == 'new' ) 
	{
		if( isset($_POST['cmd']) )
		{
			if( strlen($_POST['task_title']) > 3 && strlen($_POST['task_detail']) > 3 ) 
			{
				// calculate estimated time
				$estimated_time = 0;
				
				$query = sprintf("insert into eq2editor.tasks (category, task_title, task_detail, created_by, created_on, assigned_to, assigned_by, assigned_on, estimated_time, progress) values ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s');", 
				$_POST['category'], 
				$eq2->db->sql_escape($_POST['task_title']), 
				$eq2->db->sql_escape($_POST['task_detail']), 
				$eq2->userdata['id'], 
				time(), 
				$_POST['assigned_to'], 
				$eq2->userdata['id'], 
				time(), 
				$estimated_time, 
				$eq2->db->sql_escape($_POST['progress'])); //echo $query; exit;
				
				$eq2->runQuery($query);
				$eq2->logQuery($query);
				$p = array('eq2editor.tasks','new',$query);
				$eq2->dbEditorLog($p);
				
			} 
			else 
			{
				die("Task title/detail must be longer than 3 characters.<br /><a href='javascript:history.back(1)'>&lt;&lt; Back</a>");
			}
		}
?>
		<table width="100%" border="1" cellspacing="0" cellpadding="2">
		<form method="post" name="Form1" />
			<tr>
				<td colspan="2" height="50"><b>Add Task:</b> Here you can add a new TODO, assign it to someone if you know they are going to do the work, and set different times for the task (start, estimate, complete)</td>
			</tr>
			<tr>
				<td colspan="2"><input type="button" value="Add Task" onclick="javascript:window.open('project.php?page=tasks&type=new', target='_self');" style="width:60px; font-size:10px;" /></td>
			</tr>
			<tr>
				<td width="15%" align="right"><strong>category:</strong></td>
				<td>
					<select name="category" style="width:200px; font-size:11px;">
					<option>General
					<option>DB Editor
					<option>Spawns
					<option>Spells
					<option>Quests
					<option>Loot
					<option>Items
					<option>Zones
					<option>Instances
					<option>Tradeskills
					<option>Merchants
					<option>Web Portal
					</select>
				</td>
			</tr>
			<tr>
				<td align="right"><strong>task_title:</strong></td>
				<td>
					<input type="text" name="task_title" value="" style="width:97%; font-size:11px;" />
				</td>
			</tr>
			<tr>
				<td align="right" valign="top"><strong>task_detail:</strong></td>
				<td>
					<textarea name="task_detail" style="width:97%; height:100px; font-size:11px;"></textarea>
				</td>
			</tr>
			<tr>
				<td align="right">assigned_to:</td>
				<td>
					<select name="assigned_to" style="width:200px; font-size:11px;">
						<?= $eq2->getDBTeamSelector3($eq2->userdata['id']) ?>
					</select>
				</td>
			</tr>
			<tr>
				<td align="right">estimated_time:</td>
				<td>
					<input type="text" name="estimated_time" value="" style="width:200px; font-size:11px;" />
				</td>
			</tr>
			<tr>
				<td align="right">progress:</td>
				<td>
					<input type="text" name="progress" value="0" style="width:45px; font-size:11px; text-align:right; padding-right:2px;" />%
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<input type="submit" name="cmd" value="Add Task" style="width:100px; font-size:11px;" />&nbsp;
				</td>
			</tr>
		</form>
		</table>
<?php
	} 
	else if( $_GET['id'] )
	{
		// view task details

	}
	else
	{
		// view task list
?>
		<table width="100%" border="1" cellspacing="0" cellpadding="4">
			<tr>
				<td colspan="8" height="50"><b>Task List:</b> Do not consider this a mandatory work load, but a notepad for things we need to get done and keep forgetting ;)</td>
			</tr>
			<tr>
				<td colspan="8"><input type="button" value="Add Task" onclick="javascript:window.open('project.php?page=tasks&type=new', target='_self');" style="width:60px; font-size:10px;" /></td>
			</tr>
			<tr>
				<td width="50"><strong>id</strong></td>
				<td width="100"><strong>assigned_to</strong></td>
				<td width="350"><strong>task_title</strong></td>
				<td width="100"><strong>category</strong></td>
				<td><strong>created_on</strong></td>
				<td><strong>date_started</strong></td>
				<td width="10"><strong>progress</strong></td>
				<td width="60">&nbsp;</td>
			</tr>
<?php
		switch($type) {
			case "mine": 
				//$query = "SELECT * FROM eq2editor.tasks WHERE assigned_to = $user_id OR created_by = $user_id OR assigned_by = $user_id ORDER BY created_on DESC";
				$query = "SELECT * FROM eq2editor.tasks WHERE assigned_to = $user_id ORDER BY created_on DESC";
				break;
			case "open": 
				$query = "SELECT * FROM eq2editor.tasks WHERE archived = 0 ORDER BY created_on DESC";
				break;
			case "closed": 
				$query = "SELECT * FROM eq2editor.tasks WHERE archived = 1 ORDER BY created_on DESC";
				break;
		}
		
		$result=$eq2->db->sql_query($query);
		while($data=$eq2->db->sql_fetchrow($result)) {
			$RowColor = ( $i % 2 ) ? '#eeeeee' : '#dddddd';
			printf('<tr bgcolor="%s"><td>%lu</td><td nowrap>%s</td><td nowrap>%s</td><td nowrap>%s</td><td nowrap>%s</td><td>%s</td><td align="center">%s</td>', 
				$RowColor, 
				$data['id'], 
				$eq2->GetCharacterNameByID($data['assigned_to']), 
				$data['task_title'], 
				$data['category'], 
				date('Y-m-d', $data['created_on']), 
				( $data['date_started'] > 0 ) ? date('Y-m-d', $data['date_started']) : "N/A", 
				$data['progress']."%");
			printf("<td><input type=\"button\" value=\"Detail\" onclick=\"javascript:window.open('project.php?page=tasks&type=%s&id=%lu', target='_self');\" style=\"width:60px; font-size:10px\"></td></tr>", $_GET['type'], $data['id']);
			$i++;
		}
?>
		</table>
<?php
	}

}


function ShowBugList() {
	global $eq2, $db_name;
	
	switch($_POST['cmd']) {

		case "Assign":
			break;
			
		case "Delete":
			$eq2->processDelete($_POST['tableName']);
			print("<script>window.open('project.php?page=bugs', target='_self');</script>");
			break;
			
		case "Add Note":
			if( isset($_POST['note']) && strlen($_POST['note']) > 3 ) {
				$query = sprintf("insert into eq2editor.bug_notes (db_name, bug_id, note, author, note_date) values ('%s', %lu, '%s', %lu, %lu);", 
					$db_name, $_POST['orig_id'], addslashes($_POST['note']), $_POST['author_id'], time());
				//echo $query;
				$eq2->runQuery($query);
				$eq2->logQuery($query);
				$p = array('eq2editor.bug_notes',$_POST['orig_id'],$query);
				$eq2->dbEditorLog($p);
				
			} else {
				die("Notes must be longer than 3 characters.<br /><a href='javascript:history.back(1)'>&lt;&lt; Back</a>");
			}
			break;
			
		case "Reply":
			break;
			
	}
	?>
	<table width="100%" border="1" cellspacing="0" cellpadding="4">
		<tr>
			<td colspan="8" height="50"><b>Reminder:</b> Closing/Deleting bugs here does NOT remove them from master bug list (LE's bug list).</td>
		</tr>
		<tr>
			<td width="50"><strong>id</strong></td>
			<td width="125"><strong>bug_datetime</strong></td>
			<td width="100"><strong>player</strong></td>
			<td width="100"><strong>category</strong></td>
			<td width="100"><strong>subcategory</strong></td>
			<td><strong>summary</strong></td>
			<td><strong>notes</strong></td>
			<td width="60">&nbsp;</td>
		</tr>
	<?php

	if( isset($_GET['id']) )  {

		$query = sprintf("select * from %s.bugs where id = %lu;", $db_name, $_GET['id']);
		$data=$eq2->db->sql_fetchrow($eq2->db->sql_query($query));	
		$RowColor = ( isset($_POST['row_color']) ) ? $_POST['row_color'] : '#eeeeee'; 
		?>
		<form action="project.php?page=bugs&id=<?php print($data['id']) ?>" method="post" name="bug|<?php print($data['id']) ?>">
		<tr bgcolor="<?= $RowColor ?>">
			<td><?= $data['id'] ?></td>
			<td nowrap><?= $data['bug_datetime'] ?></td>
			<td nowrap><?= $data['player'] ?></td>
			<td nowrap><?= $data['category'] ?></td>
			<td nowrap><?= $data['subcategory'] ?></td>
			<td><?= $data['summary'] ?></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td valign="top" colspan="6">
				<table width="100%" border=1 cellspacing=0 cellpadding=4>
					<tr>
						<td colspan="8" align="center">
							<input type="button" value="Back" onclick="javascript:window.open('project.php?page=bugs', target='_self');" style="width:60px; font-size:10px" />
							<input type="button" value="Reload" onclick="javascript:window.open('project.php?page=bugs&id=<?= $data['id'] ?>', target='_self');" style="width:60px; font-size:10px" />
							<input type="submit" name="cmd" value="Assign" style="width:60px; font-size:10px" disabled="disabled" />
							<input type="submit" name="cmd" value="Reply" style="width:60px; font-size:10px" disabled="disabled" />
							<input type="submit" name="cmd" value="Delete" style="width:60px; font-size:10px" />
						</td>
					</tr>
					<tr>
						<td colspan="8" style="text-align:center; background-color:#abc; font-weight:bold;">Bug Details</td>
					</tr>
					<tr>
						<td width="20%" rowspan="5" valign="top">
							<span style="text-align:left; font-size:11px;">Other Bugs From This Account</span>
							<br />
							<?= ListBugsFromAccount($data['account_id'], $data['id']) ?>
						</td>
						<td width="80%" colspan="7"><strong>Details:</strong><br /><?= $data['description'] ?><br />&nbsp;</td>
					</tr>
					<tr bgcolor="<?= $RowColor ?>" style="font-size:11px;">
						<td valign="top"><strong>account:</strong><br /><?= $data['account_id'] ?></td>
						<td valign="top"><strong>crash:</strong><br /><?= $data['causes_crash'] ?></td>
						<td valign="top"><strong>reproduce:</strong><br /><?= $data['reproducible'] ?></td>
						<td valign="top"><strong>version:</strong><br /><?= $data['version'] ?></td>
						<td valign="top"><strong>spawn (id):</strong><br /><?= $data['spawn_name'] ?> (<?= $data['spawn_id'] ?>)</td>
						<td valign="top"><strong>zone:</strong><br /><?= $eq2->getZoneNameByID($data['zone_id']) ?></td>
						<td valign="top"><strong>assigned to:</strong><br /><?= $data['assigned_to'] ?></td>
					</tr>
					<tr>
						<td colspan="7" style="text-align:center; background-color:#acc; font-weight:bold;"><strong>Notes</strong></td>
					</tr>
					<tr>
						<td colspan="7" style="padding:10px;"><?= DisplayBugNotes($data['id']) ?><br />&nbsp;</td>
					</tr>
					<tr>
						<td colspan="7">
							<textarea name="note" style="font-size:12px; width:97%; height:50px;"></textarea><br />
							<div align="center"><input type="submit" name="cmd" value="Add Note" style="width:60px; font-size:10px" /></div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<input type="hidden" name="author_id" value="<?= $eq2->userdata['id'] ?>">
		<input type="hidden" name="orig_id" value="<?= $data['id'] ?>">
		<input type="hidden" name="tableName" value="<?= $db_name ?>.bugs">
		</form>
	<?php
	} else {
	
		$query = sprintf("select * from %s.bugs;", $db_name); echo $query;
		$result=$eq2->db->sql_query($query);
		while($data=$eq2->db->sql_fetchrow($result)) {
			$RowColor = ( $i % 2 ) ? '#eeeeee' : '#dddddd';
			printf('<tr bgcolor="%s"><td>%lu</td><td nowrap>%s</td><td nowrap>%s</td><td nowrap>%s</td><td nowrap>%s</td><td>%s</td><td align="center">%s</td>', 
				$RowColor, $data['id'], $data['bug_datetime'], $data['player'], $data['category'], $data['subcategory'], $data['summary'], $eq2->CheckHasBugNotes($data['id']));
			printf("<td><input type=\"button\" value=\"Detail\" onclick=\"javascript:window.open('project.php?page=bugs&id=%lu', target='_self');\" style=\"width:60px; font-size:10px\"></td></tr>", $data['id']);
			$i++;
		}
	}

	?>
	</table>
	<?php
}


function DisplayBugNotes($id) {
	global $eq2, $db_name;
	
	$query = sprintf("select * from eq2editor.bug_notes join eq2editor.users on author = char_id where db_name = '%s' and bug_id = %lu order by note_date;", $db_name, $id);
	//echo $query;
	$result=$eq2->db->sql_query($query);
	while($data=$eq2->db->sql_fetchrow($result)) {
		printf('<strong>Entered on %s by %s</strong><br />%s<br />', date('M d, Y H:m',$data['note_date']), $data['username'], $data['note']);
	}
	
}


function ListBugsFromAccount($account_id, $current) {
	global $eq2, $db_name;
	
	$query = sprintf("select id, summary from %s.bugs where account_id = %lu;", $db_name, $account_id);
	$result=$eq2->db->sql_query($query);
	while($data=$eq2->db->sql_fetchrow($result)) {
		$selected = ( $data['id'] == $current ) ? " style=\"text-decoration:underline;\"" : " style=\"text-decoration:none;\"";
		printf("<a href=\"project.php?page=bugs&id=%lu\" target=\"_self\"%s>%lu. %s</a><br />", $data['id'], $selected, $data['id'], $data['summary']);
	}
	
}


function ShowServerStats() {
	global $eq2, $db_name;
	
?>
	<fieldset><legend>Server Stats</legend>
	<table width="70%" cellpadding="4" border="0">
		<tr>
			<td valign="top" width="40%">
				<fieldset><legend>Quick Totals</legend> 
				<table width="100%" cellpadding="2" cellspacing="0" border="0">
					<tr bgcolor="#cccccc">
						<td width="50%"><strong>Stat</strong></td>
						<td width="50%"><strong>Value</strong></td>
					</tr>
					<?php
					$player_stats['Total Accounts'] 	= GetTotalAccounts();
					$player_stats['Total Players'] 		= GetTotalCharacters();
					$player_stats['Average Level'] 		= GetAverageLevel($player_stats['Total Players']);	

					$i = 0;
					foreach($player_stats as $key=>$val)
					{
						$row_class = ( $i % 2 ) ? " bgcolor=\"#dddddd\"" : "";
					?>
					<tr<?php print($row_class) ?>>
						<td><?php print($key) ?></td>
						<td><?php print(round($val)) ?></td>
					</tr>
					<?
					$i++;
					}
					?>
					<tr>
						<td height="135"></td>
					</tr>
				</table>
				</fieldset>
			</td>
			<td valign="top" width="60%">
				<fieldset><legend>Most Active Quests</legend> 
				<table width="100%" cellpadding="2" cellspacing="0" border="0">
					<tr bgcolor="#cccccc">
						<td><strong>QID</strong></td>
						<td><strong>Quest Name</strong></td>
						<td align="right"><strong>Completed</strong></td>
					</tr>
					<?php
					$query = "SELECT quests.quest_id, quests.lua_script, count(character_quests.quest_id) num_completed
										FROM ".$db_name.".quests, ".$db_name.".character_quests 
										WHERE quests.quest_id = character_quests.quest_id AND completed_date IS NOT NULL
										GROUP BY character_quests.quest_id
										ORDER BY num_completed desc;";
					$result = $eq2->db->sql_query($query);
					$i = 0;
					while($data = $eq2->db->sql_fetchrow($result))
					{
						$row_class = ( $i % 2 ) ? " bgcolor=\"#dddddd\"" : "";
						$pattern[0] = "/Quests\/.*?\/(.*?).lua/";
						$pattern[1] = "/_/";
						$replace[0] = "$1";
						$replace[1] = " ";
						$quest_name = preg_replace($pattern,$replace,$data['lua_script']);
					?>
					<tr<?php print($row_class) ?>>
						<td><?php print($data['quest_id']) ?></td>
						<td><?php print($quest_name) ?></td>
						<td align="right"><?php print($data['num_completed']) ?></td>
					</tr>
					<?
						$i++;
					}
					?>
				</table>
				</fieldset>
			</td>
		</tr>
	</table>
	</fieldset>
<?php
}


function ShowPlayerStats() {
	global $eq2, $db_name;

?>
	<fieldset><legend>Player Stats</legend>
	<table width="100%" cellpadding="4" border="0">
		<tr>
			<td valign="top" width="25%">
				<fieldset><legend>unused</legend> 
				<table width="100%" cellpadding="2" cellspacing="0" border="0">
					<tr bgcolor="#cccccc">
						<td width="50%"><strong></strong></td>
						<td width="50%"><strong></strong></td>
					</tr>
				</table>
				</fieldset>
			</td>
			<td valign="top" width="40%">
				<fieldset><legend>Most Experienced Players</legend> 
				<table width="100%" cellpadding="2" cellspacing="0" border="0">
					<tr bgcolor="#cccccc">
						<td><strong>Player</strong></td>
						<td><strong>Class</strong></td>
						<td align="right"><strong>Levels</strong></td>
						<td align="right"><strong>Quests</strong></td>
					</tr>
					<?php
					$query = "SELECT name, class, level, tradeskill_level, count(quest_id) as quests, admin_status 
										FROM ".$db_name.".characters, ".$db_name.".character_quests 
										WHERE characters.id = character_quests.char_id AND admin_status = 0
										GROUP BY characters.id
										ORDER BY level desc LIMIT 0, 25;";
					$result = $eq2->db->sql_query($query);
					$i = 0;
					while($data = $eq2->db->sql_fetchrow($result))
					{
						$row_class = ( $i % 2 ) ? " bgcolor=\"#dddddd\"" : "";
					?>
					<tr<?php print($row_class) ?>>
						<td><?php print($data['name']) ?></td>
						<td><?php print($eq2->eq2Classes[$data['class']]) ?></td>
						<td align="right"><?php printf("%d / %d", $data['level'], $data['tradeskill_level']) ?></td>
						<td align="right"><?php print($data['quests']) ?></td>
					</tr>
					<?
						$i++;
					}
					?>
				</table>
				</fieldset>
			</td>
			<td valign="top" width="35%">
				<fieldset><legend>unused</legend> 
				<table width="100%" cellpadding="2" cellspacing="0" border="0">
					<tr bgcolor="#cccccc">
						<td><strong></strong></td>
						<td><strong></strong></td>
						<td align="right"><strong></strong></td>
					</tr>
				</table>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td valign="top" colspan="3">
				<fieldset><legend>Players Last Login</legend> 
				<table width="100%" cellpadding="2" cellspacing="0" border="0">
					<tr bgcolor="#cccccc">
						<td><strong>ID</strong></td>
						<td><strong>Account</strong></td>
						<td><strong>Player</strong></td>
						<td><strong>Class</strong></td>
						<td align="right"><strong>Levels</strong></td>
						<td align="right"><strong>Zone</strong></td>
						<td align="right"><strong>Date</strong></td>
					</tr>
					<?php
					$query = "SELECT id, account_id, name, class, level, tradeskill_level, current_zone_id, last_played, admin_status FROM ".$db_name.".characters ORDER BY last_played desc LIMIT 0,100;";
					$result = $eq2->db->sql_query($query);
					while($data = $eq2->db->sql_fetchrow($result)) {
						$row_class = ( $i % 2 ) ? " bgcolor=\"#dddddd\"" : "";
						$player_name_class = $eq2->GetGMNameColor($data['admin_status']);
					?>
					<tr<?php print($row_class) ?>>
						<td><?php print($data['id']) ?></td>
						<td><?php print($data['account_id']) ?></td>
						<td<?php print($player_name_class) ?>><?php print($data['name']) ?></td>
						<td><?php print($eq2->eq2Classes[$data['class']]) ?></td>
						<td align="right"><?php printf("%d / %d", $data['level'], $data['tradeskill_level']) ?></td>
						<td align="right"><?php print($eq2->getZoneNameByID($data['current_zone_id'])) ?></td>
						<td align="right"><?php print($data['last_played']) ?></td>
					</tr>
					<?
						$i++;
					}
					?>
				</table>
				</fieldset>
			</td>
		</tr>
	</table>
	</fieldset>
<?php
}


function ShowProjectStats() {
	global $eq2;
?>
	<fieldset><legend>Project Stats</legend>
	<table width="100%" cellpadding="4">
		<tr>
			<td valign="top" width="25%">
				<fieldset><legend>Table Data</legend> 
				<table width="100%" cellpadding="2" cellspacing="0" border="0">
					<tr bgcolor="#cccccc">
						<td width="50%"><strong>Table</strong></td>
						<td width="50%"><strong>Records</strong></td>
					</tr>
					<?php
			
					// build server stats array
					$query = "SELECT DISTINCT TABLE_NAME FROM information_schema.tables WHERE TABLE_SCHEMA = `".DEV_DB."` AND TABLE_ROWS > 0;";
					$result = $eq2->db->sql_query($query);
					while($data = $eq2->db->sql_fetchrow($result))
					{
						$server_stats[$data['TABLE_NAME']] .= $eq2->getTotalRows($config['live_db'],$data['TABLE_NAME']);
					}

					$i = 0;
					foreach($server_stats as $key=>$val)
					{
						$row_class = ( $i % 2 ) ? " bgcolor=\"#dddddd\"" : "";
					?>
					<tr<?php print($row_class) ?>>
						<td><?php print($key) ?></td>
						<td><?php print($val) ?></td>
					</tr>
					<?
					$i++;
					}
					?>
				</table>
				</fieldset>
			</td>
			<td valign="top" colspan="2">
				<fieldset><legend>Zones Populated</legend> 
				<table width="100%" cellpadding="2" cellspacing="0" border="0">
					<tr bgcolor="#cccccc">
						<td><strong>Zone</strong></td>
						<td><strong>NPCs</strong></td>
						<td><strong>Objects</strong></td>
						<td><strong>Signs</strong></td>
						<td><strong>Widgets</strong></td>
						<td><strong>Ground</strong></td>
						<td><strong>Loot</strong></td>
						<td><strong>Quests</strong></td>
					</tr>
					<?php
					$query = "SELECT DISTINCT z1.id as zid, z1.description, z1.name
										FROM zones z1
										JOIN zonespawns z2 ON z1.id = z2.zone_id
										GROUP BY z1.id;";
					$result = $eq2->db->sql_query($query);

					$i = 0;
					while($data = $eq2->db->sql_fetchrow($result))
					{
						$row_class = ( $i % 2 ) ? " bgcolor=\"#dddddd\"" : "";
						$num_npcs					= $eq2->getSpawnTypeTotalsByZone('npcs', $data['zid']);
						$num_objects			= $eq2->getSpawnTypeTotalsByZone('objects', $data['zid']);
						$num_signs				= $eq2->getSpawnTypeTotalsByZone('signs', $data['zid']);
						$num_widgets			= $eq2->getSpawnTypeTotalsByZone('widgets', $data['zid']);
						$num_groundspawns	= $eq2->getSpawnTypeTotalsByZone('ground', $data['zid']);
						$num_loots				= $eq2->getSpawnTypeTotalsByZone('loot', $data['zid']);
						$num_quests				= $eq2->getTotalQuestsByZone($data['name']); // barbarian! since there's no way to link a quest to a zone except by it's fookin path! :/
					?>
					<tr<?php print($row_class) ?>>
						<td><?php printf("%s (%d)", $data['description'], $data['zid']) ?></td>
						<td><?php print($num_npcs) ?></td>
						<td><?php print($num_objects) ?></td>
						<td><?php print($num_signs) ?></td>
						<td><?php print($num_widgets) ?></td>
						<td><?php print($num_groundspawns) ?></td>
						<td><?php print($num_loots) ?></td>
						<td><?php print($num_quests) ?></td>
					</tr>
					<?
						$i++;
					}
					?>
				</table>
				</fieldset>
			</td>
		</tr>
	</table>
	</fieldset>
<?php
}


function GetTotalAccounts() {
	global $eq2, $db_name;
	
	$query = sprintf("select count(distinct account_id) as unique_accounts from %s.characters", $db_name); //echo $query;
	$data = $eq2->runScalarQuery($query);
	return ( !empty($data) ) ? $data['unique_accounts'] : 0;
}


function GetTotalCharacters() {
	global $eq2, $db_name;

	$query = sprintf("select count(id) as char_count from %s.characters;", $db_name); //echo $query;
	$data = $eq2->runScalarQuery($query);
	return ( !empty($data) ) ? $data['char_count'] : 0;
}


function GetAverageLevel($char_count) {
	global $eq2, $db_name;

	$query = sprintf("select sum(level) as average_levels from %s.characters;", $db_name); //echo $query;
	$data = $eq2->runScalarQuery($query);
	return ( !empty($data) ) ? $data['average_levels'] / $char_count : 0;
}

?>