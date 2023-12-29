<?php 
define('IN_EDITOR', true);
include("header.php");

if ( !$eq2->CheckAccess(M_ZONES) )
	die("ACCESS: Denied!");

// Instantiate the eq2Spawn class, which also instantiates the eq2Zones class as $spawns->zones
include("../class/eq2.zones.php");
$z = new eq2Zones();
$link = sprintf("%s%s",$_SERVER['SCRIPT_NAME'], isset($_GET['zone']) ? "?zone=".$_GET['zone'] : "" ); // temp putting this back in?
?>
<div id="sub-menu1">
	<a href="zones.php?tab=zones">Zone Editor</a> | 
	<a href="zones.php?zone=zone_list">Zone List</a> | 
	<a href="zones.php?zone=zone_status">Zone Status</a> | 
	<a href="zones.php?cl=history">Zone Changelog</a>
</div>
<?php
if( isset($_GET['cl']) ) 
{
	$eq2->DisplayChangeLogPicker($z->eq2ZoneTables);
	include("footer.php");
	exit;
}

/*
 * Process commands here
 */
switch(strtolower($_POST['cmd'] ?? "")) 
{
	case "delete"		: $eq2->ProcessDeletes(); break;
	case "update":
		$z->PreUpdate();
		$eq2->ProcessUpdates();
		break;
	case "query"		: $eq2->processQuery(); break;
	case "insert":
		$z->PreInsert(); 
		$eq2->ProcessInserts();
		break;
		
	case "delete"	: 
		if( $_GET['tab'] == "zones" )
		{
			$q->DeleteQuest(); // this will delete zone, details and script if Delete is selected on the Zones tab
			
			if( $GLOBALS['config']['readonly'] == 0 )
				$eq2->Redir($_POST['redir']);
		}
		else
			$eq2->ProcessDeletes(); 
		break;
	case "create":
		$scriptFile = $eq2->SaveLUAScript();
		$replaceCount = 1;
		$scriptFile = str_replace(SCRIPT_PATH, "", $scriptFile, $replaceCount);
		$query = sprintf("UPDATE `%s`.zones SET `lua_script` = '%s' WHERE `id` = %s", DEV_DB, $eq2->SQLEscape($scriptFile), $z->zone_id);
		$eq2->RunQuery(true, $query);
		break;
	case "save":
		$eq2->SaveLUAScript();
		break;
}

/*
 * Build zone filter
 */
$zoneOptions = $z->GetAllZoneOptions();
?>
<form action="zones.php" id="frmSearch" method="post">
<table width="1000" border="0">
	<tr>
		<td width="100" class="filter_labels">Filters:</td>
		<td width="300" nowrap="nowrap">
			<select name="zoneID" onchange="dosub(this.options[this.selectedIndex].value)" style="min-width:300px;" />
			<option value="zones.php?zone=0&tab=zones">Pick a Zone</option>
			<?php if ($eq2->CheckAccess(G_DEVELOPER)) : ?>
			<option value="zones.php?zone=add&tab=zones"<?php if ( ($_GET['zone'] ?? "") == "add") echo " selected" ?>>Add a Zone</option>
			<?php endif; ?>
			<?= $zoneOptions ?>
			</select> <a href="zones.php?<?= $_SERVER['QUERY_STRING'] ?>">Reload Page</a>
		</td>
		<td>&nbsp;</td>
	</tr>
	<script>
	function ZoneLookupAJAX() {
		if (searchReq.readyState == 4 || searchReq.readyState == 0) {
			var str = escape(document.getElementById('txtSearch').value);
			searchReq.open("GET", '../ajax/eq2Ajax.php?type=luZ&search=' + str, true);
			searchReq.onreadystatechange = handleSearchSuggest; 
			searchReq.send(null);
		}		
	}
	</script>
	<tr>
		<td class="filter_labels">Lookup:</td>
		<td colspan="3">
				<input type="text" id="txtSearch" name="txtSearch" alt="Search Criteria" onkeyup="ZoneLookupAJAX();" autocomplete="off" class="box" style="width:295px;" value="<?= $_POST['txtSearch'] ?? "" ?>" /><!--onclick="this.value='';"-->
				<input type="submit" id="cmdSearch" name="cmdSearch" value="Search" alt="Run Search" class="submit" />
				<input type="button" value="Clear" class="submit" onclick="dosub('zones.php');" />
				<div id="search_suggest">
				</div>
		</td>
	</tr>
