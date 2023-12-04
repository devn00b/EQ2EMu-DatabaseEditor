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
	die();


class eq2Admin
{

	var $page_lc;
	var $page_uc;
	var $type;
	
	public function __construct()
	{

		include_once("eq2AdminDB.class.php");
		// open DB instance
		$this->db = new eq2AdminDB();
		
	}

	/*
		Function: AddDatasource()
		Purpose	:	Adds a new MySQL DB to the DBPicker
	*/
	public function AddDatasource()
	{
		global $eq2;

		// Perform updates here
		if( isset($_POST['cmd']) )
		{
		
			// Parse Roles array and reset
			if( is_array($_POST['users|role']) )
			{
				foreach($_POST['users|role'] as $role)
					$new_role = $new_role + $role;
				$_POST['users|role'] = $new_role;
			}
			
			// Handle empty Checkbox
			if( empty($_POST['users|inactive']) && $_POST['orig_inactive'] )
				$_POST['users|inactive'] = 0;

			// Form Validation
			if( isset($_POST['users|username']) && strlen($_POST['users|username']) > 3 )
			{
				if( isset($_POST['users|password']) && strlen($_POST['users|password']) > 3 )
				{
					if( $_POST['users|password'] === $_POST['password2'] )
					{
						$_POST['users|password'] = md5($_POST['users|password']);
						$eq2->ProcessInsert();
						$eq2->AddStatus("User Added.");
					}
					else
					{
						$eq2->AddStatus("Passwords do not match.");
					}
				
				}
				else
				{
					$eq2->AddStatus("User Password must be at least 4 characters in length.");
				}
			}
			else
			{
				$eq2->AddStatus("User Name must be at least 4 characters in length.");
			}
		}
		
	/* 
		TODO: Create Popup/Lookup for developer characters (status > 0) on the configured Dev server 
	*/
	?>
		<!-- Start AddUser -->
		<div id="Editor">
			<table cellspacing="0" border="1">
			<form method="post">
				<tr>
					<td width="30%" class="title">&nbsp;</td>
					<td width="40%" align="center" class="title">Add User</td>
					<td width="30%" class="title">&nbsp;</td>
				</tr>
				<tr>
					<td valign="top" colspan="2">
						<table class="SubPanel">
							<tr>
								<td colspan="2" align="center"><strong>User Info</strong></td>
							</tr>
							<tr>
								<td width="40%" align="right">id:</td>
								<td>
									<input type="text" name="id" value="new" disabled />
								</td>
							</tr>
							<tr>
								<td width="40%" align="right">Name:</td>
								<td nowrap="nowrap">
									<input type="text" name="users|username" value="" />&nbsp;[<a href="" target="_blank">lookup dev char</a>]
								</td>
							</tr>
							<tr>
								<td align="right">Password:</td>
								<td><input type="password" name="users|password" value="" /></td>
							</tr>
							<tr>
								<td align="right">Verify Password:</td>
								<td><input type="password" name="password2" value="" /></td>
							</tr>
							<tr>
								<td align="right">Title:</td>
								<td>
									<input type="text" name="users|title" value="" />
								</td>
							</tr>
							<tr>
								<td align="right">Default Datasource:</td>
								<td>
									<select name="users|datasource">
										<option value="-1">Not Set</option>
									<?php $eq2->SelectDataSource(0); ?>
									</select>
								</td>
							</tr>
							<tr>
								<td align="right">Account Disabled:</td>
								<td>
									<input type="checkbox" name="users|inactive" value="0" />
								</td>
							</tr>
						</table>
					</td>
					<td valign="top">
						<?php $this->DisplayRoleOptions(0); ?>
					</td>
				</tr>
				<tr>
					<td colspan="3" align="center">
						<input type="submit" name="cmd" value="Insert" />&nbsp;
						<input type="hidden" name="table" value="users" />
						<input type="hidden" name="orig_role" value="0" />
						<input type="hidden" name="object" value="new_user" />
					</td>
				</tr>
			</form>
			</table>
		</div>
		<!-- End AddUser -->
	<?php
	}
	
	
	/*
		Function: 
		Purpose	:	
		Params	: 
	*/
	private function AddUser()
	{
		global $eq2;

		// Perform updates here
		if( isset($_POST['cmd']) )
		{
		
			// Parse Roles array and reset
			if( isset($_POST['users|role']) && is_array($_POST['users|role']) )
			{
				$new_role = 0;
				foreach($_POST['users|role'] as $role) {
					if (($new_role & $role) == 0)
						$new_role = $new_role + $role;
				}
				$_POST['users|role'] = $new_role;
			}
			
			// Handle empty Checkbox
			if( empty($_POST['users|is_active']) && isset($_POST['orig_is_active']) && $_POST['orig_is_active'] )
				$_POST['users|inactive'] = 0;

			// Form Validation
			if( isset($_POST['users|username']) && strlen($_POST['users|username']) > 3 )
			{
				if( isset($_POST['users|password']) && strlen($_POST['users|password']) > 3 )
				{
					if( $_POST['users|password'] === $_POST['password2'] )
					{
						$_POST['users|password'] = md5($_POST['users|password']);
						$sql = sprintf("INSERT INTO eq2editor.users (username, displayname, password, title, is_active, reset_password, role) VALUES ('%s', '%s', '%s', '%s', %u, %u, %u)", $_POST['users|username'], $_POST['users|username'], $_POST['users|password'], $_POST['users|title'], $_POST['users|is_active'], $_POST['users|reset_password'], $_POST['users|role']);
						$eq2->ProcessInsert($sql);
						$eq2->AddStatus("User Added.");
					}
					else
					{
						$eq2->AddStatus("Passwords do not match.");
					}
				
				}
				else
				{
					$eq2->AddStatus("User Password must be at least 4 characters in length.");
				}
			}
			else
			{
				$eq2->AddStatus("User Name must be at least 4 characters in length.");
			}
		}
		
	/* 
		TODO: Create Popup/Lookup for developer characters (status > 0) on the configured Dev server 
	*/
	?>
		<!-- Start AddUser -->
		<div id="Editor">
			<table cellspacing="0" border="1">
			<form method="post">
				<tr>
					<td width="30%" class="title">&nbsp;</td>
					<td width="40%" align="center" class="title">Add User</td>
					<td width="30%" class="title">&nbsp;</td>
				</tr>
				<tr>
					<td valign="top" colspan="2">
						<table class="SubPanel">
							<tr>
								<td colspan="2" align="center"><strong>User Info</strong></td>
							</tr>
							<tr>
								<td width="40%" align="right">id:</td>
								<td>
									<input type="text" name="id" value="new" disabled />
								</td>
							</tr>
							<tr>
								<td width="40%" align="right">Name:</td>
								<td nowrap="nowrap">
									<input type="text" name="users|username" value="" />&nbsp;[<a href="" target="_blank">lookup dev char</a>]
								</td>
							</tr>
							<tr>
								<td align="right">Password:</td>
								<td><input type="password" name="users|password" value="1234" /></td>
							</tr>
							<tr>
								<td align="right">Verify Password:</td>
								<td><input type="password" name="password2" value="1234" /></td>
							</tr>
							<tr>
								<td align="right">Title:</td>
								<td>
									<input type="text" name="users|title" value="Content Designer" />
								</td>
							</tr>
							<!--<tr>
								<td align="right">Default Datasource:</td>
								<td>
									<select name="users|datasource">
										<option value="-1">Not Set</option>
									<?php //$eq2->SelectDataSource(0); ?>
									</select>
								</td>
							</tr>-->
							<tr>
								<td align="right">Account Active:</td>
								<td>
									<input type="checkbox" name="users|is_active" value="1" checked />
								</td>
							</tr>
							<tr>
								<td align="right">Reset Password:</td>
								<td>
									<input type="checkbox" name="users|reset_password" value="1" checked />
								</td>
							</tr>
						</table>
					</td>
					<td valign="top">
						<?php $this->DisplayRoleOptions(); ?>
					</td>
				</tr>
				<tr>
					<td colspan="3" align="center">
						<input type="submit" name="cmd" value="Insert" />&nbsp;
						<input type="hidden" name="table" value="users" />
						<input type="hidden" name="orig_role" value="0" />
						<input type="hidden" name="object" value="new_user" />
					</td>
				</tr>
			</form>
			</table>
		</div>
		<!-- End AddUser -->
	<?php
	}
	
	
	/*
		Function: 
		Purpose	:	
		Params	: 
	*/
	private function DisplayDatasourcesGrid($datasources)
	{
		global $eq2;
		
		if( !is_array($datasources) )
			return;
		
		if( isset($_GET['id']) && $_GET['id'] == 'add' )
		{
			$this->AddDatasource();
			return;
		}
		else
		{
			echo $eq2->TestExtends;
			?>
			<br />
			<div id="SelectGrid">
			<table cellspacing="0" id="SelectGrid" border="0">
				<tr>
					<td colspan="6"><input type="button" value="Add Datasource" class="Submit" onclick="dosub('<?= $eq2->BackLink ?>&id=add');" /></td>
				</tr>
				<tr>
					<td class="title">&nbsp;</td>
					<td colspan="6" align="center" class="title">Edit Datasources</td>
					<td class="title">&nbsp;</td>
				</tr>
				<tr>
					<th width="5%">&nbsp;</th>
					<th width="5%">id</th>
					<th width="15%">display</th>
					<th width="10%">name</th>
					<th width="40%">description</th>
					<th width="20%">host</th>
					<th width="5%">active</th>
					<th>&nbsp;</th>
				</tr>
				<?php
				
				$i = 0;
				foreach($datasources as $data)
				{
					$RowColor = ( $i % 2 ) ? "row1" : "row2";
				?>
				<tr class="<?= $RowColor ?>">
					<td class="detail">&nbsp;[&nbsp;<a href="<?= $eq2->PageLink ?>&id=<?= $data['id'] ?>">Edit</a>&nbsp;]</td>
					<td class="detail" align="right">&nbsp;<?= $data['id'] ?>&nbsp;</td>
					<td class="detail">&nbsp;<?= $data['db_display_name'] ?></td>
					<td class="detail">&nbsp;<?= $data['db_name'] ?></td>
					<td class="detail">&nbsp;<?= $data['db_description'] ?></td>
					<td class="detail">&nbsp;<?= $data['db_host'] ?></td>
					<td class="detail">&nbsp;<?= $data['is_active'] ?></td>
					<td>&nbsp;</td>
				</tr>
				<?php
					$i++;
				}
			?>
			</table>
			</div><!-- End SelectGrid -->
			<?php
			$eq2->AddStatus($i . " records found.");
		}
	}


