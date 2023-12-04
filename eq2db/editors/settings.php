<?php 
define('IN_EDITOR', true);
include("header.php"); 
?>
	<div id="Editor">
	<form method="post" name="Password" />
		<table class="SubPanel" cellspacing="0">
			<tr>
				<td id="EditorStatus" colspan="2"><? if( !empty($eq2->Status) ) $eq2->DisplayStatus(); ?></td>
			</tr>
			<tr>
				<td class="Title" colspan="2">
					My Settings: <?= $eq2->userdata['username'] ?> (<?= $eq2->userdata['id'] ?>)
				</td>
			</tr>
			<tr>
				<td valign="top">
					<table class="SectionMain" cellspacing="0">
						<tr>
							<td class="SectionBody">
								<fieldset><legend>Account</legend> 
								<table cellspacing="0">
								</table>
								</fieldset>
							</td>
							<td class="SectionBody">
								<fieldset><legend>Roles</legend> 
								<table cellspacing="0">
								</table>
								</fieldset>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="SectionBody">
								<fieldset><legend>Messages</legend> 
								<table cellspacing="0">
								</table>
								</fieldset>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</form>
	</div>
My password, my edits, editor preferences, etc...

<?php 
include("footer.php"); 
?>