</table>
</form>
<?php
// once the filters are set, show the spell selector grid
if( ($_POST['cmdSearch'] ?? "") == 'Search' )
{
	$data = $z->GetZonesMatching();
	DisplayZoneSelectionGrid($data);
	include("footer.php");
	exit; // end page here, since actions requires none of the code below
}

if ( isset($z->zone_id) )
{
	$querystring = sprintf("zones.php?zone=%s", $z->zone_id);

	// Build the Tab menu
	$current_tab_idx = $_GET['tab'] ?? 'Zones';
	
	// only show Zone tab if adding new zone
	$tab_array = array(
		'zones' => "Zones"
		);

	$tab_array2 = array(
		'zone_script' => "Script",
		'revive_points' => "Revive",
		'locations' => "Location",
		//'map_data' => "Map Data",
		//'instances' => "Instance",
		//'starting_zones' => "Starting",
		//'transporters' => "Transport"
		);

	if( $_GET['zone'] != "add" ) {
		$tab_array = array_merge($tab_array, $tab_array2);
		$eq2->TabGenerator($current_tab_idx, $tab_array, $querystring, false);
	}

	switch($_GET['tab'] ?? "") 
	{
		case "zone_script"		:	zone_script(); break;
		case "revive_points"	:	revive_points($z->zone_id); break;
		case "locations"		:	locations($z->zone_id); break;
		//case "map_data"			:	map_data(); break;
		//case "starting_zones"	:	starting_zones(); break;
		//case "transporters"		:	transporters($z->zone_id); break;
		default					:	Zone(); break;
	}	
}
else
{
	switch($_GET['zone'] ?? "")
	{
		//case "zone_status"	: zone_status(); break;
		case "zone_list"	: zone_list(); break;
	}
}

include("footer.php");
exit; // end of page

/*
 * Functions
 */
function DisplayZoneSelectionGrid($data)
{
	global $eq2, $z;
	
	if( is_array($data) )
	{
	?>
	<table width="100%" cellpadding="4" cellspacing="0" border="0">
		<tr bgcolor="#cccccc">
			<td width="50"><strong>ID</strong></td>
			<td width="120"><strong>Name</strong></td>
			<td width="100"><strong>File</strong></td>
			<td><strong>Description</strong></td>
			<td width="100"><strong>Zone Type</strong></td>
			<td width="100"><strong>Instance</strong></td>
			<td width="100"><strong>Ruleset</strong></td>
			<td width="100"><strong>Script</strong></td>
		</tr>
		<?php 
		$i = 0;
		foreach($data as $row)
		{
			$row_class = ( $i % 2 ) ? " bgcolor=\"#eeeeee\"" : "";
			//$description = ( strlen($row['description']) > 90 ) ? substr($row['description'],0,90).'...' : $row['description'];
			$description = $row['description']; // use above to truncate descriptions
			
			// having a problem switching classes once in the editor
			$querystring = sprintf("zones.php?zone=%s", $row['id']);
			
		?>
		<tr<?= $row_class ?> valign="top">
			<td><a href="<?= $querystring ?>&tab=zones"><?= $row['id'] ?></a></td>
			<td nowrap>
				<a href="http://census.daybreakgames.com/xml/get/eq2/zone/?zone_data.name=<?= $row['description'] ?>&c:limit=100" target="_blank"><img src="../images/soe.png" border="0" align="top" title="SOE" alt="SOE" height="20" /></a>
				<a href="http://eq2.wikia.com/wiki/<?= $row['description'] ?>" target="_blank"><img src="../images/wikia.png" border="0" align="top" title="Wikia" alt="Wikia" height="20" /></a>
				<a href="http://eq2.zam.com/search.html?q=<?= $row['description'] ?>" target="_blank"><img src="../images/zam.png" border="0" align="top" title="Zam" alt="Zam" height="20" /></a>
				<?= $row['name'] ?>
			</td>
			<td><?= $row['file'] ?></td>
			<td><?= $description ?></td>
			<td><?= $row['zone_type'] ?></td>
			<td nowrap><?= $row['instance_type'] ?></td>
			<td nowrap><?= $row['ruleset_id'] ?></td>
			<td nowrap><?= $row['lua_script'] ?></td>
		</tr>
		<?php
		$i++;
		}
		?>
		<tr bgcolor="#CCCCCC">
			<td colspan="10"><?= $i ?> rows returned...</td>
		</tr>
	</table>
	<?php
	}
	else
		print("&nbsp;No data found for set filters. Lookup the quest by name.");
}