	private function DisplayRoleOptions($user = null)
	{
		$roles = $this->db->GetRoles();

		$roleval = isset($user) ? intval($user['role']) : 1234;

		if( is_array($roles) )
		{
			?>
			<table class="inner">
				<tr>
					<td colspan="2" align="center"><strong>Permissions</strong></td>
				</tr>
			<?php	
			$i = 0;
			foreach($roles as $role)
			{
				//$checked = ( (intval($role['role_value']) & intval($roleval)) > 0 ) ? " checked" : "";
				$checked = ($roleval & $role['role_value']) > 0 ? " checked" : "";
			?>
				<tr>
					<td width="80%" align="right" nowrap="nowrap"><?= $role['role_description'] ?>:</td>
					<td width="20%" align="left"><input type="checkbox" name="users|role[<?= $i ?>]" value="<?= $role['role_value'] ?>"<?= $checked ?> /></td>
				</tr>
			<?php
				$i++;
			}
			?>
			</table>
			<?php	
		}
	}


	/*
		Function: 
		Purpose	:	
		Params	: 
	*/
	private function DisplayUserGrid($users)
	{
		global $eq2;
		
		?>
		<br />
		<div id="SelectGrid">
		<table cellspacing="0" id="SelectGrid" border="0">
			<tr>
				<td class="title">&nbsp;</td>
				<td colspan="3" align="center" class="title">Editor Users</td>
				<td class="title">&nbsp;</td>
			</tr>
			<tr>
				<th width="5%">&nbsp;</th>
				<th width="5%">user_id</th>
				<th width="60%">username</th>
				<th width="30%">role(s)</th>
				<th>&nbsp;</th>
			</tr>
			<?php
			
			$i = 0;
			foreach($users as $data)
			{
				$RowColor = ( $i % 2 ) ? "row1" : "row2";
			?>
			<tr class="<?= $RowColor ?>">
				<td class="detail">&nbsp;[&nbsp;<a href="?page=admin&type=users&id=<?= $data['id'] ?>">Edit</a>&nbsp;]</td>
				<td class="detail" align="right">&nbsp;<?= $data['id'] ?>&nbsp;</td>
				<td class="detail">&nbsp;<?= $data['username'] ?></td>
				<td class="detail"><?= $eq2->DisplayRoleListToolTip($data['role']) ?></td>
				<td>&nbsp;</td>
			</tr>
			<?php
				$i++;
			}
		?>
		</table>
		</div><!-- End SelectGrid -->
		<?php
		$eq2->AddStatus($i . " records found.");
	}


	private function DisplayUserMessages($user)
	{
		global $eq2;

		?>
		<table class="inner">
			<tr>
				<td align="center"><strong>Messages</strong></td>
			</tr>
		<?php	
		$messages = $this->db->GetMessages($user['id']);
		if( is_array($messages) )
		{
			foreach($messages as $message)
			{
				$message_from = $this->db->GetUserInfo($message['from_user_id']);
			?>
				<tr>
					<td><strong>From:</strong> <?= $message_from['username'] ?></td>
				</tr>
				<tr>
					<td><strong>Subject:</strong> <?= $message['subject'] ?></td>
				</tr>
				<tr>
					<td><strong>Date:</strong> <?= date("m-d-Y H:i a", $message['message_date']) ?></td>
				</tr>
				<tr>
					<td>
						<br />
						<?= $message['message_text'] ?></td>
				</tr>
			<?php
			}
		}
		else
		{
			print("<tr><td>No Messages.</td></tr>"); //$eq2->AddStatus("No Messages.");
		}
		?>
		</table>
		<?php	
	}
	
	
	private function Roles()
	{
	}


