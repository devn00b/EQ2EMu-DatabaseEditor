<?php 
include_once('config.php');
?>
<table width="100%" border="1" cellpadding="4" cellspacing="1">
	<tr>
		<td>id</td>
		<td>file</td>
		<td>name</td>
		<td>description</td>
		<td>expansion</td>
		<td>min_level</td>
		<td>zone type</td>
		<td> &nbsp; </td>
	</tr>
<?php 
$sql = "SELECT rz.id, rz.file, rz.name, rz.description, expansion, rz.min_level, rz.zone_type FROM eq2raw.zones rz LEFT JOIN eq2expansions X ON expansion_id = x.id ORDER BY description, min_level";
if( !$result = $eq2->db->sql_query($sql) )
	die("SQL ERROR!");
while( $data = $eq2->db->sql_fetchrow($result) )
{
	print("<tr>");
	foreach($data as $field=>$value)
		printf("<td>%s</td>", $value);
	print("</tr>");
}

$sql = "SELECT rz.id, rz.file, rz.name, rz.description, expansion, rz.min_level, rz.zone_type FROM eq2raw.zones_new rz, eq2expansions X WHERE expansion_id = x.id ORDER BY description, min_level";
if( !$result = $eq2->db->sql_query($sql) )
	die("SQL ERROR!");
while( $data = $eq2->db->sql_fetchrow($result) )
{
	foreach($data as $field=>$value)
	{
		printf("field: %s, value: %s<br />", $field, $value);
	}
}

?>
</table>