function Zone() 
{
	global $eq2, $z;

	//?>
	<?php if ($z->zone_id == "add") : ?>
	<br>
	<span id="EditorStatus">
		<?php 
		if( !empty($eq2->Status) ){
		 $eq2->DisplayStatus(); 
		}
		 ?></span>
	<?php $z->PrintNewZoneForm(); return; ?>
	<?php endif; ?>

	<div id="Editor">
	<form method="post" name="ZoneForm">
	<table class="SubPanel" cellspacing="0" border="0">
		<tr>
			<td id="EditorStatus" colspan="2"><?php if( !empty($eq2->Status) ) $eq2->DisplayStatus(); ?></td>
		</tr>
		<?php
			$data = $z->GetZoneData();
			$script_full_name = $data['lua_script'];
			$tmp = explode("/", $script_full_name);
			if( count($tmp) == 2 )
			{
				$script_path = sprintf("/%s/", $tmp[0], $tmp[1]);
				$script_name = $tmp[1];
			}
		?>
		<tr>
			<td class="Title" colspan="2">Editing: <?= $z->zone_name ?> (<?= $z->zone_id ?>) (<?= strlen($script_full_name) ? $script_full_name : "n/a" ?>)<?php $z->PrintOffsiteLinks(); ?></td>
		</tr>
		<tr>
			<td valign="top">
				<table class="SectionMainFloat" cellspacing="0" border="0" width="680">
					<tr>
						<td class="SectionTitle">General / Text</td>
					</tr>
					<tr>
						<td class="SectionBody">
							<table cellspacing="0">
								<tr>
									<td class="Label">id:</td>
									<td class="Detail">
										<?= $data['id'] ?>
										<input type="hidden" name="orig_id" value="<?= $data['id'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="Label">expansion_id:</td>
									<td class="Detail">
										<select name="zones|expansion_id" style="width:400px;">
											<?= $eq2->getExpansionOptions($data['expansion_id']) ?>
										</select>
										<input type="hidden" name="orig_expansion_id" value="<?= $data['expansion_id'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="Label">file:</td>
									<td class="Detail">
										<input type="text" name="zones|file" value="<?= $data['file'] ?>" style="width:400px;" />
										<input type="hidden" name="orig_file" value="<?= $data['file'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="Label">sky_file:</td>
									<td class="Detail">
										<input type="text" name="zones|sky_file" value="<?= $data['sky_file'] ?>" style="width:400px;" />
										<input type="hidden" name="orig_sky_file" value="<?= $data['sky_file'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="Label">name:</td>
									<td class="Detail">
										<input type="text" name="zones|name" value="<?= $data['name'] ?>" style="width:400px;" />
										<input type="hidden" name="orig_name" value="<?= $data['name'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="Label">description:</td>
									<td class="Detail">
										<input type="text" name="zones|description" value="<?= stripslashes($data['description']) ?>" style="width:400px;" />
										<input type="hidden" name="orig_description" value="<?= $data['description'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="Label">safe_x:</td>
									<td class="Detail">
										<input type="text" name="zones|safe_x" value="<?= $data['safe_x'] ?>" style="width:50px;" />
										<input type="hidden" name="orig_safe_x" value="<?= $data['safe_x'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="Label">safe_y:</td>
									<td class="Detail">
										<input type="text" name="zones|safe_y" value="<?= $data['safe_y'] ?>" style="width:50px;" />
										<input type="hidden" name="orig_safe_y" value="<?= $data['safe_y'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="Label">safe_z:</td>
									<td class="Detail">
										<input type="text" name="zones|safe_z" value="<?= $data['safe_z'] ?>" style="width:50px;" />
										<input type="hidden" name="orig_safe_z" value="<?= $data['safe_z'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="Label">safe_heading:</td>
									<td class="Detail">
										<input type="text" name="zones|safe_heading" value="<?= $data['safe_heading'] ?>" style="width:50px;" />
										<input type="hidden" name="orig_safe_heading" value="<?= $data['safe_heading'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="Label">underworld:</td>
									<td class="Detail">
										<input type="text" name="zones|underworld" value="<?= $data['underworld'] ?>" style="width:100px;" />
										<input type="hidden" name="orig_underworld" value="<?= $data['underworld'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="Label">zone_motd:</td>
									<td class="Detail">
										<input type="text" name="zones|zone_motd" value="<?= stripslashes($data['zone_motd']) ?>" style="width:400px;" />
										<input type="hidden" name="orig_zone_motd" value="<?= $data['zone_motd'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="Label">zone_type:</td>
									<td class="Detail">
										<select name="zones|zone_type">
											<option value=""<?php if( $data['zone_type']=="" ) print(" selected") ?>></option>
											<option value="City"<?php if( $data['zone_type']=="City" ) print(" selected") ?>>City</option>
											<option value="Indoor"<?php if( $data['zone_type']=="Indoor" ) print(" selected") ?>>Indoor</option>
											<option value="Instanced Indoor"<?php if( $data['zone_type']=="Instanced Indoor" ) print(" selected") ?>>Instanced Indoor</option>
											<option value="Instanced Outdoor"<?php if( $data['zone_type']=="Instanced Outdoor" ) print(" selected") ?>>Instanced Outdoor</option>
											<option value="Outdoor"<?php if( $data['zone_type']=="Outdoor" ) print(" selected") ?>>Outdoor</option>
											<option value="Player Housing"<?php if( $data['zone_type']=="Player Housing" ) print(" selected") ?>>Player Housing</option>
											<option value="Tradeskill"<?php if( $data['zone_type']=="Tradeskill" ) print(" selected") ?>>Tradeskill</option>
										</select>
										<input type="hidden" name="orig_zone_type" value="<?= $data['zone_type'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="Label">lua_script:</td>
									<td class="Detail"><?php if (strlen($data['lua_script']) > 0) echo $data['lua_script']; else echo "Create one from the script tab!"; ?></td>
								</tr>
								<tr>
									<td class="Label">start_zone:</td>
									<td class="Detail">
										<select name="zones|start_zone">
											<option value="0">---</option>
											<option value="1"<?php if( $data['start_zone']==1 ) print(" selected") ?>>Qeynos (Queen's Colony)</option>
											<option value="2"<?php if( $data['start_zone']==2 ) print(" selected") ?>>Freeport (Outpost of the Overlord)</option>
											<option value="4"<?php if( $data['start_zone']==4 ) print(" selected") ?>>Kelethin (Greater Faydark)</option>
											<option value="8"<?php if( $data['start_zone']==8 ) print(" selected") ?>>Neriak (Darklight Wood)</option>
											<option value="16"<?php if( $data['start_zone']==16 ) print(" selected") ?>>Timorous Deep</option>
											<option value="32"<?php if( $data['start_zone']==32 ) print(" selected") ?>>Halas</option>
										</select> (choices from client character create screen)
										<input type="hidden" name="orig_start_zone" value="<?= $data['start_zone'] ?>" />
									</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td><strong>Note:</strong> Only one zone can be assigned as a start_zone for each client choice.</td>
								</tr>
								<tr>
									<td colspan="2">
									<fieldset>
									<legend>Flags</legend>
									<table>
										<tr>
										<td class="Label">always_loaded:</td>
										<td class="Detail">
											<?php $eq2->GenerateBlueCheckbox("zones|always_loaded", $data['always_loaded'] == 1) ?>
											<input type="hidden" name="orig_always_loaded" value="<?= $data['always_loaded'] ?>" />
										</td>
										<td class="Label">city_zone:</td>
										<td class="Detail">
											<?php $eq2->GenerateBlueCheckbox("zones|city_zone", $data['city_zone'] == 1) ?>
											<input type="hidden" name="orig_city_zone" value="<?= $data['city_zone'] ?>" />
										</td>
										<td class="Label">can_bind:</td>
										<td class="Detail">
											<?php $eq2->GenerateBlueCheckbox("zones|can_bind", $data['can_bind'] == 1) ?>
											<input type="hidden" name="orig_can_bind" value="<?= $data['can_bind'] ?>" />
										</td>
										<td class="Label">can_gate:</td>
										<td class="Detail">
											<?php $eq2->GenerateBlueCheckbox("zones|can_gate", $data['can_gate'] == 1) ?>
											<input type="hidden" name="orig_can_gate" value="<?= $data['can_gate'] ?>" />
										</td>
										</tr>
									</table>
									</fieldset>
									</td>
								</tr>
								<?php if($eq2->CheckAccess(G_DEVELOPER)) { ?>
								<tr>
									<td colspan="2" align="center">
										<input type="submit" name="cmd" value="Update" class="submit" />
										<input type="hidden" name="object_id" value="<?= $z->zone_name ?>" />
										<input type="hidden" name="table_name" value="zones" />
									</td>
								</tr>
								<?php } ?>
							</table>
						</td>
					</tr>
				</table>
			</td>
			<td valign="top">
				<table class="SectionToggles" cellspacing="0" border="0">
					<tr>
						<td class="SectionTitle">Settings</td>
					</tr>
					<tr>
						<td class="SectionBody">
							<table cellspacing="0">
								<tr>
									<td class="Label">shutdown_timer:</td>
									<td>
										<input type="text" name="zones|shutdown_timer" value="<?= $data['shutdown_timer'] ?>" style="width:100px;" />
										<input type="hidden" name="orig_shutdown_timer" value="<?= $data['shutdown_timer'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="Label">ruleset_id:</td>
									<td>
										<input type="text" name="zones|ruleset_id" value="<?= $data['ruleset_id'] ?>" style="width:100px;" />
										<input type="hidden" name="orig_ruleset_id" value="<?= $data['ruleset_id'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="Label">xp_modifier:</td>
									<td>
										<input type="text" name="zones|xp_modifier" value="<?= $data['xp_modifier'] ?>" style="width:100px;" />
										<input type="hidden" name="orig_xp_modifier" value="<?= $data['xp_modifier'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="Label">min_recommended:</td>
									<td>
										<input type="text" name="zones|min_recommended" value="<?= $data['min_recommended'] ?>" style="width:100px;" />
										<input type="hidden" name="orig_min_recommended" value="<?= $data['min_recommended'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="Label">max_recommended:</td>
									<td>
										<input type="text" name="zones|max_recommended" value="<?= $data['max_recommended'] ?>" style="width:100px;" />
										<input type="hidden" name="orig_max_recommended" value="<?= $data['max_recommended'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="Label">min_status:</td>
									<td>
										<input type="text" name="zones|min_status" value="<?= $data['min_status'] ?>" style="width:100px;"<?php if( $eq2->CheckAccess(G_DEVELOPER) < $data['min_status'] ) echo " disabled" ?> />
										<input type="hidden" name="orig_min_status" value="<?= $data['min_status'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="Label">min_level:</td>
									<td>
										<input type="text" name="zones|min_level" value="<?= $data['min_level'] ?>" style="width:100px;" />
										<input type="hidden" name="orig_min_level" value="<?= $data['min_level'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="Label">max_level:</td>
									<td>
										<input type="text" name="zones|max_level" value="<?= $data['max_level'] ?>" style="width:100px;" />
										<input type="hidden" name="orig_max_level" value="<?= $data['max_level'] ?>" />
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<table class="SectionToggles" cellspacing="0" border="0">
					<tr>
						<td class="SectionTitle">Instancing</td>
					</tr>
					<tr>
						<td class="SectionBody">
							<table cellspacing="0">
								<tr>
									<td class="Label">instance_type:</td>
									<td>
										<?php $z->DisplayInstanceTypeDropdown($data); ?>
										<input type="hidden" name="orig_instance_type" value="<?= $data['instance_type'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="Label">default_reenter_time:</td>
									<td>
										<input type="text" name="zones|default_reenter_time" value="<?= $data['default_reenter_time'] ?>" style="width:100px;" />
										<input type="hidden" name="orig_default_reenter_time" value="<?= $data['default_reenter_time'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="Label">default_reset_time:</td>
									<td>
										<input type="text" name="zones|default_reset_time" value="<?= $data['default_reset_time'] ?>" style="width:100px;" />
										<input type="hidden" name="orig_default_reset_time" value="<?= $data['default_reset_time'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="Label">default_lockout_time:</td>
									<td>
										<input type="text" name="zones|default_lockout_time" value="<?= $data['default_lockout_time'] ?>" style="width:100px;" />
										<input type="hidden" name="orig_default_lockout_time" value="<?= $data['default_lockout_time'] ?>" />
									</td>
								</tr>
								<tr>
									<td class="Label">force_group_to_zone:</td>
									<td>
										<input type="text" name="zones|force_group_to_zone" value="<?= $data['force_group_to_zone'] ?>" style="width:100px;" />
										<input type="hidden" name="orig_force_group_to_zone" value="<?= $data['force_group_to_zone'] ?>" />
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	</form>
	</div>
	<?php	
}

function zone_script() {
	global $eq2, $z;

	$script_full_name = $z->GetZoneScriptName($z->zone_id);

	if (!isset($script_full_name) || $script_full_name == "") {
		$zname = $z->zone_name;
		preg_replace('/[^\w]/', "", $zname);
		$script_full_name = sprintf("ZoneScripts/%s.lua", $zname);
	}

	echo "<br/>";
	print($eq2->DisplayScriptEditor($script_full_name, $z->zone_name, sprintf("%s|%s", $z->zone_name, $z->zone_id), "zones")); 
}

function locations($id) 
{
	global $eq2, $objectName, $z;

	$table="locations";
?>
	<div id="Editor">
	<table border="0" cellpadding="5" align="left">
		<tr>
			<td width="750" valign="top">
				<fieldset>
				<legend>General</legend>
				<table width="100%" cellpadding="0" border="0">
					<tr>
						<td width="55">id</td>
						<td width="55">zone_id</td>
						<td width="105">grid_id</td>

						<td width="255">name</td>
						<td width="55">include_y</td>
						<td align="center">discovery</td>
						<td colspan="2">&nbsp;</td>
					</tr>

						<?php
						$query=sprintf("select * from `%s`.%s where zone_id = %s",DEV_DB, $table, $id);
						$rows =$eq2->RunQueryMulti($query);
						foreach ($rows as $data) {
						?>
						<form method="post" name="multiForm|<?php $data['id']; ?>">
					<tr>
						<td>
							<input type="hidden" name="objectName" value="<?= $objectName ?>" />
							<input type="hidden" name="table_name" value="<?= $table ?>" />
							<input type="text" name="locations|id" value="<?php print($data['id']) ?>" style="width:45px;  background-color:#ddd;" readonly />
							<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
						</td>
						<td>
							<input type="text" name="locations|zone_id" value="<?php print($data['zone_id']) ?>" style="width:45px;  background-color:#ddd;" readonly />
							<input type="hidden" name="orig_zone_id" value="<?php print($data['zone_id']) ?>" />
						</td>
						<td>
							<input type="text" name="locations|grid_id" value="<?php print($data['grid_id']) ?>" style="width:95px;" />

							<input type="hidden" name="orig_grid_id" value="<?php print($data['grid_id']) ?>" />
						</td>
						<td>
							<input type="text" name="locations|name" value="<?php print($data['name']) ?>" style="width:255px;" />
							<input type="hidden" name="orig_name" value="<?php print($data['name']) ?>" />
						</td>
						<td align="center">
							<?php $eq2->GenerateBlueCheckbox('locations|include_y', $data['include_y'] == 1); ?>
							<input type="hidden" name="orig_include_y" value="<?php print($data['include_y']) ?>" />
						</td>
						<td align="center">
							<?php $eq2->GenerateBlueCheckbox('locations|discovery', $data['discovery'] == 1); ?>
							<input type="hidden" name="orig_discovery" value="<?php print($data['discovery']) ?>" />
						</td>
						<td>
							<?php if($eq2->CheckAccess(G_DEVELOPER)) : ?>
								<input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" />
							<?php endif; ?>
						</td>
						<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" /><?php } ?></td>
					</tr>
							</form>
				<?php
				}
				?>

				<?php if($eq2->CheckAccess(G_DEVELOPER)) { ?>
					<form method="post" name="multiForm|new">
					<tr>
						<td align="center"><strong>new</strong></td>
						<td>
							<input type="hidden" name="objectName" value="<?= $objectName ?>" />
							<input type="hidden" name="table_name" value="<?= $table ?>" />
							<input type="text" name="locations|zone_id|new" value="<?= $id ?>" style="width:45px; background-color:#ddd" readonly />
						</td>
						<td>
							<input type="text" name="locations|grid_id|new" value="" style="width:95px;" />
						</td>
						<td>
							<input type="text" name="locations|name|new" value="" style="width:255px;" />
						</td>
						<td align="center">
							<?php $eq2->GenerateBlueCheckbox('locations|include_y|new', false); ?>
						</td>
						<td align="center">
							<?php $eq2->GenerateBlueCheckbox('locations|discovery|new', false); ?>
						</td>
						<td>
							<input type="submit" name="cmd" value="Insert" style="font-size:10px; width:60px" />
						</td>
					</tr>
					</form>
				<?php } ?>
				</table>
				</fieldset>
			</td>
		</tr>

	</table>
	</div>
<?php
}


function location_details($id) {
	global $eq2,$objectName;

	$table="location_details";
?>
	<table border="0" cellpadding="5">
		<tr>
			<td width="540" valign="top">
				<fieldset><legend>General</legend>
				<table width="100%" cellpadding="0" border="0">
					<tr>
						<td width="25">#</td>
						<td width="55">id</td>
						<td width="75">location_id</td>
						<td width="75">x</td>

						<td width="75">y</td>
						<td width="75">z</td>
						<td colspan="2">&nbsp;</td>
					</tr>

						<?php
						$query=sprintf("select * from %s where location_id = %s",$table, $id);
						$result=$eq2->db->sql_query($query);
						$i = 1;
						while($data=$eq2->db->sql_fetchrow($result)) {
						?>
					<form method="post" name="multiForm|<?php print($data['id']); ?>" />
					<tr>
						<td><?= $i ?>.</td>
						<td>
							<input type="text" name="location_details|id" value="<?php print($data['id']) ?>" style="width:45px;  background-color:#ddd;" readonly />
							<input type="hidden" name="orig_id" value="<?php print($data['id']) ?>" />
						</td>
						<td>
							<input type="text" name="location_details|location_id" value="<?php print($data['location_id']) ?>" style="width:75px;" />
							<input type="hidden" name="orig_location_id" value="<?php print($data['location_id']) ?>" />
						</td>
						<td>
							<input type="text" name="location_details|x" value="<?php print($data['x']) ?>" style="width:75px;" />
							<input type="hidden" name="orig_x" value="<?php print($data['x']) ?>" />
						</td>
						<td>
							<input type="text" name="location_details|y" value="<?php print($data['y']) ?>" style="width:75px;" />
							<input type="hidden" name="orig_y" value="<?php print($data['y']) ?>" />
						</td>
						<td>
							<input type="text" name="location_details|z" value="<?php print($data['z']) ?>" style="width:75px;" />
							<input type="hidden" name="orig_z" value="<?php print($data['z']) ?>" />

						</td>
						<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" /><?php } ?></td>
						<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" /><?php } ?></td>
					</tr>
					<input type="hidden" name="objectName" value="<?= $objectName ?>" />
					<input type="hidden" name="tableName" value="<?= $table ?>" />
					</form>
				<?php
					$i++;
				}
				?>

				<?php if($eq2->CheckAccess(G_DEVELOPER)) { ?>
					<form method="post" name="sdForm|new" />
					<tr>
						<td>&nbsp;</td>
						<td align="center"><strong>new</strong></td>
						<td>
							<input type="text" name="location_details|location_id|new" value="" style="width:75px;" />
						</td>
						<td>
							<input type="text" name="location_details|x|new" value="" style="width:75px;" />
						</td>
						<td>
							<input type="text" name="location_details|y|new" value="" style="width:75px;" />
						</td>
						<td>
							<input type="text" name="location_details|z|new" value="" style="width:75px;" />
						</td>
						<td>
							<input type="submit" name="cmd" value="Insert" style="font-size:10px; width:60px" />

						</td>
					</tr>
					<input type="hidden" name="tableName" value="<?= $table ?>" />
					</form>
				<?php } ?>
				</table>
				</fieldset>
			</td>
		</tr>

	</table>
<?php
}


function revive_points($id) 
{
	global $eq2, $objectName, $z;	

	?>
	<div id="Editor">
	<table border="0" cellpadding="5">
		<tr>
			<td valign="top">
			<fieldset>
			<legend>Revive Points</legend> 
				<table width="100%">
					<tr>
						<td>id</td>
						<td>location_name</td>
						<td>zone_id</td>
						<td>respawn_zone_id</td>
						<td>safe_x</td>
						<td>safe_y</td>
						<td>safe_z</td>
						<td>heading</td>
					</tr>
			<?php
			$query = sprintf("select * from `%s`.revive_points where zone_id = '%s'", DEV_DB, $id);
			$rows = $eq2->RunQueryMulti($query);
	
			foreach ($rows as $data)
			{
				$objectName = $data['location_name'];
			?>
			<form method="post" name="rpForm|<?php echo $data['id']; ?>">
				<tr>
					<td>
						<?= $data['id'] ?>
						<input type="hidden" name="orig_id" value="<?= $data['id'] ?>" />
						<input type="hidden" name="orig_object" value="<?= $objectName ?>" />
						<input type="hidden" name="table_name" value="revive_points" />
					</td>
					<td>
						<input type="text" name="revive_points|location_name" value="<?= stripslashes($data['location_name']) ?>" style="width:150px;" />
						<input type="hidden" name="orig_location_name" value="<?= $data['location_name'] ?>" />
						<input type="hidden" name="orig_object" value="<?= $data['location_name'] ?>" />
					</td>
					<td>
						<select name="revive_points|zone_id" style="width:180px;">
							<?php echo $z->getZoneOptionsByID($data['zone_id']); ?>
						</select>
						<input type="hidden" name="orig_zone_id" value="<?= $data['zone_id'] ?>" />
					</td>
					<td>
						<select name="revive_points|respawn_zone_id" style="width:180px;">
							<?php echo $z->getZoneOptionsByID($data['respawn_zone_id']); ?>
						</select>
						<input type="hidden" name="orig_respawn_zone_id" value="<?= $data['respawn_zone_id'] ?>" />
					</td>
					<td>
						<input type="text" name="revive_points|safe_x" value="<?= $data['safe_x'] ?>" style="width:60px;" />
						<input type="hidden" name="orig_safe_x" value="<?= $data['safe_x'] ?>" />
					</td>
					<td>
						<input type="text" name="revive_points|safe_y" value="<?= $data['safe_y'] ?>" style="width:60px;" />
						<input type="hidden" name="orig_safe_y" value="<?= $data['safe_y'] ?>" />
					</td>
					<td>
						<input type="text" name="revive_points|safe_z" value="<?= $data['safe_z'] ?>" style="width:60px;" />
						<input type="hidden" name="orig_safe_z" value="<?= $data['safe_z'] ?>" />
					</td>
					<td>
						<input type="text" name="revive_points|heading" value="<?= $data['heading'] ?>" style="width:60px;" />
						<input type="hidden" name="orig_heading" value="<?= $data['heading'] ?>" />
					</td>
					<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Update" style="font-size:10px; width:60px" /><?php } ?></td>
					<td><?php if($eq2->CheckAccess(G_DEVELOPER)) { ?><input type="submit" name="cmd" value="Delete" style="font-size:10px; width:60px" /><?php } ?></td>
				</tr>
			</form>
			<?php
			}
			?>
			<?php if($eq2->CheckAccess(G_DEVELOPER)) { ?>
			<form method="post" name="rpForm|new">
					<tr>
						<td><strong>new</strong></td>
						<td>
							<input type="text" name="revive_points|location_name|new" value="" style="width:150px;" />
							<input type="hidden" name="table_name" value="revive_points" />
						</td>
						<td>
							<select name="revive_points|zone_id|new" style="width:180px;">
							<?php echo $z->getZoneOptionsByID(); ?>
							</select>
						</td>
						<td>
							<select name="revive_points|respawn_zone_id|new" style="width:180px;">
							<?php echo $z->getZoneOptionsByID(); ?>
							</select>
						</td>
						<td>
							<input type="text" name="revive_points|safe_x|new" value="" style="width:60px;" />
						</td>
						<td>
							<input type="text" name="revive_points|safe_y|new" value="" style="width:60px;" />
						</td>
						<td>
							<input type="text" name="revive_points|safe_z|new" value="" style="width:60px;" />
						</td>
						<td>
							<input type="text" name="revive_points|heading|new" value="" style="width:60px;" />
						</td>
						<td><input type="submit" name="cmd" value="Insert" style="font-size:10px; width:60px" /></td>
						<td>&nbsp;</td>
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
	</div>
<?php	
}


function zone_list() {
	global $eq2;
	$query="select * from zones order by description;";
	$result=$eq2->db->sql_query($query);
	?>
	<table border="0" cellpadding="5">
		<tr>
			<td valign="top">
				<fieldset><legend>Current Zone List</legend> 
				<table width="100%" cellpadding="2" cellspacing="0">
					<tr>
						<th width="50">id</th>
						<th>name</th>
						<th>description</th>
						<th>safe_x</th>
						<th>safe_y</th>
						<th>safe_z</th>
						<th>always<br />loaded</th>
						<th>city<br />zone</th>
					</tr>
	<?php
	$i=0;
	while($data=$eq2->db->sql_fetchrow($result)) {
		$rowStyle = ( $i % 2 ) ? "#dddddd" : "#ffffff";
	?>
					<tr nowrap="nowrap" bgcolor="<?= $rowStyle ?>">
						<td><a href="zones.php?z=<?= $data['id'] ?>&t=zones" target="_self"><?= $data['id'] ?></a></td>
						<td><?= $data['name'] ?></td>
						<td><?= $data['description'] ?></td>
						<td><?= $data['safe_x'] ?></td>
						<td><?= $data['safe_y'] ?></td>
						<td><?= $data['safe_z'] ?></td>
						<td align="center"><?= ( $data['always_loaded'] ) ? "yes":"no" ?></td>
						<td align="center"><?= ( $data['city_zone'] ) ? "yes":"no" ?></td>
					</tr>
	<?php
		$i++;
	}
	?>
					<tr>
						<td colspan="8" height="30" valign="bottom"><strong><?= $i ?> records found.</strong></td>
					</tr>
				</table>
				</fieldset>
			</td>
		</tr>
	</table>
	<?php
}
?>
