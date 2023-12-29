<?php
define('IN_EDITOR', true);
include("header_short.php");
$type = ( isset( $_GET['func'] ) ) ? $_GET['func'] : "";
if( isset($type) )
{
	$strHTML = "";
	switch($type)
	{
		case "model":
			$showResults = ( strlen($_POST['luModel'] ?? "") >= 3 ) ? true : false;
			$strHTML = "\n";
			$strHTML .= "<table width='640' border='1' cellspacing='0' cellpadding='5'>\n";
			$strHTML .= "  <form method='post'>\n";
			$strHTML .= "    <tr>\n";
			$strHTML .= "      <td align='center' valign='top'><strong>Lookup Model Type</strong></td>\n";
			$strHTML .= "    </tr>\n";
			$strHTML .= "    <tr>\n";
			$strHTML .= "      <td align='center'>\n";
			$strHTML .= "        This will search Category, Sub-Category and Model Name values for what you enter here<br>(use at least 3 chars min):<br>\n";
			$strHTML .= "        <input type='text' name='luModel' value='" . $_POST['luModel'] . ">&nbsp;<input type='submit' name='cmd' value='Search'>\n";
			$strHTML .= "      </td>\n";
			$strHTML .= "    </tr>\n";
			$strHTML .= "  </form>\n";
			$strHTML .= "</table>\n";
			if( $showResults ) 
			{ 
				$strHTML .= "<table width='640' border='1' cellspacing='0'>\n";
				/*
				<!--<tr>
					<th>category</th>
					<th>subcategory</th>
					<th>model_type</th>
					<th>model_name</th>
				</tr>-->
				*/
				$strHTML .= "  <tr>\n";
				$strHTML .= "    <th>id</th>\n";
				$strHTML .= "    <th>appearance</th>\n";
				$strHTML .= "    <th>min_client</th>\n";
				$strHTML .= "  </tr>\n";

				//$eq2->SQLQuery = "SELECT * FROM eq2models WHERE (category RLIKE '".$_POST['luModel']."') OR (subcategory RLIKE '".$_POST['luModel']."') OR (model_name RLIKE '".$_POST['luModel']."');"; 
				$eq2->SQLQuery = "SELECT * FROM `" . DEV_DB . "`.appearances WHERE (name RLIKE '" . $_POST['luModel'] . "') ORDER BY appearance_id"; 
				$results = $eq2->RunQueryMulti();
				foreach($results as $data) 
				{
					$strHTML .= "  <tr>\n";
					$strHTML .= "  <td>" . $data['appearance_id'] . "&nbsp;</td>\n";
					$strHTML .= "  <td>" . $data['name'] . "&nbsp;</td>\n";
					$strHTML .= "  <td>" . $data['min_client_version']  . "&nbsp;</td>\n";
					$strHTML .= "  </tr>\n";
				} 
				$strHTML .= "</table>\n";
			} 
			break;

		case "visual":
			$showResults = ( strlen($_POST['luVisualState']) >= 3 ) ? true : false;

			$strHTML .= "<table width='640' border='1' cellspacing='0' cellpadding='5'>\n";
			$strHTML .= "  <form method='post'>\n";
			$strHTML .= "    <tr>\n";
			$strHTML .= "      <td align='center' valign='top'><strong>Lookup Visual Effects</strong></td>\n";
			$strHTML .= "    </tr>\n";
			$strHTML .= "    <tr>\n";
			$strHTML .= "      <td align='center'>Enter at least 3 characters and click Search:&nbsp;\n";
			$strHTML .= "        <input type='text' name='luVisualState' value='" . $_POST['luVisualState'] . "'>&nbsp;<input type='submit' name='cmd' value='Search'>\n";
			$strHTML .= "      </td>\n";
			$strHTML .= "    </tr>\n";
			$strHTML .= "  </form>\n";
			$strHTML .= "</table>\n";
			if( $showResults ) 
			{ 
				$strHTML .= "<table width='640' border='1' cellspacing='0'>\n";
				$strHTML .= "  <tr>\n";
				$strHTML .= "    <th>id</th>\n";
				$strHTML .= "    <th>name</th>\n";
				$strHTML .= "  </tr>\n";

				$eq2->SQLQuery = "select * from `".DEV_DB."`.visual_states where name rlike '".$_POST['luVisualState']."';";
				$results = $eq2->RunQueryMulti();
				foreach($results as $data) 
				{
					$strHTML .= "  <tr>\n";
					$strHTML .= "    <td>" . $data['visual_state_id'] . "&nbsp;</td>\n";
					$strHTML .= "    <td>" . $data['name'] . "&nbsp;</td>\n";
					$strHTML .= "  </tr>\n";
				} 
				$strHTML .= "</table>\n";
			} 
			break;
		
		case "clone":
			if( isset($_POST['cmd']) && $_POST['cmd'] == "Clone Spawn" ) {
				clone_spawn();
				break;
			}

			$strHTML .= "<table width='100%' border='1' cellspacing='0' cellpadding='5' align='center'>\n";
			$strHTML .= "  <form method='post'>\n";
			$strHTML .= "    <tr>\n";
			$strHTML .= "      <td align='center' valign='top' colspan='2'><strong>Cloning: " . $eq2->getSpawnNameByID($_GET['id']) . "</strong></td>\n";
			$strHTML .= "    </tr>\n";
			$strHTML .= "    <tr>\n";
			$strHTML .= "      <td align='right'>Destination Zone:&nbsp;</td>\n";
			$strHTML .= "      <td>\n";
			$strHTML .= "        <select name='zone_id' onChange='dosub(this.options[this.selectedIndex].value)'>\n";

			$new_query_string = sprintf("id=%lu&func=clone&type=%s",$_GET['id'], $_GET['type']);
			$result= $eq2->RunQueryMulti("select id,`description` from `".DEV_DB."`.zones order by `description`, `id`");
			foreach ($result as $row) 
			{
				$selected = ( $_GET['zone'] == $row['id'] ) ? " selected" : "";
				$optText = sprintf("%s (%s)", $row['description'], $row['id']);
				$strHTML .= "          <option value=\"spawn_func.php?zone=" . $row['id'] . "&" . $new_query_string . "\" $selected>" . $optText . "</option>\n";
			}
			$strHTML .= "        </select>\n";
			$strHTML .= "      </td>\n";
			$strHTML .= "    </tr>\n";
			$strHTML .= "    <tr>\n";
			$strHTML .= "      <td align='right'>Suggested spawn ID:&nbsp;</td>\n";
			$strHTML .= "      <td>&nbsp;\n";

			$query = sprintf("select max(id)+1 as next_id from `%s`.spawn where id like '%u____'", DEV_DB, $_GET['zone']);
			$data = $eq2->RunQuerySingle($query);
			$next_spawn_id = ( isset($data['next_id']) ) ? $data['next_id'] : sprintf("%d0000", $_GET['id']);

			$strHTML .= "        <input type='text' name='new_spawn_id' value='" . $next_spawn_id . "' />\n";
			$strHTML .= "      </td>\n";
			$strHTML .= "    </tr>\n";
			$strHTML .= "    <tr>\n";
			$strHTML .= "      <td colspan='2'>\n";
			$strHTML .= "This process will create a new spawn ID record based on the spawn selected in the zone you specify.<br />\n";
			$strHTML .= "<strong>This does NOT duplicate the spawn location!</strong> You have to go into the zone, /spawn {new_id}, and /spawn add new to make the spawn permanent.<br />\n";
			$strHTML .= "<br>\n";
			$strHTML .= "Click &quot;Clone Spawn&quot; to complete this task, or &quot;Close Window&quot; to abort.\n";
			$strHTML .= "      </td>\n";
			$strHTML .= "    </tr>\n";
			$strHTML .= "    <tr>\n";
			$strHTML .= "      <td colspan='2' align='center'>\n";
			$strHTML .= "        <input type='submit' name='cmd' value='Clone Spawn' />&nbsp;\n";
			$strHTML .= "        <input type='button' value='Close Window' onClick='javascript:window.close()' />\n";
			$strHTML .= "        <input type='hidden' name='orig_id' value='" . $_GET['id'] . "' />\n";
			$strHTML .= "      </td>\n";
			$strHTML .= "    </tr>\n";
			$strHTML .= "  </form>\n";
			$strHTML .= "</table>\n";
			break;
	}
	print($strHTML);
}
// direct access? I don't think so.
include("footer.php");
exit;