	public function Start()
	{
		global $eq2;

		if( !(M_ADMIN & $eq2->user_role || G_ADMIN & $eq2->user_role) )
		{
			print('<p>&nbsp;</p><p><b style="color:#f00">Access Denied!</b> Click <a href="index.php"><u>here</u></a> to return to Home page.</p><p>&nbsp;</p>');
			return;
		}

		global $page_lc;// = $_GET['page'];
		global $page_uc;// = ucfirst($_GET['page']);
		global $type;// = isset($_GET['type']) ? $_GET['type'] : null;
		
		?>
		<table cellspacing="0" id="main-body" class="main">
			<tr>
				<td class="sidebar" nowrap="nowrap">
					<strong>Admin:</strong><br />
					<li><a href="?page=<?= $page_lc ?>"><?= $page_uc ?> News</a><br />
					<li><a href="?page=<?= $page_lc ?>&type=stats">Server Stats</a><br />
					<li><a href="?page=<?= $page_lc ?>&type=users">User Manager</a><br />
					<li><a href="?page=<?= $page_lc ?>&type=settings">Editor Settings</a><br />
					<li><a href="?page=<?= $page_lc ?>&type=newsedit">News Editor</a><br />
					<br />
					<strong>Project:</strong><br />
					<li><a href="?page=<?= $page_lc ?>">Tasks</a><br />
					<li><a href="?page=<?= $page_lc ?>">ChangeLogs</a><br />
					<li><a href="?page=<?= $page_lc ?>">SQL Logs</a><br />
					<li><a href="?page=<?= $page_lc ?>&type=roles">Roles Viewer</a><br />
					<br />
					<strong>Migrate Data:</strong><br />
					<li><a href="?page=<?= $page_lc ?>">Zones</a><br />
					<li><a href="?page=<?= $page_lc ?>">Items</a><br />
					<li><a href="?page=<?= $page_lc ?>">Spells</a><br />
					<li><a href="?page=<?= $page_lc ?>">Merchants</a><br />
					<li><a href="?page=<?= $page_lc ?>">Locations</a><br />
					<br />
					<strong>Purge:</strong><br />
					<li><a href="?page=<?= $page_lc ?>">Purge a Zone</a><br />
					<br />
					<strong>Scripting:</strong><br />
					<li><a href="?page=<?= $page_lc ?>">Dialogs</a><br />
					<li><a href="?page=<?= $page_lc ?>">Conversations</a><br />
					<li><a href="?page=<?= $page_lc ?>">Movement</a><br />
					<li><a href="?page=<?= $page_lc ?>">Voiceovers</a><br />
					<li><a href="?page=<?= $page_lc ?>">Validate Scripts</a><br />
					<br />

					<li><a href="?page=<?= $page_lc ?>">Help</a><br />
					<li><a href="<?= $eq2->PageLink ?>">Reload Page</a><br />
					<?php 
					if( isset($_GET['id']) )
						printf("<li><a href=\"%s\">Back</a><br />", $eq2->BackLink);
					else
						print("<li>&nbsp;<br />");
					?>
					&nbsp;
				</td>
				<td class="page-text">
					<div id="PageTitle"><?= $eq2->PageTitle ?></div>
					<?php
					switch($page_lc)
					{
						case "help": 
							// use common (eq2Functions) class to display non-specific data
							$eq2->DisplaySiteText($page_lc, $_GET['cat']); 
							break;
							
						default:
							switch($type)
							{
								case "roles"		:	$this->Roles(); break;
								case "settings"		:	$this->Settings(); break;
								case "stats"		:	$this->ServerStats(); break;
								case "users"		:	$this->UserManager(); break;
								case "newsedit"		:	$this->News(); break;
								default				: 
									$eq2->DisplaySiteText('welcome', $page_lc);	
									// Clear status as it was already printed in the above function
									// and prints again in the function below resulting in the status 
									// for both being printed, for example "Status:   No text found.   No text found."
									// maybe have the status clear after it is printed?
									$eq2->ClearStatus();
									$eq2->DisplaySiteText('news', $page_lc);	
									break;
							}
							break;
					}
					?>
				</td>
			</tr>
		</table>
		<?php
	}

	
	/*
		Function: Settings()
		Purpose	:	DB Editor Settings page.
	*/
	private function Settings()
	{
		global $eq2;
		
		// Build the Tab menu
		$current_tab_idx = ( isset($_GET['tab']) ) ? $_GET['tab'] : 'general';
		$tab_array = array(
			'general' 	=> 'General',
			'data' 			=> 'Data Sources',
			'paths' 		=> 'Paths',
			'logging' 	=> 'Logging',
			'misc' 			=> 'Misc',
		);
		$eq2->TabGenerator2($current_tab_idx, $tab_array, false);

		switch($current_tab_idx)
		{
			case "data"			: $this->Settings_DataSources(); break;
			case "paths"		: $this->Settings_Paths(); break;
			case "logging"	: $this->Settings_Logging(); break;
			case "misc"			: $this->Settings_Misc(); break;
			case "general"	:
			default					: $this->Settings_General(); break;
		}

	}


	/*
		Function: Settings_General()
		Purpose	:	Editor Settings - General tab screen
	*/
	function Settings_General()
	{
		// build a new form
		$eq2Form = new eq2FormBuilder();
		
		$properties = array("div"=>"SettingsGrid");
		//$eq2Form->NewContainer($properties['div']);
		//$eq2Form->NewTable($properties);
		//exit;
		
		?>
		<div id="SettingsGrid">
		<div id="update_status" class="status">&nbsp;</div>
		<table cellspacing="0" id="SettingsGrid" border="0">
		<?php
			
		?>
			<tr>
				<td class="label">App Name</td>
				<td class="detail">
					<input type="text" name="config|app_name" value="<?= $GLOBALS['config']['app_name'] ?>" class="text" onkeypress="isDirty(this);" onblur="AJAXSaveData(this);" />
					<input type="hidden" name="config|app_name" id="app_name" value="<?= $GLOBALS['config']['app_name'] ?>" />			
				</td>
			</tr>
			<tr>
				<td class="label">App Short Name</td>
				<td class="detail">
					<input type="text" name="config|app_short_name" value="<?= $GLOBALS['config']['app_short_name'] ?>" class="text" onkeypress="isDirty(this);" onblur="AJAXSaveData(this);" />
					<input type="hidden" name="config|app_short_name" id="app_short_name" value="<?= $GLOBALS['config']['app_short_name'] ?>" />			
				</td>
			</tr>
			<tr>
				<td class="label">App Version</td>
				<td class="detail">
				<input type="text" name="config|app_version" value="<?= $GLOBALS['config']['app_version'] ?>" style="width:350px;" />
				</td>
			</tr>
			<tr>
				<td class="label">Language</td>
				<td class="detail">
				<input type="text" name="config|language" value="<?= $GLOBALS['config']['language'] ?>" />
				</td>
			</tr>
			<tr>
				<td class="label">News Age (Days)</td>
				<td class="detail">
				<input type="text" name="config|news_age_days" value="<?= $GLOBALS['config']['news_age_days'] ?>" style="width:50px;" />
				</td>
			</tr>
			<tr>
				<td class="label">Cookie Domain</td>
				<td class="detail">
				<input type="text" name="config|cookie_domain" value="<?= $GLOBALS['config']['cookie_domain'] ?>" style="width:350px;" />
				</td>
			</tr>
			<tr>
				<td class="label">Cookie Path</td>
				<td class="detail">
				<input type="text" name="config|cookie_path" value="<?= $GLOBALS['config']['cookie_path'] ?>" style="width:350px;" />
				</td>
			</tr>
			<tr>
				<td class="label">Server ID</td>
				<td class="detail">
				<input type="text" name="config|server_id" value="<?= $GLOBALS['config']['server_id'] ?>" style="width:50px;" />
				</td>
			</tr>
		</table>
		</div>
		<?php
		
		return;
	}

