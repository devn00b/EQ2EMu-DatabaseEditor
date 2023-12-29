<?php
class eq2Zones
{
	var $eq2ZoneTables = array('zones','locations','location_details','instances','instance_spawns_removed','map_data','revive_points','starting_zones','transporters','transport_maps');
	
	var $eq2ZoneFlags = array(
		"always_loaded",
		"city_zone",
		"can_bind",
		"can_gate"
	);

	public function __construct() 
	{
		if( !empty($_GET['zone']) )
		{
			$this->zone_id		= $_GET['zone'];
			$this->zone_name	= ( $this->zone_id > 0 ) ? $this->GetZoneName() : "";
		}
	}
	
	// List of ALL zones
	public function GetAllZones()
	{
		global $eq2;

		$eq2->SQLQuery = "SELECT id,name,description FROM `".DEV_DB."`.zones ORDER BY description";
		return $eq2->RunQueryMulti();
	}

	// These <options> build the zone filter showing all zones in the database
	public function GetAllZoneOptions()
	{
		global $eq2;

		// add this to the querystring only if page= is iset
		$Page = ( isset($_GET['page']) ) ? sprintf("%s?page=%s&", $eq2->GetPHPScriptName(), $_GET['page']) : sprintf("%s?", $eq2->GetPHPScriptName());
		
		$eq2->SQLQuery = sprintf("SELECT id,description FROM `".DEV_DB."`.zones ORDER BY description");
		 
		$results = $eq2->RunQueryMulti();
		
		if( is_array($results) )
		{
			$ret = "";
			foreach($results as $data)
				$ret .= sprintf('<option value="%szone=%s&tab=zones"%s>%s (%s)</option>', $Page, $data['id'], ( isset($this->zone_id) && $this->zone_id == $data['id'] ) ? " selected" : "", $data['description'], $data['id']);
		}
		return $ret;
	}

	// List of zones with population
	public function GetPopulatedZones()
	{
		global $eq2;

		$eq2->SQLQuery = "SELECT DISTINCT z.id as zid, z.description, z.name FROM `".DEV_DB."`.zones z JOIN `".DEV_DB."`.spawn_location_placement slp ON z.id = slp.zone_id GROUP BY z.id";
		return $eq2->RunQueryMulti();
	}

	// These <options> build the zone filter showing only populated zones in the database
	public function GetPopulatedZoneOptions()
	{
		global $eq2;
		
		// add this to the querystring only if page= is iset
		$Page = ( strlen($_GET['page']) > 0 ) ? sprintf("%s?page=%s&", $eq2->GetPHPScriptName(), $_GET['page']) : sprintf("%s?", $eq2->GetPHPScriptName());
			
		$eq2->SQLQuery = sprintf("SELECT z.id,z.name,z.description FROM `".DEV_DB."`.zones z WHERE z.id IN (SELECT DISTINCT zone_id FROM `".DEV_DB."`.spawn_location_placement ) ORDER BY z.description");
		
		$results = $eq2->RunQueryMulti();
		
		if( is_array($results) )
		{
			$ret = "";
			foreach($results as $data)
				$ret .= sprintf('<option value="%szone=%s"%s>%s (%s)</option>', $Page, $data['id'], ( $this->zone_id == $data['id'] ) ? " selected" : "", $data['description'], $data['name']);
		}
		return $ret;
	}

	public function GetZoneData()
	{
		global $eq2;
		if(!is_numeric($this->zone_id)){
			$this->zone_id = 0;
		}
		
		$eq2->SQLQuery = sprintf("SELECT * FROM `".DEV_DB."`.zones WHERE id = %s", $this->zone_id);
		return $eq2->RunQuerySingle();
	}
	
	public function GetZoneName($id = 0) 
	{
		global $eq2;
		
		if( $this->zone_id > 0 )
			$id = $this->zone_id;
			
		$eq2->SQLQuery = sprintf("SELECT name FROM `".DEV_DB."`.zones WHERE id = %s", $id);
		$data = $eq2->RunQuerySingle();
		return ( strlen($data['name']) > 0 ) ? $data['name'] : "Not Found.";
	}	

