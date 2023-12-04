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

class eq2Site
{

	public $Status;

	public function __construct()
	{
		include_once("eq2SiteDB.class.php");
		$this->db = new eq2SiteDB();
	}


	public function Start()
	{
		global $eq2;

		/* Defined in index.php so get them with global
		$page_lc = $_GET['page'];
		$page_uc = ucfirst($_GET['page']);
		$type = $_GET['type'];*/
		global $page_lc;
		global $page_uc;
		global $type;
		
		?>
	
		<table cellspacing="0" id="main-body" class="main">
			<!-- start left menu -->
			<tr>
				<td class="sidebar" nowrap="nowrap"><strong>Options:</strong><br />
				
				<!--  other menu items here -->
				<ul>
				<li><a href="?page=home&type=news">Current News</a>
				<li><a href="?page=home&type=archive">Archive News</a>
				<li><a href="?page=home&type=inactive">Inactive News</a>

				
				<!-- Following 2 are temporary for quick access to item search for now -->
				<br />
				<li><a href="#" onclick="javascript:window.open('popup.php?type=item_search','lookup','width=1024,height=768,left=10,top=75,scrollbars=yes');">Item Search</a><br />
				</ul>

				<?php							
				// if( M_ADMIN & $eq2->user_role || 16 & $eq2->user_role ) // this menu is for GM's or greater, not guides.
				if( G_ADMIN & $eq2->user_role ) // this menu is for GM's or greater, not guides.
				{
					?>
					<br /><strong>Admin:</strong><br />
					<ul><li><a href="?page=admin&type=newsedit">News Editor</a></ul><br />
					<?php
				}
				?>

				<br /><strong>Editor Help:</strong><br />
				<ul>
				<li><a href="?page=help&cat=news">News</a>
				<li><a href="?page=help&cat=characters">Characters</a>
				<li><a href="?page=help&cat=items">Items</a>
				<li><a href="?page=help&cat=quests">Quests</a>
				<li><a href="?page=help&cat=scripts">Scripts</a>
				<li><a href="?page=help&cat=spells">Spells</a>
				<li><a href="?page=help&cat=spawns">Spawns</a>
				<li><a href="?page=help&cat=server">Server</a>
				<li><a href="?page=help&cat=admin">Admin</a><br />
				</ul>
				
				<!-- nav stuff here -->
				<br /><a href="<?= $eq2->PageLink ?>">Reload Page</a><br />
				<?php 
				if( isset($_GET['id']) ) {
					?>
					<a href="<?= $eq2->BackLink ?>">Back</a><br />
					<?php
				}
				?>
		
				<!-- Help Links / Stats Details -->
				<br /><strong><?= $page_uc ?> Help:</strong><br />&nbsp;
				<br /><strong><?= $page_uc ?> Stats:</strong><br />&nbsp;
		
				<!-- start main page -->
				</td>
				<td class="page-text">
					<div id="PageTitle"><?= $eq2->PageTitle ?></div>
				
					<?php
					switch($page_lc)
					{
					case "help":
						// use common (eq2Functions) class to display non-specific data
						$help_cat = ( isset($_GET['cat']) ) ? $_GET['cat'] : "";
						$eq2->DisplaySiteText($page_lc, $help_cat); 
					break;
					default: 
						$this->DisplayNews(/*$type*/);
					break;
					}
					?>
				
				<!-- end page -->
				</td>
			</tr>
		</table>
		<?php 
	}


	/*
		Function: DisplaySiteText()
		Purpose	: Displays the various eq2editor.site_text pages
		Params	: $type is site_text.type, and $subtype is site_text.subtype (for different levels of news/text/help
	*/
	public function DisplayNews()
	{
		global $eq2;
		
		?>
		<table style="width:100%; border-collapse:collapse; border-spacing:0; padding:4px;">
		<?php 
			$row = $this->db->GetNews(isset($_GET['type']) ? $_GET['type'] : null);
			if( is_array($row) )
			{
				foreach($row as $data)
				{
		?>
					<tr style="background-color:#ccc;">
						<td>
							<div style="float:left; font-weight:bold; font-size:13px;"><?= $data['title'] ?></div>
							<div style="float:right">by <?= $data['username'] ?> on <?= date('Y.m.d', $data['created_date']) ?></div>
						</td>
					</tr>
					<tr>
						<td style="border:1px #999999 solid;"><p><?= $data['description'] ?></p></td>
					</tr>
		<?php
				}
			}
			else
			{
				$eq2->AddStatus("No text found.");
			}
		?>
		</table>
		<div id="EditorStatus">
		<?php 
			if( !empty($eq2->Status) ) $eq2->DisplayStatus(); 
		?>
		</div>
		<?php
	}
	
	
}
?>