	/*
		Function: Settings_Database()
		Purpose	:	Editor Settings - DataSources tab screen
	*/
	private function Settings_DataSources()
	{
		global $eq2;
		
		$ds = $this->db->GetDatasources();
		$this->DisplayDatasourcesGrid($ds);
	}


	/*
		Function: Settings_Paths()
		Purpose	:	Editor Settings - General tab screen
	*/
	private function Settings_Paths()
	{
		?>
		<div id="SettingsGrid">
		<div id="update_status" class="status">&nbsp;</div>
		<table cellspacing="0" id="SettingsGrid" border="0">
			<tr>
				<td class="label">Scripts Path</td>
				<td class="detail">
					<input type="text" name="config|scripts_path" value="<?= $GLOBALS['config']['script_path'] ?>" class="text" onkeypress="isDirty(this);" onblur="AJAXSaveData(this);" />
					<input type="hidden" name="config|scripts_path" id="scripts_path" value="<?= $GLOBALS['config']['scripts_path'] ?>" />			
				</td>
			</tr>
			<tr>
				<td class="label">Server Log Path</td>
				<td class="detail">
					<input type="text" name="config|log_path" value="<?= $GLOBALS['config']['log_path'] ?>" class="text" onkeypress="isDirty(this);" onblur="AJAXSaveData(this);" />
					<input type="hidden" name="config|log_path" id="log_path" value="<?= $GLOBALS['config']['log_path'] ?>" />			
				</td>
			</tr>
			<tr>
				<td class="label">Server Log Time</td>
				<td class="detail">
					<input type="text" name="config|server_log_time" value="<?= $GLOBALS['config']['server_log_time'] ?>" class="text" onkeypress="isDirty(this);" onblur="AJAXSaveData(this);" />
					<input type="hidden" name="config|server_log_time" id="server_log_time" value="<?= $GLOBALS['config']['server_log_time'] ?>" />			
				</td>
			</tr>
		</table>
		</div>
		<?php
	}


	/*
		Function: Settings_Logging()
		Purpose	:	Editor Settings - Logging tab screen
	*/
	private function Settings_Logging()
	{
		?>
		<div id="SettingsGrid">
		<div id="update_status" class="status">&nbsp;</div>
		<table cellspacing="0" id="SettingsGrid" border="0">
			<tr>
				<td class="label">Log Editor Commands</td>
				<td class="detail">
					Off: <input type="radio" name="config|editor_log" value="off" onclick="ToggleEditorSettingsAJAX(this);"<?php if( $GLOBALS['config']['editor_log']==0 ) print(" checked") ?> />
					 On: <input type="radio" name="config|editor_log" value="on" onclick="ToggleEditorSettingsAJAX(this);"<?php if( $GLOBALS['config']['editor_log']==1 ) print(" checked") ?> />
							 <input type="hidden" name="config|editor_log" id="editor_log" value="<?php print($GLOBALS['config']['editor_log']) ?>" />
				</td>
			</tr>
			<tr>
				<td class="label">Log SQL Queries</td>
				<td class="detail">
					Off: <input type="radio" name="config|sql_log" value="off" onclick="ToggleEditorSettingsAJAX(this);"<?php if( $GLOBALS['config']['sql_log']==0 ) print(" checked") ?> />
					 On: <input type="radio" name="config|sql_log" value="on" onclick="ToggleEditorSettingsAJAX(this);"<?php if( $GLOBALS['config']['sql_log']==1 ) print(" checked") ?> />
						 	 <input type="hidden" name="config|sql_log" id="sql_log" value="<?php print($GLOBALS['config']['sql_log']) ?>" />
				</td>
			</tr>
			<tr>
				<td class="label">Log To SQL File</td>
				<td class="detail">
					Off: <input type="radio" name="config|sql_log_file" value="off" onclick="ToggleEditorSettingsAJAX(this);"<?php if( $GLOBALS['config']['sql_log_file']==0 ) print(" checked") ?> />
					 On: <input type="radio" name="config|sql_log_file" value="on" onclick="ToggleEditorSettingsAJAX(this);"<?php if( $GLOBALS['config']['sql_log_file']==1 ) print(" checked") ?> />
							<input type="hidden" name="config|orig_sql_log_file" id="sql_log_file" value="<?php print($GLOBALS['config']['sql_log_file']) ?>" />
				</td>
			</tr>
		</table>
		</div>
		<?php
	}


	/*
		Function: Settings_Misc()
		Purpose	:	Editor Settings - General tab screen
	*/
	private function Settings_Misc()
	{
		?>
		<div id="SettingsGrid">
		<div id="update_status" class="status">&nbsp;</div>
		<table cellspacing="0" id="SettingsGrid" border="0">
			<tr>
				<td class="label">Debugging</td>
				<td class="detail">
					Off: <input type="radio" name="config|debug" value="off" onclick="ToggleEditorSettingsAJAX(this);"<?php if( $GLOBALS['config']['debug']==0 ) print(" checked") ?> />
					 On: <input type="radio" name="config|debug" value="on" onclick="ToggleEditorSettingsAJAX(this);"<?php if( $GLOBALS['config']['debug']==1 ) print(" checked") ?> />
							 <input type="hidden" name="config|debug" id="debug" value="<?php print($GLOBALS['config']['debug']) ?>" />
				</td>
			</tr>
			<?php if( $GLOBALS['config']['debug']==1 ) { ?>
			<tr>
				<td class="label">Debug Data</td>
				<td class="detail">
					Off: <input type="radio" name="config|debug_data" value="off" onclick="ToggleEditorSettingsAJAX(this);"<?php if( $GLOBALS['config']['debug_data']==0 ) print(" checked") ?> />
					 On: <input type="radio" name="config|debug_data" value="on" onclick="ToggleEditorSettingsAJAX(this);"<?php if( $GLOBALS['config']['debug_data']==1 ) print(" checked") ?> />
							 <input type="hidden" name="config|debug_data" id="debug_data" value="<?php print($GLOBALS['config']['debug_data']) ?>" />
				</td>
			</tr>
			<tr>
				<td class="label">Debug Forms</td>
				<td class="detail">
					Off: <input type="radio" name="config|debug_forms" value="off" onclick="ToggleEditorSettingsAJAX(this);"<?php if( $GLOBALS['config']['debug_forms']==0 ) print(" checked") ?> />
					 On: <input type="radio" name="config|debug_forms" value="on" onclick="ToggleEditorSettingsAJAX(this);"<?php if( $GLOBALS['config']['debug_forms']==1 ) print(" checked") ?> />
							 <input type="hidden" name="config|debug_forms" id="debug_forms" value="<?php print($GLOBALS['config']['debug_forms']) ?>" />
				</td>
			</tr>
			<tr>
				<td class="label">Debug Function</td>
				<td class="detail">
					Off: <input type="radio" name="config|debug_func" value="off" onclick="ToggleEditorSettingsAJAX(this);"<?php if( $GLOBALS['config']['debug_func']==0 ) print(" checked") ?> />
					 On: <input type="radio" name="config|debug_func" value="on" onclick="ToggleEditorSettingsAJAX(this);"<?php if( $GLOBALS['config']['debug_func']==1 ) print(" checked") ?> />
							 <input type="hidden" name="config|debug_func" id="debug_func" value="<?php print($GLOBALS['config']['debug_func']) ?>" />
				</td>
			</tr>
			<tr>
				<td class="label">Debug Query</td>
				<td class="detail">
					Off: <input type="radio" name="config|debug_query" value="off" onclick="ToggleEditorSettingsAJAX(this);"<?php if( $GLOBALS['config']['debug_query']==0 ) print(" checked") ?> />
					 On: <input type="radio" name="config|debug_query" value="on" onclick="ToggleEditorSettingsAJAX(this);"<?php if( $GLOBALS['config']['debug_query']==1 ) print(" checked") ?> />
							 <input type="hidden" name="config|debug_query" id="debug_query" value="<?php print($GLOBALS['config']['debug_query']) ?>" />
				</td>
			</tr>
			<?php } ?>
		</table>
		</div>
		<?php
	}


