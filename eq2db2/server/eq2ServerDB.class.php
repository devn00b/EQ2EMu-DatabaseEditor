<?php
/*  
    EQ2Editor:  Everquest II Database Editor v2.0
    Copyright (C) 2009  EQ2EMulator Development Team (http://www.eq2emulator.net or https://www.eq2emu.com))

    This file is part of EQ2Editor.

    EQ2Editor is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    EQ2Editor is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with EQ2Editor.  If not, see <http://www.gnu.org/licenses/>.
*/
if (!defined('IN_EDITOR'))
{
	die("Hack attempt recorded.");
}

class eq2ServerDB
{

	public function __construct()
	{
		global $eq2;
		// Transfer instance of eq2db to local db member
		$this->db = $eq2->eq2db;
	}
	
	
	public function GetStartingItemsData()
	{
		$sql = sprintf("SELECT * FROM ".$GLOBALS['db_name'].".starting_items;");
		return $this->db->RunQueryMulti($sql);
	}
	
	
	public function GetEntityCommands($name)
	{
		if ($name != "all")
		{
			$item_name = $this->db->SQLEscape($name);
			$sql = sprintf("SELECT * FROM " . $GLOBALS['db_name'] . ".entity_commands WHERE command_text RLIKE '%s' ORDER BY id;", $item_name);
		}
		else
		{
			$sql = "SELECT * FROM " . $GLOBALS['db_name'] . ".entity_commands ORDER BY id LIMIT 0,100;";
		}
		return $this->db->RunQueryMulti($sql);
	}
	
	
	public function GetNextEntityCommandID()
	{
		$sql = "SELECT max(`id`) as id FROM " . $GLOBALS['db_name'] . ".entity_commands";
		$data = $this->db->RunQuerySingle($sql); 
		return $data['id'] + 1;
	}
	
	
	public function GetGroundSpawns()
	{
		$sql = "SELECT groundspawn_id, tablename, min_skill_level, min_adventure_level, enabled FROM " . $GLOBALS['db_name'] . ".groundspawns ORDER BY groundspawn_id;";
		return $this->db->RunQueryMulti($sql);
	}
	
	
	public function GetGroundSpawn($id)
	{
		$sql = "SELECT * FROM " . $GLOBALS['db_name'] . ".groundspawns WHERE groundspawn_id = " . $id . ";";
		return $this->db->RunQuerySingle($sql);
	}
	
	
	public function GetGroundSpawnItems($id)
	{
		$sql = "SELECT * FROM " . $GLOBALS['db_name'] . ".groundspawn_items WHERE groundspawn_id = " . $id . ";";
		return $this->db->RunQueryMulti($sql);
	}
	
	
	public function GetNextGroundSpawnID()
	{
		$sql = "SELECT max(groundspawn_id) as id FROM " . $GLOBALS['db_name'] . ".groundspawns;";
		$data = $this->db->RunQuerySingle($sql);
		return $data['id'] + 1;
	}
	
	
	public function GetLootTables()
	{
		$sql = "SELECT id, name, mincoin, maxcoin, maxlootitems FROM " . $GLOBALS['db_name'] . ".loottable ORDER BY id;";
		return $this->db->RunQueryMulti($sql);
	}
	
	public function GetLootTable($id)
	{
		$sql = "SELECT * FROM " . $GLOBALS['db_name'] . ".loottable WHERE id = " . $id .";";
		return $this->db->RunQuerySingle($sql);
	}
	
	public function GetLoottableDrops($id)
	{
		$sql = "SELECT ld.*, i.`name`, i.tier, i.icon FROM " . $GLOBALS['db_name'] . ".lootdrop ld INNER JOIN " . $GLOBALS['db_name'] . ".items i ON ld.item_id = i.id WHERE loot_table_id = " . $id . ";";
		return $this->db->RunQueryMulti($sql);
	}
	
	public function GetNextLootTableID()
	{
		$sql = "SELECT max(id) as id FROM " . $GLOBALS['db_name'] . ".loottable;";
		$data = $this->db->RunQuerySingle($sql);
		return  $data['id'] + 1;
	}
	
