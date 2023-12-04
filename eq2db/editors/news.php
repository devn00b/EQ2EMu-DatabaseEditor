<?php
// News & Updates that show up on the Index page 
$sql = "SELECT * FROM site_text WHERE type = 'news' AND (is_active = 1 OR is_sticky = 1) ORDER BY created_date DESC";
if( !$result = $eq2->db->sql_query($sql) )
	die("SQL Error: <br />" . $sql);

$news_articles = "";
while( $data = $eq2->db->sql_fetchrow($result) )
	$news_articles .= $data['description'];
?>
<table>
	<tr>
		<td>
		  <h3>News &amp; Stuff</h3>
			<?php echo $news_articles ?>
    	</td>
	</tr>
</table>