	/*
		Function: ServerStats()
		Purpose	:	Quick overview of server functionality and data statistics.
		Params	: 
	*/
	private function ServerStats()
	{
		global $eq2;
		
		// Build the Tab menu
		$current_tab_idx = ( isset($_GET['tab']) ) ? $_GET['tab'] : 'quick';
		$tab_array = array(
			'quick'		=> 'Quick Stats',
			'last10'	=> 'Last 10 Players',
			'tables'	=> 'Table Data',
			'zones'		=> 'Zones Data'
		);
		$eq2->TabGenerator2($current_tab_idx, $tab_array);

		switch($current_tab_idx)
		{
			case "last10"	: $this->ServerStats_LastPlayers(); break;
			case "tables"	: $this->ServerStats_TableData(); break;
			case "zones"	: $this->ServerStats_ZoneData(); break;
			case "quick"	:
			default				: $this->ServerStats_PlayerStats(); break;
		}

		// TODO: Someday, get this working again...
		//	print($this->PrintSessions());
	}
	
	
	private function ServerStats_PlayerStats()
	{
		global $eq2;
		
		// build player stats array
		$player_stats['Total Accounts'] 	= $this->db->GetTotalAccounts();
		$player_stats['Total Players'] 		= $this->db->GetTotalCharacters();
		$player_stats['Average Level'] 		= $this->db->GetAverageLevel($player_stats['Total Players']);	
		?>
		<div id="SelectGrid">
		<p>Quick overview of accounts and characters in your server database.</p>
		<table width="100%" cellpadding="4" border="0">
			<tr>
				<td valign="top" width="25%">
					<fieldset><legend>Quick Totals</legend> 
					<table width="100%" cellpadding="2" cellspacing="0" border="0">
						<tr>
							<th width="50%"><strong>Stat</strong></th>
							<th width="50%"><strong>Value</strong></th>
						</tr>
						<?php
						$i = 0;
						foreach($player_stats as $key=>$val)
						{
							$row_class = ( $i % 2 ) ? ' class="row1"' : ' class="row2"';
						?>
						<tr<?php print($row_class) ?>>
							<td><?php print($key) ?></td>
							<td><?php print(round($val)) ?></td>
						</tr>
						<?php
						$i++;
						}
						?>
						<tr>
							<td height="135"></td>
						</tr>
					</table>
					</fieldset>
				</td>
				<td valign="top" width="40%">
					<fieldset><legend>Most Experienced Players</legend> 
					<table width="100%" cellpadding="2" cellspacing="0" border="0">
						<tr bgcolor="#cccccc">
							<td><strong>Player</strong></td>
							<td><strong>Class</strong></td>
							<td align="right"><strong>Levels</strong></td>
							<td align="right"><strong>Quests</strong></td>
						</tr>
						<?php
						$player_data = $this->db->GetMostExperiencedPlayers();
						if( is_array($player_data) )
						{
							$i = 0;
							foreach($player_data as $data)
							{
								$row_class = ( $i % 2 ) ? ' class="row1"' : ' class="row2"';
								?>
								<tr<?php print($row_class) ?>>
									<td><?php print($data['name']) ?></td>
									<td><?php print($eq2->eq2Classes[$data['class']]) ?></td>
									<td align="right"><?php printf("%d / %d", $data['level'], $data['tradeskill_level']) ?></td>
									<td align="right"><?php print($data['quests']) ?></td>
								</tr>
								<?php
								$i++;
							}
						}
					?>
					</table>
					</fieldset>
				</td>
				<td valign="top" width="35%">
					<fieldset><legend>Most Active Quests</legend> 
					<table width="100%" cellpadding="2" cellspacing="0" border="0">
						<tr bgcolor="#cccccc">
							<td><strong>QID</strong></td>
							<td><strong>Quest Name</strong></td>
							<td align="right"><strong>Completed</strong></td>
						</tr>
						<?php
						$quest_data = $this->db->GetMostActiveQuests();
						if( is_array($quest_data) )
						{
							$i = 0;
							foreach($quest_data as $data)
							{
								$row_class = ( $i % 2 ) ? ' class="row1"' : ' class="row2"';
								$pattern[0] = "/Quests\/.*?\/(.*?).lua/";
								$pattern[1] = "/_/";
								$replace[0] = "$1";
								$replace[1] = " ";
								$quest_name = preg_replace($pattern,$replace,$data['lua_script']);
								?>
								<tr<?php print($row_class) ?>>
									<td><?php print($data['quest_id']) ?></td>
									<td><?php print($quest_name) ?></td>
									<td align="right"><?php print($data['num_completed']) ?></td>
								</tr>
								<?php
								$i++;
							}
						}
						?>
					</table>
					</fieldset>
				</td>
			</tr>
		</table>
		<br />
		</div>
		<?php
	}
	
			
	private function ServerStats_LastPlayers()
	{
		global $eq2;
		
		?>
		<div id="SelectGrid">
		<p>This page displays a quick overview of the player and server stats, and content data available on this server.</p>
		<fieldset><legend>Last 10 Players</legend> 
		<table width="100%" cellpadding="2" cellspacing="0" border="0">
			<tr>
				<th><strong>ID</strong></th>
				<th><strong>Account</strong></th>
				<th><strong>Player</strong></th>
				<th><strong>Class</strong></th>
				<th align="right"><strong>Levels</strong></th>
				<th align="right"><strong>Zone</strong></th>
				<th align="right"><strong>Date</strong></th>
			</tr>
			<?php
			$player_data = $this->db->GetLast10Players();
			if( is_array($player_data) )
			{
				$i = 0;
				foreach($player_data as $data)
				{
					$row_class = ( $i % 2 ) ? ' class="row1"' : ' class="row2"';
					if( $data['admin_status'] >= 200 ) 
						$player_name_class = ' style="color:#ff0000; font-weight:bold;"';
					else
						$player_name_class = '';
					?>
					<tr<?php print($row_class) ?>>
						<td><?php print($data['id']) ?></td>
						<td><?php print($data['account_id']) ?></td>
						<td<?php print($player_name_class) ?>><a href="index.php?page=characters&type=edit&id=<? print($data['id']) ?>" class=""><?php print($data['name']) ?></a></td>
						<td><?php print($eq2->eq2Classes[$data['class']]) ?></td>
						<td align="right"><?php printf("%d / %d", $data['level'], $data['tradeskill_level']) ?></td>
						<td align="right"><?php print($eq2->eq2db->GetZoneNameByID($data['current_zone_id'])) ?></td>
						<td align="right"><?php print($data['last_played']) ?></td>
					</tr>
					<?php
					$i++;
				}
			}
				?>
		</table>
		</fieldset>
		<br />
		</div>
		<?php
	}
	
			
	private function ServerStats_TableData()
	{
		global $eq2;
		
		?>
		<div id="SelectGrid">
		<p>This page displays a quick overview of the Table data for your server.</p>
		<fieldset><legend>Table Data</legend> 
		<table cellpadding="2" cellspacing="0" border="0">
			<tr>
				<td valign="top">
			<?php
			$server_stats = $this->db->GetTableData();
			if( is_array($server_stats) )
			{
				$max_rows = (count($server_stats) / 4);
				$max_rows = intval($max_rows);
				$max_cols	= count($server_stats) / $max_rows;

				// draw columns, each with their own list of fields
				$index = 0;
				for( $b = 1; $b <= $max_cols; $b++ )
				{
					$columns = array_slice($server_stats, $index, $max_rows);
					$index = $index + $max_rows; 
					
					$i = 0;
					print('<table cellspacing="0" width="220"><tr><th width="80%"><strong>Table</strong></th><th><strong>Records</strong></th></tr>');
					foreach($columns as $key=>$val)
					{
						$row_class = ( $i % 2 ) ? ' class="row1"' : ' class="row2"';
						printf('<tr%s><td>%s</td><td>%s</td></tr>', $row_class, $key, $val);
						$i++;
					}
					print('</table></td><td valign="top">');
				}
				print('</table>');
			}
			?>
				</td>
			</tr>
		</table>
		</fieldset>
		</div>
		<?php
	}
	
			
	private function ServerStats_ZoneData()
	{
		?>
		<div id="SelectGrid">
		<p>This page displays a quick overview of the Zone configuration data on your server.</p>
		<fieldset><legend>Zones Populated</legend> 
		<table width="100%" cellpadding="2" cellspacing="0" border="0">
			<tr bgcolor="#cccccc">
				<th><strong>Zone</strong></th>
				<th><strong>NPCs</strong></th>
				<th><strong>Objects</strong></th>
				<th><strong>Signs</strong></th>
				<th><strong>Widgets</strong></th>
				<th><strong>Ground</strong></th>
				<th><strong>Loot</strong></th>
				<th><strong>Quests</strong></th>
			</tr>
			<?php
			$zone_data = $this->db->GetZonePopulationData();
			if( is_array($zone_data) )
			{
				$i = 0;
				foreach($zone_data as $data)
				{
					$row_class = ( $i % 2 ) ? ' class="row1"' : ' class="row2"';
					$num_npcs					= $this->db->getSpawnTypeTotalsByZone('npcs', $data['zid']);
					$num_objects			= $this->db->getSpawnTypeTotalsByZone('objects', $data['zid']);
					$num_signs				= $this->db->getSpawnTypeTotalsByZone('signs', $data['zid']);
					$num_widgets			= $this->db->getSpawnTypeTotalsByZone('widgets', $data['zid']);
					$num_groundspawns	= $this->db->getSpawnTypeTotalsByZone('ground', $data['zid']);
					$num_loots				= $this->db->getSpawnTypeTotalsByZone('loot', $data['zid']);
					$num_quests				= $this->db->getTotalQuestsByZone($data['zid']); // barbarian! since there's no way to link a quest to a zone except by it's fookin path! :/
				?>
				<tr<?php print($row_class) ?>>
					<td><?php printf("%s (%d)", $data['description'], $data['zid']) ?></td>
					<td><?php print($num_npcs) ?></td>
					<td><?php print($num_objects) ?></td>
					<td><?php print($num_signs) ?></td>
					<td><?php print($num_widgets) ?></td>
					<td><?php print($num_groundspawns) ?></td>
					<td><?php print($num_loots) ?></td>
					<td><?php print($num_quests) ?></td>
				</tr>
				<?php
					$i++;
				}
			}
		?>
		</table>
		</fieldset>
		</div>
		<?php
	}
	
			
	private function PrintSessions()
	{
		$data = $this->db->GetSessionData();
		if( is_array($data) )
		{
			$page = preg_replace("/\/eq2db\/editors\/(.*?)\.php/","$1.php",$data['session_page']);
			if( $data['admin_status'] >= 200 )
				$sess_color = '#990000';
			else if( $data['admin_status'] >= 150 && $data['admin_status'] < 200 )
				$sess_color = '#000099';
			else if( $data['admin_status'] >= 100 && $data['admin_status'] < 150 )
				$sess_color = '#009900';
			else if( $data['admin_status'] >= 70 && $data['admin_status'] < 100 )
				$sess_color = '#999900';
			else if( $data['admin_status'] >= 50 && $data['admin_status'] < 70 )
				$sess_color = '#009999';
			else if( $data['admin_status'] >= 10 && $data['admin_status'] < 50 )
				$sess_color = '#999999';
			else 
				$sess_color = '#000000';
			printf("<span style=\"color:%s;\" title=\"%s\">%s (%d)%s", $sess_color, $page, $data['name'], $data['session_time'], $num_rows > 1 ? ", " : "");
		}
	}
	

