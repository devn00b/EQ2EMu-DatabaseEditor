<?php 
define('IN_EDITOR', true);
include("header.php");

if ( !$eq2->CheckAccess(M_CHARACTERS) )
	die("Access denied!");

include("../class/eq2.zones.php");
include("../class/eq2.characters.php");

$charClass = new eq2Characters;

?>
<div id="sub-menu1"><a href="characters.php">Character Editor</a> | <a href="characters.php?cl=history">Characters Changelog</a></div>
<?php
if( isset($_GET['cl']) ) {
	?>
	<table>
		<tr>
			<td>
				<select name="table_name" onchange="dosub(this.options[this.selectedIndex].value)">
					<option>Pick a table</option>
					<option value="characters.php?cl=history&t=characters"<?php if( $_GET['t']=="characters" ) echo " selected" ?>>characters</option> 
					<option value="characters.php?cl=history&t=character_details"<?php if( $_GET['t']=="character_details" ) echo " selected" ?>>character_details</option> 
					<option value="characters.php?cl=history&t=char_colors"<?php if( $_GET['t']=="char_colors" ) echo " selected" ?>>char_colors</option> 
					<option value="characters.php?cl=history&t=character_factions"<?php if( $_GET['t']=="character_factions" ) echo " selected" ?>>character_factions</option> 
					<option value="characters.php?cl=history&t=character_buyback"<?php if( $_GET['t']=="character_buyback" ) echo " selected" ?>>character_buyback</option> 
					<option value="characters.php?cl=history&t=character_house"<?php if( $_GET['t']=="character_house" ) echo " selected" ?>>character_house</option>
					<option value="characters.php?cl=history&t=character_house_access"<?php if( $_GET['t']=="character_house_access" ) echo " selected" ?>>character_house_access</option>
					<option value="characters.php?cl=history&t=character_house_deposit"<?php if( $_GET['t']=="character_house_deposit" ) echo " selected" ?>>character_house_deposit</option>
					<option value="characters.php?cl=history&t=character_house_spawn"<?php if( $_GET['t']=="character_house_spawn" ) echo " selected" ?>>character_house_spawn</option>
					<option value="characters.php?cl=history&t=character_items"<?php if( $_GET['t']=="character_items" ) echo " selected" ?>>character_items</option> 
					<option value="characters.php?cl=history&t=character_mail"<?php if( $_GET['t']=="character_mail" ) echo " selected" ?>>character_mail</option> 
					<option value="characters.php?cl=history&t=character_quests"<?php if( $_GET['t']=="character_quests" ) echo " selected" ?>>character_quests</option> 
					<option value="characters.php?cl=history&t=character_quest_progress"<?php if( $_GET['t']=="character_quest_progress" ) echo " selected" ?>>character_quest_progress</option> 
					<option value="characters.php?cl=history&t=character_skillbar"<?php if( $_GET['t']=="character_skillbar" ) echo " selected" ?>>character_skillbar</option> 
					<option value="characters.php?cl=history&t=character_skills"<?php if( $_GET['t']=="character_skills" ) echo " selected" ?>>character_skills</option> 
					<option value="characters.php?cl=history&t=character_spells"<?php if( $_GET['t']=="character_spells" ) echo " selected" ?>>character_spells</option> 
					<option value="characters.php?cl=history&t=character_achievements"<?php if( $_GET['t']=="character_achievements" ) echo " selected" ?>>character_achievements</option> 
					<option value="characters.php?cl=history&t=character_access"<?php if( $_GET['t']=="character_access" ) echo " selected" ?>>character_access</option> 
					<option value="characters.php?cl=history&t=character_friendlist"<?php if( $_GET['t']=="character_friendlist" ) echo " selected" ?>>character_friendlist</option> 
					<option value="characters.php?cl=history&t=character_language"<?php if( $_GET['t']=="character_language" ) echo " selected" ?>>character_language</option> 
					<option value="characters.php?cl=history&t=character_macros"<?php if( $_GET['t']=="character_macros" ) echo " selected" ?>>character_macros</option> 
					<option value="characters.php?cl=history&t=character_pet"<?php if( $_GET['t']=="character_pet" ) echo " selected" ?>>character_pet</option> 
					<option value="characters.php?cl=history&t=character_timer"<?php if( $_GET['t']=="character_timer" ) echo " selected" ?>>character_timer</option> 
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
?>

<script>
function CharAjaxSelect() {
	let e = document.getElementById("txtSearch");

	//Find the selected character id via regex
	const id_pat = / \((\d+)\)$/;

	const m = e.value.match(id_pat);

	window.location.search = "?c=" + m[1];
}

function CharLookupAJAX() {
	if (searchReq.readyState == 4 || searchReq.readyState == 0) {
		var str = escape(document.getElementById('txtSearch').value);
		if (str.length == 0) {
				let ss = document.getElementById('search_suggest')
				ss.innerHTML = '';
				return;
		}
		searchReq.open("GET", '../ajax/eq2Ajax.php?type=luCh&search=' + str, true);
		searchReq.onreadystatechange = handleSearchSuggest; 
		ajaxSelectCallback = CharAjaxSelect;
		searchReq.send(null);
	}		
}
</script>

<table>
	<tr>
		<td>
			<strong>Lookup:</strong>
			<?php 
			$charName = $eq2->GetCharacterNameByID($_GET['c'] ?? 0);
			?>
			<input type="text" id="txtSearch" name="txtSearch" onkeyup="CharLookupAJAX();" autocomplete="off" class="medium"<?php if (isset($_GET['c'])) echo ' value="'.$charName.'" ';?>/>
			<a href="characters.php?<?php echo $_SERVER['QUERY_STRING'] ?>">Reload Page</a>
			<div id="search_suggest"></div>
		</td>
	</tr>
</table>

<?php
if( isset($_GET['c']) ) 
{

	if( isset($_POST['swapSpells']) ) $eq2->swapPlayerSpellSet();
	if( isset($_POST['csDelete']) ) $eq2->deletePlayerSpellSet($_POST['char_id']);

	// do updates/deletes here
	switch(strtolower($_POST['cmd'] ?? "")) {
		case "insert": $eq2->ProcessInserts(); break;
		case "update": $eq2->ProcessUpdates(); break;
		case "delete": $eq2->ProcessDeletes(); break;
	}


	drawCharacterEditor($_GET['c']);
	include("footer.php");
	
	exit; // end of page
}

function drawCharacterEditor($id) {
	global $eq2, $charName;
	$link = sprintf("%s?c=%d",$_SERVER['SCRIPT_NAME'],$id);
	?>
	<div id="sub-menu1">
		<table cellspacing="0">
			<tr>
				<td align="right"><strong>Data:&nbsp;</strong></td>
				<td>
					[ <a href="<?php print($link) ?>">characters</a> ] &bull; 
					[ <a href="<?php print($link) ?>&p=character_details">character_details</a> ] &bull; 
					[ <a href="<?php print($link) ?>&p=char_colors">char_colors</a> ] &bull; 
					[ <a href="<?php print($link) ?>&p=character_factions">character_factions</a> ]
				</td>
			</tr>
			<tr>
				<td align="right"><strong>Inventory:&nbsp;</strong></td>
				<td>
					[ <a href="<?php print($link) ?>&p=character_buyback">character_buyback</a> ] &bull; 
					[ <a href="<?php print($link) ?>&p=character_house"></a>character_house ] &bull; 
					[ <a href="<?php print($link) ?>&p=character_house_access"></a>character_house_access ] &bull;
					[ <a href="<?php print($link) ?>&p=character_house_deposit"></a>character_house_deposit ] &bull;
					[ <a href="<?php print($link) ?>&p=character_house_spawn"></a>character_house_spawn ] &bull;
					[ <a href="<?php print($link) ?>&p=character_items">character_items</a> ]
				</td>
			</tr>
			<tr>
				<td align="right"><strong>Knowledge:&nbsp;</strong></td>
				<td>
					[ <a href="<?php print($link) ?>&p=character_quests">character_quests</a> ] &bull;
					[ <a href="<?php print($link) ?>&p=character_quest_progress">character_quest_progress</a> ] &bull;
					[ <a href="<?php print($link) ?>&p=character_skillbar">character_skillbar</a> ] &bull;
					[ <a href="<?php print($link) ?>&p=character_skills">character_skills</a> ] &bull;
					[ <a href="<?php print($link) ?>&p=character_spells">character_spells</a> ] &bull; 
					[ <a href="<?php print($link) ?>&p=character_achievements"></a>character_achievements ]
				</td>
			</tr>
			<tr>
				<td align="right"><strong>Misc:&nbsp;</strong></td>
				<td>
					[ <a href="<?php print($link) ?>&p=character_access"></a>character_access ] &bull;
					[ <a href="<?php print($link) ?>&p=character_friendlist"></a>character_friendlist ] &bull; 
					[ <a href="<?php print($link) ?>&p=character_language"></a>character_language ] &bull; 
					[ <a href="<?php print($link) ?>&p=character_macros">character_macros</a> ] &bull;
					[ <a href="<?php print($link) ?>&p=character_pet"></a>character_pet ] &bull;
					[ <a href="<?php print($link) ?>&p=character_timer"></a>character_timer ] &bull;
					[ <a href="<?php print($link) ?>&p=character_mail"></a>character_mail ]
				</td>
			</tr>
		</table>
	</div>
	<?php
	switch(isset($_GET['p']) ? $_GET['p'] : "") {

		case "character_mail":
			character_mail($_GET['c']);
			break;
			
		case "character_buyback":
			character_buyback($_GET['c']);
			break;
			
		case "template":
			$query=sprintf("select * from `".DEV_DB."`.character_details where character_id = %d",$id);
			$result=$eq2->db->sql_query($query);
			$data=$eq2->db->sql_fetchrow($result);
			?>
			<table border="0" cellpadding="5">
			<form method="post" name="CharEdit" />
				<tr>
					<td width="680" valign="top">
						<fieldset style="width:675px;"><legend>General</legend> 
						<table width="100%" border="0">
							<tr>
								<td colspan="6">
									<span class="heading">Editing: <?= $charName ?></span><br />
									<input type="hidden" name="orig_object" value="<?= $charName ?>" />
								</td>
							</tr>
						</table>
						</fieldset>
					</td>
				</tr>
				<?php if($eq2->CheckAccess(G_GUIDE)) { ?>
				<tr>
					<td colspan="2" align="center">
						<input type="submit" name="sUpdate" value="Update" style="width:100px;" />&nbsp;
						<input type="button" value="Help" style="width:100px" onclick="javascript:window.open('help.php#spawns','help','resizable,width=480,height=640,left=10,top=75,scrollbars=yes');" />						
					</td>
				</tr>
				<?php } ?>
			</form>
			</table>
			<?php
			break;

		case "character_macros":
			$query=sprintf("select * from `".DEV_DB."`.character_macros where character_id = %d",$id);
			$result=$eq2->db->sql_query($query);
			$data=$eq2->db->sql_fetchrow($result);
			?>
			<table border="0" cellpadding="5">
				<tr>
					<td width="680" valign="top">
						<fieldset style="width:675px;"><legend>General</legend> 
						<table width="100%" border="0">
							<tr>
								<td colspan="6">
									<span class="heading">Editing: <?= $charName ?></span><br />
								</td>
							</tr>
							<tr>
								<td>id</td>
								<td>char_id</td>
								<td>macro_number</td>
								<td>macro_icon</td>
								<td>macro_name</td>
								<td>macro_text</td>
								<td colspan="2">&nbsp;</td>
							</tr>

							<?php
							$query=sprintf("select * from `".DEV_DB."`.character_macros where char_id = %d",$id);
							$result=$eq2->db->sql_query($query);
							while($data=$eq2->db->sql_fetchrow($result)) {
							?>
							<form method="post" name="multiForm|<?php print($data['id']); ?>" />
							<tr>
								<td>
									<input type="text" name="character_macros|id" value="<?php print($data['id']) ?>"  style="width:50px;  background-color:#ddd;" readonly />
									<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
									<input type="hidden" name="orig_object" value="<?= $charName ?>" />
								</td>
								<td>
									<input type="text" name="character_macros|char_id" value="<?php print($data['char_id']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_char_id" value="<?php print($data['char_id']) ?>" />
								</td>
								<td>
									<input type="text" name="character_macros|macro_number" value="<?php print($data['macro_number']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_macro_number" value="<?php print($data['macro_number']) ?>" />
								</td>
								<td>
									<input type="text" name="character_macros|macro_icon" value="<?php print($data['macro_icon']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_macro_icon" value="<?php print($data['macro_icon']) ?>" />
								</td>
								<td>
									<input type="text" name="character_macros|macro_name" value="<?php print($data['macro_name']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_macro_name" value="<?php print($data['macro_name']) ?>" />
								</td>
								<td>
									<input type="text" name="character_macros|macro_text" value="<?php print($data['macro_text']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_macro_text" value="<?php print($data['macro_text']) ?>" />
								</td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" /><?php } ?></td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" /><?php } ?></td>
							</tr>
							</form>
							<?php
							}
							?>
						</table>
						</fieldset>
					</td>
				</tr>
			</table>
			<?php
			break;

		case "character_spells":
			$query=sprintf("select c.id as cid,c.class,c.name as cname,cs.* from `".DEV_DB."`.characters c left join `".DEV_DB."`.character_spells cs on c.id = cs.char_id where c.id = %d",$id);
			$result=$eq2->db->sql_query($query);
			$data1=$eq2->db->sql_fetchrow($result);
			$class_id = $data1['class'];
			$char_id = $data1['cid'];
			$objectName = $data1['cname'];
			$table = 'character_spells';
			?>
			<table border="0" cellpadding="5">
				<tr>
					<td width="680" valign="top">
						<fieldset><legend>General</legend> 
						<table width="100%" border="0">
							<tr>
								<td colspan="6">
									<span class="heading">Editing: <?= $objectName ?></span><br />
								</td>
							</tr>
							<tr>
								<td>id</td>
								<td>char_id</td>
								<td>spell_id</td>
								<td>tier</td>
								<td>knowledge_slot</td>
								<td colspan="2">&nbsp;</td>
							</tr>
							<?php
							$query=sprintf("select * from %s where char_id = %d", $table, $id);
							$result=$eq2->db->sql_query($query);
							while($data=$eq2->db->sql_fetchrow($result)) {
							?>
							<form method="post" name="multiForm|<?php print($data['id']); ?>" />
							<tr>
								<td>
									<input type="text" name="character_spells|id" value="<?php print($data['id']) ?>"  style="width:40px;  background-color:#ddd;" readonly />
									<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
									<input type="hidden" name="orig_object" value="<?= $charName ?>" />
								</td>
								<td>
									<input type="text" name="character_spells|char_id" value="<?php print($data['char_id']) ?>"  style="width:40px;" />
									<input type="hidden" name="orig_char_id" value="<?php print($data['char_id']) ?>" />
								</td>
								<td>
									<input type="text" name="character_spells|spell_id" value="<?php print($data['spell_id']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_spell_id" value="<?php print($data['spell_id']) ?>" />
								</td>
								<td>
									<input type="text" name="character_spells|tier" value="<?php print($data['tier']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_tier" value="<?php print($data['tier']) ?>" />
								</td>
								<td>
									<input type="text" name="character_spells|knowledge_slot" value="<?php print($data['knowledge_slot']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_knowledge_slot" value="<?php print($data['knowledge_slot']) ?>" />
								</td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" /><?php } ?></td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" /><?php } ?></td>							
							</tr>
							<input type="hidden" name="orig_object" value="<?= $objectName ?>" />
							<input type="hidden" name="table_name" value="<?= $table ?>" />
							</form>
							<?php
							}
							if( $eq2->CheckAccess(G_SUPERADMIN) ) {
							?>
							<form method="post" name="swapForm" />
							<tr>
								<td colspan="6" valign="bottom">
									<select name="class_id">
									<?= $eq2->GetClasses($class_id); ?>
									</select>&nbsp;
									<input type="checkbox" name="is-live" value="1" checked="checked" title="Only set spells that have been validated." />
									<input type="submit" name="swapSpells" value="Set" title="This sets the character Knowledge entries to the selected class." style="width:100px;" />&nbsp;
									<input type="submit" name="csDelete" value="Purge Book" title="This will erase all Knowledge entries from this character." style="width:100px;" />
								</td>
							</tr>
							<input type="hidden" name="char_id" value="<?php print($char_id) ?>" />
							</form>
							<?php } ?>
						</table>
						</fieldset>
					</td>
				</tr>
				<tr>
					<td><strong>Usage:</strong><br />
						<ul>
							<li>Select the Class of spells to push to the players Knowledge book.</li>
							<li>The checkmark active will only load spell data that has been validated for general availability ("Release").</li>
							<li>Clear the checkmark to load every spell for that classes archetype into the Knowledge book of the current player.</li>
							<li>The Set button performs a Knowledge book purge, then fill with the selected class data.</li>
							<li>Purge will only delete all the players Knowledge data.</li>
						</ul>
					</td>
				</tr>
			</table>
			<?php
			break;			

		case "character_skills":

			?>
			<table border="0" cellpadding="5">
				<tr>
					<td width="680" valign="top">
						<fieldset style="width:675px;"><legend>General</legend> 
						<table width="100%" border="0">
							<tr>
								<td colspan="6">
									<span class="heading">Editing: <?= $charName ?></span><br />
								</td>
							</tr>
							<tr>
								<td>id</td>
								<td>char_id</td>
								<td>skill_id</td>
								<td>current_val</td>
								<td>max_val</td>
								<td>progress</td>
								<td colspan="2">&nbsp;</td>
							</tr>

							<?php
							$query=sprintf("select * from `".DEV_DB."`.character_skills where char_id = %d",$id);
							$result=$eq2->db->sql_query($query);
							while($data=$eq2->db->sql_fetchrow($result)) {
							?>
							<form method="post" name="multiForm|<?php print($data['id']); ?>" />
							<tr>
								<td>
									<input type="text" name="character_skills|id" value="<?php print($data['id']) ?>"  style="width:50px;  background-color:#ddd;" readonly />
									<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
								</td>
								<td>
									<input type="text" name="character_skills|char_id" value="<?php print($data['char_id']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_char_id" value="<?php print($data['char_id']) ?>" />
								</td>
								<td>
									<select name="character_skills|skill_id" style="width:100px; font-size:11px;">
										<?= $eq2->getClassSkills($data['skill_id']) ?>
									</select>
									<input type="hidden" name="orig_skill_id" value="<?php print($data['skill_id']) ?>" />
								</td>
								<td>
									<input type="text" name="character_skills|current_val" value="<?php print($data['current_val']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_current_val" value="<?php print($data['current_val']) ?>" />
								</td>
								<td>
									<input type="text" name="character_skills|max_val" value="<?php print($data['max_val']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_max_val" value="<?php print($data['max_val']) ?>" />
								</td>
								<td>
									<input type="text" name="character_skills|progress" value="<?php print($data['progress']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_progress" value="<?php print($data['progress']) ?>" />
								</td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" /><?php } ?></td>
								<td>
									<?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" /><?php } ?>
									<input type="hidden" name="orig_object" value="<?= $charName ?>" />
									<input type="hidden" name="table_name" value="character_skills" />									
								</td>
							</tr>
							</form>
							<?php
							}
							?>
						</table>
						</fieldset>
					</td>
				</tr>
			</table>
			<?php
			break;

		case "character_skillbar":
			$query=sprintf("select * from `".DEV_DB."`.character_skillbar where character_id = %d",$id);
			$result=$eq2->db->sql_query($query);
			$data=$eq2->db->sql_fetchrow($result);
			?>
			<table border="0" cellpadding="5">
				<tr>
					<td width="680" valign="top">
						<fieldset style="width:675px;"><legend>General</legend> 
						<table width="100%" border="0">
							<tr>
								<td colspan="6">
									<span class="heading">Editing: <?= $charName ?></span><br />
								</td>
							</tr>
							<tr>
								<td>id</td>
								<td>char_id</td>
								<td>type</td>
								<td>hotbar</td>
								<td>spell_id</td>
								<td>slot</td>
								<td>text_val</td>
								<td colspan="2">&nbsp;</td>
							</tr>

							<?php
							$query=sprintf("select * from `".DEV_DB."`.character_skillbar where char_id = %d",$id);
							$result=$eq2->db->sql_query($query);
							while($data=$eq2->db->sql_fetchrow($result)) {
							?>
							<form method="post" name="multiForm|<?php print($data['id']); ?>" />
							<tr>
								<td>
									<input type="text" name="character_skillbar|id" value="<?php print($data['id']) ?>"  style="width:50px;  background-color:#ddd;" readonly />
									<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
									<input type="hidden" name="orig_object" value="<?= $charName ?>" />
								</td>
								<td>
									<input type="text" name="character_skillbar|char_id" value="<?php print($data['char_id']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_char_id" value="<?php print($data['char_id']) ?>" />
								</td>
								<td>
									<input type="text" name="character_skillbar|type" value="<?php print($data['type']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_type" value="<?php print($data['type']) ?>" />
								</td>
								<td>
									<input type="text" name="character_skillbar|hotbar" value="<?php print($data['hotbar']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_hotbar" value="<?php print($data['hotbar']) ?>" />
								</td>
								<td>
									<input type="text" name="character_skillbar|spell_id" value="<?php print($data['spell_id']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_spell_id" value="<?php print($data['spell_id']) ?>" />
								</td>
								<td>
									<input type="text" name="character_skillbar|slot" value="<?php print($data['slot']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_slot" value="<?php print($data['slot']) ?>" />
								</td>
								<td>
									<input type="text" name="character_skillbar|text_val" value="<?php print($data['text_val']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_text_val" value="<?php print($data['text_val']) ?>" />
								</td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" /><?php } ?></td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" /><?php } ?></td>
							</tr>
							</form>
							<?php
							}
							?>
						</table>
						</fieldset>
					</td>
				</tr>
			</table>
			<?php
			break;

		case "character_quest_progress":

			$table = "character_quest_progress";
			?>
			<table border="0" cellpadding="5">
				<tr>
					<td width="680" valign="top">
						<fieldset style="width:675px;"><legend>General</legend> 
						<table width="100%" border="0">
							<tr>
								<td colspan="6">
									<span class="heading">Editing: <?= $eq2->GetCharacterNameByID($_GET['c']) ?></span><br />
								</td>
							</tr>
							<tr>
								<td>id</td>
								<td>char_id</td>
								<td>quest_id</td>
								<td>step_id</td>
								<td>progress</td>
								<td colspan="2">&nbsp;</td>
							</tr>

							<?php
							$query=sprintf("select * from `%s`.`%s` where char_id = %d",DEV_DB,$table,$id);
							$result = $eq2->RunQueryMulti($query);
							foreach ($result as $data) :
							?>
							<form method="post" name="multiForm|<?php print($data['id']); ?>">
							<tr>
								<td>
									<input type="text" name="character_quest_progress|id" value="<?php print($data['id']) ?>"  style="width:50px;  background-color:#ddd;" readonly />
									<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
								</td>
								<td>
									<input type="text" name="character_quest_progress|char_id" value="<?php print($data['char_id']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_char_id" value="<?php print($data['char_id']) ?>" />
								</td>
								<td>
									<input type="text" name="character_quest_progress|quest_id" value="<?php print($data['quest_id']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_quest_id" value="<?php print($data['quest_id']) ?>" />
								</td>
								<td>
									<input type="text" name="character_quest_progress|step_id" value="<?php print($data['step_id']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_step_id" value="<?php print($data['step_id']) ?>" />
								</td>
								<td>
									<input type="text" name="character_quest_progress|progress" value="<?php print($data['progress']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_progress" value="<?php print($data['progress']) ?>" />
								</td>
								<?php if($eq2->CheckAccess(G_DEVELOPER)) : ?>
								<td>
									<input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" />
								</td>
								<td>
									<input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" />
								</td>
								<?php endif; ?>
							</tr>
							<input type="hidden" name="orig_object" value="" />
							<input type="hidden" name="table_name" value="<?= $table ?>" />
							</form>
							<?php endforeach; ?>
						</table>
						</fieldset>
					</td>
				</tr>
			</table>
			<?php
			break;

		case "character_quests":
		
			$table= "character_quests";
			?>
			<table border="0" cellpadding="5">
				<tr>
					<td width="680" valign="top">
						<fieldset style="width:675px;"><legend>General</legend> 
						<table width="100%" border="0">
							<tr>
								<td colspan="6">
									<span class="heading">Editing: <?= $eq2->GetCharacterNameByID($_GET['c']) ?></span><br />
								</td>
							</tr>
							<tr>
								<td>id</td>
								<td>char_id</td>
								<td>quest_id</td>
								<td>completed_date</td>
								<td colspan="2">&nbsp;</td>
							</tr>

							<?php
							$query=sprintf("select * from `%s`.%s where char_id = %d",DEV_DB,$table,$id);
							$result = $eq2->RunQueryMulti($query);
							foreach ($result as $data) {
							?>
							<form method="post" name="multiForm|<?php print($data['id']); ?>">
							<tr>
								<td>
									<input type="text" name="character_quests|id" value="<?php print($data['id']) ?>"  style="width:50px;  background-color:#ddd;" readonly />
									<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
								</td>
								<td>
									<input type="text" name="character_quests|char_id" value="<?php print($data['char_id']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_char_id" value="<?php print($data['char_id']) ?>" />
								</td>
								<td>
									<input type="text" name="character_quests|quest_id" value="<?php print($data['quest_id']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_quest_id" value="<?php print($data['quest_id']) ?>" />
								</td>
								<td>
									<input type="text" name="character_quests|completed_date" value="<?php print($data['completed_date']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_completed_date" value="<?php print($data['completed_date']) ?>" />
								</td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" /><?php } ?></td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" /><?php } ?></td>
							</tr>
							<input type="hidden" name="orig_object" value="<?= $objectName ?>" />
							<input type="hidden" name="table_name" value="<?= $table ?>" />
							</form>
							<?php
							}
							?>
						</table>
						</fieldset>
					</td>
				</tr>
			</table>
			<?php
			break;

		case "character_items":
			$query=sprintf("select * from `".DEV_DB."`.character_bank where character_id = %d",$id);
			$result=$eq2->db->sql_query($query);
			$data=$eq2->db->sql_fetchrow($result);
			?>
			<table border="0" cellpadding="5">
				<tr>
					<td width="680" valign="top">
						<fieldset style="width:675px;"><legend>General</legend> 
						<table width="100%" border="0">
							<tr>
								<td colspan="6">
									<span class="heading">Editing: <?= $charName ?></span><br />
								</td>
							</tr>
							<tr>
								<td>unique_id</td>
								<td>type</td>
								<td>char_id</td>
								<td>bag_slot</td>
								<td>slot</td>
								<td>item_id</td>
								<td>creator</td>
								<td>condition_</td>
								<td>attuned</td>
								<td>bag_id</td>
								<td>count</td>
								<td colspan="2">&nbsp;</td>
							</tr>

							<?php
							$query=sprintf("select * from `".DEV_DB."`.character_items where char_id = %d",$id);
							$result=$eq2->db->sql_query($query);
							while($data=$eq2->db->sql_fetchrow($result)) {
							?>
							<form method="post" name="multiForm|<?php print($data['id']); ?>" />
							<tr>
								<td>
			            <input type="text" name="character_items|id" value="<?= $data['id'] ?>" readonly style="width:50px; background-color:#ddd;" />
									<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
									<input type="hidden" name="orig_object" value="<?= $charName ?>" />
								</td>
								<td>
									<input type="text" name="character_items|type" value="<?php print($data['type']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_type" value="<?php print($data['type']) ?>" />
								</td>
								<td>
									<input type="text" name="character_items|char_id" value="<?php print($data['char_id']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_char_id" value="<?php print($data['char_id']) ?>" />
								</td>
								<td>
									<input type="text" name="character_items|bag_slot" value="<?php print($data['bag_slot']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_bag_slot" value="<?php print($data['bag_slot']) ?>" />
								</td>
								<td>
									<input type="text" name="character_items|slot" value="<?php print($data['slot']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_slot" value="<?php print($data['slot']) ?>" />
								</td>
								<td>
									<input type="text" name="character_items|item_id" value="<?php print($data['item_id']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_item_id" value="<?php print($data['item_id']) ?>" />
								</td>
								<td>
									<input type="text" name="character_items|creator" value="<?php print($data['creator']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_creator" value="<?php print($data['creator']) ?>" />
								</td>
								<td>
									<input type="text" name="character_items|condition_" value="<?php print($data['condition_']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_condition_" value="<?php print($data['condition_']) ?>" />
								</td>
								<td>
									<input type="text" name="character_items|attuned" value="<?php print($data['attuned']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_attuned" value="<?php print($data['attuned']) ?>" />
								</td>
								<td>
									<input type="text" name="character_items|bag_id" value="<?php print($data['bag_id']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_bag_id" value="<?php print($data['bag_id']) ?>" />
								</td>
								<td>
									<input type="text" name="character_items|count" value="<?php print($data['count']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_count" value="<?php print($data['count']) ?>" />
								</td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="ciUpdate" value="Update" style="font-size:10px; width:60px" /><?php } ?></td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="ciDelete" value="Delete" style="font-size:10px; width:60px" /><?php } ?></td>
							</tr>
							</form>
							<?php
							}
							?>
						</table>
						</fieldset>
					</td>
				</tr>
			</table>
			<?php
			break;

		case "character_factions":
			global $charClass;
			$charClass->CharacterFactions($id);
			break;

		case "char_colors":
			$query=sprintf("select * from ". DEV_DB . ".char_colors where character_id = %d",$id);
			$result=$eq2->db->sql_query($query);
			$data=$eq2->db->sql_fetchrow($result);
			?>
			<table border="0" cellpadding="5">
				<tr>
					<td width="680" valign="top">
						<fieldset style="width:675px;"><legend>General</legend> 
						<table width="100%" border="0">
							<tr>
								<td colspan="6">
									<span class="heading">Editing: <?= $charName ?></span><br />
								</td>
							</tr>
							<tr>
								<td>id</td>
								<td>char_id</td>
								<td>signed_value</td>
								<td>type</td>
								<td>red</td>
								<td>green</td>
								<td>blue</td>
								<td colspan="2">&nbsp;</td>
							</tr>
							<?php
							$query=sprintf("select * from char_colors where char_id = %d",$id);
							$result=$eq2->db->sql_query($query);
							while($data=$eq2->db->sql_fetchrow($result)) {
							?>
							<form method="post" name="multiForm|<?php print($data['id']); ?>" />
							<tr>
								<td>
									<input type="text" name="char_colors|id" value="<?php print($data['id']) ?>"  style="width:50px;  background-color:#ddd;" readonly />
									<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
									<input type="hidden" name="orig_object" value="<?= $charName ?>" />
									<input type="hidden" name="table_name" value="char_colors" />
								</td>
								<td>
									<input type="text" name="char_colors|char_id" value="<?php print($data['char_id']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_char_id" value="<?php print($data['char_id']) ?>" />
								</td>
								<td>
									<input type="text" name="char_colors|signed_value" value="<?php print($data['signed_value']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_signed_value" value="<?php print($data['signed_value']) ?>" />
								</td>
								<td>
									<input type="text" name="char_colors|type" value="<?php print($data['type']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_type" value="<?php print($data['type']) ?>" />
								</td>
								<td>
									<input type="text" name="char_colors|red" value="<?php print($data['red']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_red" value="<?php print($data['red']) ?>" />
								</td>
								<td>
									<input type="text" name="char_colors|green" value="<?php print($data['green']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_green" value="<?php print($data['green']) ?>" />
								</td>
								<td>
									<input type="text" name="char_colors|blue" value="<?php print($data['blue']) ?>"  style="width:50px;" />
									<input type="hidden" name="orig_blue" value="<?php print($data['blue']) ?>" />
								</td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="ccUpdate" value="Update" style="font-size:10px; width:60px" /><?php } ?></td>
								<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="ccDelete" value="Delete" style="font-size:10px; width:60px" /><?php } ?></td>
							</tr>
							</form>
							<?php
							}
							?>
						</table>
						</fieldset>
					</td>
				</tr>
			</table>
			<?php
			break;

		case "character_details":
			$query=sprintf("select * from `".DEV_DB."`.character_details where char_id = %d",$id);
			$result=$eq2->db->sql_query($query);
			$data=$eq2->db->sql_fetchrow($result);
			?>
			<table border="0" cellpadding="5">
			<form method="post" name="CharEdit" />
				<tr>
					<td width="680" valign="top">
						<fieldset style="width:675px;"><legend>General</legend> 
						<table width="100%" border="0">
							<tr>
								<td colspan="6">
									<span class="heading">Editing: <?= $charName ?></span><br />
									<input type="hidden" name="orig_object" value="<?= $charName ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">character_id:</td>
								<td>
									<input type="text" name="character_details|character_id" value="<?php print($data['char_id']) ?>" style="width:50px" />
									<input type="hidden" name="orig_character_id" value="<?php print($data['char_id']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">hp:</td>
								<td>
									<input type="text" name="character_details|hp" value="<?php print($data['hp']) ?>" style="width:50px" />
									<input type="hidden" name="orig_hp" value="<?php print($data['hp']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">power:</td>
								<td>
									<input type="text" name="character_details|power" value="<?php print($data['power']) ?>" style="width:50px" />
									<input type="hidden" name="orig_power" value="<?php print($data['power']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">max_concentration:</td>
								<td>
									<input type="text" name="character_details|max_concentration" value="<?php print($data['max_concentration']) ?>" style="width:50px" />
									<input type="hidden" name="orig_max_concentration" value="<?php print($data['max_concentration']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">attack:</td>
								<td>
									<input type="text" name="character_details|attack" value="<?php print($data['attack']) ?>" style="width:50px" />
									<input type="hidden" name="orig_attack" value="<?php print($data['attack']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">mitigation:</td>
								<td>
									<input type="text" name="character_details|mitigation" value="<?php print($data['mitigation']) ?>" style="width:50px" />
									<input type="hidden" name="orig_mitigation" value="<?php print($data['mitigation']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">avoidance:</td>
								<td>
									<input type="text" name="character_details|avoidance" value="<?php print($data['avoidance']) ?>" style="width:50px" />
									<input type="hidden" name="orig_avoidance" value="<?php print($data['avoidance']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">parry:</td>
								<td>
									<input type="text" name="character_details|parry" value="<?php print($data['parry']) ?>" style="width:50px" />
									<input type="hidden" name="orig_parry" value="<?php print($data['parry']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">deflection:</td>
								<td>
									<input type="text" name="character_details|deflection" value="<?php print($data['deflection']) ?>" style="width:50px" />
									<input type="hidden" name="orig_deflection" value="<?php print($data['deflection']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">block:</td>
								<td>
									<input type="text" name="character_details|block" value="<?php print($data['block']) ?>" style="width:50px" />
									<input type="hidden" name="orig_block" value="<?php print($data['block']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">str:</td>
								<td>
									<input type="text" name="character_details|str" value="<?php print($data['str']) ?>" style="width:50px" />
									<input type="hidden" name="orig_str" value="<?php print($data['str']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">sta:</td>
								<td>
									<input type="text" name="character_details|sta" value="<?php print($data['sta']) ?>" style="width:50px" />
									<input type="hidden" name="orig_sta" value="<?php print($data['sta']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">agi:</td>
								<td>
									<input type="text" name="character_details|agi" value="<?php print($data['agi']) ?>" style="width:50px" />
									<input type="hidden" name="orig_agi" value="<?php print($data['agi']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">wis:</td>
								<td>
									<input type="text" name="character_details|wis" value="<?php print($data['wis']) ?>" style="width:50px" />
									<input type="hidden" name="orig_wis" value="<?php print($data['wis']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">intel:</td>
								<td>
									<input type="text" name="character_details|intel" value="<?php print($data['intel']) ?>" style="width:50px" />
									<input type="hidden" name="orig_intel" value="<?php print($data['intel']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">heat:</td>
								<td>
									<input type="text" name="character_details|heat" value="<?php print($data['heat']) ?>" style="width:50px" />
									<input type="hidden" name="orig_heat" value="<?php print($data['heat']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">cold:</td>
								<td>
									<input type="text" name="character_details|cold" value="<?php print($data['cold']) ?>" style="width:50px" />
									<input type="hidden" name="orig_cold" value="<?php print($data['cold']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">magic:</td>
								<td>
									<input type="text" name="character_details|magic" value="<?php print($data['magic']) ?>" style="width:50px" />
									<input type="hidden" name="orig_magic" value="<?php print($data['magic']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">mental:</td>
								<td>
									<input type="text" name="character_details|mental" value="<?php print($data['mental']) ?>" style="width:50px" />
									<input type="hidden" name="orig_mental" value="<?php print($data['mental']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">divine:</td>
								<td>
									<input type="text" name="character_details|divine" value="<?php print($data['divine']) ?>" style="width:50px" />
									<input type="hidden" name="orig_divine" value="<?php print($data['divine']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">disease:</td>
								<td>
									<input type="text" name="character_details|disease" value="<?php print($data['disease']) ?>" style="width:50px" />
									<input type="hidden" name="orig_disease" value="<?php print($data['disease']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">poison:</td>
								<td>
									<input type="text" name="character_details|poison" value="<?php print($data['poison']) ?>" style="width:50px" />
									<input type="hidden" name="orig_poison" value="<?php print($data['poison']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">coin_copper:</td>
								<td>
									<input type="text" name="character_details|coin_copper" value="<?php print($data['coin_copper']) ?>" style="width:50px" />
									<input type="hidden" name="orig_coin_copper" value="<?php print($data['coin_copper']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">coin_silver:</td>
								<td>
									<input type="text" name="character_details|coin_silver" value="<?php print($data['coin_silver']) ?>" style="width:50px" />
									<input type="hidden" name="orig_coin_silver" value="<?php print($data['coin_silver']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">coin_gold:</td>
								<td>
									<input type="text" name="character_details|coin_gold" value="<?php print($data['coin_gold']) ?>" style="width:50px" />
									<input type="hidden" name="orig_coin_gold" value="<?php print($data['coin_gold']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">coin_plat:</td>
								<td>
									<input type="text" name="character_details|coin_plat" value="<?php print($data['coin_plat']) ?>" style="width:50px" />
									<input type="hidden" name="orig_coin_plat" value="<?php print($data['coin_plat']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">pet_name:</td>
								<td>
									<input type="text" name="character_details|pet_name" value="<?php print($data['pet_name']) ?>" style="width:50px" />
									<input type="hidden" name="orig_pet_name" value="<?php print($data['pet_name']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">status_points:</td>
								<td>
									<input type="text" name="character_details|status_points" value="<?php print($data['status_points']) ?>" style="width:50px" />
									<input type="hidden" name="orig_status_points" value="<?php print($data['status_points']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">max_power:</td>
								<td>
									<input type="text" name="character_details|max_power" value="<?php print($data['max_power']) ?>" style="width:50px" />
									<input type="hidden" name="orig_max_power" value="<?php print($data['max_power']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">max_hp:</td>
								<td>
									<input type="text" name="character_details|max_hp" value="<?php print($data['max_hp']) ?>" style="width:50px" />
									<input type="hidden" name="orig_max_hp" value="<?php print($data['max_hp']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">xp:</td>
								<td>
									<input type="text" name="character_details|xp" value="<?php print($data['xp']) ?>" style="width:50px" />
									<input type="hidden" name="orig_xp" value="<?php print($data['xp']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">xp_needed:</td>
								<td>
									<input type="text" name="character_details|xp_needed" value="<?php print($data['xp_needed']) ?>" style="width:50px" />
									<input type="hidden" name="orig_xp_needed" value="<?php print($data['xp_needed']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">xp_debt:</td>
								<td>
									<input type="text" name="character_details|xp_debt" value="<?php print($data['xp_debt']) ?>" style="width:50px" />
									<input type="hidden" name="orig_xp_debt" value="<?php print($data['xp_debt']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">xp_vitality:</td>
								<td>
									<input type="text" name="character_details|xp_vitality" value="<?php print($data['xp_vitality']) ?>" style="width:50px" />
									<input type="hidden" name="orig_xp_vitality" value="<?php print($data['xp_vitality']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">bank_copper:</td>
								<td>
									<input type="text" name="character_details|bank_copper" value="<?php print($data['bank_copper']) ?>" style="width:50px" />
									<input type="hidden" name="orig_bank_copper" value="<?php print($data['bank_copper']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">bank_silver:</td>
								<td>
									<input type="text" name="character_details|bank_silver" value="<?php print($data['bank_silver']) ?>" style="width:50px" />
									<input type="hidden" name="orig_bank_silver" value="<?php print($data['bank_silver']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">bank_gold:</td>
								<td>
									<input type="text" name="character_details|bank_gold" value="<?php print($data['bank_gold']) ?>" style="width:50px" />
									<input type="hidden" name="orig_bank_gold" value="<?php print($data['bank_gold']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">bank_plat:</td>
								<td>
									<input type="text" name="character_details|bank_plat" value="<?php print($data['bank_plat']) ?>" style="width:50px" />
									<input type="hidden" name="orig_bank_plat" value="<?php print($data['bank_plat']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">bind_zone:</td>
								<td>
									<input type="text" name="character_details|bind_zone_id" value="<?php print($data['bind_zone_id']) ?>" style="width:50px" />
									<input type="hidden" name="orig_bind_zone_id" value="<?php print($data['bind_zone_id']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">bind_x:</td>
								<td>
									<input type="text" name="character_details|bind_x" value="<?php print($data['bind_x']) ?>" style="width:50px" />
									<input type="hidden" name="orig_bind_x" value="<?php print($data['bind_x']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">bind_y:</td>
								<td>
									<input type="text" name="character_details|bind_y" value="<?php print($data['bind_y']) ?>" style="width:50px" />
									<input type="hidden" name="orig_bind_y" value="<?php print($data['bind_y']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">bind_z:</td>
								<td>
									<input type="text" name="character_details|bind_z" value="<?php print($data['bind_z']) ?>" style="width:50px" />
									<input type="hidden" name="orig_bind_z" value="<?php print($data['bind_z']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">house_zone_id:</td>
								<td>
									<input type="text" name="character_details|house_zone_id" value="<?php print($data['house_zone_id']) ?>" style="width:50px" />
									<input type="hidden" name="orig_house_zone_id" value="<?php print($data['house_zone_id']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">combat_voice:</td>
								<td>
									<input type="text" name="character_details|combat_voice" value="<?php print($data['combat_voice']) ?>" style="width:50px" />
									<input type="hidden" name="orig_combat_voice" value="<?php print($data['combat_voice']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">emote_voice:</td>
								<td>
									<input type="text" name="character_details|emote_voice" value="<?php print($data['emote_voice']) ?>" style="width:50px" />
									<input type="hidden" name="orig_emote_voice" value="<?php print($data['emote_voice']) ?>" />
								</td>
							</tr>
						</table>
						</fieldset>
					</td>
				</tr>
				<?php if($eq2->CheckAccess(G_GUIDE)) { ?>
				<tr>
					<td colspan="2" align="center">
						<input type="submit" name="cdUpdate" value="Update" style="width:100px;" />&nbsp;					
					</td>
				</tr>
				<?php } ?>
			</form>
			</table>
			<?php
			break;
			
		case "characters":
		default:
			$query=sprintf("select * from `".DEV_DB."`.characters where id = %d",$id);
			$result=$eq2->db->sql_query($query);
			$data=$eq2->db->sql_fetchrow($result);
			?>
			<form method="post" name="CharEdit">
			<table border="0" cellpadding="5">
				<tr>
					<td width="680" valign="top">
						<fieldset style="height:350px; width:675px;"><legend>General</legend> 
						<table width="100%" border="0">
							<tr>
								<td colspan="6">
									<span class="heading">Editing: <?= $charName ?></span><br />
									<input type="hidden" name="orig_object" value="<?= $charName ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">id:</td>
								<td>
									<input type="text" name="characters|id" value="<?= $data['id'] ?>" readonly style="width:50px; background-color:#ddd;" />
									<input type="hidden" name="orig_id" value="<?= $data['id'] ?>" />
								</td>
								<td align="right">name:</td>
								<td colspan="3">
									<input type="text" name="characters|name" value="<?php print($data['name']); ?>" style="width:300px" />
									<input type="hidden" name="orig_name" value="<?= $data['name'] ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">account_id:</td>
								<td>
									<input type="text" name="characters|account_id" value="<?php print($data['account_id']); ?>" style="width:50px;" />
									<input type="hidden" name="orig_account_id" value="<?= $data['account_id'] ?>" />
								</td>
								<td align="right">admin_status:</td>
								<td>
									<input type="text" name="characters|admin_status" value="<?php print($data['admin_status']) ?>"<?php if( !$eq2->CheckAccess(G_SUPERADMIN) ) echo " disabled" ?> />
									<input type="hidden" name="orig_admin_status" value="<?= $data['admin_status'] ?>" />
								</td>
								<td align="right">deleted:</td>
								<td>
									<input type="checkbox" name="characters|deleted" value="1"<?php ( $data['deleted'] ) ? print(" checked") : "" ?> />
									<input type="hidden" name="orig_deleted" value="<?= $data['deleted'] ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">server_id:</td>
								<td>
									<input type="text" name="characters|server_id" value="<?php print($data['server_id']); ?>" style="width:50px" />
									<input type="hidden" name="orig_server_id" value="<?= $data['server_id'] ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">level:</td>
								<td>
									<input type="text" name="characters|level" value="<?php print($data['level']); ?>" style="width:50px" />
									<input type="hidden" name="orig_level" value="<?= $data['level'] ?>" />
								</td>
								<td align="right">class:</td>
								<td>
									<input type="text" name="characters|class" value="<?php print($data['class']); ?>" style="width:50px" />
									<input type="hidden" name="orig_class" value="<?= $data['class'] ?>" />
								</td>
								<td align="right">race:</td>
								<td>
									<input type="text" name="characters|race" value="<?php print($data['race']); ?>" style="width:50px" />
									<input type="hidden" name="orig_race" value="<?= $data['race'] ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">gender:</td>
								<td>
									<input type="text" name="characters|gender" value="<?php print($data['gender']); ?>" style="width:50px" />
									<input type="hidden" name="orig_gender" value="<?= $data['gender'] ?>" />
								</td>
								<td align="right">body_size:</td>
								<td>
									<input type="text" name="characters|body_size" value="<?php print($data['body_size']); ?>" style="width:50px" />
									<input type="hidden" name="orig_body_size" value="<?= $data['body_size'] ?>" />
								</td>
								<td align="right">body_age:</td>
								<td>
									<input type="text" name="characters|body_age" value="<?php print($data['body_age']); ?>" style="width:50px" />
									<input type="hidden" name="orig_body_age" value="<?= $data['body_age'] ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">deity:</td>
								<td>
									<input type="text" name="characters|deity" value="<?php print($data['deity']); ?>" style="width:50px" />
									<input type="hidden" name="orig_deity" value="<?= $data['deity'] ?>" />
								</td>
								<td align="right">current_zone_id:</td>
								<td colspan="3">
									<select name="characters|current_zone_id" style="width:350px">
										<?php echo (new eq2Zones)->getZoneOptionsByID($data['current_zone_id']) ?>
									</select>
									<input type="hidden" name="orig_current_zone" value="<?= $data['current_zone_id'] ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">x:</td>
								<td>
									<input type="text" name="characters|x" value="<?php print($data['x']) ?>" style="width:50px" />
									<input type="hidden" name="orig_x" value="<?php print($data['x']) ?>" />
								</td>
								<td align="right">y:</td>
								<td>
									<input type="text" name="characters|y" value="<?php print($data['y']) ?>" style="width:50px" />
									<input type="hidden" name="orig_y" value="<?= print($data['y']) ?>" />
								</td>
								<td align="right">z:</td>
								<td>
									<input type="text" name="characters|z" value="<?php print($data['z']) ?>" style="width:50px" />
									<input type="hidden" name="orig_z" value="<?= print($data['z']) ?>" />
								</td>
							</tr>
							<tr>
							<td align="right">heading:</td>
								<td>
									<input type="text" name="characters|heading" value="<?php print($data['heading']) ?>" style="width:50px" />
									<input type="hidden" name="orig_heading" value="<?= $data['heading'] ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">unix_timestamp:</td>
								<td>
									<input type="text" name="characters|unix_timestamp" value="<?php print($data['unix_timestamp']) ?>" style="width:80px" />
									<input type="hidden" name="orig_unix_timestamp" value="<?= print($data['unix_timestamp']) ?>" />
								</td>
								<td align="right">created_date:</td>
								<td>
									<input type="text" name="characters|created_date" value="<?php print($data['created_date']) ?>" style="width:120px" />
									<input type="hidden" name="orig_created_date" value="<?= print($data['created_date']) ?>" />
								</td>
								<td align="right">last_played:</td>
								<td>
									<input type="text" name="characters|last_played" value="<?php print($data['last_played']) ?>" style="width:120px" />
									<input type="hidden" name="orig_last_played" value="<?= print($data['last_played']) ?>" />
								</td>
							</tr>
						</table>
						</fieldset>
					</td>
					<td valign="top">
						<fieldset style="height:350px; width:180px;"><legend>Appearance</legend> 
						<table>
							<tr>
								<td>soga_wing_type:</td>
								<td>
									<input type="text" name="characters|soga_wing_type" value="<?php print($data['soga_wing_type']) ?>" style="width:50px" />
									<input type="hidden" name="orig_soga_wing_type" value="<?php print($data['soga_wing_type']) ?>" />
								</td>
							</tr>
							<tr>
								<td>soga_chest_type:</td>
								<td>
									<input type="text" name="characters|soga_chest_type" value="<?php print($data['soga_chest_type']) ?>" style="width:50px" />
									<input type="hidden" name="orig_soga_chest_type" value="<?php print($data['soga_chest_type']) ?>" />
								</td>
							</tr>
							<tr>
								<td>soga_legs_type:</td>
								<td>
									<input type="text" name="characters|soga_legs_type" value="<?php print($data['soga_legs_type']) ?>" style="width:50px" />
									<input type="hidden" name="orig_soga_legs_type" value="<?php print($data['soga_legs_type']) ?>" />
								</td>
							</tr>
							<tr>
								<td>soga_hair_type:</td>
								<td>
									<input type="text" name="characters|soga_hair_type" value="<?php print($data['soga_hair_type']) ?>" style="width:50px" />
									<input type="hidden" name="orig_soga_hair_type" value="<?php print($data['soga_hair_type']) ?>" />
								</td>
							</tr>
							<tr>
								<td>soga_model_type:</td>
								<td>
									<input type="text" name="characters|soga_model_type" value="<?php print($data['soga_model_type']) ?>" style="width:50px" />
									<input type="hidden" name="orig_soga_model_type" value="<?php print($data['soga_model_type']) ?>" />
								</td>
							</tr>
							<tr>
								<td>legs_type:</td>
								<td>
									<input type="text" name="characters|legs_type" value="<?php print($data['legs_type']) ?>" style="width:50px" />
									<input type="hidden" name="orig_legs_type" value="<?php print($data['legs_type']) ?>" />
								</td>
							</tr>
							<tr>
								<td>chest_type:</td>
								<td>
									<input type="text" name="characters|chest_type" value="<?php print($data['chest_type']) ?>" style="width:50px" />
									<input type="hidden" name="orig_chest_type" value="<?php print($data['chest_type']) ?>" />
								</td>
							</tr>
							<tr>
								<td>wing_type:</td>
								<td>
									<input type="text" name="characters|wing_type" value="<?php print($data['wing_type']) ?>" style="width:50px" />
									<input type="hidden" name="orig_wing_type" value="<?php print($data['wing_type']) ?>" />
								</td>
							</tr>
							<tr>
								<td>hair_type:</td>
								<td>
									<input type="text" name="characters|hair_type" value="<?php print($data['hair_type']) ?>" style="width:50px" />
									<input type="hidden" name="orig_hair_type" value="<?php print($data['hair_type']) ?>" />
								</td>
							</tr>
						</table>
						</fieldset>
					</td>
				</tr>
				<?php if($eq2->CheckAccess(G_DEVELOPER) ) { ?>
				<tr>
					<td colspan="2" align="center">
						<input type="submit" name="cmd" value="Update" style="width:100px;" />&nbsp;
						<input type="hidden" name="table_name" value="characters" />
						<input type="button" value="Help" style="width:100px" onclick="javascript:window.open('help.php#spawns','help','resizable,width=480,height=640,left=10,top=75,scrollbars=yes');" />						
					</td>
				</tr>
				<?php } ?>
				<tr>
					<td colspan="2">
						<p>
						<strong>Note:</strong> Due to the number of race options, I have chosen to not to list them in a combo box at this time because it would slow the page performance tremendously.<br />
						Please keep a race and model_type reference handy.
						</p>
					</td>
				</tr>
			</table>
			</form>
			<?php
	}	
}


