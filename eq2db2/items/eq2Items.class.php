<?php
/*  
    EQ2Editor:  Everquest II Database Editor v2.0
    Copyright (C) 2007 EQ2EMulator Development Team (http://www.eq2emulator.net or https://www.eq2emu.com)

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

class eq2Items
{

	public function __construct()
	{
		include_once("eq2ItemsDB.class.php");
		$this->db = new eq2ItemsDB();
	}
	
	/*
		
	*/
	public function Start()
	{
		global $eq2;
		global $page_lc;
		global $type;

		print('<table cellspacing="0" id="main-body" class="main">');

		// start left menu
		printf('<tr><td class="sidebar" nowrap="nowrap"><strong>Options:</strong><br />');
		
		// nav stuff here
		printf('<br /><li><a href="%s">Reload Page</a><br />', $eq2->PageLink);
		if( isset($_GET['id']) ) 
			printf('<li><a href="%s">Back</a><br />', $eq2->BackLink);
		else
			printf('<li><br />');
		
		// Help Links / Stats Details
		printf('<br /><strong>Help:</strong><br />&nbsp;');
		printf('<br /><strong>Stats:</strong><br />&nbsp;');
		
		// start main page
		printf('</td><td class="page-text"><div id="PageTitle">%s</div>', $eq2->PageTitle);
		switch($page_lc)
		{
			case "help": 
				// use common (eq2Functions) class to display non-specific data
				$eq2->DisplaySiteText($page_lc, $_GET['cat']); 
				break;
				
			default: 
				switch($type)
				{
					case "edit"			: $this->ItemEdit(); break;
					case "summary"		: break;
					default				: $this->ItemLookup(); break;
				}
				break;
		}
		
		// end page
		print('</td></tr></table>');
	}


	private function ItemLookup()
	{
		global $eq2;
		?>
		
		<form method="post">
		<table class="SearchFilters">
			<tr>
				<th colspan="4">Filters</th>
			</tr>
			<tr>
				<td class="label">Name</td>
				<td class="label">Level Range</td>
				<td class="label">Class</td>
			</tr>
			<tr>
				<td>
					<input type="text" id="txtSearch" name="txtSearch" alt="Search Criteria" autocomplete="off" class="full" value="<?= isset($_POST['txtSearch']) ? $_POST['txtSearch'] : '' ?>" onclick="this.value='';" />
					<div id="search_suggest">
					</div>
				</td>
				<td>
					<input type="text" id="minLevel" name="minLevel" alt="Minimum Level" autocomplete="off" class="small" <?php if (isset($_POST['minLevel'])) { print("value='" . $_POST['minLevel'] ."' "); } ?> />
					&nbsp;-&nbsp;
					<input type="text" id="maxLevel" name="maxLevel" alt="Maximum Level" autocomplete="off" class="small" <?php if (isset($_POST['maxLevel'])) { print("value='" . $_POST['maxLevel'] ."' "); } ?> />
				</td>
				<td>
					<?php
					print('<select name="classPicker">');
					print('<option value="">Pick a Class</option>');
		
					foreach($eq2->eq2Classes as $class=>$name)
						printf('<option value="%s"%s>%s (%s)</option>', $class, (isset($_POST['classPicker']) && !$eq2->IsStringNullOrEmpty($_POST['classPicker']) && $_POST['classPicker'] == $class) ? " selected" : "", $name, $class);
			
					print('</select>');
					?>
				</td>
				
			</tr>
			<tr>
				<td class="label">Type</td>
				<td class="label">Slot</td>
				<td class="label">Tier</td>
				<td class="label">Results</td>
			</tr>
			<tr>
				<td>
					<select name="itemTypePicker">
						<option value="">Pick a Type</option>
						<option value="1"<?php if (isset($_POST['itemTypePicker']) && $_POST['itemTypePicker'] == 1) { print(" selected"); } ?>>Normal</option>
						<option value="2"<?php if (isset($_POST['itemTypePicker']) && $_POST['itemTypePicker'] == 2) { print(" selected"); } ?>>Weapon</option>
						<option value="3"<?php if (isset($_POST['itemTypePicker']) && $_POST['itemTypePicker'] == 3) { print(" selected"); } ?>>Ranged</option>
						<option value="4"<?php if (isset($_POST['itemTypePicker']) && $_POST['itemTypePicker'] == 4) { print(" selected"); } ?>>Armor</option>
						<option value="5"<?php if (isset($_POST['itemTypePicker']) && $_POST['itemTypePicker'] == 5) { print(" selected"); } ?>>Shield</option>
						<option value="6"<?php if (isset($_POST['itemTypePicker']) && $_POST['itemTypePicker'] == 6) { print(" selected"); } ?>>Bag</option>
						<option value="7"<?php if (isset($_POST['itemTypePicker']) && $_POST['itemTypePicker'] == 7) { print(" selected"); } ?>>Skill</option>
						<option value="8"<?php if (isset($_POST['itemTypePicker']) && $_POST['itemTypePicker'] == 8) { print(" selected"); } ?>>Recipe</option>
						<option value="9"<?php if (isset($_POST['itemTypePicker']) && $_POST['itemTypePicker'] == 9) { print(" selected"); } ?>>Food</option>
						<option value="10"<?php if (isset($_POST['itemTypePicker']) && $_POST['itemTypePicker'] == 10) { print(" selected"); } ?>>Bauble</option>
						<option value="11"<?php if (isset($_POST['itemTypePicker']) && $_POST['itemTypePicker'] == 11) { print(" selected"); } ?>>House</option>
						<option value="12"<?php if (isset($_POST['itemTypePicker']) && $_POST['itemTypePicker'] == 12) { print(" selected"); } ?>>Thrown</option>
						<option value="13"<?php if (isset($_POST['itemTypePicker']) && $_POST['itemTypePicker'] == 13) { print(" selected"); } ?>>House Container</option>
						<option value="14"<?php if (isset($_POST['itemTypePicker']) && $_POST['itemTypePicker'] == 14) { print(" selected"); } ?>>Adornment</option>
						<option value="15"<?php if (isset($_POST['itemTypePicker']) && $_POST['itemTypePicker'] == 15) { print(" selected"); } ?>>Profile</option>
						<option value="16"<?php if (isset($_POST['itemTypePicker']) && $_POST['itemTypePicker'] == 16) { print(" selected"); } ?>>Pattern</option>
						<option value="17"<?php if (isset($_POST['itemTypePicker']) && $_POST['itemTypePicker'] == 17) { print(" selected"); } ?>>Armor Set</option>
						<option value="18"<?php if (isset($_POST['itemTypePicker']) && $_POST['itemTypePicker'] == 18) { print(" selected"); } ?>>Book</option>
						<option value="19"<?php if (isset($_POST['itemTypePicker']) && $_POST['itemTypePicker'] == 19) { print(" selected"); } ?>>Decoration</option>
						<option value="20"<?php if (isset($_POST['itemTypePicker']) && $_POST['itemTypePicker'] == 20) { print(" selected"); } ?>>Dungeon Maker</option>
						<option value="21"<?php if (isset($_POST['itemTypePicker']) && $_POST['itemTypePicker'] == 21) { print(" selected"); } ?>>Marketplace</option>
					</select>
				</td>
				<td>
					<select name="itemSlotPicker">
						<option value="">Pick a Slot</option>
						<option value="0"<?php if (isset($_POST['itemSlotPicker']) && !$eq2->IsStringNullOrEmpty($_POST['itemSlotPicker']) && $_POST['itemSlotPicker'] == 0) { print (" selected"); } ?>>Primary</option>
						<option value="1"<?php if (isset($_POST['itemSlotPicker']) && $_POST['itemSlotPicker'] == 1) { print (" selected"); } ?>>Secondary</option>
						<option value="2"<?php if (isset($_POST['itemSlotPicker']) && $_POST['itemSlotPicker'] == 2) { print (" selected"); } ?>>Head</option>
						<option value="3"<?php if (isset($_POST['itemSlotPicker']) && $_POST['itemSlotPicker'] == 3) { print (" selected"); } ?>>Chest</option>
						<option value="4"<?php if (isset($_POST['itemSlotPicker']) && $_POST['itemSlotPicker'] == 4) { print (" selected"); } ?>>Shoulders</option>
						<option value="5"<?php if (isset($_POST['itemSlotPicker']) && $_POST['itemSlotPicker'] == 5) { print (" selected"); } ?>>Forearms</option>
						<option value="6"<?php if (isset($_POST['itemSlotPicker']) && $_POST['itemSlotPicker'] == 6) { print (" selected"); } ?>>Hands</option>
						<option value="7"<?php if (isset($_POST['itemSlotPicker']) && $_POST['itemSlotPicker'] == 7) { print (" selected"); } ?>>Legs</option>
						<option value="8"<?php if (isset($_POST['itemSlotPicker']) && $_POST['itemSlotPicker'] == 8) { print (" selected"); } ?>>Feet</option>
						<option value="9"<?php if (isset($_POST['itemSlotPicker']) && $_POST['itemSlotPicker'] == 9) { print (" selected"); } ?>>Left Ring</option>
						<option value="10"<?php if (isset($_POST['itemSlotPicker']) && $_POST['itemSlotPicker'] == 10) { print (" selected"); } ?>>Right Ring</option>
						<option value="11"<?php if (isset($_POST['itemSlotPicker']) && $_POST['itemSlotPicker'] == 11) { print (" selected"); } ?>>Left Ear</option>
						<option value="12"<?php if (isset($_POST['itemSlotPicker']) && $_POST['itemSlotPicker'] == 12) { print (" selected"); } ?>>Right Ear</option>
						<option value="13"<?php if (isset($_POST['itemSlotPicker']) && $_POST['itemSlotPicker'] == 13) { print (" selected"); } ?>>Neck</option>
						<option value="14"<?php if (isset($_POST['itemSlotPicker']) && $_POST['itemSlotPicker'] == 14) { print (" selected"); } ?>>Left Wrist</option>
						<option value="15"<?php if (isset($_POST['itemSlotPicker']) && $_POST['itemSlotPicker'] == 15) { print (" selected"); } ?>>Right Wrist</option>
						<option value="16"<?php if (isset($_POST['itemSlotPicker']) && $_POST['itemSlotPicker'] == 16) { print (" selected"); } ?>>Range</option>
						<option value="17"<?php if (isset($_POST['itemSlotPicker']) && $_POST['itemSlotPicker'] == 17) { print (" selected"); } ?>>Ammo</option>
						<option value="18"<?php if (isset($_POST['itemSlotPicker']) && $_POST['itemSlotPicker'] == 18) { print (" selected"); } ?>>Waist</option>
						<option value="19"<?php if (isset($_POST['itemSlotPicker']) && $_POST['itemSlotPicker'] == 19) { print (" selected"); } ?>>Cloak</option>
						<option value="20"<?php if (isset($_POST['itemSlotPicker']) && $_POST['itemSlotPicker'] == 20) { print (" selected"); } ?>>Charm Slot 1</option>
						<option value="21"<?php if (isset($_POST['itemSlotPicker']) && $_POST['itemSlotPicker'] == 21) { print (" selected"); } ?>>Charm Slot 2</option>
						<option value="22"<?php if (isset($_POST['itemSlotPicker']) && $_POST['itemSlotPicker'] == 22) { print (" selected"); } ?>>Food</option>
						<option value="23"<?php if (isset($_POST['itemSlotPicker']) && $_POST['itemSlotPicker'] == 23) { print (" selected"); } ?>>Drink</option>
						<option value="24"<?php if (isset($_POST['itemSlotPicker']) && $_POST['itemSlotPicker'] == 24) { print (" selected"); } ?>>Textures</option>
						<option value="25"<?php if (isset($_POST['itemSlotPicker']) && $_POST['itemSlotPicker'] == 25) { print (" selected"); } ?>>Hair</option>
						<option value="26"<?php if (isset($_POST['itemSlotPicker']) && $_POST['itemSlotPicker'] == 26) { print (" selected"); } ?>>Beard</option>
						<option value="27"<?php if (isset($_POST['itemSlotPicker']) && $_POST['itemSlotPicker'] == 27) { print (" selected"); } ?>>Wings</option>
						<option value="28"<?php if (isset($_POST['itemSlotPicker']) && $_POST['itemSlotPicker'] == 28) { print (" selected"); } ?>>Naked Chest</option>
						<option value="29"<?php if (isset($_POST['itemSlotPicker']) && $_POST['itemSlotPicker'] == 29) { print (" selected"); } ?>>Naked Legs</option>
						<option value="30"<?php if (isset($_POST['itemSlotPicker']) && $_POST['itemSlotPicker'] == 30) { print (" selected"); } ?>>Back</option>
					</select>
				</td>
				<td>
					<select name="itemTierPicker">
						<option value="">Pick a Tier</option>
						<option value="1"<?php if (isset($_POST['itemTierPicker']) && $_POST['itemTierPicker'] == 1) { print(" selected"); } ?>>None</option>
						<option value="2"<?php if (isset($_POST['itemTierPicker']) && $_POST['itemTierPicker'] == 2) { print(" selected"); } ?>>Uncommon</option>
						<option value="3"<?php if (isset($_POST['itemTierPicker']) && $_POST['itemTierPicker'] == 3) { print(" selected"); } ?>>Treasured</option>
						<option value="4"<?php if (isset($_POST['itemTierPicker']) && $_POST['itemTierPicker'] == 4) { print(" selected"); } ?>>Legendary</option>
						<option value="5"<?php if (isset($_POST['itemTierPicker']) && $_POST['itemTierPicker'] == 5) { print(" selected"); } ?>>Fabled</option>
						<option value="6"<?php if (isset($_POST['itemTierPicker']) && $_POST['itemTierPicker'] == 6) { print(" selected"); } ?>>Mythical</option>
					</select>
				</td>
				<td>
					<select name="itemResultsPicker">
						<option<?php if (isset($_POST['itemResultsPicker']) && $_POST['itemResultsPicker'] == '25') { print(" selected"); } ?>>25</option>
						<option<?php if (!isset($_POST['itemResultsPicker']) || (isset($_POST['itemResultsPicker']) && $_POST['itemResultsPicker'] == '50')) { print(" selected"); } ?>>50</option>
						<option<?php if (isset($_POST['itemResultsPicker']) && $_POST['itemResultsPicker'] == '75') { print(" selected"); } ?>>75</option>
						<option<?php if (isset($_POST['itemResultsPicker']) && $_POST['itemResultsPicker'] == '100') { print(" selected"); } ?>>100</option>
						<option<?php if (isset($_POST['itemResultsPicker']) && $_POST['itemResultsPicker'] == '150') { print(" selected"); } ?>>150</option>
						<option<?php if (isset($_POST['itemResultsPicker']) && $_POST['itemResultsPicker'] == '200') { print(" selected"); } ?>>200</option>
						<option<?php if (isset($_POST['itemResultsPicker']) && $_POST['itemResultsPicker'] == '250') { print(" selected"); } ?>>250</option>
						<option<?php if (isset($_POST['itemResultsPicker']) && $_POST['itemResultsPicker'] == '300') { print(" selected"); } ?>>300</option>
						<option<?php if (isset($_POST['itemResultsPicker']) && $_POST['itemResultsPicker'] == '350') { print(" selected"); } ?>>350</option>
						<option<?php if (isset($_POST['itemResultsPicker']) && $_POST['itemResultsPicker'] == '400') { print(" selected"); } ?>>400</option>
						<option<?php if (isset($_POST['itemResultsPicker']) && $_POST['itemResultsPicker'] == '450') { print(" selected"); } ?>>450</option>
						<option<?php if (isset($_POST['itemResultsPicker']) && $_POST['itemResultsPicker'] == '500') { print(" selected"); } ?>>500</option>
						<option<?php if (isset($_POST['itemResultsPicker']) && $_POST['itemResultsPicker'] == '550') { print(" selected"); } ?>>550</option>
						<option<?php if (isset($_POST['itemResultsPicker']) && $_POST['itemResultsPicker'] == '600') { print(" selected"); } ?>>600</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="4" align="center">
					<input type="submit" name="cmd" value="Search" alt="Run Search" class="submit" />
					<input type="button" value="Clear" class="submit" onclick="dosub('index.php?page=items');" />
					
				</td>
			</tr>
		</table>
		</form>
		
		<br/> <br/>
		
		<table class="SearchResultsTemp">
			<tr class="Title">
				<th width="100px">Item ID</th>
				<th width="45px">Icon</th>
				<th>Name</th>
				<th>Level</th>
			</tr>
			<?php
			$items = $this->db->GetItems();
			$count = 0;
			foreach ($items as $item) {
				$count++;
			?>
				<tr>
					<td><?= $item['id'] ?></td>
					<td><img src="characters/eq2Icon.php?id=<?= $item['icon'] ?>" /></td>
					<td><a href="<?= $eq2->PageLink ?>&type=edit&id=<?= $item['id'] ?>"><?= $item['name'] ?></a><br/><br/><?= $item['description'] ?></td>
					<td><?= $item['adventure_default_level'] ?> / <?= $item['tradeskill_default_level'] ?></td>
				</tr>
			<?php			
			}
			if ((isset($_POST['itemResultsPicker']) && $count >= $_POST['itemResultsPicker']) || (!isset($_POST['itemResultsPicker']) && $count >= 50)) {
				?>
				<tr>
					<td colspan="7" style="text-align: center; font-style: italic; color: #F00;">Max rows displayed (<?= isset($_POST['itemResultsPicker']) ? $_POST['itemResultsPicker'] : "50" ?>) - apply additional filters</td>
				</tr>
				<?php
			}
			else {
				?>
				<tr>
					<td colspan="7" style="text-align: center; font-style: italic;"><?= $count ?> rows returned</td>
				</tr>
				<?php
			}
			?>
		</table>
		<?php
	}
	
	private function ItemEdit() {
		global $eq2;
		
		$current_tab_idx = ( isset($_GET['tab']) ) ? $_GET['tab'] : 'item';
		$tab_array = array(
			'item'			=> 'Item',
			'details'		=> 'Details',
			'stats'			=> 'Stats',
			'effects'		=> 'Effects',
			'appearances'	=> 'Appearances',
			'script'		=> 'Script'
		);
		$eq2->TabGenerator2($current_tab_idx, $tab_array);
		/*
			Details		: Item details
			Stats		: Stats the item has if any
			Script		: Item script
		*/
		
		
		switch($current_tab_idx)
		{
			case "details"		: $this->Item_Details();	break;
			case "stats"		: $this->Item_Stats();		break;
			case "effects"		: $this->Item_Effects();	break;
			case "appearances"	: $this->Item_Appearances();break;
			case "script"		: $this->Item_Script();		break;
			case "item"			:
			default				: $this->Item_Base();		break; 
		}
	}
	
	private function Item_Base() {
		global $eq2;
		
		if (isset($_POST['cmd'])) {
			switch($_POST['cmd']) {
				case "Update": $eq2->ProcessUpdate(); break;
				case "Delete": $eq2->ProcessDelete(); break;
			}
		}
		
		$item = $this->db->GetItem();
		if (!is_array($item)) {
			$eq2->AddStatus("Item not found.");
			return;
		}
		?>
		
		<div id="Editor">
			<form method="post">
			<table style="width: 770px; border-collapse: collapse; border-spacing: 0;">
				<tr>
					<td class="Title" colspan="3" align="center">Editing Item: <?= $item['name'] ?></td>
				</tr>
				<tr>
					<td colspan="2">
						<fieldset>
							<legend>General</legend>
							<table style="width: 100%; border: 0; border-collapse: collapse; border-spacing: 0;">
								<tr>
									<td align="right">ID:</td>
									<td>
										<input type="text" name="items|id" value="<?= $item['id'] ?>" readonly style="width:55px;background-color:#ddd;" />
										<input type="hidden" name="orig_id" value="<?= $item['id'] ?>" />
									</td>
									<td align="right">SOE ID:</td>
									<td>
										<input type="text" name="items|soe_item_id" value="<?= $item['soe_item_id'] ?>" readonly style="width:55px;background-color:#ddd;" />
										<input type="hidden" name="orig_soe_item_id" value="<?= $item['soe_item_id'] ?>" />
									</td>
									<td align="right">SOE CRC:</td>
									<td>
										<input type="text" name="items|soe_item_crc" value="<?= $item['soe_item_crc'] ?>" readonly style="width:55px;background-color:#ddd;" />
										<input type="hidden" name="orig_soe_item_crc" value="<?= $item['soe_item_crc'] ?>" />
									</td>
									<td align="right">Item Type:</td>
									<td>
										<select style="width:100px;" name="items|item_type">
											<option<?php if ($item['item_type'] == "Normal") printf(" selected")?>>Normal</option>
											<option<?php if ($item['item_type'] == "Weapon") printf(" selected")?>>Weapon</option>
											<option<?php if ($item['item_type'] == "Ranged") printf(" selected")?>>Ranged</option>
											<option<?php if ($item['item_type'] == "Armor") printf(" selected")?>>Armor</option>
											<option<?php if ($item['item_type'] == "Shield") printf(" selected")?>>Shield</option>
											<option<?php if ($item['item_type'] == "Bag") printf(" selected")?>>Bag</option>
											<option<?php if ($item['item_type'] == "Scroll") printf(" selected")?>>Scroll</option>
											<option<?php if ($item['item_type'] == "Recipe") printf(" selected")?>>Recipe</option>
											<option<?php if ($item['item_type'] == "Food") printf(" selected")?>>Food</option>
											<option<?php if ($item['item_type'] == "Bauble") printf(" selected")?>>Bauble</option>
											<option<?php if ($item['item_type'] == "House") printf(" selected")?>>House</option>
											<option<?php if ($item['item_type'] == "Thrown") printf(" selected")?>>Thrown</option>
											<option<?php if ($item['item_type'] == "House Container") printf(" selected")?>>House Container</option>
											<option<?php if ($item['item_type'] == "Adornment") printf(" selected")?>>Adornment</option>
											<option<?php if ($item['item_type'] == "Profile") printf(" selected")?>>Profile</option>
											<option<?php if ($item['item_type'] == "Pattern Set") printf(" selected")?>>Pattern Set</option>
											<option<?php if ($item['item_type'] == "Item Set") printf(" selected")?>>Item Set</option>
											<option<?php if ($item['item_type'] == "Book") printf(" selected")?>>Book</option>
											<option<?php if ($item['item_type'] == "Decoration") printf(" selected")?>>Decoration</option>
											<option<?php if ($item['item_type'] == "Dungeon Maker") printf(" selected")?>>Dungeon Maker</option>
											<option<?php if ($item['item_type'] == "Marketplace") printf(" selected")?>>Marketplace</option>
										</select>
										<input type="hidden" name="orig_item_type" value="<?= $item['item_type'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="right">Name:</td>
									<td colspan="5">
										<input type="text" name="items|name" class="full" value="<?= $item['name'] ?>" />
										<input type="hidden" name="orig_name" value="<?= $item['name'] ?>" />
									</td>
									<td align="right">Icon:</td>
									<td>
										<script>
										function SetIcon() {
											var iconID = document.getElementById("ItemIcon").value;
											var icon = parseInt(iconID, 10);
											document.getElementById("ItemIconImage").style.backgroundImage = 'url(characters/eq2Icon.php?id=' + icon + ')';
										}
										</script>
										<input Type="text" id="ItemIcon" style="width:65px; float:left;" name="items|icon" value="<?= $item['icon'] ?>" OnBlur="SetIcon()" />
										<input Type="hidden" name="orig_icon" value="<?= $item['icon'] ?>" />
										<div id="ItemIconImage" style="width:42px; height:42px; float:right; background-image: url(characters/eq2Icon.php?id=<?= $item['icon'] ?>);"></div>
									</td>
								</tr>
								<tr>
									<td align="right">Tier:</td>
									<td>
										<input Type="text" name="items|tier" class="medium" value="<?= $item['tier'] ?>" />
										<input Type="hidden" name="orig_tier" value="<?= $item['tier'] ?>" />
									</td>
									<td align="right">Count:</td>
									<td>
										<input Type="text" name="items|count" class="medium" value="<?= $item['count'] ?>" />
										<input Type="hidden" name="orig_count" value="<?= $item['count'] ?>" />
									</td>
									<td align="right">Stack Count:</td>
									<td>
										<input Type="text" name="items|stack_count" class="medium" value="<?= $item['stack_count'] ?>" />
										<input Type="hidden" name="orig_stack_count" value="<?= $item['stack_count'] ?>" />
									</td>
									<td align="right">Max Charges:</td>
									<td>
										<input Type="text" name="items|max_charges" class="medium" value="<?= $item['max_charges'] ?>" />
										<input Type="hidden" name="orig_max_charges" value="<?= $item['max_charges'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="right">Weight:</td>
									<td>
										<input Type="text" name="items|weight" class="medium" value="<?= $item['weight'] ?>" />
										<input Type="hidden" name="orig_weight" value="<?= $item['weight'] ?>" />
									</td>
									<td align="right">Sell Price:</td>
									<td>
										<input Type="text" name="items|sell_price" class="medium" value="<?= $item['sell_price'] ?>" />
										<input Type="hidden" name="orig_sell_price" value="<?= $item['sell_price'] ?>" />
									</td>
									<td align="right">Set Name:</td>
									<td colspan="3">
										<input Type="text" name="items|set_name" class="full" value="<?= $item['set_name'] ?>" />
										<input Type="hidden" name="orig_set_name" value="<?= $item['set_name'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="right">Description:</td>
									<td colspan="7">
										<textarea name="items|description" style="font:12px Arial, Helvetica, sans-serif; width:99%; height: 50px;"><?php print($item['description']); ?></textarea>
										<input type="hidden" name="orig_description" value="<?= $item['description'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="right">LUA Script:</td>
									<td colspan="7">
										<input type="text" name="items|lua_script" class="full" value="<?= $item['lua_script'] ?>" />
										<input type="hidden" name="orig_lua_script" value="<?= $item['lua_script'] ?>" />
									</td>
								</tr>
								
								<!-- 
								`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
								`soe_item_id` INT(10) NOT NULL DEFAULT '0',
								`soe_item_crc` INT(10) NOT NULL DEFAULT '0',	
								`item_type` ENUM('Normal','Weapon','Ranged','Armor','Shield','Bag','Scroll','Recipe','Food','Bauble','House','Thrown','House Container','Adornment','Profile','Pattern Set','Item Set','Book','Decoration','Dungeon Maker','Marketplace') NOT NULL DEFAULT 'Normal' COLLATE 'latin1_general_ci',
								
								`name` VARCHAR(100) NOT NULL DEFAULT '' COLLATE 'latin1_general_ci',
								`icon` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
								
								`tier` TINYINT(3) UNSIGNED NOT NULL DEFAULT '1',
								`count` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
								`stack_count` TINYINT(3) UNSIGNED NOT NULL DEFAULT '1',
								`max_charges` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',

								`weight` INT(10) UNSIGNED NOT NULL DEFAULT '0',
								`sell_price` INT(10) UNSIGNED NOT NULL DEFAULT '0',
								`set_name` VARCHAR(250) NULL DEFAULT NULL COLLATE 'latin1_general_ci',

								`description` TEXT NULL COLLATE 'latin1_general_ci',
								`lua_script` VARCHAR(255) NULL DEFAULT NULL COLLATE 'latin1_general_ci',
								-->
								
							</table>							
						</fieldset>
					</td>
					<td rowspan="4" style="vertical-align: top; height:1px"> <!-- used height of 1px and fieldset height of 97% to make the toggles fill out the rowspan -->
						<fieldset style="height:97%;">
							<legend>Toggles</legend>
							<table style="border: 0; border-collapse: collapse; border-spacing: 0;">
								<tr>
									<td align="right">show_name</td>
									<td>
										<select class="yesno" name="items|show_name">
											<option value="0"<?php if( $item['show_name'] == 0 ) print(" selected") ?>>false</option>
											<option value="1"<?php if( $item['show_name'] > 0 ) print(" selected") ?>>true</option>
										</select>
										<input type="hidden" name="orig_show_name" value="<?= $item['show_name'] ?>" />
									</td>
								
									<td align="right">attuneable</td>
									<td>
										<select class="yesno" name="items|attuneable">
											<option value="0"<?php if( $item['attuneable'] == 0 ) print(" selected") ?>>false</option>
											<option value="1"<?php if( $item['attuneable'] > 0 ) print(" selected") ?>>true</option>
										</select>
										<input type="hidden" name="orig_attuneable" value="<?= $item['attuneable'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="right">artifact</td>
									<td>
										<select class="yesno" name="items|artifact">
											<option value="0"<?php if( $item['artifact'] == 0 ) print(" selected") ?>>false</option>
											<option value="1"<?php if( $item['artifact'] > 0 ) print(" selected") ?>>true</option>
										</select>
										<input type="hidden" name="orig_artifact" value="<?= $item['artifact'] ?>" />
									</td>
								
									<td align="right">lore</td>
									<td>
										<select class="yesno" name="items|lore">
											<option value="0"<?php if( $item['lore'] == 0 ) print(" selected") ?>>false</option>
											<option value="1"<?php if( $item['lore'] > 0 ) print(" selected") ?>>true</option>
										</select>
										<input type="hidden" name="orig_lore" value="<?= $item['lore'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="right">temporary</td>
									<td>
										<select class="yesno" name="items|temporary">
											<option value="0"<?php if( $item['temporary'] == 0 ) print(" selected") ?>>false</option>
											<option value="1"<?php if( $item['temporary'] > 0 ) print(" selected") ?>>true</option>
										</select>
										<input type="hidden" name="orig_temporary" value="<?= $item['temporary'] ?>" />
									</td>
								
									<td align="right">notrade</td>
									<td>
										<select class="yesno" name="items|notrade">
											<option value="0"<?php if( $item['notrade'] == 0 ) print(" selected") ?>>false</option>
											<option value="1"<?php if( $item['notrade'] > 0 ) print(" selected") ?>>true</option>
										</select>
										<input type="hidden" name="orig_notrade" value="<?= $item['notrade'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="right">novalue</td>
									<td>
										<select class="yesno" name="items|novalue">
											<option value="0"<?php if( $item['novalue'] == 0 ) print(" selected") ?>>false</option>
											<option value="1"<?php if( $item['novalue'] > 0 ) print(" selected") ?>>true</option>
										</select>
										<input type="hidden" name="orig_novalue" value="<?= $item['novalue'] ?>" />
									</td>
								
									<td align="right">nozone</td>
									<td>
										<select class="yesno" name="items|nozone">
											<option value="0"<?php if( $item['nozone'] == 0 ) print(" selected") ?>>false</option>
											<option value="1"<?php if( $item['nozone'] > 0 ) print(" selected") ?>>true</option>
										</select>
										<input type="hidden" name="orig_nozone" value="<?= $item['nozone'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="right">body_drop</td>
									<td>
										<select class="yesno" name="items|body_drop">
											<option value="0"<?php if( $item['body_drop'] == 0 ) print(" selected") ?>>false</option>
											<option value="1"<?php if( $item['body_drop'] > 0 ) print(" selected") ?>>true</option>
										</select>
										<input type="hidden" name="orig_body_drop" value="<?= $item['body_drop'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="right">nodestroy</td>
									<td>
										<select class="yesno" name="items|nodestroy">
											<option value="0"<?php if( $item['nodestroy'] == 0 ) print(" selected") ?>>false</option>
											<option value="1"<?php if( $item['nodestroy'] > 0 ) print(" selected") ?>>true</option>
										</select>
										<input type="hidden" name="orig_nodestroy" value="<?= $item['nodestroy'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="right">crafted</td>
									<td>
										<select class="yesno" name="items|crafted">
											<option value="0"<?php if( $item['crafted'] == 0 ) print(" selected") ?>>false</option>
											<option value="1"<?php if( $item['crafted'] > 0 ) print(" selected") ?>>true</option>
										</select>
										<input type="hidden" name="orig_crafted" value="<?= $item['crafted'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="right">good_only</td>
									<td>
										<select class="yesno" name="items|good_only">
											<option value="0"<?php if( $item['good_only'] == 0 ) print(" selected") ?>>false</option>
											<option value="1"<?php if( $item['good_only'] > 0 ) print(" selected") ?>>true</option>
										</select>
										<input type="hidden" name="orig_good_only" value="<?= $item['good_only'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="right">evil_only</td>
									<td>
										<select class="yesno" name="items|evil_only">
											<option value="0"<?php if( $item['evil_only'] == 0 ) print(" selected") ?>>false</option>
											<option value="1"<?php if( $item['evil_only'] > 0 ) print(" selected") ?>>true</option>
										</select>
										<input type="hidden" name="orig_evil_only" value="<?= $item['evil_only'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="right">stacklore</td>
									<td>
										<select class="yesno" name="items|stacklore">
											<option value="0"<?php if( $item['stacklore'] == 0 ) print(" selected") ?>>false</option>
											<option value="1"<?php if( $item['stacklore'] > 0 ) print(" selected") ?>>true</option>
										</select>
										<input type="hidden" name="orig_stacklore" value="<?= $item['stacklore'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="right">lore_equip</td>
									<td>
										<select class="yesno" name="items|lore_equip">
											<option value="0"<?php if( $item['lore_equip'] == 0 ) print(" selected") ?>>false</option>
											<option value="1"<?php if( $item['lore_equip'] > 0 ) print(" selected") ?>>true</option>
										</select>
										<input type="hidden" name="orig_lore_equip" value="<?= $item['lore_equip'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="right">no_transmute</td>
									<td>
										<select class="yesno" name="items|no_transmute">
											<option value="0"<?php if( $item['no_transmute'] == 0 ) print(" selected") ?>>false</option>
											<option value="1"<?php if( $item['no_transmute'] > 0 ) print(" selected") ?>>true</option>
										</select>
										<input type="hidden" name="orig_no_transmute" value="<?= $item['no_transmute'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="right">flags_32768</td>
									<td>
										<select class="yesno" name="items|flags_32768">
											<option value="0"<?php if( $item['flags_32768'] == 0 ) print(" selected") ?>>false</option>
											<option value="1"<?php if( $item['flags_32768'] > 0 ) print(" selected") ?>>true</option>
										</select>
										<input type="hidden" name="orig_flags_32768" value="<?= $item['flags_32768'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="right">ornate</td>
									<td>
										<select class="yesno" name="items|ornate">
											<option value="0"<?php if( $item['ornate'] == 0 ) print(" selected") ?>>false</option>
											<option value="1"<?php if( $item['ornate'] > 0 ) print(" selected") ?>>true</option>
										</select>
										<input type="hidden" name="orig_ornate" value="<?= $item['ornate'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="right">heirloom</td>
									<td>
										<select class="yesno" name="items|heirloom">
											<option value="0"<?php if( $item['heirloom'] == 0 ) print(" selected") ?>>false</option>
											<option value="1"<?php if( $item['heirloom'] > 0 ) print(" selected") ?>>true</option>
										</select>
										<input type="hidden" name="orig_heirloom" value="<?= $item['heirloom'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="right">appearance_only</td>
									<td>
										<select class="yesno" name="items|appearance_only">
											<option value="0"<?php if( $item['appearance_only'] == 0 ) print(" selected") ?>>false</option>
											<option value="1"<?php if( $item['appearance_only'] > 0 ) print(" selected") ?>>true</option>
										</select>
										<input type="hidden" name="orig_appearance_only" value="<?= $item['appearance_only'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="right">unlocked</td>
									<td>
										<select class="yesno" name="items|unlocked">
											<option value="0"<?php if( $item['unlocked'] == 0 ) print(" selected") ?>>false</option>
											<option value="1"<?php if( $item['unlocked'] > 0 ) print(" selected") ?>>true</option>
										</select>
										<input type="hidden" name="orig_unlocked" value="<?= $item['unlocked'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="right">norepair</td>
									<td>
										<select class="yesno" name="items|norepair">
											<option value="0"<?php if( $item['norepair'] == 0 ) print(" selected") ?>>false</option>
											<option value="1"<?php if( $item['norepair'] > 0 ) print(" selected") ?>>true</option>
										</select>
										<input type="hidden" name="orig_norepair" value="<?= $item['norepair'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="right">etheral</td>
									<td>
										<select class="yesno" name="items|etheral">
											<option value="0"<?php if( $item['etheral'] == 0 ) print(" selected") ?>>false</option>
											<option value="1"<?php if( $item['etheral'] > 0 ) print(" selected") ?>>true</option>
										</select>
										<input type="hidden" name="orig_etheral" value="<?= $item['etheral'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="right">refined</td>
									<td>
										<select class="yesno" name="items|refined">
											<option value="0"<?php if( $item['refined'] == 0 ) print(" selected") ?>>false</option>
											<option value="1"<?php if( $item['refined'] > 0 ) print(" selected") ?>>true</option>
										</select>
										<input type="hidden" name="orig_refined" value="<?= $item['refined'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="right">usable</td>
									<td>
										<select class="yesno" name="items|usable">
											<option value="0"<?php if( $item['usable'] == 0 ) print(" selected") ?>>false</option>
											<option value="1"<?php if( $item['usable'] > 0 ) print(" selected") ?>>true</option>
										</select>
										<input type="hidden" name="orig_usable" value="<?= $item['usable'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="right">collectable</td>
									<td>
										<select class="yesno" name="items|collectable">
											<option value="0"<?php if( $item['collectable'] == 0 ) print(" selected") ?>>false</option>
											<option value="1"<?php if( $item['collectable'] > 0 ) print(" selected") ?>>true</option>
										</select>
										<input type="hidden" name="orig_collectable" value="<?= $item['collectable'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="right">display_charges</td>
									<td>
										<select class="yesno" name="items|display_charges">
											<option value="0"<?php if( $item['display_charges'] == 0 ) print(" selected") ?>>false</option>
											<option value="1"<?php if( $item['display_charges'] > 0 ) print(" selected") ?>>true</option>
										</select>
										<input type="hidden" name="orig_display_charges" value="<?= $item['display_charges'] ?>" />
									</td>
								</tr>
								<tr>
									<td align="right">harvest</td>
									<td>
										<select class="yesno" name="items|harvest">
											<option value="0"<?php if( $item['harvest'] == 0 ) print(" selected") ?>>false</option>
											<option value="1"<?php if( $item['harvest'] > 0 ) print(" selected") ?>>true</option>
										</select>
										<input type="hidden" name="orig_harvest" value="<?= $item['harvest'] ?>" />
									</td>
								</tr>
							</table>
						</fieldset>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<fieldset>
							<legend>Quests</legend>
							<table style="border: 0; border-collapse: collapse; border-spacing: 0;">
								<tr>
									<td align="right">Offers Quest ID:</td>
									<td>
										<input Type="text" name="items|offers_quest_id" class="large" value="<?= $item['offers_quest_id'] ?>" />
										<input Type="hidden" name="orig_offers_quest_id" value="<?= $item['offers_quest_id'] ?>" />
									</td>
									<td align="right">Part of Quest ID:</td>
									<td>
										<input Type="text" name="items|part_of_quest_id" class="large" value="<?= $item['part_of_quest_id'] ?>" />
										<input Type="hidden" name="orig_part_of_quest_id" value="<?= $item['part_of_quest_id'] ?>" />
									</td>
									<td align="right">Quest Unknown:</td>
									<td>
										<input Type="text" name="items|quest_unknown" class="large" value="<?= $item['quest_unknown'] ?>" />
										<input Type="hidden" name="orig_quest_unknown" value="<?= $item['quest_unknown'] ?>" />
									</td>
								</tr>
								<!-- 
								`offers_quest_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
								`part_of_quest_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
								`quest_unknown` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
								 -->
							</table>
						</fieldset>
					</td>
	 			</tr>
 				<tr>
 					<td colspan="2">
 						<fieldset>
 							<legend>Adornments</legend>
		 					<table style="border: 0; border-collapse: collapse; border-spacing: 0;">
		 						<tr>
		 							<td align="right">Adorn Slot 1:</td>
		 							<td>
		 								<input type="text" name="items|adornment_slot1" class="large" value="<?= $item['adornment_slot1'] ?>" />
		 								<input type="hidden" name="orig_adornment_slot1" value="<?= $item['adornment_slot1'] ?>" />
		 							</td>
		 							<td align="right">Adorn Slot 2:</td>
		 							<td>
		 								<input type="text" name="items|adornment_slot2" class="large" value="<?= $item['adornment_slot2'] ?>" />
		 								<input type="hidden" name="orig_adornment_slot2" value="<?= $item['adornment_slot2'] ?>" />
		 							</td>
		 							<td align="right">Adorn Slot 3:</td>
		 							<td>
		 								<input type="text" name="items|adornment_slot3" class="large" value="<?= $item['adornment_slot3'] ?>" />
		 								<input type="hidden" name="orig_adornment_slot3" value="<?= $item['adornment_slot3'] ?>" />
		 							</td>
		 						</tr>
		 						<tr>
		 							<td align="right">Adorn Slot 4:</td>
		 							<td>
		 								<input type="text" name="items|adornment_slot4" class="large" value="<?= $item['adornment_slot4'] ?>" />
		 								<input type="hidden" name="orig_adornment_slot4" value="<?= $item['adornment_slot4'] ?>" />
		 							</td>
		 							<td align="right">Adorn Slot 5:</td>
		 							<td>
		 								<input type="text" name="items|adornment_slot5" class="large" value="<?= $item['adornment_slot5'] ?>" />
		 								<input type="hidden" name="orig_adornment_slot5" value="<?= $item['adornment_slot5'] ?>" />
		 							</td>
		 							<td align="right">Adorn Slot 6:</td>
		 							<td>
		 								<input type="text" name="items|adornment_slot6" class="large" value="<?= $item['adornment_slot6'] ?>" />
		 								<input type="hidden" name="orig_adornment_slot6" value="<?= $item['adornment_slot6'] ?>" />
		 							</td>
		 						</tr>
		 						<tr>
		 							<td align="right">Adorn Description:</td>
		 							<td colspan="5">
		 								<input type="text" name="items|adornment_description" class="full" value="<?= $item['adornment_description'] ?>" />
		 								<input type="hidden" name="orig_adornment_description" value="<?= $item['adornment_description'] ?>" />
		 							</td>
		 						</tr>
		 						<!-- 
	 							`adornment_slot1` TINYINT(3) UNSIGNED NOT NULL DEFAULT '255',
								`adornment_slot2` TINYINT(3) UNSIGNED NOT NULL DEFAULT '255',
								`adornment_slot3` TINYINT(3) UNSIGNED NOT NULL DEFAULT '255',
								`adornment_slot4` TINYINT(3) UNSIGNED NOT NULL DEFAULT '255',
								`adornment_slot5` TINYINT(3) UNSIGNED NOT NULL DEFAULT '255',
								`adornment_slot6` TINYINT(3) UNSIGNED NOT NULL DEFAULT '255',
								`adornment_description` VARCHAR(250) NULL DEFAULT NULL COLLATE 'latin1_general_ci',
		 						 -->
		 					</table>
 						</fieldset>
 					</td>
 				</tr>
 				<tr>
 					<td colspan="2">
 						<fieldset>
 							<legend>Slots</legend>
 							<script>
 								function UpdateSlots() {
 	 								var slots = 0;

 	 								if (document.getElementById("cbSlotPrimary").checked) slots += 1;
 	 								if (document.getElementById("cbSlotSecondary").checked) slots += 2;
	 								if (document.getElementById("cbSlotHead").checked) slots += 4;
 	 								if (document.getElementById("cbSlotChest").checked) slots += 8;
 	 								if (document.getElementById("cbSlotShoulders").checked) slots += 16;
 	 								if (document.getElementById("cbSlotForearms").checked) slots += 32;
 	 								if (document.getElementById("cbSlotHands").checked) slots += 64;
 	 								if (document.getElementById("cbSlotLegs").checked) slots += 128;
 	 								if (document.getElementById("cbSlotFeet").checked) slots += 256;
 	 								if (document.getElementById("cbSlotLRing").checked) slots += 512;
 	 								if (document.getElementById("cbSlotRRing").checked) slots += 1024;
 	 								if (document.getElementById("cbSlotLEar").checked) slots += 2048;
 	 								if (document.getElementById("cbSlotREar").checked) slots += 4096;
 	 								if (document.getElementById("cbSlotNeck").checked) slots += 8192;
 	 								if (document.getElementById("cbSlotLWrist").checked) slots += 16384;
 	 								if (document.getElementById("cbSlotRWrist").checked) slots += 32768;
 	 								if (document.getElementById("cbSlotRange").checked) slots += 65536;
 	 								if (document.getElementById("cbSlotAmmo").checked) slots += 131072;
 	 								if (document.getElementById("cbSlotWaist").checked) slots += 262144;
 	 								if (document.getElementById("cbSlotCloak").checked) slots += 524288;
 	 								if (document.getElementById("cbSlotCharm1").checked) slots += 1048576;
 	 								if (document.getElementById("cbSlotCharm2").checked) slots += 2097152;
 	 								if (document.getElementById("cbSlotFood").checked) slots += 4194304;
 	 								if (document.getElementById("cbSlotDrink").checked) slots += 8388608;

 	 								document.getElementById("ItemSlots").value = slots;
 								}
 							</script>
		 					<table style="border: 0; border-collapse: collapse; border-spacing: 0;">
			 					<tr>
			 						<td align="right" colspan="6">Slots:</td>
			 						<td colspan="6">
										<input type="text" id="ItemSlots" name="items|slots" value="<?= $item['slots'] ?>" readonly style="width:55px;background-color:#ddd;" />
										<input type="hidden" name="orig_slots" value="<?= $item['slots'] ?>" />
			 						</td>
			 					</tr>
		 						<tr>
		 							<td align="right">Primary</td>
		 							<td>
		 								<input type="checkbox" id="cbSlotPrimary"<?php if (($item['slots'] & 1) > 0) printf(" checked"); ?> onchange="UpdateSlots()" />
	 								</td>
	 								<td align="right">Secondary</td>
		 							<td>
		 								<input type="checkbox" id="cbSlotSecondary"<?php if (($item['slots'] & 2) > 0) printf(" checked"); ?> onchange="UpdateSlots()" />
	 								</td>
	 								<td align="right">Head</td>
		 							<td>
		 								<input type="checkbox" id="cbSlotHead"<?php if (($item['slots'] & 4) > 0) printf(" checked"); ?> onchange="UpdateSlots()" />
	 								</td>
	 								<td align="right">Chest</td>
		 							<td>
		 								<input type="checkbox" id="cbSlotChest"<?php if (($item['slots'] & 8) > 0) printf(" checked"); ?> onchange="UpdateSlots()" />
	 								</td>
	 								<td align="right">Shoulders</td>
		 							<td>
		 								<input type="checkbox" id="cbSlotShoulders"<?php if (($item['slots'] & 16) > 0) printf(" checked"); ?> onchange="UpdateSlots()" />
	 								</td>
	 								<td align="right">Forearms</td>
		 							<td>
		 								<input type="checkbox" id="cbSlotForearms"<?php if (($item['slots'] & 32) > 0) printf(" checked"); ?> onchange="UpdateSlots()" />
	 								</td>
		 						</tr>
		 						<tr>
		 							<td align="right">Hands</td>
		 							<td>
		 								<input type="checkbox" id="cbSlotHands"<?php if (($item['slots'] & 64) > 0) printf(" checked"); ?> onchange="UpdateSlots()" />
		 							</td>
		 							<td align="right">Legs</td>
		 							<td>
		 								<input type="checkbox" id="cbSlotLegs"<?php if (($item['slots'] & 128) > 0) printf(" checked"); ?> onchange="UpdateSlots()" />
		 							</td>
		 							<td align="right">Feet</td>
		 							<td>
		 								<input type="checkbox" id="cbSlotFeet"<?php if (($item['slots'] & 256) > 0) printf(" checked"); ?> onchange="UpdateSlots()" />
		 							</td>
		 							<td align="right">Left Ring</td>
		 							<td>
		 								<input type="checkbox" id="cbSlotLRing"<?php if (($item['slots'] & 512) > 0) printf(" checked"); ?> onchange="UpdateSlots()" />
		 							</td>
		 							<td align="right">Right Ring</td>
		 							<td>
		 								<input type="checkbox" id="cbSlotRRing"<?php if (($item['slots'] & 1024) > 0) printf(" checked"); ?> onchange="UpdateSlots()" />
		 							</td>
		 							<td align="right">Left Ear</td>
		 							<td>
		 								<input type="checkbox" id="cbSlotLEar"<?php if (($item['slots'] & 2048) > 0) printf(" checked"); ?> onchange="UpdateSlots()" />
		 							</td>
		 						</tr>
		 						<tr>
		 							<td align="right">Right Ear</td>
		 							<td>
		 								<input type="checkbox" id="cbSlotREar"<?php if (($item['slots'] & 4096) > 0) printf(" checked"); ?> onchange="UpdateSlots()" />
		 							</td>
		 							<td align="right">Neck</td>
		 							<td>
		 								<input type="checkbox" id="cbSlotNeck"<?php if (($item['slots'] & 8192) > 0) printf(" checked"); ?> onchange="UpdateSlots()" />
		 							</td>
		 							<td align="right">Left Wrist</td>
		 							<td>
		 								<input type="checkbox" id="cbSlotLWrist"<?php if (($item['slots'] & 16384) > 0) printf(" checked"); ?> onchange="UpdateSlots()" />
		 							</td>
		 							<td align="right">Right Wrist</td>
		 							<td>
		 								<input type="checkbox" id="cbSlotRWrist"<?php if (($item['slots'] & 32768) > 0) printf(" checked"); ?> onchange="UpdateSlots()" />
		 							</td>
		 							<td align="right">Range</td>
		 							<td>
		 								<input type="checkbox" id="cbSlotRange"<?php if (($item['slots'] & 65536) > 0) printf(" checked"); ?> onchange="UpdateSlots()" />
		 							</td>
		 							<td align="right">Ammo</td>
		 							<td>
		 								<input type="checkbox" id="cbSlotAmmo"<?php if (($item['slots'] & 131072) > 0) printf(" checked"); ?> onchange="UpdateSlots()" />
		 							</td>
		 						</tr>
		 						<tr>
		 							<td align="right">Waist</td>
		 							<td>
		 								<input type="checkbox" id="cbSlotWaist"<?php if (($item['slots'] & 262144) > 0) printf(" checked"); ?> onchange="UpdateSlots()" />
		 							</td>
		 							<td align="right">Cloak</td>
		 							<td>
		 								<input type="checkbox" id="cbSlotCloak"<?php if (($item['slots'] & 524288) > 0) printf(" checked"); ?> onchange="UpdateSlots()" />
		 							</td>
		 							<td align="right">Charm Slot 1</td>
		 							<td>
		 								<input type="checkbox" id="cbSlotCharm1"<?php if (($item['slots'] & 1048576) > 0) printf(" checked"); ?> onchange="UpdateSlots()" />
		 							</td>
		 							<td align="right">Charm Slot 2</td>
		 							<td>
		 								<input type="checkbox" id="cbSlotCharm2"<?php if (($item['slots'] & 2097152) > 0) printf(" checked"); ?> onchange="UpdateSlots()" />
		 							</td>
		 							<td align="right">Food</td>
		 							<td>
		 								<input type="checkbox" id="cbSlotFood"<?php if (($item['slots'] & 4194304) > 0) printf(" checked"); ?> onchange="UpdateSlots()" />
		 							</td>
		 							<td align="right">Drink</td>
		 							<td>
		 								<input type="checkbox" id="cbSlotDrink"<?php if (($item['slots'] & 8388608) > 0) printf(" checked"); ?> onchange="UpdateSlots()" />
		 							</td>
	 							</tr>
		 						<!--
		 						`slots` INT(10) UNSIGNED NOT NULL DEFAULT '0',
								-->
		 					</table>
 						</fieldset>
 					</td>
 				</tr>
 				<tr>
	 				<td colspan="3">
	 					<fieldset>
		 					<legend>Requirements</legend>
		 					<script>
		 					window.onload = function () {
			 					var advClasses = document.getElementById("AdvClasses").value;

			 					if ( (advClasses & 2) > 0) document.getElementById("cbFighter").checked = true;
			 					if ( (advClasses & 4) > 0) document.getElementById("cbWarrior").checked = true;
			 					if ( (advClasses & 8) > 0) document.getElementById("cbGuardian").checked = true;
			 					if ( (advClasses & 16) > 0) document.getElementById("cbBerserker").checked = true;
			 					if ( (advClasses & 32) > 0) document.getElementById("cbBrawler").checked = true;
			 					if ( (advClasses & 64) > 0) document.getElementById("cbMonk").checked = true;
			 					if ( (advClasses & 128) > 0) document.getElementById("cbBruiser").checked = true;
			 					if ( (advClasses & 256) > 0) document.getElementById("cbCrusader").checked = true;
			 					if ( (advClasses & 512) > 0) document.getElementById("cbShadowknight").checked = true;
			 					if ( (advClasses & 1024) > 0) document.getElementById("cbPaladin").checked = true;
			 					if ( (advClasses & 2048) > 0) document.getElementById("cbPriest").checked = true;
			 					if ( (advClasses & 4096) > 0) document.getElementById("cbCleric").checked = true;
			 					if ( (advClasses & 8192) > 0) document.getElementById("cbTemplar").checked = true;
			 					if ( (advClasses & 16384) > 0) document.getElementById("cbInquisitor").checked = true;
			 					if ( (advClasses & 32768) > 0) document.getElementById("cbDruid").checked = true;
			 					if ( (advClasses & 65536) > 0) document.getElementById("cbWarden").checked = true;
			 					if ( (advClasses & 131072) > 0) document.getElementById("cbFury").checked = true;
			 					if ( (advClasses & 262144) > 0) document.getElementById("cbShaman").checked = true;
			 					if ( (advClasses & 524288) > 0) document.getElementById("cbMystic").checked = true;
			 					if ( (advClasses & 1048576) > 0) document.getElementById("cbDefiler").checked = true;
			 					if ( (advClasses & 2097152) > 0) document.getElementById("cbMage").checked = true;;
			 					if ( (advClasses & 4194304) > 0) document.getElementById("cbSorcerer").checked = true;
			 					if ( (advClasses & 8388608) > 0) document.getElementById("cbWizard").checked = true;
			 					if ( (advClasses & 16777216) > 0) document.getElementById("cbWarlock").checked = true;
			 					if ( (advClasses & 33554432) > 0) document.getElementById("cbEnchanter").checked = true;
			 					if ( (advClasses & 67108864) > 0) document.getElementById("cbIllusionist").checked = true;
			 					if ( (advClasses & 134217728) > 0) document.getElementById("cbCoercer").checked = true;
			 					if ( (advClasses & 268435456) > 0) document.getElementById("cbSummoner").checked = true;
			 					if ( (advClasses & 536870912) > 0) document.getElementById("cbConjuror").checked = true;
			 					if ( (advClasses & 1073741824) > 0) document.getElementById("cbNecromancer").checked = true;
			 					if ( (advClasses & 2147483648) > 0) document.getElementById("cbScout").checked = true;


			 					var advClasses2 = Math.floor(advClasses / 4294967296);
			 					if ( (advClasses2 & 1) > 0) document.getElementById("cbRogue").checked = true;
			 					if ( (advClasses2 & 2) > 0) document.getElementById("cbSwashbuckler").checked = true;
			 					if ( (advClasses2 & 4) > 0) document.getElementById("cbBrigand").checked = true;
			 					if ( (advClasses2 & 8) > 0) document.getElementById("cbBard").checked = true;
			 					if ( (advClasses2 & 16) > 0) document.getElementById("cbTroubador").checked = true;
			 					if ( (advClasses2 & 32) > 0) document.getElementById("cbDirge").checked = true;
			 					if ( (advClasses2 & 64) > 0) document.getElementById("cbPredator").checked = true;
			 					if ( (advClasses2 & 128) > 0) document.getElementById("cbRanger").checked = true;
			 					if ( (advClasses2 & 256) > 0) document.getElementById("cbAssassin").checked = true;
			 					if ( (advClasses2 & 512) > 0) document.getElementById("cbAnimalist").checked = true;
			 					if ( (advClasses2 & 1024) > 0) document.getElementById("cbBeastlord").checked = true;
			 					if ( (advClasses2 & 2048) > 0) document.getElementById("cbShaper").checked = true;
			 					if ( (advClasses2 & 4096) > 0) document.getElementById("cbChanneler").checked = true;

			 							 					
/*			 					if ( (advClasses & 4294967296) > 0) document.getElementById("cbRogue").checked = true;
			 					if ( (advClasses & 8589934592) > 0) document.getElementById("cbSwashbuckler").checked = true;
			 					if ( (advClasses & 17179869184) > 0) document.getElementById("cbBrigand").checked = true;
			 					if ( (advClasses & 34359738368) > 0) document.getElementById("cbBard").checked = true;
			 					if ( (advClasses & 68719476736) > 0) document.getElementById("cbTroubador").checked = true;
			 					if ( (advClasses & 137438953472) > 0) document.getElementById("cbDirge").checked = true;
			 					if ( (advClasses & 274877906944) > 0) document.getElementById("cbPredator").checked = true;
			 					if ( (advClasses & 549755813888) > 0) document.getElementById("cbRanger").checked = true;
			 					if ( (advClasses & 1099511627776) > 0) document.getElementById("cbAssassin").checked = true;
			 					if ( (advClasses & 2199023255552) > 0) document.getElementById("cbAnimalist").checked = true;
			 					if ( (advClasses & 4398046511104) > 0) document.getElementById("cbBeastlord").checked = true;
			 					if ( (advClasses & 8796093022208) > 0) document.getElementById("cbShaper").checked = true;
			 					if ( (advClasses & 17592186044416) > 0) document.getElementById("cbChanneler").checked = true;*/
		 					}
		 					
		 					function UpdateClasses() {
			 					var advClasses = 0;

			 					if (document.getElementById("cbFighter").checked) advClasses += 2;
			 					if (document.getElementById("cbWarrior").checked) advClasses += 4;
			 					if (document.getElementById("cbGuardian").checked) advClasses += 8;
			 					if (document.getElementById("cbBerserker").checked) advClasses += 16;
			 					if (document.getElementById("cbBrawler").checked) advClasses += 32;
			 					if (document.getElementById("cbMonk").checked) advClasses += 64;
			 					if (document.getElementById("cbBruiser").checked) advClasses += 128;
			 					if (document.getElementById("cbCrusader").checked) advClasses += 256;
			 					if (document.getElementById("cbShadowknight").checked) advClasses += 512;
			 					if (document.getElementById("cbPaladin").checked) advClasses += 1024;
			 					if (document.getElementById("cbPriest").checked) advClasses += 2048;
			 					if (document.getElementById("cbCleric").checked) advClasses += 4096;
			 					if (document.getElementById("cbTemplar").checked) advClasses += 8192;
			 					if (document.getElementById("cbInquisitor").checked) advClasses += 16384;
			 					if (document.getElementById("cbDruid").checked) advClasses += 32768;
			 					if (document.getElementById("cbWarden").checked) advClasses += 65536;
			 					if (document.getElementById("cbFury").checked) advClasses += 131072;
			 					if (document.getElementById("cbShaman").checked) advClasses += 262144;
			 					if (document.getElementById("cbMystic").checked) advClasses += 524288;
			 					if (document.getElementById("cbDefiler").checked) advClasses += 1048576;
			 					if (document.getElementById("cbMage").checked) advClasses += 2097152;
			 					if (document.getElementById("cbSorcerer").checked) advClasses += 4194304;
			 					if (document.getElementById("cbWizard").checked) advClasses += 8388608;
			 					if (document.getElementById("cbWarlock").checked) advClasses += 16777216;
			 					if (document.getElementById("cbEnchanter").checked) advClasses += 33554432;
			 					if (document.getElementById("cbIllusionist").checked) advClasses += 67108864;
			 					if (document.getElementById("cbCoercer").checked) advClasses += 134217728;
			 					if (document.getElementById("cbSummoner").checked) advClasses += 268435456;
			 					if (document.getElementById("cbConjuror").checked) advClasses += 536870912;
			 					if (document.getElementById("cbNecromancer").checked) advClasses += 1073741824;
			 					if (document.getElementById("cbScout").checked) advClasses += 2147483648;
			 					if (document.getElementById("cbRogue").checked) advClasses += 4294967296;
			 					if (document.getElementById("cbSwashbuckler").checked) advClasses += 8589934592;
			 					if (document.getElementById("cbBrigand").checked) advClasses += 17179869184;
			 					if (document.getElementById("cbBard").checked) advClasses += 34359738368;
			 					if (document.getElementById("cbTroubador").checked) advClasses += 68719476736;
			 					if (document.getElementById("cbDirge").checked) advClasses += 137438953472;
			 					if (document.getElementById("cbPredator").checked) advClasses += 274877906944;
			 					if (document.getElementById("cbRanger").checked) advClasses += 549755813888;
			 					if (document.getElementById("cbAssassin").checked) advClasses += 1099511627776;
			 					if (document.getElementById("cbAnimalist").checked) advClasses += 2199023255552;
			 					if (document.getElementById("cbBeastlord").checked) advClasses += 4398046511104;
			 					if (document.getElementById("cbShaper").checked) advClasses += 8796093022208;
			 					if (document.getElementById("cbChanneler").checked) advClasses += 17592186044416;

			 					document.getElementById("AdvClasses").value = advClasses;

			 					var tsClasses = 0;
			 					
			 					if (document.getElementById("cbArtisan").checked) tsClasses += 2;
			 					if (document.getElementById("cbCraftsman").checked) tsClasses += 4;
			 					if (document.getElementById("cbProvisioner").checked) tsClasses += 8;
			 					if (document.getElementById("cbWoodworker").checked) tsClasses += 16;
			 					if (document.getElementById("cbCarpenter").checked) tsClasses += 32;
			 					if (document.getElementById("cbOutfitter").checked) tsClasses += 64;
			 					if (document.getElementById("cbArmorer").checked) tsClasses += 128;
			 					if (document.getElementById("cbWeaponsmith").checked) tsClasses += 256;
			 					if (document.getElementById("cbTailor").checked) tsClasses += 512;
			 					if (document.getElementById("cbScholar").checked) tsClasses += 1024;
			 					if (document.getElementById("cbJeweler").checked) tsClasses += 2048;
			 					if (document.getElementById("cbSage").checked) tsClasses += 4096;
			 					if (document.getElementById("cbAlchemist").checked) tsClasses += 8192;

			 					document.getElementById("TSClasses").value = tsClasses;
		 					}
		 					</script>
		 					<table style="border: 0; border-collapse: collapse; border-spacing: 0;">
		 						<tr>
		 							<td align="right" colspan="5">Adevnture Default Level:</td>
		 							<td>
		 								<input type="text" name="items|adventure_default_level" class="medium" value="<?= $item['adventure_default_level'] ?>" />
										<input type="hidden" name="orig_adventure_default_level" value="<?= $item['adventure_default_level'] ?>" />
		 							</td>
		 							<td align="right" colspan="3">Adventure Classes:</td>
		 							<td colspan="10">
		 								<input type="text" id="AdvClasses" name="items|adventure_classes" value="<?= $item['adventure_classes'] ?>" readonly style="background-color:#ddd;" />
										<input type="hidden" name="orig_adventure_classes" value="<?= $item['adventure_classes'] ?>" />
		 							</td>
		 						</tr>
		 						<tr>
		 							<td align="right">Fighter</td>
		 							<td>
			 							<input type="checkbox" id="cbFighter" onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Warrior</td>
		 							<td>
			 							<input type="checkbox" id="cbWarrior" onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Guardian</td>
		 							<td>
			 							<input type="checkbox" id="cbGuardian" onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Berserker</td>
		 							<td>
			 							<input type="checkbox" id="cbBerserker" onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Brawler</td>
		 							<td>
			 							<input type="checkbox" id="cbBrawler" onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Monk</td>
		 							<td>
			 							<input type="checkbox" id="cbMonk" onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Bruiser</td>
		 							<td>
			 							<input type="checkbox" id="cbBruiser" onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Crusader</td>
		 							<td>
			 							<input type="checkbox" id="cbCrusader" onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Shadowknight</td>
		 							<td>
			 							<input type="checkbox" id="cbShadowknight" onchange="UpdateClasses()" />
		 							</td>
	 							</tr>
	 							<tr>
		 							<td align="right">Paladin</td>
		 							<td>
			 							<input type="checkbox" id="cbPaladin" onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Priest</td>
		 							<td>
			 							<input type="checkbox" id="cbPriest" onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Cleric</td>
		 							<td>
			 							<input type="checkbox" id="cbCleric" onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Templar</td>
		 							<td>
			 							<input type="checkbox" id="cbTemplar" onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Inquisitor</td>
		 							<td>
			 							<input type="checkbox" id="cbInquisitor" onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Druid</td>
		 							<td>
			 							<input type="checkbox" id="cbDruid" onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Warden</td>
		 							<td>
			 							<input type="checkbox" id="cbWarden" onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Fury</td>
		 							<td>
			 							<input type="checkbox" id="cbFury" onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Shaman</td>
		 							<td>
			 							<input type="checkbox" id="cbShaman" onchange="UpdateClasses()" />
		 							</td>
		 						</tr>
		 						<tr>
		 							<td align="right">Mystic</td>
		 							<td>
			 							<input type="checkbox" id="cbMystic" onchange="UpdateClasses()" />
			 						</td>
		 							<td align="right">Defiler</td>
		 							<td>
			 							<input type="checkbox" id="cbDefiler" onchange="UpdateClasses()" />
			 						</td>
		 							<td align="right">Mage</td>
		 							<td>
			 							<input type="checkbox" id="cbMage" onchange="UpdateClasses()" />
			 						</td>
		 							<td align="right">Sorcerer</td>
		 							<td>
			 							<input type="checkbox" id="cbSorcerer" onchange="UpdateClasses()" />
			 						</td>
		 							<td align="right">Wizard</td>
		 							<td>
			 							<input type="checkbox" id="cbWizard" onchange="UpdateClasses()" />
			 						</td>
		 							<td align="right">Warlock</td>
		 							<td>
			 							<input type="checkbox" id="cbWarlock" onchange="UpdateClasses()" />
			 						</td>
		 							<td align="right">Enchanter</td>
		 							<td>
			 							<input type="checkbox" id="cbEnchanter" onchange="UpdateClasses()" />
			 						</td>
		 							<td align="right">Illusionist</td>
		 							<td>
			 							<input type="checkbox" id="cbIllusionist" onchange="UpdateClasses()" />
			 						</td>
		 							<td align="right">Coercer</td>
		 							<td>
			 							<input type="checkbox" id="cbCoercer" onchange="UpdateClasses()" />
		 							</td>
		 						</tr>
		 						<tr>
		 							<td align="right">Summoner</td>
		 							<td>
			 							<input type="checkbox" id="cbSummoner" onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Conjuror</td>
		 							<td>
			 							<input type="checkbox" id="cbConjuror" onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Necromancer</td>
		 							<td>
			 							<input type="checkbox" id="cbNecromancer" onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Scout</td>
		 							<td>
			 							<input type="checkbox" id="cbScout" onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Rogue</td>
		 							<td>
			 							<input type="checkbox" id="cbRogue" onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Swashbuckler</td>
		 							<td>
			 							<input type="checkbox" id="cbSwashbuckler" onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Brigand</td>
		 							<td>
			 							<input type="checkbox" id="cbBrigand" onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Bard</td>
		 							<td>
			 							<input type="checkbox" id="cbBard" onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Troubador</td>
		 							<td>
			 							<input type="checkbox" id="cbTroubador" onchange="UpdateClasses()" />
		 							</td>
		 						</tr>
		 						<tr>
		 							<td align="right">Dirge</td>
		 							<td>
			 							<input type="checkbox" id="cbDirge" onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Predator</td>
		 							<td>
			 							<input type="checkbox" id="cbPredator" onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Ranger</td>
		 							<td>
			 							<input type="checkbox" id="cbRanger" onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Assassin</td>
		 							<td>
			 							<input type="checkbox" id="cbAssassin" onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Animalist</td>
		 							<td>
			 							<input type="checkbox" id="cbAnimalist" onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Beastlord</td>
		 							<td>
			 							<input type="checkbox" id="cbBeastlord" onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Shaper</td>
		 							<td>
			 							<input type="checkbox" id="cbShaper" onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Channeler</td>
		 							<td>
			 							<input type="checkbox" id="cbChanneler" onchange="UpdateClasses()" />
		 							</td>
		 						</tr>
		 						<tr><td>&nbsp;</td></tr><!-- Empty row to put some space between tradeskill boxes and class checkboxes -->
		 						<tr>
		 							<td align="right" colspan="5">Tradeskill Default Level:</td>
		 							<td>
		 								<input type="text" name="items|tradeskill_default_level" class="medium" value="<?= $item['tradeskill_default_level'] ?>" />
										<input type="hidden" name="orig_tradeskill_default_level" value="<?= $item['tradeskill_default_level'] ?>" />
		 							</td>
		 							<td align="right" colspan="3">Tradeskill Classes:</td>
		 							<td colspan="10">
		 								<input type="text" id="TSClasses" name="items|tradeskill_classes" value="<?= $item['tradeskill_classes'] ?>" readonly style="background-color:#ddd;" />
										<input type="hidden" name="orig_tradeskill_classes" value="<?= $item['tradeskill_classes'] ?>" />
		 							</td>
		 						</tr>
		 						<tr>
			 						<td colspan="4"></td>
		 							<td align="right">Artisan</td>
		 							<td>
			 							<input type="checkbox" id="cbArtisan"<?php if (($item['tradeskill_classes'] & 2) > 0) printf(" checked"); ?> onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Craftsman</td>
		 							<td>
			 							<input type="checkbox" id="cbCraftsman"<?php if (($item['tradeskill_classes'] & 4) > 0) printf(" checked"); ?> onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Provisioner</td>
		 							<td>
			 							<input type="checkbox" id="cbProvisioner"<?php if (($item['tradeskill_classes'] & 8) > 0) printf(" checked"); ?> onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Woodworker</td>
		 							<td>
			 							<input type="checkbox" id="cbWoodworker"<?php if (($item['tradeskill_classes'] & 16) > 0) printf(" checked"); ?> onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Carpenter</td>
		 							<td>
			 							<input type="checkbox" id="cbCarpenter"<?php if (($item['tradeskill_classes'] & 32) > 0) printf(" checked"); ?> onchange="UpdateClasses()" />
		 							</td>
		 						</tr>
		 						<tr>
			 						<td colspan="4"></td>
		 							<td align="right">Outfitter</td>
		 							<td>
			 							<input type="checkbox" id="cbOutfitter"<?php if (($item['tradeskill_classes'] & 64) > 0) printf(" checked"); ?> onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Armorer</td>
		 							<td>
			 							<input type="checkbox" id="cbArmorer"<?php if (($item['tradeskill_classes'] & 128) > 0) printf(" checked"); ?> onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Weaponsmith</td>
		 							<td>
			 							<input type="checkbox" id="cbWeaponsmith"<?php if (($item['tradeskill_classes'] & 256) > 0) printf(" checked"); ?> onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Tailor</td>
		 							<td>
			 							<input type="checkbox" id="cbTailor"<?php if (($item['tradeskill_classes'] & 512) > 0) printf(" checked"); ?> onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Scholar</td>
		 							<td>
			 							<input type="checkbox" id="cbScholar"<?php if (($item['tradeskill_classes'] & 1024) > 0) printf(" checked"); ?> onchange="UpdateClasses()" />
		 							</td>
		 						</tr>
		 						<tr>
			 						<td colspan="6"></td>
		 							<td align="right">Jeweler</td>
		 							<td>
			 							<input type="checkbox" id="cbJeweler"<?php if (($item['tradeskill_classes'] & 2048) > 0) printf(" checked"); ?> onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Sage</td>
		 							<td>
			 							<input type="checkbox" id="cbSage"<?php if (($item['tradeskill_classes'] & 1) > 4096) printf(" checked"); ?> onchange="UpdateClasses()" />
		 							</td>
		 							<td align="right">Alchemist</td>
		 							<td>
			 							<input type="checkbox" id="cbAlchemist"<?php if (($item['tradeskill_classes'] & 8192) > 0) printf(" checked"); ?> onchange="UpdateClasses()" />
		 							</td>
		 						</tr>
		 						<!-- 
		 						`skill_id_req` INT(10) UNSIGNED NOT NULL DEFAULT '0',
								`skill_id_req2` INT(10) UNSIGNED NOT NULL DEFAULT '0',
								`skill_min` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
								`recommended_level` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
								`adventure_default_level` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '1',
								`tradeskill_default_level` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '1',
								`adventure_classes` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
								`tradeskill_classes` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
		 						 -->
		 						 <tr><td>&nbsp;</td></tr>
		 						 <tr>
		 						 	<td colspan="18">
		 						 		<table style="border: 0; border-collapse: collapse; border-spacing: 0;">
		 						 			<tr>
					 						 	<td align="right">Recommended Level:</td>
					 						 	<td>
													<input type="text" name="items|recommended_level" class="medium" value="<?= $item['recommended_level'] ?>" />
													<input type="hidden" name="orig_recommended_level" value="<?= $item['recommended_level'] ?>" />
		 									 	</td>
					 						 	<td align="right">Skill Req:</td>
		 									 	<td>
													<select name="items|skill_id_req" style="width:175px;">
														<option value="0">---</option>
														<?php $eq2->GetSkillsOptions($item['skill_id_req']); ?>
													</select>
													<input type="hidden" name="orig_skill_id_req" value="<?= $item['skill_id_req'] ?>" />
												</td>
					 						 	<td align="right">Skill2 Req:</td>
		 									 	<td>
													<select name="items|skill_id_req2" style="width:175px;">
														<option value="0">---</option>
														<?php $eq2->GetSkillsOptions($item['skill_id_req2']); ?>
													</select>
													<input type="hidden" name="orig_skill_id_req2" value="<?= $item['skill_id_req2'] ?>" />
												</td>
												<td align="right">Min Skill:</td>
												<td>
													<input type="text" name="items|skill_min" class="medium" value="<?= $item['skill_min'] ?>" />
													<input type="hidden" name="orig_skill_min" value="<?= $item['skill_min'] ?>" />
												</td>
											</tr>
										</table>
									</td>
		 						 </tr>
	 						</table>
	 					</fieldset>
	 				</td>
 				</tr>
 				<tr>
 					<td colspan="3" align="center">
	 					<input type="submit" name="cmd" value="Update" style="width:100px;" />&nbsp;
						<input type="hidden" name="table" value="items" />
						<input type="hidden" name="object" value="<?= $item['name'] ?>" />
 					</td>
				</tr>
			</table>
			</form>
		</div>
		
		<?php
	}
	
	private function Item_Script() {
		global $eq2;
		
		if (isset($_POST['cmd'])) {
			if ($_POST['cmd'] == 'Create') {
				$eq2->ProcessUpdate(NULL);
				$lua = "--[[\n\tScript Name\t\t:\t" . $_POST['items|lua_script'] . "\n\tScript Purpose\t:\t\n\tScript Author\t:\t" . $eq2->userdata['username'] . "\n\tScript Date\t\t:\t" . date('n/j/Y') . "\n\tScript Notes\t:\t\n--]]";
				$eq2->SaveScript($_POST['items|lua_script'], $lua);
			}
			else if ($_POST['cmd'] == 'Save' && isset($_POST['script'])) {
				$eq2->SaveScript($_POST['ScriptPath'], $_POST['script']);

			}
		}
		
		$item = $this->db->GetItem();
		if (!is_array($item)) {
			$eq2->AddStatus("Item not found.");
			return;
		}
		
		
		
		?>
		
		<div id="Editor">
			<form method="post">
			<table style="width: 100%;">
				<tr>
					<td class="Title" align="center">Editing Item: <?= $item['name'] ?></td>
				</tr>
				
				<?php
				if ($eq2->IsStringNullOrEmpty($item['lua_script'])) {
					?>
					<tr>
						<td>
							<p style="text-align: center; font-weight: bold; font-size: 14;">
								No lua script found for this item!
							</p>
							<!-- <form method="post">
								<?php
									/*
									$file = preg_replace("([^\w\s\d\-_~,;:\[\]\(\).])", '', $item['name']);
									$file = preg_replace("([\.]{2,})", '', $file);
									$file = str_replace(' ', '', $file);
									$file = str_replace(',', '', $file);
									*/
								?>
								<input type="hidden" name="table" value="items" />
								<input type="hidden" name="object" value="<?= $item['name'] ?>" />
								<input type="hidden" name="orig_id" value="<?= $item['id'] ?>" />
								<input type="hidden" name="items|lua_script" value="ItemScripts/<?= $file ?>.lua" />
								<input type="hidden" name="orig_lua_script" value="" />
								<input type="submit" class="submit" name="cmd" value="Create" />
							</form> -->
						</td>
					</tr>
					<?php
				}
				else {
					?>
						<tr>
							<td height="480px"><?php $eq2->ScriptEditor($item['lua_script']); ?></td>
						</tr>
						<tr>
							<td align="center">

								<input type="hidden" name="ScriptPath" value="<?= $item['lua_script'] ?>" />
								<input type="submit" class="submit" id="save" name="cmd" value="Save" />
								<script>
									$(function(){
										$('#save').click(function () {
											var mysave = $('#LuaEditor').html();
											$('#LuaScript').val(editor.getValue());
										});
									});
								</script>
							</td>
						</tr>
					<?php
				}
				?>
			</table>
			</form>
		</div>
		
		<?php
	}
	
	private function Item_Stats() {
		
	}
	
	private function Item_Effects() {
		global $eq2;
		
		if (isset($_POST['cmd']))
		{
			switch ($_POST['cmd'])
			{
				case "Update":
					$eq2->ProcessUpdate();
					break;
				case "Delete":
					$eq2->ProcessDelete(NULL);
					break;
				case "Insert":
					$eq2->ProcessInsert(NULL);
					break;
			}
		}
		
		$id = $_GET['id'];
		$name = $this->db->GetItemName($id);
		$item_effects = $this->db->GetItemEffects($id);
		
		?>
		<div id="Editor">
			<table>
				<tr>
					<td class="Title" align="center">Editing Item Effects: <?= $name ?> (<?= $id ?>)</td>
				</tr>
				<tr>
				<td>
					<table id="SelectGrid" cellspacing="0" style="width: 1030px;">
						<tr>
							<th>ID:</th>
							<th>Item ID:</th>
							<th>Effect:</th>
							<th>Percentage:</th>
							<th>Bullet:</th>
							<th width="120"></th>
						</tr>
			<?php
			if (!empty($item_effects))
			{
				foreach ($item_effects as $data)
				{
					?>
					<form method="POST">
						<tr>
							<td>
								<input type="text" name="item_effects|id" value="<?= $data['id'] ?>" class="small" style="background-color:#ddd;" readonly />
								<input type="hidden" name="orig_id" value="<?= $data['id'] ?>" />
							</td>
							
							<td>
								<input type="text" name="item_effects|item_id" value="<?= $data['item_id'] ?>" class="medium" style="background-color:#ddd;" readonly />
								<input type="hidden" name="orig_item_id" value="<?= $data['item_id'] ?>" />
							</td>
							
							<td>
								<input type="text" name="item_effects|effect" value="<?= $data['effect'] ?>" class="full" />
								<input type="hidden" name="orig_effect" value="<?= $data['effect'] ?>" />
							</td>
							
							<td>
								<input type="text" name="item_effects|percentage" value="<?= $data['percentage'] ?>" class="small" />
								<input type="hidden" name="orig_percentage" value="<?= $data['percentage'] ?>" />
							</td>
							
							<td>
								<input type="text" name="item_effects|bullet" value="<?= $data['bullet'] ?>" class="small" />
								<input type="hidden" name="orig_bullet" value="<?= $data['bullet'] ?>" />
							</td>

							<td nowrap="nowrap">
								<input type="submit" name="cmd" value="Update" class="submit" />
								<input type="submit" name="cmd" value="Delete" class="submit" />
								<input type="hidden" name="table" value="item_effects" />
								<input type="hidden" name="object" value="Edit Item Effects" />
							</td>
						</tr>
					</form>
					<?php
				}
			}
			?>
			<form method="POST">
				<tr>
					<td>new</td>
					<td><input type="text" name="item_effects|item_id" value="<?= $id ?>" class="medium" style="background-color:#ddd;" readonly /></td>
					<td><input type="text" name="item_effects|effect" value="" class="full" /></td>
					<td><input type="text" name="item_effects|percentage" value="100" class="small" /></td>
					<td><input type="text" name="item_effects|bullet" value="0" class="small" /></td>
					<td nowrap="nowrap">
						<input type="submit" name="cmd" value="Insert" class="submit" />
						<input type="hidden" name="table" value="item_effects" />
						<input type="hidden" name="object" value="Add Item Effect" />
					</td>
				</tr>
			</form>
		</table>
		</td>
		</tr>
		</table>
		</div>
		<?php
	}
	
	private function Item_Appearances() {
		global $eq2;
		
		if (isset($_POST['cmd']))
		{
			switch ($_POST['cmd'])
			{
				case "Update":
					$eq2->ProcessUpdate();
					break;
				case "Delete":
					$eq2->ProcessDelete(NULL);
					break;
				case "Insert":
					$eq2->ProcessInsert(NULL);
					break;
			}
		}
		
		$id = $_GET['id'];
		$name = $this->db->GetItemName($id);
		$item_appearance = $this->db->GetItemAppearance($id);
		
		?>
		<div id="Editor">
			<form method="POST">
				<table>
					<tr>
						<td colspan="6" class="Title" align="center">Editing Item Appearance: <?= $name ?> (<?= $id ?>)</td>
					</tr>
					<tr>
						<?php
						if (!empty($item_appearance))
						{
							?>
							<td class="LabelRight">ID:</td>
							<td>
								<input type="text" name="item_appearances|item_id" value="<?= $item_appearance['id'] ?>" class="small" style="background-color:#ddd;" readonly />
								<input type="hidden" name="orig_id" value="<?= $item_appearance['id'] ?>" />
							</td>
							
							<td class="LabelRight">Item ID:</td>
							<td>
								<input type="text" name="item_appearances|item_id" value="<?= $id ?>" class="medium" style="background-color:#ddd;" readonly />
								<input type="hidden" name="orig_item_id" value="<?= $item_appearance['item_id'] ?>" />
							</td>
							
							<td class="LabelRight">Equip Type:</td>
							<td>
								<input type="text" name="item_appearances|equip_type" value="<?= $item_appearance['equip_type'] ?>" class="medium" />
								<input type="hidden" name="orig_equip_type" value="<?= $item_appearance['equip_type'] ?>" />
							</td>
							
						</tr>
						<tr>
							<td class="LabelRight">Red:</td>
							<td>
								<input type="text" name="item_appearances|red" value="<?= $item_appearance['red'] ?>" class="medium" />
								<input type="hidden" name="orig_red" value="<?= $item_appearance['red'] ?>" />
							</td>
							
							<td class="LabelRight">Green:</td>
							<td>
								<input type="text" name="item_appearances|green" value="<?= $item_appearance['green'] ?>" class="medium" />
								<input type="hidden" name="orig_green" value="<?= $item_appearance['green'] ?>" />
							</td>
							
							<td class="LabelRight">Blue:</td>
							<td>
								<input type="text" name="item_appearances|blue" value="<?= $item_appearance['blue'] ?>" class="medium" />
								<input type="hidden" name="orig_blue" value="<?= $item_appearance['blue'] ?>" />
							</td>
						</tr>
						<tr>
							<td class="LabelRight">Highlight Red:</td>
							<td>
								<input type="text" name="item_appearances|highlight_red" value="<?= $item_appearance['highlight_red'] ?>" class="medium" />
								<input type="hidden" name="orig_highlight_red" value="<?= $item_appearance['highlight_red'] ?>" />
							</td>
							
							<td class="LabelRight">Highlight Green:</td>
							<td>
								<input type="text" name="item_appearances|highlight_green" value="<?= $item_appearance['highlight_green'] ?>" class="medium" />
								<input type="hidden" name="orig_highlight_green" value="<?= $item_appearance['highlight_green'] ?>" />
							</td>
							
							<td class="LabelRight">Highlight Blue:</td>
							<td>
								<input type="text" name="item_appearances|highlight_blue" value="<?= $item_appearance['highlight_blue'] ?>" class="medium" />
								<input type="hidden" name="orig_highlight_blue" value="<?= $item_appearance['highlight_blue'] ?>" />
							</td>
						</tr>
						<tr>
							<td colspan="6" align="center">
								<input type="submit" name="cmd" value="Update" class="submit" />
								<input type="submit" name="cmd" value="Delete" class="submit" />
								<input type="hidden" name="table" value="item_appearances" />
								<input type="hidden" name="object" value="Edit Item Appearance" />
							</td>
							<?php
						}
						else {
							?>
							<td class="LabelRight">ID:</td>
							<td>new</td>
														
							<td class="LabelRight">Item ID:</td>
							<td><input type="text" name="item_appearances|item_id" value="<?= $id ?>" class="medium" style="background-color:#ddd;" readonly /></td>
														
							<td class="LabelRight">Equip Type:</td>
							<td><input type="text" name="item_appearances|equip_type" value="" class="medium" /></td>
						</tr>
						<tr>
							<td class="LabelRight">Red:</td>
							<td><input type="text" name="item_appearances|red" value="255" class="medium" /></td>
														
							<td class="LabelRight">Green:</td>
							<td><input type="text" name="item_appearances|green" value="255" class="medium" /></td>
														
							<td class="LabelRight">Blue:</td>
							<td><input type="text" name="item_appearances|blue" value="255" class="medium" /></td>
						</tr>
						<tr>
							<td class="LabelRight">Highlight Red:</td>
							<td><input type="text" name="item_appearances|highlight_red" value="255" class="medium" /></td>
														
							<td class="LabelRight">Highlight Green:</td>
							<td><input type="text" name="item_appearances|highlight_green" value="255" class="medium" /></td>
														
							<td class="LabelRight">Highlight Blue:</td>
							<td><input type="text" name="item_appearances|highlight_blue" value="255" class="medium" /></td>
						</tr>
						<tr>
							<td colspan="6" align="center">
								<input type="submit" name="cmd" value="Insert" class="submit" />
								<input type="hidden" name="table" value="item_appearances" />
								<input type="hidden" name="object" value="Add Item Appearance" />
							</td>
							<?php
						}
						?>
					</tr>
				</table>
			</form>
		</div>
		<?php
	}
	
	private function Item_Details() {
		
	}
	
	private function Item_Details_Adornments() {
		
	}
	
	private function Item_Details_Armor() {
		
	}
	
	private function Item_Details_Armorset() {
		
	}
	
	private function Item_Details_Bag() {
		
	}
	
	private function Item_Details_Bauble() {
		
	}
	
	private function Item_Details_Book() {
		
	}
	
	private function Item_Details_Decorations() {
		
	}
	
	private function Item_Details_Food() {
		
	}
	
	private function Item_Details_House() {
		
	}
	
	private function Item_Details_House_Container() {
		
	}
	
	private function Item_Details_Itemset() {
		
	}
	
	private function Item_Details_Marketplace() {
		
	}
	
	private function Item_Details_Pattern() {
		
	}
	
	private function Item_Details_Range() {
		
	}
	
	private function Item_Details_Recipe() {
		
	}
	
	private function Item_Details_Recipe_Items() {
		
	}
	
	private function Item_Details_Shield() {
		
	}
	
	private function Item_Details_Skill() {
		
	}
	
	private function Item_Details_Skills() {
		
	}
	
	private function Item_Details_Thrown() {
		
	}
	
	private function Item_Details_Weapon() {
		
	}
}

?>