	public function GetFlightPaths($name)
	{
		if ($name != "all")
		{
			$path_name = $this->db->SQLEscape($name);
			$sql = sprintf("SELECT id, zone_id, name FROM " . $GLOBALS['db_name'] . ".flight_paths WHERE name RLIKE '%s' ORDER BY id LIMIT 0,50;", $path_name);
		}
		else
		{
			$sql = "SELECT id, zone_id, name FROM " . $GLOBALS['db_name'] . ".flight_paths ORDER BY id LIMIT 0,50;";
		}
		return $this->db->RunQueryMulti($sql);
	}
	
	
	public function GetFlightPathsByZone($id)
	{
		$sql = "SELECT id, zone_id, name FROM " . $GLOBALS['db_name'] . ".flight_paths WHERE zone_id = " . $id . " ORDER BY id ASC;";
		return $this->db->RunQueryMulti($sql);
	}
	
	
	public function GetFlightPath($id)
	{
		$sql = "SELECT * FROM " . $GLOBALS['db_name'] . ".flight_paths WHERE id = " . $id . ";";
		return $this->db->RunQuerySingle($sql);
	}
	
	
	public function GetFlightPathLocations($id)
	{
		$sql = "SELECT * FROM " . $GLOBALS['db_name'] . ".flight_paths_locations WHERE flight_path = " . $id .";";
		return $this->db->RunQueryMulti($sql);
	}
	
	
	public function GetFlightPathZones()
	{
		$sql = "SELECT DISTINCT path.zone_id, z.description FROM " . $GLOBALS['db_name'] . ".flight_paths path
					JOIN " .  $GLOBALS['db_name'] . ".zones z
					ON path.zone_id = z.id;";
		return $this->db->RunQueryMulti($sql);
	}
	
	public function GetNPCSpellLists()
	{
		$sql = "SELECT spell_list_id, description FROM " . $GLOBALS['db_name'] .".spawn_npc_spells GROUP BY spell_list_id;";
		return $this->db->RunQueryMulti($sql);
	}
	
	public function GetNPCSpellList($id)
	{
		$sql = "SELECT * FROM " . $GLOBALS['db_name'] . ".spawn_npc_spells WHERE spell_list_id = " . $id . " ORDER BY id;";
		return $this->db->RunQueryMulti($sql);
	}
	
	public function GetSpellListInfo($id)
	{
		$sql = "SELECT id, spell_list_id, description FROM " . $GLOBALS['db_name'] . ".spawn_npc_spells WHERE spell_list_id = " . $id . " GROUP BY spell_list_id;";
		return $this->db->RunQuerySingle($sql);
	}
	
	public function GetNextSpellListID()
	{
		$sql = "SELECT MAX(spell_list_id) + 1 as spell_list_id FROM " . $GLOBALS['db_name'] . ".spawn_npc_spells;";
		return $this->db->RunQuerySingle($sql);
	}
	
	public function GetTransporters()
	{
		$sql = "SELECT id, transport_id, display_name, destination_zone_id FROM " . $GLOBALS['db_name'] . ".transporters;";
		return $this->db->RunQueryMulti($sql);
	}
	
	public function GetTransporter($id)
	{
		$sql = "SELECT * FROM " . $GLOBALS['db_name'] . ".transporters WHERE id = " . $id . ";";
		return $this->db->RunQuerySingle($sql);
	}
	
	public function GetNextTransportersID() {
		$sql = "SELECT MAX(id) as id FROM " . $GLOBALS['db_name'] . ".transporters;";
		$data = $this->db->RunQuerySingle($sql);
		return $data['id'] + 1;
	}
	
	public function GetRecipes($book)
	{
		if (isset($book))
		{
			$sql = "SELECT recipe_id, name, book, device FROM " . $GLOBALS['db_name'] . ".recipes WHERE book = \"" . $book . "\" ORDER BY recipe_id;";
		}
		else {
			$sql = "SELECT recipe_id, name, book, device FROM " . $GLOBALS['db_name'] . ".recipes ORDER BY recipe_id;";
		}
		return $this->db->RunQueryMulti($sql);
	}
	
