<?php
/*  
    EQ2Editor:  Everquest II Database Editor v2.0
    Copyright (C) 2007  EQ2EMulator Development Team (http://www.eq2emulator.net)

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

class eq2Spawns
{

	public function __construct()
	{
		include_once("eq2SpawnsDB.class.php");
		$this->db = new eq2SpawnsDB();
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
					case "summary"	: break;
					case "edit"			: 
					default					: $this->SpawnLookup(); break;
				}
				break;
		}
		
		// end page
		print('</td></tr></table>');
	}
	
	private function SpawnLookup()
	{
		global $eq2;
		$ret = false;
		
		?>
		<div>
			<table class="SearchFilters">
				<tr>
					<th style="width:300px;">Filters</th>
					<th style="width:300px;">&nbsp;</th>
					<th style="text-align:left;">&nbsp;</th>
				</tr>
				<tr>
					<td class="label">Pick:</td>
					<td>
						<select name="zoneID" onchange="dosub(this.options[this.selectedIndex].value)" style="width: 500px">
							<option value="index.php?page=spawns">Pick a Zone</option>
							<?= $this->db->GetZones(); ?>
							<?php
								if (isset($_GET['zone'])) printf('<option value="index.php?page=spawns&zone%s">Spawn Type</option>', $_GET['zone'] );
							?>
						</select>
					</td>
					<?php
						if (isset($_GET['zone'])) { ?>
							<td><?php $this->SpawnTypePicker() ?></td>
							<?php
						} 
					?>

				</tr>
			</table>
			<script language="JavaScript">
			<!--
			document.getElementById('txtSearch').focus = true;
			//-->
			</script>
		</div><!-- End SearchAll -->
		<br />
		<?php
		
		

		// is the return value ever really used?
		if( $ret ) 
			return;
	}

	private function SpawnTypePicker() {
		$typeArray = Array(
			0 => "NPCs",
			1 => "Objects",
			2 => "Signs",
			3 => "Widgets",
			4 => "Ground"
		);
	
		print('<select name="Spawntype" onchange="dosub(this.options[this.selectedIndex].value)">');
		printf('<option value="index.php?page=spawns&zone%s">Spawn Type</option>', $_GET['zone'] );
		
		foreach($typeArray as $key=>$name)
			printf('<option value="index.php?page=spawns&zone=%s&type=%s%s"%s>%s</option>', $_GET['zone'], $key, $name,( isset($_GET['type']) && $key == $_GET['type']  ) ? " selected" : "", $name);
		print('</select>');
	}
	
}

?>