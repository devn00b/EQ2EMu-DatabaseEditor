<?php

class eq2Characters
{
    public function CharacterFactions($id) {
        global $eq2, $charName;
        $query=sprintf("select * from ".DEV_DB.".character_factions where character_id = %d",$id);
		$result=$eq2->db->sql_query($query);
		$data=$eq2->db->sql_fetchrow($result);
		?>
		<table border="0" cellpadding="5">
			<tr>
				<td valign="top">
					<fieldset style="width:550px"><legend>General</legend> 
					<table width="100%" border="0">
						<tr>
							<td colspan="6">
								<span class="heading">Editing: <?= $charName ?></span><br />
							</td>
						</tr>
						<tr align="center">
							<td>char_id</td>
							<td>name</td>
							<td>faction_id</td>
							<td>faction_level</td>
							<td colspan="2">&nbsp;</td>
						</tr>

						<?php
						$query=sprintf("select cf.*, f.`name` as faction_name from ".DEV_DB.".character_factions cf INNER JOIN ".DEV_DB.".factions f ON f.id = cf.faction_id WHERE cf.char_id = %d",$id);
						$result = $eq2->RunQueryMulti($query);
						foreach ($result as $data) : ?>
						<form method="post" name="multiForm|<?php print($data['id']); ?>">
						<tr align="center">
							<td>
								<?php echo $data['char_id'] ?>
								<input type="hidden" name="orig_char_id" value="<?php print($data['char_id']) ?>" />
								<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
								<input type="hidden" name="orig_object" value="<?= $charName ?>" />
								<input type="hidden" name="table_name" value="character_factions" />
							</td>
							<td>
							<?php echo $data['faction_name'] ?>
							</td>
							<td>
								<input type="text" name="character_factions|faction_id" value="<?php print($data['faction_id']) ?>"  style="width:50px;" />
								<input type="hidden" name="orig_faction_id" value="<?php print($data['faction_id']) ?>" />
							</td>
							<td>
								<input type="text" name="character_factions|faction_level" value="<?php print($data['faction_level']) ?>"  style="width:50px;" />
								<input type="hidden" name="orig_faction_level" value="<?php print($data['faction_level']) ?>" />
							</td>
							<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" /><?php } ?></td>
							<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" /><?php } ?></td>
						</tr>
						</form>
						<?php endforeach; ?>
						<?php if ($eq2->CheckAccess(G_DEVELOPER)) : ?>
						<form method="post" name="newFactionRow">
						<tr align="center">
							<td>
								<?php echo $id ?>
								<input type="hidden" name="character_factions|char_id|new" value="<?php echo $id ?>"  style="width:50px;" />
								<input type="hidden" name="table_name" value="character_factions" />
							</td>
							<td></td>
							<td>
								<input type="text" name="character_factions|faction_id|new" value=""  style="width:50px;" />
							</td>
							<td>
								<input type="text" name="character_factions|faction_level|new" value=""  style="width:50px;" />
							</td>
							<td colspan="2">
								<input type="submit" name="cmd" value="Insert" style="font-size:10px; width:60px" />
							</td>
						</tr>
						<?php endif; ?>
					</table>
					</fieldset>
				</td>
			</tr>
		</table>
        <?php
    }
}

?>