	public function GetZoneOptionsByID($id = 0) 
	{
		global $eq2;

		if ($id == 0) $id = $this->zone_id;

		$eq2->SQLQuery = "SELECT id, description FROM `".DEV_DB."`.zones ORDER BY description";
		$rows = $eq2->RunQueryMulti();
		
		$ret = "";
		foreach($rows as $row) 
			$ret .= sprintf('<option value="%s"%s>%s (%s)</option>', $row['id'], ( $id == $row['id'] ) ? " selected" : "", $row['description'], $row['id']);

		return $ret;
	}
	
	function processPurgeZone($id)
	{
		global $eq2;
		
		if( $id ) 
		{
			// purge 1 zone
			
			// Step 1: Idenfity all spawn_location_placements in the zone to purge
			$eq2->SQLQuery = sprintf("SELECT DISTINCT slp.spawn_location_id, sle.spawn_id " . 
															 "FROM `".DEV_DB."`.spawn_location_placement slp " . 
															 "JOIN `".DEV_DB."`.spawn_location_entry sle ON slp.spawn_location_id = sle.spawn_location_id " . 
															 "WHERE slp.zone_id = %s", $id);
			$results = $eq2->RunQueryMulti();
			
			if( is_array($results) )
			{
				foreach($results as $data) 
				{
					$placement_array[] = $data['spawn_location_id'];
					$spawn_id_array[] = $data['spawn_id'];
				}
			}
			
			// hack to get the table_name and object_id data for logging
			$eq2->ObjectID = "PurgeZone: " . $this->zone_name;
			$eq2->TableName = "spawn_location_name";

			// Step 2: Delete all spawn_location_name, entry, placement, group, association data collected in step 1
			$eq2->SQLQuery = sprintf("DELETE FROM `".DEV_DB."`.spawn_location_name WHERE id IN (%s)", implode(",", $placement_array));
			$eq2->RunQuery();

			// Step 3: Delete spawn records
			$eq2->TableName = "spawn";
			$eq2->SQLQuery = sprintf("DELETE FROM `".DEV_DB."`.spawn WHERE id IN (%s)", implode(",", $spawn_id_array));
			$eq2->RunQuery();

			// Step 4: Un-set all Processed records in this zone so they can be re-migrated
			$eq2->SQLQuery = sprintf("UPDATE `".RAW_DB."`.spawn_location_placement " . 
															 "SET processed = 0 " . 
															 "WHERE processed = 1 AND spawn_location_id IN (" .
																																							"SELECT spawn_location_id " . 
																																							"FROM `".RAW_DB."`.spawn_location_entry " . 
																																							"WHERE spawn_id IN (%s)" . 
																																							")", 
															 implode(",", $spawn_id_array));
			$eq2->RunQuery(false);
			
			$eq2->SQLQuery = sprintf("UPDATE `".RAW_DB."`.spawn " . 
															 "SET processed = 0 " . 
															 "WHERE processed = 1 AND id IN (%s)", 
															 implode(",", $spawn_id_array));
			$eq2->RunQuery(false);

		}
		else if( $id === 0 )
		{
			// purge all zones
			die("purge all - not yet!<br>");
		}
	}

	public function GetZonesMatching()
	{
		global $eq2;
		
		if( strlen($_POST['txtSearch']) > 0 )
		{
			$search = $eq2->SQLEscape($_POST['txtSearch']);
			$eq2->SQLQuery = "SELECT * FROM `".DEV_DB."`.zones WHERE (name RLIKE '".$search."') OR (description RLIKE '".$search."') OR (file RLIKE '".$search."') OR (lua_script RLIKE '".$search."') ORDER BY name";
			return $eq2->RunQueryMulti();
		}
	}
	
	public function PrintOffsiteLinks()
	{
	}

	public function GetZoneScriptName($id) {
		global $eq2;

		$row = $eq2->RunQuerySingle(
			sprintf("SELECT lua_script FROM `%s`.zones WHERE id = %s",
			DEV_DB, $id)
		);

		return $row['lua_script'];
	}