	private function UserEditor()
	{
		global $eq2;

		// Perform updates here
		if( isset($_POST['cmd']) )
		{
		
			// Parse Roles array and reset
			if( is_array($_POST['users|role']) )
			{
				foreach($_POST['users|role'] as $role)
					$new_role = $new_role + $role;
				$_POST['users|role'] = $new_role;
			}
			
			if( empty($_POST['users|inactive']) && $_POST['orig_inactive'] )
				$_POST['users|inactive'] = 0;

			switch($_POST['cmd']) 
			{
				case "Insert": $eq2->ProcessInsert(); break;
				case "Update": $eq2->ProcessUpdate(); break;
				case "Delete": $eq2->ProcessDelete(); break;
			}

		}

		// Load User Info
		$user = $this->db->GetUserInfo();
		if( !is_array($user) )
		{
			$eq2->AddStatus("No user data found.");
			return;
		}
	?>
		<!-- Start UserEditor -->
		<div id="Editor">
			<table cellspacing="0" border="0">
			<form method="post">
				<tr>
					<td width="30%" class="title">&nbsp;</td>
					<td width="40%" align="center" class="title">Editing User: <?= $user['username'] ?></td>
					<td width="30%" class="title">&nbsp;</td>
				</tr>
				<tr>
					<td valign="top" colspan="2">
						<table class="SubPanel">
							<tr>
								<td colspan="2" align="center"><strong>User Info</strong></td>
							</tr>
							<tr>
								<td width="40%" align="right">id:</td>
								<td>
									<input type="text" name="users|id" value="<?= $user['id'] ?>" readonly />
									<input type="hidden" name="orig_id" value="<?= $user['id'] ?>" />
								</td>
							</tr>
							<tr>
								<td width="40%" align="right">Name:</td>
								<td>
									<input type="text" name="users|username" value="<?= stripslashes($user['username']) ?>" />
									<input type="hidden" name="orig_username" value="<?= stripslashes($user['username']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">Change Password:</td>
								<td><input type="password" name="users|password" value="" /></td>
							</tr>
							<tr>
								<td align="right">Verify Password:</td>
								<td><input type="password" name="password2" value="" /></td>
							</tr>
							<tr>
								<td align="right">Title:</td>
								<td>
									<input type="text" name="users|title" value="<?= stripslashes($user['title']) ?>" />
									<input type="hidden" name="orig_title" value="<?= stripslashes($user['title']) ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">Default Datasource:</td>
								<td>
									<select name="users|datasource">
										<option value="-1">Not Set</option>
									<?php $eq2->SelectDataSource($user['datasource_id']); ?>
									</select>
									<input type="hidden" name="orig_datasource" value="<?= $user['datasource_id'] ?>" />
								</td>
							</tr>
							<tr>
								<td align="right">Account Disabled:</td>
								<td>
									<input type="checkbox" name="users|inactive" value="1"<?php if( !$user['is_active'] ) print(" checked") ?> />
									<input type="hidden" name="orig_inactive" value="<?= $user['inactive'] ?>" />
								</td>
							</tr>
						</table>
						<?php $this->DisplayUserMessages($user); ?>
					</td>
					<td valign="top">
						<?php $this->DisplayRoleOptions($user); ?>
					</td>
				</tr>
				<tr>
					<td colspan="3" align="center">
						<input type="submit" name="cmd" value="Update" />&nbsp;
						<input type="submit" name="cmd" value="Delete" />&nbsp;
						<input type="hidden" name="table" value="users" />
						<input type="hidden" name="orig_role" value="<?= $user['role'] ?>" />
					</td>
				</tr>
			</form>
			</table>
		</div>
		<!-- End UserEditor -->
	<?php
	}