function character_mail($id) {
	global $eq2,$objectName,$link;

	$table= ".character_mail";
	$query=sprintf("select * from %s where player_to_id = %d",$table,$id);
	$result=$eq2->db->sql_query($query);
	if($eq2->db->sql_numrows($result) > 0) {
		$data=$eq2->db->sql_fetchrow($result);
?>
		<table border="0" cellpadding="5">
		<form method="post" name="Form1" />
			<tr>
				<td width="880" valign="top">
					<fieldset><legend>General</legend>
					<table width="100%" cellpadding="0" border="0">
						<tr>

							<td colspan="3">
								<span class="heading">Editing: <?= $objectName ?></span><br />&nbsp;
							</td>
						</tr>
						<tr>
							<td align="right">id:</td>
							<td>
								<input type="text" name="character_mail|id" value="<?php print($data['id']) ?>" style="width:45px;  background-color:#ddd;" readonly />
								<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
							</td>
						</tr>
						<tr>
							<td align="right">player_to_id:</td>
							<td>
								<input type="text" name="character_mail|player_to_id" value="<?php print($data['player_to_id']) ?>" style="width:45px;" />
								<input type="hidden" name="orig_player_to_id" value="<?php print($data['player_to_id']) ?>" />

							</td>
						</tr>
						<tr>
							<td align="right">player_from:</td>
							<td>
								<input type="text" name="character_mail|player_from" value="<?php print($data['player_from']) ?>" style="width:150px;" />
								<input type="hidden" name="orig_player_from" value="<?php print($data['player_from']) ?>" />
							</td>

						</tr>
						<tr>
							<td align="right">subject:</td>
							<td>
								<input type="text" name="character_mail|subject" value="<?php print($data['subject']) ?>" style="width:200px;" />
								<input type="hidden" name="orig_subject" value="<?php print($data['subject']) ?>" />
							</td>
						</tr>

						<tr>
							<td align="right">mail_body:</td>
							<td>
								<textarea name="character_mail|mail_body" style="width:600px; height:75px;"><?php print($data['mail_body']) ?></textarea>
								<input type="hidden" name="orig_mail_body" value="<?php print($data['mail_body']) ?>" />
							</td>
						</tr>
						<tr>

							<td align="right">already_read:</td>
							<td>
								<input type="text" name="character_mail|already_read" value="<?php print($data['already_read']) ?>" style="width:45px;" />
								<input type="hidden" name="orig_already_read" value="<?php print($data['already_read']) ?>" />
							</td>
						</tr>
						<tr>
							<td align="right">mail_type:</td>

							<td>
								<input type="text" name="character_mail|mail_type" value="<?php print($data['mail_type']) ?>" style="width:45px;" />
								<input type="hidden" name="orig_mail_type" value="<?php print($data['mail_type']) ?>" />
							</td>
						</tr>
						<tr>
							<td align="right">coin_copper:</td>
							<td>

								<input type="text" name="character_mail|coin_copper" value="<?php print($data['coin_copper']) ?>" style="width:45px;" />
								<input type="hidden" name="orig_coin_copper" value="<?php print($data['coin_copper']) ?>" />
							</td>
						</tr>
						<tr>
							<td align="right">coin_silver:</td>
							<td>
								<input type="text" name="character_mail|coin_silver" value="<?php print($data['coin_silver']) ?>" style="width:45px;" />

								<input type="hidden" name="orig_coin_silver" value="<?php print($data['coin_silver']) ?>" />
							</td>
						</tr>
						<tr>
							<td align="right">coin_gold:</td>
							<td>
								<input type="text" name="character_mail|coin_gold" value="<?php print($data['coin_gold']) ?>" style="width:45px;" />
								<input type="hidden" name="orig_coin_gold" value="<?php print($data['coin_gold']) ?>" />

							</td>
						</tr>
						<tr>
							<td align="right">coin_plat:</td>
							<td>
								<input type="text" name="character_mail|coin_plat" value="<?php print($data['coin_plat']) ?>" style="width:45px;" />
								<input type="hidden" name="orig_coin_plat" value="<?php print($data['coin_plat']) ?>" />
							</td>

						</tr>
						<tr>
							<td align="right">stack:</td>
							<td>
								<input type="text" name="character_mail|stack" value="<?php print($data['stack']) ?>" style="width:45px;" />
								<input type="hidden" name="orig_stack" value="<?php print($data['stack']) ?>" />
							</td>
						</tr>

						<tr>
							<td align="right">postage_cost:</td>
							<td>
								<input type="text" name="character_mail|postage_cost" value="<?php print($data['postage_cost']) ?>" style="width:45px;" />
								<input type="hidden" name="orig_postage_cost" value="<?php print($data['postage_cost']) ?>" />
							</td>
						</tr>
						<tr>

							<td align="right">attachment_cost:</td>
							<td>
								<input type="text" name="character_mail|attachment_cost" value="<?php print($data['attachment_cost']) ?>" style="width:45px;" />
								<input type="hidden" name="orig_attachment_cost" value="<?php print($data['attachment_cost']) ?>" />
							</td>
						</tr>
						<tr>
							<td align="right">char_item_id:</td>

							<td>
								<input type="text" name="character_mail|char_item_id" value="<?php print($data['char_item_id']) ?>" style="width:45px;" />
								<input type="hidden" name="orig_char_item_id" value="<?php print($data['char_item_id']) ?>" />
							</td>
						</tr>
						<tr>
							<td align="right">time_sent:</td>
							<td>

								<input type="text" name="character_mail|time_sent" value="<?php print($data['time_sent']) ?>" style="width:75px;" />&nbsp;<?php print(date("Y/m/d h:n:s", $data['time_sent'])) ?>
								<input type="hidden" name="orig_time_sent" value="<?php print($data['time_sent']) ?>" />
							</td>
						</tr>
						<tr>
							<td align="right">expire_time:</td>
							<td>
								<input type="text" name="character_mail|expire_time" value="<?php print($data['expire_time']) ?>" style="width:75px;" />&nbsp;<?php print(date("Y/m/d h:n:s", $data['expire_time'])) ?>
								<input type="hidden" name="orig_expire_time" value="<?php print($data['expire_time']) ?>" />
							</td>
						</tr>
					</table>
					</fieldset>
				</td>
			</tr>
			<?php if($eq2->CheckAccess(G_DEVELOPER)) { ?>
			<tr>

				<td colspan="4" align="center">
					<input type="submit" name="iUpdate" value="Update" style="width:100px;" />&nbsp;
					<input type="button" value="Help" style="width:100px" onclick="javascript:window.open('help.php#items','help','resizable,width=480,height=640,left=10,top=75,scrollbars=yes');" />
					<input type="hidden" name="cmd" value="update" />
					<input type="hidden" name="orig_object" value="<?= $objectName ?>" />
					<input type="hidden" name="table_name" value="<?= $table ?>" />
				</td>
			</tr>
			<?php } ?>

		</table>
		<?php
		} else {
		if( $eq2->CheckAccess(G_DEVELOPER) ) { ?>
		<table border="0" cellpadding="5">
		<form method="post" name="Form1|new" />
			<tr>
				<td width="680" valign="top">
					<fieldset><legend>General</legend>
					<table width="100%" cellpadding="0" border="1">
						<tr>

							<td colspan="4">
								<span class="heading">Editing: <?= $objectName ?></span><br />&nbsp;
							</td>
						</tr>
						<tr>
							<td colspan="4">No data found for this item. You may insert a new record if necessary.</td>
						</tr>
						<tr>

							<td align="right">id:</td>
							<td>
								<input type="text" name="character_mail|id|new" value="0" style="width:45px;  background-color:#ddd;" readonly />
							</td>
						</tr>
						<tr>
							<td align="right">player_to_id:</td>
							<td>

								<input type="text" name="character_mail|player_to_id|new" value="0" style="width:45px;" />
							</td>
						</tr>
						<tr>
							<td align="right">player_from:</td>
							<td>
								<input type="text" name="character_mail|player_from|new" value="0" style="width:45px;" />
							</td>

						</tr>
						<tr>
							<td align="right">subject:</td>
							<td>
								<input type="text" name="character_mail|subject|new" value="0" style="width:45px;" />
							</td>
						</tr>
						<tr>

							<td align="right">mail_body:</td>
							<td>
								<input type="text" name="character_mail|mail_body|new" value="0" style="width:45px;" />
							</td>
						</tr>
						<tr>
							<td align="right">already_read:</td>
							<td>

								<input type="text" name="character_mail|already_read|new" value="0" style="width:45px;" />
							</td>
						</tr>
						<tr>
							<td align="right">mail_type:</td>
							<td>
								<input type="text" name="character_mail|mail_type|new" value="0" style="width:45px;" />
							</td>

						</tr>
						<tr>
							<td align="right">coin_copper:</td>
							<td>
								<input type="text" name="character_mail|coin_copper|new" value="0" style="width:45px;" />
							</td>
						</tr>
						<tr>

							<td align="right">coin_silver:</td>
							<td>
								<input type="text" name="character_mail|coin_silver|new" value="0" style="width:45px;" />
							</td>
						</tr>
						<tr>
							<td align="right">coin_gold:</td>
							<td>

								<input type="text" name="character_mail|coin_gold|new" value="0" style="width:45px;" />
							</td>
						</tr>
						<tr>
							<td align="right">coin_plat:</td>
							<td>
								<input type="text" name="character_mail|coin_plat|new" value="0" style="width:45px;" />
							</td>

						</tr>
						<tr>
							<td align="right">stack:</td>
							<td>
								<input type="text" name="character_mail|stack|new" value="0" style="width:45px;" />
							</td>
						</tr>
						<tr>

							<td align="right">postage_cost:</td>
							<td>
								<input type="text" name="character_mail|postage_cost|new" value="0" style="width:45px;" />
							</td>
						</tr>
						<tr>
							<td align="right">attachment_cost:</td>
							<td>

								<input type="text" name="character_mail|attachment_cost|new" value="0" style="width:45px;" />
							</td>
						</tr>
						<tr>
							<td align="right">char_item_id:</td>
							<td>
								<input type="text" name="character_mail|char_item_id|new" value="0" style="width:45px;" />
							</td>

						</tr>
						<tr>
							<td align="right">time_sent:</td>
							<td>
								<input type="text" name="character_mail|time_sent|new" value="0" style="width:45px;" />
							</td>
						</tr>
						<tr>

							<td align="right">expire_time:</td>
							<td>
								<input type="text" name="character_mail|expire_time|new" value="0" style="width:45px;" />
							</td>
						</tr>
					</table>
					</fieldset>
				</td>

			</tr>
			<tr>
				<td colspan="4" align="center">
					<input type="submit" name="iInsert" value="Insert" style="width:100px;" />&nbsp;
					<input type="button" value="Help" style="width:100px" onclick="javascript:window.open('help.php#items','help','resizable,width=480,height=640,left=10,top=75,scrollbars=yes');" />
					<input type="hidden" name="cmd" value="insert" />
					<input type="hidden" name="orig_object" value="<?= $objectName ?>" />
					<input type="hidden" name="table_name" value="<?= $table ?>" />
				</td>

			</tr>
		</table>
		<?php
		}
	}
}