	public function GetDistinctRecipeBooks()
	{
		$sql = "SELECT DISTINCT book FROM " . $GLOBALS['db_name'] . ".recipes ORDER BY book;";
		return $this->db->RunQueryMulti($sql);
	}
	
	public function GetRecipe($id)
	{
		$sql = "SELECT * FROM " . $GLOBALS['db_name'] . ".recipes WHERE id = " . $id . ";";
		return $this->db->RunQuerySingle($sql);
	}
	
	public function GetRecipeComponents($id)
	{
		$sql = "SELECT * FROM " . $GLOBALS['db_name'] . ".recipe_components WHERE recipe_id = " . $id . ";";
		return $this->db->RunQueryMulti($sql);
	}
	
	public function GetRecipeProducts($id)
	{
		$sql = "SELECT * FROM " . $GLOBALS['db_name'] . ".recipe_products WHERE recipe_id = " . $id . ";";
		return $this->db->RunQueryMulti($sql);
	}
	
	public function GetNextRecipeID()
	{
		$sql = "SELECT max(recipe_id) as id FROM " . $GLOBALS['db_name'] . ".recipes;";
		$data = $this->db->RunQuerySingle($sql);
		return $data['id'] + 1;
	}
	
	public function GetFactions()
	{
		$sql = "SELECT * FROM " . $GLOBALS['db_name'] . ".factions ORDER BY id;";
		return $this->db->RunQueryMulti($sql);
	}
	
	public function GetFaction($id)
	{
		$sql = "SELECT * FROM " . $GLOBALS['db_name'] . ".factions WHERE id = " . $id . ";";
		return $this->db->RunQuerySingle($sql);
	}
	
	public function GetFactionAlliances($id)
	{
		$sql = "SELECT * FROM " . $GLOBALS['db_name'] . ".faction_alliances WHERE faction_id = " . $id . ";";
		return $this->db->RunQueryMulti($sql);
	}
	
	public function GetNextFactionID()
	{
		$sql = "SELECT max(id) as id FROM " . $GLOBALS['db_name'] . ".factions;";
		$data = $this->db->RunQuerySingle($sql);
		return $data['id'] + 1;
	}
	
	public function GetCollections() {
		$sql = "SELECT * FROM " . $GLOBALS['db_name'] . ".collections ORDER BY id;";
		return $this->db->RunQueryMulti($sql);
	}
	
	public function GetCollection($id) {
		$sql = "SELECT * FROM " . $GLOBALS['db_name'] . ".collections WHERE id = " . $id . ";";
		return $this->db->RunQuerySingle($sql);
	}
	
	public function GetCollectionDetails($id) {
		$sql = "SELECT cd.*, i.name FROM " . $GLOBALS['db_name'] . ".collection_details cd
		INNER JOIN " . $GLOBALS['db_name'] . ".items i
		ON cd.item_id = i.id
		WHERE collection_id = " . $id .";";
		return $this->db->RunQueryMulti($sql);
	}
	
	public function GetCollectionRewards($id) {
		$sql = "SELECT * FROM " . $GLOBALS['db_name'] . ".collection_rewards WHERE collection_id = " . $id . ";";
		return $this->db->RunQueryMulti($sql);
	}

	public function GetNextCollectionID()
	{
		$sql = "SELECT max(id) as id FROM " . $GLOBALS['db_name'] . ".collections;";
		$data = $this->db->RunQuerySingle($sql);
		return $data['id'] + 1;
	}
	
	public function GetLootTableNames() {
		$sql = "SELECT id, `name` FROM " . $GLOBALS['db_name'] . ".loottable ORDER BY `name`";
		return $this->db->RunQueryMulti($sql);
	}
	
	public function GetGlobalLoot() {
		$sql = "SELECT * FROM " . $GLOBALS['db_name'] . ".loot_global";
		return $this->db->RunQueryMulti($sql);
	}

	public function GetMerchants() {
		$sql = "SELECT * FROM " . $GLOBALS['db_name'] . ".merchants";
		return $this->db->RunQueryMulti($sql);
	}

