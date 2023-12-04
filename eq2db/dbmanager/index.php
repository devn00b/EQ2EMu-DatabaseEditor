<?php 
include("header.php"); 
?>
<div id="dbmanager-body">
<table width="100%" cellspacing="0" cellpadding="4">
	<tr>
		<td><h3>DB Manager News & Stuff</h3></td>
	</tr>
<?php 

// News & Updates that show up on the Index page 
$query = "select * from eq2editor.site_text join eq2editor.users u on u.id = author where type = 'news' order by created_date desc limit 0, 10;";
$result = $eq2->db->sql_query($query);
while( $data = $eq2->db->sql_fetchrow($result) ) {
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
?>
</table>
</div>