	public function DisplayInstanceTypeDropdown($zone)
	{
		$INSTANCE_TYPE_OPTS = array("NONE"=>"None", 
							"GROUP_LOCKOUT_INSTANCE"=>"Group Lockout",
							"GROUP_PERSIST_INSTANCE"=>"Group Persistent",
							"RAID_LOCKOUT_INSTANCE"=>"Raid Lockout",
							"RAID_PERSIST_INSTANCE"=>"Raid Persistent",
							"SOLO_LOCKOUT_INSTANCE"=>"Solo Lockout",
							"SOLO_PERSIST_INSTANCE"=>"Solo Persistent",
							"TRADESKILL_INSTANCE"=>"Tradeskill",
							"PUBLIC_INSTANCE"=>"Public",
							"PERSONAL_HOUSE_INSTANCE"=>"Player House",
							"GUILD_HOUSE_INSTANCE"=>"Guild Hall",
							"QUEST_INSTANCE"=>"Quest");


		echo '<select name="zones|instance_type">';
		
		foreach ($INSTANCE_TYPE_OPTS as $k=>$v) {
			$selected = $zone['instance_type'] == $k ? " selected" : "";
			printf('<option value="%s"%s>%s</option>', $k, $selected, $v);
		}

		echo '</select>';
	}

	public function DoesZoneExist($id) {
		global $eq2;

		$row = $eq2->RunQuerySingle("SELECT COUNT('id') as cnt FROM `".DEV_DB."`.zones WHERE id = '".$eq2->SQLEscape($id)."'");

		return $row['cnt'] == 1;
	}

	public function PrintNewZoneForm() {
		?>
		<form method="post" name="NewZoneForm">
		<fieldset align="center" style="width:fit-content;">
	<table>
		<tr>
			<td>
			<strong>Insert New Zone</strong>
			</td>
		</tr>
		<tr>
			<td>
			<label>Name:</label>
			<input type="text" name="NEWZONENAME" />
			</td>
		</tr>
		<tr>
			<td>
				<input type="submit" class="submit" name="cmd" value="Insert"/>
			</td>
		</tr>
	</table>
	</fieldset>
	</form>
	<?php
	}

	public function InsertNewZone() {
		global $eq2;

		$name = $_POST["NEWZONENAME"] ?? "";

		if (strlen($name) == 0) {
			$eq2->AddStatus("Must provide a new zone name!");
			return;
		}

		$shortname = preg_replace("/[^\\d\\w]/", "", $name);

		$eq2->SQLQuery = sprintf("INSERT INTO `%s`.zones (`name`, `description`) VALUES (\"%s\", \"%s\")", DEV_DB, $shortname, $eq2->SQLEscape($name));
		$eq2->RunQuery();

		header(sprintf("Location: zones.php?zone=%s&tab=zones", $eq2->db->sql_last_insert_id()));
		exit;
	}

	public function PreUpdate(){
		$tab = $_GET['tab'] ?? "zones";

		if ($tab == "locations") {
			$_POST['locations|discovery'] = isset($_POST['locations|discovery']) ? 1 : 0;
			$_POST['locations|include_y'] = isset($_POST['locations|include_y']) ? 1 : 0;
		}
		else if ($tab == "zones") {
			foreach ($this->eq2ZoneFlags as $flag) {
				$field = 'zones|'.$flag;
				$_POST[$field] = isset($_POST[$field]) ? 1 : 0;
			}
		}
	}

	public function PreInsert(){
		global $eq2;
		$tab = $_GET['tab'] ?? "zones";

		if( $_GET['zone'] == "add" && $eq2->CheckAccess(G_DEVELOPER)) {
			$this->InsertNewZone();
		}
		else if ($tab == "locations") {
			$_POST['locations|discovery|new'] = isset($_POST['locations|discovery|new']) ? 1 : 0;
			$_POST['locations|include_y|new'] = isset($_POST['locations|include_y|new']) ? 1 : 0;
		} 
	}
}
?>