/* Functions */

function clone_spawn()
{
	global $eq2;
	
	$success = false;
	if( isset($_GET['type']) && isset($_POST['orig_id']) && isset($_POST['new_spawn_id']) )
	{
		// clone parent record
		$eq2->BeginSQLTransaction();

		$query = $eq2->GetRowCloneQuery(DEV_DB, "spawn", "id", $_POST['orig_id'], $_POST['new_spawn_id']);
		if ($success = $eq2->RunQuery(true, $query) == 1)
		{
			$type = $_GET['type'];
			$type_tbl = "spawn_".$type;
			$success = $eq2->RunQuery(true, $eq2->GetRowCloneQuery(DEV_DB, $type_tbl, "spawn_id", $_POST['orig_id'], $_POST['new_spawn_id'], "'id'")) == 1;
			if ($success && $type == "npcs") {
				$eq2->RunQuery(true, $eq2->GetRowCloneQuery(DEV_DB, "npc_appearance", "spawn_id", $_POST['orig_id'], $_POST['new_spawn_id'], "'id'"));
				$success = $eq2->LastSQLError() == null;

				$success = $success && ($eq2->RunQuery(true, $eq2->GetRowCloneQuery(DEV_DB, "npc_appearance_equip", "spawn_id", $_POST['orig_id'], $_POST['new_spawn_id'], "'id'")) >= 1 || $eq2->LastSQLError() == null);
			}
		}

		if (!$success)
		{
			$eq2->SQLTransactionRollback();
		}else{
			 $eq2->SQLTransactionCommit();
		}
	}

	if ($success) {	
		$strHTML .= "Spawn Cloned Successfully!\n";
		$strHTML .= "<br><input type='button' value='Close Window' onclick=\"window.close();\" />\n";
		$strHTML .= "<a href='spawns.php?type=" . $type . "&id=" . $_POST['new_spawn_id'] . "&zone=" . $_GET['zone'] . "'>Link To Spawn</a>\n";
	}else {
		$strHTML .= "Could not clone spawn! <br><input type='button' value='Close Window' onclick=\"javascript:window.close();\" />\n";
	}
	print($strHTML);
}
?>
