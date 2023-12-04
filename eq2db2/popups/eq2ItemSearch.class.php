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

class eq2ItemSearch
{

	public function __construct()
	{
		include_once("eq2ItemSearchDB.class.php");
		$this->db = new eq2ItemSearchDB();
	}
	
	
	public function Start()
	{
		global $eq2;
		global $page_lc;
		global $type;
	
		print('<table cellspacing="0" id="main-body" class="main">');

		// start main page
		print('</td><td class="page-text"><div id="PageTitle">Item Search</div>');
		?>

		<div id="SearchControls">
			<table>
				<tr>
					<td class="label">Lookup:</td>
					<td>
						<form action="popup.php?type=item_search" id="frmSearch" method="post">
							<!-- onkeyup="ServerLookupAJAX('<?= $_GET['type'] ?>');" -->
							<input type="text" id="txtSearch" name="txtSearch" alt="Search Criteria" autocomplete="off" class="box" value="<?= isset($_POST['txtSearch']) ? $_POST['txtSearch'] : '' ?>" onclick="this.value='';" />
							<input type="submit" id="cmdSearch" name="cmdSearch" value="Search" alt="Run Search" class="submit" />
							<input type="button" value="Clear" class="submit" onclick="dosub('popup.php?type=item_search');" />
							<input type="hidden" name="cmd" value="ItemSearch" />
							<div id="search_suggest">
							</div>
						</form>
						<script>
							document.getElementById('txtSearch').focus = true;
						</script>
					</td>
				</tr>
			</table>
		</div>
		<?php

		if (isset($_POST['txtSearch'])) {
			$items = $this->db->SearchItems($_POST['txtSearch']);
			$this->DisplaySearchGrid($items);
		}

		?>
		<div id="EditorStatus">
			<?php 
			if( !empty($eq2->Status) ) 
				$eq2->DisplayStatus(); 
			?>
		</div>
		<?php
		// end page
		print('</td></tr></table>');
	}
	
	private function DisplaySearchGrid($items)
	{
		?>
		<div id="SelectGrid">
		
		<table id ="SelectGrid" cellspacing="0" border="0">
			<tr>
				<td class="title" align="center" colspan="6">Ground Spawns</td>
			</tr>
			<tr>
				<th width="5%">id</th>
				<th width="8%">type</th>
				<th width="12%">name</th>
				<th width="55%">description</th>
				<th width="10%">soe id</th>
				<th width="10%">soe crc</th>
			</tr>
			
			<?php
			if (is_array($items))
			{
				$i = 0;
				foreach ($items as $data)
				{
					$RowColor = ( $i % 2 ) ? "row1" : "row2";
					?>
					<tr class="<?= $RowColor ?>">
						<td>&nbsp;<?= $data['id'] ?>&nbsp;</td>
						<td>&nbsp;<?= $data['item_type'] ?>&nbsp;</td>
						<td>&nbsp;<?= $data['name'] ?>&nbsp;</td>
						<td>&nbsp;<?= $data['description'] ?>&nbsp;</td>
						<td>&nbsp;<?= $data['soe_item_id'] ?>&nbsp;</td>
						<td>&nbsp;<?= $data['soe_item_crc'] ?>&nbsp;</td>
					</tr>
					<?php
					$i++;
				}
			}
			?>
		</table>
		</div>
		<?php
	}
}
?>