<?php
define('IN_EDITOR', true);
include("header_short.php");

$Page = ( isset($_GET['page']) ) ? $_GET['page'] : "";
$Type = ( isset($_GET['type']) ) ? $_GET['type'] : "none";

if( isset($Page) && isset($Type) )
{
	switch($Page)
	{
		case "spells":
			
			switch($Type)
			{
				case "data":
					ParseSpellData();
					break;

				default:
					break;
			}
			break;
			
		case "proximity": // Migrate Zone - to find spawns in area
			FindProximitySpawns();
			break;
			
		default:
			break;
	}
}
else
{
	die("No Page/Type Set; aborting.");
}
include("footer.php");
exit;


function FindProximitySpawns()
{
	global $eq2;

	include("../class/eq2.spawns.php");
	$s = new eq2Spawns();

	switch($_POST['cmd'])
	{
		case "Spawn These":
			$s->ProcessMigrateProximitySpawns();
			break;
	}
	
	$distance_offset = ( isset($_GET['distance']) ) ? $_GET['distance'] : 5;
	
	$x_low = round($_GET['x']) - $distance_offset;
	$x_high = round($_GET['x']) + $distance_offset;
	$z_low = round($_GET['z']) - $distance_offset;
	$z_high = round($_GET['z']) + $distance_offset;

	$eq2->SQLQuery = sprintf("SELECT s1.id, s1.name, sub_title, model_type, x, z, s4.spawn_location_id
														FROM `".DEV_DB."`.spawn s1 
														JOIN `".DEV_DB."`.spawn_npcs s2 ON s1.id = s2.spawn_id 
														JOIN `".DEV_DB."`.spawn_location_entry s3 ON s1.id = s3.spawn_id 
														JOIN `".DEV_DB."`.spawn_location_placement s4 ON s4.spawn_location_id = s3.spawn_location_id 
														WHERE 
															s4.zone_id = %s 
															AND (
																	 s1.processed <> 1 
																	 AND s4.processed <> 1
																	 )
															AND (x BETWEEN %s AND %s) AND (z BETWEEN %s AND %s) 
														ORDER BY s1.name, s1.id", $s->zone_id, $x_low, $x_high, $z_low, $z_high);
	
	$results = $eq2->RunQueryMulti();

	?>
  <table>
		<form method="post" name="spawn_proximity">
		<tr style="background-color:#000; color:#fff;">
			<td width="125" colspan="2">&nbsp;<strong>Spawn ID</strong></td>
			<td width="250">&nbsp;<strong>Name</strong></td>
			<td width="250"&nbsp;><strong>Sub-Title</strong></td>
			<td width="50">&nbsp;<strong>Model</strong></td>
			<td width="100">&nbsp;<strong>LocID</strong></td>
			<td width="50">&nbsp;<strong>X</strong></td>
			<td width="50">&nbsp;<strong>Z</strong></td>
    </tr>
    <?php
		if( is_array($results) )
		{
			foreach($results as $data)
			{
			?>
		<tr>
			<td colspan="2" align="right"><?= $data['id'] ?></td>
			<td><strong>
				<?= $data['name'] ?>
				</strong></td>
			<td><?= $data['sub_title'] ?></td>
			<td><?= $data['model_type'] ?></td>
			<td><?= $data['spawn_location_id'] ?></td>
			<td><?= $data['x'] ?></td>
			<td><?= $data['z'] ?></td>
		</tr>
		<input type="hidden" name="spawnID[]" value="<?= $data['id']?>" />
		  <?php
			}
		}
		?>
    <tr>
    	<td colspan="7" align="center">&nbsp;<input type="submit" name="cmd" value="Spawn These" class="submit" /></td>
    </tr>
		</form>
  </table>
  <?php
}

function ParseSpellData()
{
	global $eq2;
	
	include("../class/eq2.spells.php");
	$spells = new eq2Spells();
	
	$options = array('options' => array('min_range' => 0));

	if( isset($_POST['cmd']) )
	{
		$index_field = GetNextIndex();
		$query = "INSERT INTO `".DEV_DB."`.spell_data (spell_id, tier, index_field, value_type, value) VALUES ";
		
		foreach($_POST as $key=>$val)
		{
			$idx = explode("_", $key);
			
			if( strstr($key, "value") && !empty($val) /*&& $val >= 0*/ )
			{
				//$index_value++;
				//printf("val: %s, idx: %s<br>", $val, $idx[1]); 
				if( $val == "No Effect" )
					continue;
				elseif( $val < 0 && $idx[1] == 0 )
				{
					// hack to get Slashing (id 0) to save properly... see hostile comments below :/
					$value_type = 'INT';
					$val = 0;
				}
				else if( filter_var($val, FILTER_VALIDATE_INT) )
					$value_type = 'INT';
				else if( filter_var($val, FILTER_VALIDATE_FLOAT) )
					$value_type = 'FLOAT';
				else
					$value_type = 'STRING';
				
				//printf("value_type: %s<br>", $value_type); 
				if( isset($query_rows) )
					$query_rows .= sprintf(", ('%s', '%s', '%s', '%s', '%s')", $_POST['spellid'], $_POST['tier'], $index_field, $value_type, $val);
				else
					$query_rows = sprintf(" ('%s', '%s', '%s', '%s', '%s')", $_POST['spellid'], $_POST['tier'], $index_field, $value_type, $val);
					
				$index_field++;
			}
		}
		$eq2->SQLQuery = $query . $query_rows;
		$eq2->RunQuery();
	}

	?>
	<div id="Editor">
	<table class="SubPanel" cellspacing="0" border="0">
		<tr>
			<td align="center" valign="top" colspan="2"><strong>Parse Spell Effects Data</strong></td>
		</tr>
		<tr>
			<td valign="top" colspan="2">
				<strong>Purpose:</strong><br>
				This script will attempt to identify data in Spell Display Effects, and parse it into Spell Data entries.<br />
				<br>
				<strong>Instructions:</strong><br>
				For each tier, verify and submit the data you feel best fits the spell display effects shown.<br /> 
				<br />
				The script will attempt to show a list of known &quot;damage&quot; types, and if it cannot find one will then allow you to pick one from the phrase,<br />
				or leave it blank so you can change it manually.
			</td>
		</tr>
		<tr>
			<td id="EditorStatus" colspan="2"><?php if( !empty($eq2->Status) ) $eq2->DisplayStatus(); ?></td>
		</tr>
	<?
	/* this one was hiding inserted records until index 1 turned into 3 pieces of data and the index match got completely fucked.
	$sql = sprintf("SELECT DISTINCT s1.*, s2.index_field
										FROM spell_display_effects s1 
										LEFT JOIN spell_data s2 ON s1.spell_id = s2.spell_id AND s1.tier = s2.tier AND s1.index = s2.index_field
										WHERE 
											s2.index_field IS NULL AND
											s1.spell_id = %s", $_GET['id']);*/
	$eq2->SQLQuery = sprintf("SELECT DISTINCT s1.* " .
										"FROM `".DEV_DB."`.spell_display_effects s1 " .
										"LEFT JOIN `".DEV_DB."`.spell_data s2 ON s1.spell_id = s2.spell_id " .
										"WHERE s1.spell_id = %`s", $_GET['id']);
	
	$rows = $eq2->RunQueryMulti();
	
	if( is_array($rows) )
	{
		foreach($rows as $row)
		{
			if( $current_tier != $row['tier'] )
			{
				$current_tier = $row['tier'];
				printf('<tr><td class="SectionTitle">Tier %s</td></tr>', $current_tier);
			}
				
			$myArr = array();
			$data_values = array();
			
			// let the parsing begin!
			$myArr = explode(" ", $row['description']);
			$direction = ( in_array("Decreases", $myArr) > 0 ) ? "-" : "";
	
			foreach($myArr as $key=>$val)
			{
				if (preg_match('/\,/', $val) == 1) {
					$val = preg_replace('/\,/', "", $val);
				}

				if( filter_var($val, FILTER_VALIDATE_INT) || filter_var($val, FILTER_VALIDATE_FLOAT) || strstr($val, "%") )
				{
					$pattern[0] = '/\%/';
					$pattern[1] = '/\.$/';
					
					$replace[0] = "";
					$replace[1] = "";
					
					$val = $direction . $val;
					$data_values[] = preg_replace($pattern, $replace, $val);
				}
			}
		
			?>
			<form method="post" name="data|<?= $row['id'] ?>">
			<tr>
				<td class="Detail">Effect (<?= $row['bullet'] ?>,<?= $row['index'] ?>): <span style="color:#c00; font-weight:bold;"><?= $row['description'] ?></span></td>
			</tr>
			<tr>
				<td class="Label">
					<strong>Type:</strong> 
					<select name="value_0" style="font-size:12px; width:100px;">
						<?= GetDamageOptionsByName($myArr) ?>
					</select>&nbsp;
					<strong>Val1:</strong> <input type="text" name="value_1" value="<?= $data_values[0] ?>" style="font-size:10px; width:35px;">&nbsp;
					<strong>Val2:</strong> <input type="text" name="value_2" value="<?= $data_values[1] ?>" style="font-size:10px; width:35px;">&nbsp;
					<strong>Val3:</strong> <input type="text" name="value_3" value="<?= $data_values[2] ?>" style="font-size:10px; width:35px;">&nbsp;
					<strong>Val4:</strong> <input type="text" name="value_4" value="<?= $data_values[3] ?>" style="font-size:10px; width:35px;">&nbsp;
					<strong>Val5:</strong> <input type="text" name="value_5" value="<?= $data_values[4] ?>" style="font-size:10px; width:35px;">&nbsp;
					<input type="hidden" name="spellid" value="<?= $_GET['id'] ?>" />
					<input type="hidden" name="tier" value="<?= $row['tier'] ?>" />
					<input type="hidden" name="object_id" value="<?= $spells->GetSpellName() ?>|Tier:<?php print($row['tier']); ?>" />
					<input type="hidden" name="table_name" value="spell_data" />
					<input type="submit" name="cmd" value="Submit" style="font-size:11px; width:60px;" />&nbsp;
					<!--<input type="button" value="Next" class="submit" onclick="dosub('?page=spells&type=data&id=<?= $_GET['id']+1 ?>');" />-->
				</td>
			</tr>
			</form>
			<?
		}
	}
	else
	{
	?>	
		<tr>
			<td id="EditorStatus" class="warning">No matching display effects found.</td>
		</tr>
	<?php 
	} 
	?>
	</table>
	</div>
<?= 
}


function GetNextIndex()
{
	global $eq2;
	
	$eq2->SQLQuery = sprintf("SELECT MAX(index_field) AS next_idx FROM `".DEV_DB."`.spell_data WHERE spell_id = %s AND tier = %s", $_POST['spellid'], $_POST['tier']);
	$data = $eq2->RunQuerySingle();
	
	if( strlen($data['next_idx']) > 0 )
		$ret = $data['next_idx']+1;
	else
		$ret = 0;
	
	return $ret;
}


function GetDamageOptionsByName($effectsArray)
{
	global $eq2;
	
	$ok = false;
	$genericOptions = "<option>No Effect</option>";;
	$damageOptions = "";
	
	if( is_array($effectsArray) )
	{
		foreach($effectsArray as $effect)
		{
			if( !intval($effect) )
			{
				// build the list of options should all else fail
				$genericOptions .= "<option>$effect</option>";
				
				foreach($eq2->eq2DamageTypes as $key=>$val) 
				{
					if( strtolower($val) == strtolower($effect) )
					{
						$selected = " selected";
						$ok = true;
					}
					else
					{
						$selected = "";
					}
					
					// hack to get fucking slashing (0) to show up in shitty HTML forms
					if( $key == 0 )
						$key = -1;
						
					$damageOptions .= "<option value='$key'$selected>$val</option>\n";
				}
				if( !$ok )
					$damageOptions = $genericOptions;
			}
		}
		return $damageOptions;
	}
}
?>