	private function UserManager()
	{
		global $eq2;
		$ret = false;

		?>
		<!-- Start UserSelect -->
		<div id="UserSelect">
			<table cellspacing="0" id="UserSelect" border="0">
				<tr>
					<td width="75" align="right"><strong>Pick User:</strong>&nbsp;</td>
					<td class="select">
						<select name="userID" onchange="dosub(this.options[this.selectedIndex].value)" class="user">
							<option value="?page=admin&type=users">Pick a User</option>
							<?= $this->db->GetUserOptions(); ?>
						</select>
					</td>
				</tr>
			</table>
		</div>
		<!-- End UserSelect -->
		<script language="javascript">
		<!--
		//Called from keyup on the search textbox.
		//Starts the AJAX request.
		function UserSearch() {
			if (searchReq.readyState == 4 || searchReq.readyState == 0) {
				var str = escape(document.getElementById('txtSearch').value);
				searchReq.open("GET", 'common/ajax.php?type=user&search=' + str, true);
				searchReq.onreadystatechange = handleSearchSuggest; 
				searchReq.send(null);
			}		
		}
		-->
		</script>
		<div id="SearchAll">
			<table cellspacing="0" id="SearchAll" border="0">
				<tr>
					<td width="75" align="right" valign="top"><strong>Search All:</strong>&nbsp;</td>
					<td>
						<form action="index.php?page=admin&type=users" id="frmSearch" method="post">
							<input type="text" id="txtSearch" name="txtSearch" alt="Search Criteria" onkeyup="UserSearch();" autocomplete="off" class="box" />
							&nbsp;<input type="submit" id="cmdSearch" name="cmdSearch" value="Search" alt="Run Search" class="submit" />&nbsp;
							&nbsp;<input type="button" value="Clear" class="submit" onclick="dosub('index.php?page=admin&type=users');" />
							&nbsp;<input type="button" value="Add" class="submit" onclick="dosub('index.php?page=admin&type=users&id=add');" />
							<input type="hidden" name="cmd" value="UserByName" />
							<div id="search_suggest">
							</div>
						</form>
					</td>
				</tr>
			</table>
			<script language="JavaScript">
			<!--
			document.getElementById('txtSearch').focus = true;
			//-->
			</script>
		</div><!-- End SearchAll -->
		<?php
		// Check if searchText was used to find a user
		if( isset($_POST['txtSearch']) )
		{
			if ( strlen($_POST['txtSearch']) > 0 )
				$arr = $this->db->GetUserByName($_POST['txtSearch']);
			else
				$arr = $this->db->GetUserByName("all");
			
			if( is_array($arr) )
			{
				$this->DisplayUserGrid($arr);
			}
			else
			{
				$eq2->AddStatus('No users match your search.');
			}
			$ret = true;
		}
		else if( isset($_POST['cmd']) && isset($_GET['id']) && $_POST['cmd'] == 'UserByName' && $_GET['id'] != "add" )
		{
			//$eq2->AddStatus('Search must contain at least 1 letter/number.');
			//$ret = true;
			$arr = $this->db->GetUserByName("all");
			if( is_array($arr) )
			{
				$this->DisplayUserGrid($arr);
			}
			else
			{
				$eq2->AddStatus('No users match your search.');
			}
			$ret = true;
		}
		else if( isset($_GET['id']) && $_GET['id'] == "add" )
		{
			$this->AddUser();
		}

		// If a zone is selected, display quests associated with the zone
		if( isset($_GET['id']) && $_GET['id'] > 0 )
		{
			$this->UserEditor();
			$ret = true;
		}
		?>
		<div id="EditorStatus">
			<?php 
			if( !empty($eq2->Status) ) $eq2->DisplayStatus(); 
			?>
		</div><!-- End EditorStatus -->
		<?php
		if( $ret ) 
			return $ret;
	} // UserSearch

	
	private function News()
	{
		global $eq2;
		if (isset($_GET['id']))
		{
			if ($_GET['id'] > 0)
			{
				$this->NewsEditor();
				return;
			}
			
			if ($_GET['id'] == 'add')
			{
				$this->AddNews();
				return;
			}
		}
		
		if (isset($_POST['cmd']))
		{
			// Can't use $eq2->Process Update/Insert/Delete as it runs against the world db not the editor
			switch ($_POST['cmd'])
			{
				case "Submit":
					$sql = sprintf("INSERT INTO site_text (type, subtype, title, description, author, created_date, is_sticky, is_active) VALUES ('%s', '%s', '%s', '%s', %s, unix_timestamp(), %s, %s);", $_POST['site_text|type'], $_POST['site_text|subtype'], $_POST['site_text|title'], $_POST['site_text|description'], $_POST['site_text|author'], isset($_POST['site_text|is_sticky']) ? "1" : "0", isset($_POST['site_text|is_active']) ? "1" : "0");
					$rows = $eq2->eq2db->RunQuery('UPDATE', 'site_text', $_POST['object'], $sql);
					if( $rows )
						$eq2->AddStatus(sprintf("%s row(s) affected", $rows));
					break;
				case "Update":
						$sql = sprintf("UPDATE site_text SET type = '%s', subtype = '%s', title = '%s', description = '%s', is_sticky = %s, is_active = %s WHERE id = %s;", $_POST['newsedit|type'], $_POST['newsedit|subtype'], $_POST['newsedit|title'], $_POST['site_text|description'], isset($_POST['newsedit|is_sticky']) ? "1" : "0", isset($_POST['newsedit|is_active']) ? "1" : "0", $_POST['newsedit|id']);
						$eq2->ProcessUpdate($sql);
					break;
			}
		}
		?>
		<div id="SelectGrid">
		<div align="center"><input type="button" value="Add New" class="submit" onclick="dosub('index.php?page=admin&type=newsedit&id=add');" /><br /><br /></div>
		<table cellspacing="0" id="SelectGrid" border="0">
			<tr>
				<td colspan="8" class="title" align="center">News</td>
			</tr>
			<tr>
				<th width="5%">&nbsp;</th>
				<th width="5%">ID</th>
				<th width="10%">Date</th>
				<th width="20%">Type</th>
				<th width="20%">Subtype</th>
				<th width="30%">Title</th>
				<th width="5%">Sticky</th>
				<th width="5%">Active</th>
			</tr>
			<?php
			$data = $this->db->GetAllNews();
			if (is_array($data))
			{
				$i = 0;
				foreach ($data as $news)
				{
					$RowColor = ( $i % 2 ) ? "row1" : "row2";
					?>
					<tr class="<?= $RowColor ?>">
						<td class="detail">&nbsp;[&nbsp;<a href="index.php?page=admin&type=newsedit&id=<?= $news['id'] ?>">Edit</a>&nbsp;]&nbsp;</td>
						<td class="detail">&nbsp;<?= $news['id'] ?>&nbsp;</td>
						<td class="detail">&nbsp;<?= date('m.d.Y', $news['created_date']) ?>&nbsp;</td>
						<td class="detail">&nbsp;<?= $news['type'] ?>&nbsp;</td>
						<td class="detail">&nbsp;<?= $news['subtype'] ?>&nbsp;</td>
						<td class="detail">&nbsp;<?= $news['title'] ?>&nbsp;</td>
						<td class="detail">&nbsp;<?= $news['is_sticky'] == 1 ? "Yes" : "No" ?>&nbsp;</td>
						<td class="detail">&nbsp;<?= $news['is_active'] == 1 ? "Yes" : "No" ?>&nbsp;</td>
					<?php
					$i++;
				}
			}
			?>
		</table>
		</div>
		<div id="EditorStatus">
			<?php if( !empty($eq2->Status) ) $eq2->DisplayStatus(); ?>
		</div>
		<?php
	}
	
	
	private function NewsEditor()
	{
		global $eq2;
		$news = $this->db->GetNews($_GET['id']);
		
		if (is_array($news))
		{
			$user = $this->db->GetUserNameByID($news['author']);
			?>
			<div id="Editor">
		
			<!-- Editor -->
			<form action="index.php?page=admin&type=newsedit" method="post">
			<table cellspacing="0" width="900">
				<tr>
					<td class="Title" align="center">Editing: News (TODO)</td>
				</tr>
				<tr>
					<td>
						<table cellspacing="0" style="border:0px; margin:0px; width:99%;">
							<tr>
								<td width="15px">ID:</td>
								<?php $eq2->DrawInputTextBox($news, 'id', 'small', 1); ?>
								<td width="45px">Author:</td>
								<?php $eq2->DrawInputTextBox($user, 'username', 'xl', 1); ?>
								<td width="25px">Date:</td>
								<?php $eq2->DrawInputTextBox($news, 'created_date', "large", 1); ?>
							</tr>
						</table>
					</td>	
				</tr>
				<tr>
					<td>
						<table cellspacing="0" style="border:0px; margin:0px; width:99%;">
							<!-- This tr is only for formatting -->
							<tr style="visibility:hidden;">
								<td width="25px"></td>
								<td></td>
								<td width="45px"></td>
								<td></td>
								<td width="30px"></td>
								<td width="5px"></td>
								<td width="30px"></td>
								<td width="5px"></td>
							</tr>
							<tr>
								<td>Type:</td>
								<?php $eq2->DrawInputTextBox($news, 'type', 'full', 0); ?>
								<td>Subtype:</td>
								<?php $eq2->DrawInputTextBox($news, 'subtype', 'full', 0); ?>
								<td>Sticky:</td>
								<?php $eq2->DrawCheckBox($news, 'is_sticky', '', 0 , $news['is_sticky']); ?>
								<td>Active:</td>
								<?php $eq2->DrawCheckBox($news, 'is_active', '', 0, $news['is_active']); ?>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table cellspacing="0" style="border:0px; margin:0px; width:99%;">
							<tr>
								<td width="25px"><strong>Title:</strong></td>
								<?php $eq2->DrawInputTextBox($news, 'title', 'full', 0); ?>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<fieldset style="height:497px;">
							<legend>Description:</legend>
							<textarea name="site_text|description" wrap="off" class="Script" style="width:99.2%;"><?= $news['description'] ?></textarea>
						</fieldset>
					</td>
				</tr>
				<tr>
					<td align="center">
						<input type="button" value="Cancel" class="submit" onclick="dosub('index.php?page=admin&type=newsedit');" />
						<input type="submit" name="cmd" value="Update" class="submit" />
						<input type="hidden" name="table" value="site_text" />
						<input type="hidden" name="object" value="Update Site Text" />
					</td>
				</tr>
			</table>
			</form>
			</div>
			<?php
		}
		else
		{
			$eq2->AddStatus("Unable to get news.");
		}
		?>
		<div id="EditorStatus">
			<?php if( !empty($eq2->Status) ) $eq2->DisplayStatus(); ?>
		</div>
		<?php
	}
	
	
	private function AddNews()
	{
		global $eq2;
		
		?>
		<div id="Editor">
		
		<!-- Editor -->
		<form action="index.php?page=admin&type=newsedit" method="post">
		<table cellspacing="0" width="900">
			<tr>
				<td class="Title" align="center">Editing: News (TODO)</td>
			</tr>
			<tr>
				<td>
					<table cellspacing="0" style="border:0px; margin:0px; width:99%;">
						<!-- This tr is only for formatting -->
						<tr style="visibility:hidden;">
							<td width="25px"></td>
							<td></td>
							<td width="45px"></td>
							<td></td>
							<td width="30px"></td>
							<td width="5px"></td>
							<td width="30px"></td>
							<td width="5px"></td>
						</tr>
						<tr>
							<td>Type:</td>
							<td><input type="text" name="site_text|type" value="" class="full" /></td>
							<td>Subtype:</td>
							<td><input type="text" name="site_text|subtype" value="" class="full" /></td>
							<td>Sticky:</td>
							<td><input type="checkbox" name="site_text|is_sticky" /></td>
							<td>Active:</td>
							<td><input type="checkbox" name="site_text|is_active" checked /></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table cellspacing="0" style="border:0px; margin:0px; width:99%;">
						<tr>
							<td width="25px"><strong>Title:</strong></td>
							<td><input type="text" name="site_text|title" value="" class="full" /></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<fieldset style="height:497px;">
						<legend>Description:</legend>
						<textarea name="site_text|description" wrap="off" class="Script" style="width:99.2%;"></textarea>
					</fieldset>
				</td>
			</tr>
			<tr>
				<td align="center">
					<input type="button" value="Cancel" class="submit" onclick="dosub('index.php?page=admin&type=newsedit');" />
					<input type="submit" name="cmd" value="Submit" class="submit" />
					<input type="hidden" name="site_text|author" value="<?= $eq2->userdata['id'] ?>" />
					<input type="hidden" name="table" value="site_text" />
					<input type="hidden" name="object" value="New Site Text" />
				</td>
			</tr>
		</table>
		</form>
		</div>
		<?php
	}
}
?>