function character_buyback($id) {
	global $eq2,$objectName,$link;

	$table= ".character_buyback";
?>
	<table border="0" cellpadding="5">
		<tr>
			<td width="680" valign="top">
				<fieldset><legend>General</legend>
				<table width="100%" cellpadding="0" border="0">
					<tr>
						<td colspan="3">

							<span class="heading">Editing: <?= $objectName ?></span><br />&nbsp;
						</td>
					</tr>
					<tr>
						<td width="55">id</td>
						<td width="75">char_id</td>
						<td width="75">item_id</td>

						<td width="55">quantity</td>
						<td width="105">price</td>
						<td colspan="2">&nbsp;</td>
					</tr>

						<?php
						$query=sprintf("select * from %s where char_id = %s",$table, $id);
						$result=$eq2->db->sql_query($query);
						while($data=$eq2->db->sql_fetchrow($result)) {
						?>
					<form method="post" name="multiForm|<?php print($data['id']); ?>" />
					<tr>
						<td>

							<input type="text" name="character_buyback|id" value="<?php print($data['id']) ?>" style="width:45px;  background-color:#ddd;" readonly />
							<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
						</td>
						<td>
							<input type="text" name="character_buyback|char_id" value="<?php print($data['char_id']) ?>" style="width:70px;" />
							<input type="hidden" name="orig_char_id" value="<?php print($data['char_id']) ?>" />
						</td>
						<td>
							<input type="text" name="character_buyback|item_id" value="<?php print($data['item_id']) ?>" style="width:70px;" />

							<input type="hidden" name="orig_item_id" value="<?php print($data['item_id']) ?>" />
						</td>
						<td>
							<input type="text" name="character_buyback|quantity" value="<?php print($data['quantity']) ?>" style="width:45px;" />
							<input type="hidden" name="orig_quantity" value="<?php print($data['quantity']) ?>" />
						</td>
						<td>
							<input type="text" name="character_buyback|price" value="<?php print($data['price']) ?>" style="width:100px;" />
							<input type="hidden" name="orig_price" value="<?php print($data['price']) ?>" />

						</td>
						<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" /><?php } ?></td>
						<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" /><?php } ?></td>
					</tr>
					<input type="hidden" name="objectName" value="<?= $objectName ?>" />
					<input type="hidden" name="table_name" value="<?= $table ?>" />
					</form>
				<?php
				}
				?>

				<?php if($eq2->CheckAccess(G_DEVELOPER)) { ?>
					<form method="post" name="sdForm|new" />
					<tr>
						<td align="center"><strong>new</strong></td>
						<td>
							<input type="text" name="character_buyback|char_id|new" value="" style="width:70px;" />
						</td>
						<td>
							<input type="text" name="character_buyback|item_id|new" value="" style="width:70px;" />

						</td>
						<td>
							<input type="text" name="character_buyback|quantity|new" value="" style="width:45px;" />
						</td>
						<td>
							<input type="text" name="character_buyback|price|new" value="" style="width:100px;" />
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

include("footer.php");

?>