	public function GetMerchant($id) {
		$sql = "SELECT * FROM " . $GLOBALS['db_name'] . ".merchants WHERE id = " . $id . ";";
		return $this->db->RunQuerySingle($sql);
	}

	public function GetMerchantInventoryList($id) {
		$sql = "SELECT mi.*, i.name, i.tier, i.icon FROM " . $GLOBALS['db_name'] . ".merchant_inventory mi INNER JOIN " . $GLOBALS['db_name'] . ".items i ON mi.item_id = i.id WHERE inventory_id = " . $id . ";";
		return $this->db->RunQueryMulti($sql);
	}

	public function GetNextMerchantID()
	{
		$sql = "SELECT max(`id`) as id FROM " . $GLOBALS['db_name'] . ".merchants";
		$data = $this->db->RunQuerySingle($sql); 
		return $data['id'] + 1;
	}

	public function GetNextMerchantID2()
	{
		$sql = "SELECT max(`merchant_id`) as id FROM " . $GLOBALS['db_name'] . ".merchants";
		$data = $this->db->RunQuerySingle($sql); 
		return $data['id'] + 1;
	}

	public function GetNextMerchantInventoryID()
	{
		$sql = "SELECT max(`inventory_id`) as id FROM " . $GLOBALS['db_name'] . ".merchant_inventory";
		$data = $this->db->RunQuerySingle($sql); 
		return $data['id'] + 1;
	}

	public function CopyMerchant($id) {
		$merchant_id = $this->GetNextMerchantID2();
		$inventory_id = $this->GetNextMerchantInventoryID();

		$sql = "SELECT `inventory_id`, `description` FROM " . $GLOBALS['db_name'] . ".merchants WHERE id = " . $id . ";";
		$data = $this->db->RunQuerySingle($sql);
		$orig_inventory_id = $data['inventory_id'];
		$description = $data['description'];
		$description = $description . "-COPY";
		$description = $this->db->SQLEscape($description);

		$sql = "INSERT INTO " . $GLOBALS['db_name'] . ".merchants (`merchant_id`, `inventory_id`, `description`) VALUES (" . $merchant_id . ", " . $inventory_id . ", \"" . $description ."\");";
		$this->db->RunQuery("INSERT", "merchants", "MerchantCopy", $sql);

		$sql = "SELECT LAST_INSERT_ID() AS id";
		$data = $this->db->RunQuerySingle($sql);
		$new_id = $data['id'];

		$sql = "INSERT INTO " . $GLOBALS['db_name'] . ".merchant_inventory (`inventory_id`, `item_id`, `quantity`, `price_item_id`, `price_item_qty`, `price_item2_id`, `price_item2_qty`, `price_status`, `price_coins`, `price_stationcash`) SELECT " . $inventory_id . ", `item_id`, `quantity`, `price_item_id`, `price_item_qty`, `price_item2_id`, `price_item2_qty`, `price_status`, `price_coins`, `price_stationcash` FROM " . $GLOBALS['db_name'] . ".merchant_inventory WHERE inventory_id = " . $orig_inventory_id . ";";
		$this->db->RunQuery("INSERT", "merchant_inventory", "MerchantInventoryCopy", $sql);

		?>
			<script type="text/javascript">
				window.location.href = '/editors/eq2db2/index.php?page=server&type=merchants&id=<?= $new_id ?>';
			</script>
		<?php
	}

	public function GetHouses() {
		$sql = "SELECT `id`, `name` FROM " . $GLOBALS['db_name'] . ".houses";
		return $this->db->RunQueryMulti($sql);
	}

	public function GetHouse($id) {
		$sql = "SELECT * FROM " . $GLOBALS['db_name'] . ".houses WHERE id = " . $id . ";";
		return $this->db->RunQuerySingle($sql);
	}

	public function GetNextHouseID()
	{
		$sql = "SELECT max(`id`) as id FROM " . $GLOBALS['db_name'] . ".houses";
		$data = $this->db->RunQuerySingle($sql); 
		return $data['id'] + 1;
	}
}

?>