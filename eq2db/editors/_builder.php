<?php include("header.php"); 
$table=( isset($_GET['table']) ) ? $_GET['table'] : "characters";

switch($_GET['m']) {
	case 0:
		printf("function %s(\$id) {\n",$table);
		print("	global \$eq2,\$objectName,\$link;\n\n");
		printf("	\$table=\"%s\";\n",$table);
		print("	\$query=sprintf(\"select * from %s where item_id = %d\",\$table,\$id);\n");
		print("	\$result=\$eq2->db->sql_query(\$query);\n");
		print("	if(\$eq2->db->sql_numrows(\$result) > 0) {\n");
		print("		\$data=\$eq2->db->sql_fetchrow(\$result);\n");
		print("?>\n");
		print("		<table border=\"0\" cellpadding=\"5\">\n");
		print("		<form method=\"post\" name=\"Form1\" />\n");
		print("			<tr>\n");
		print("				<td width=\"680\" valign=\"top\">\n");
		print("					<fieldset><legend>General</legend>\n");
		print("					<table width=\"100%\" cellpadding=\"0\" border=\"1\">\n");
		print("						<tr>\n");
		print("							<td colspan=\"3\">\n");
		print("								<span class=\"heading\">Editing: <?= \$objectName ?></span><br />&nbsp;\n");
		print("							</td>\n");
		print("						</tr>\n");		
		$result=$eq2->db->sql_query("select COLUMN_NAME from information_schema.columns where table_schema=`".DEV_DB."` and table_name='$table';");
		$count=$eq2->db->sql_numrows($result);
		while($data=$eq2->db->sql_fetchrow($result)) {
			$field[].=$data['COLUMN_NAME'];
		}

		for($i=0;$i<$count;$i++) {
			$style = ( $field[$i]=='id' ) ? " style=\"width:45px;  background-color:#ddd;\" readonly" : " class=\"small\"";
			$detailRow.="						<tr>\n";
			$detailRow.="							<td align=\"right\">$field[$i]:</td>\n";
			$detailRow.="							<td>\n";
			$detailRow.="								<input type=\"text\" name=\"$table|$field[$i]\" value=\"<?= \$".$table."['$field[$i]'] ?>\"$style />\n";
			$detailRow.="								<input type=\"hidden\" name=\"orig_$field[$i]\" value=\"<?= \$".$table."['$field[$i]'] ?>\" />\n";
			$detailRow.="							</td>\n";
			$detailRow.="						</tr>\n";
		}

		$detailRow.="					</table>\n";
		$detailRow.="					</fieldset>\n";
		$detailRow.="				</td>\n";
		$detailRow.="			</tr>\n";
		$detailRow.="			<?php if(\$eq2->CheckAccess(100)) { ?>\n";
		$detailRow.="			<tr>\n";
		$detailRow.="				<td colspan=\"4\" align=\"center\">\n";
		$detailRow.="					<input type=\"submit\" name=\"iUpdate\" value=\"Update\" style=\"width:100px;\" />&nbsp;\n";
		$detailRow.="					<input type=\"button\" value=\"Help\" style=\"width:100px\" onclick=\"javascript:window.open('help.php#items','help','resizable,width=480,height=640,left=10,top=75,scrollbars=yes');\" />\n";
		$detailRow.="					<input type=\"hidden\" name=\"cmd\" value=\"update\" />\n";
		$detailRow.="					<input type=\"hidden\" name=\"orig_object\" value=\"<?= \$objectName ?>\" />\n";
		$detailRow.="					<input type=\"hidden\" name=\"tableName\" value=\"<?= \$table ?>\" />\n";
		$detailRow.="				</td>\n";
		$detailRow.="			</tr>\n";
		$detailRow.="			<?php } ?>\n";
		$detailRow.="		</table>\n";
		$detailRow.="		<?\n";
		$detailRow.="		} else {\n";
		$detailRow.="		if( \$eq2->CheckAccess(100) ) { ?>\n";

		$detailRow.="		<table border=\"0\" cellpadding=\"5\">\n";
		$detailRow.="		<form method=\"post\" name=\"Form1|new\" />\n";
		$detailRow.="			<tr>\n";
		$detailRow.="				<td width=\"680\" valign=\"top\">\n";
		$detailRow.="					<fieldset><legend>General</legend>\n";
		$detailRow.="					<table width=\"100%\" cellpadding=\"0\" border=\"1\">\n";
		$detailRow.="						<tr>\n";
		$detailRow.="							<td colspan=\"4\">\n";
		$detailRow.="								<span class=\"heading\">Editing: <?= \$objectName ?></span><br />&nbsp;\n";
		$detailRow.="							</td>\n";
		$detailRow.="						</tr>\n";
		$detailRow.="						<tr>\n";
		$detailRow.="							<td colspan=\"4\">No data found for this item. You may insert a new record if necessary.</td>\n";
		$detailRow.="						</tr>\n";
		
		for($i=0;$i<$count;$i++) {
			$style = ( $field[$i]=='id' ) ? " style=\"width:45px;  background-color:#ddd;\" readonly" : " style=\"width:45px;\"";
			$detailRow.="						<tr>\n";
			$detailRow.="							<td align=\"right\">$field[$i]:</td>\n";
			$detailRow.="							<td>\n";
			$detailRow.="								<input type=\"text\" name=\"$table|$field[$i]|new\" value=\"0\"$style />\n";
			$detailRow.="							</td>\n";
			$detailRow.="						</tr>\n";
		}

		$detailRow.="					</table>\n";
		$detailRow.="					</fieldset>\n";
		$detailRow.="				</td>\n";
		$detailRow.="			</tr>\n";
		$detailRow.="			<tr>\n";
		$detailRow.="				<td colspan=\"4\" align=\"center\">\n";
		$detailRow.="					<input type=\"submit\" name=\"iInsert\" value=\"Insert\" style=\"width:100px;\" />&nbsp;\n";
		$detailRow.="					<input type=\"button\" value=\"Help\" style=\"width:100px\" onclick=\"javascript:window.open('help.php#items','help','resizable,width=480,height=640,left=10,top=75,scrollbars=yes');\" />\n";
		$detailRow.="					<input type=\"hidden\" name=\"cmd\" value=\"insert\" />\n";
		$detailRow.="					<input type=\"hidden\" name=\"orig_object\" value=\"<?= \$objectName ?>\" />\n";
		$detailRow.="					<input type=\"hidden\" name=\"tableName\" value=\"<?= \$table ?>\" />\n";
		$detailRow.="				</td>\n";
		$detailRow.="			</tr>\n";
		$detailRow.="		</table>\n";
		$detailRow.="		<?php\n";
		$detailRow.="		}\n";
		$detailRow.="	}\n";
		$detailRow.="}\n";
		print($detailRow);
		break;

	case 1:
		printf("function %s(\$id) {\n",$table);
		print("\tglobal \$eq2,\$objectName,\$link;\n\n");
		printf("\t\$table=\"%s\";\n",$table);
		print("?>\n");
		print("	<table border=\"0\" cellpadding=\"5\">\n");
		print("		<tr>\n");
		print("			<td width=\"680\" valign=\"top\">\n");
		print("				<fieldset><legend>General</legend>\n");
		print("				<table width=\"100%\" cellpadding=\"0\" border=\"0\">\n");
		print("					<tr>\n");
		print("						<td colspan=\"3\">\n");
		print("							<span class=\"heading\">Editing: <?= \$objectName ?></span><br />&nbsp;\n");
		print("						</td>\n");
		print("					</tr>\n");		
		$result=$eq2->db->sql_query("select COLUMN_NAME from information_schema.columns where table_schema=`".DEV_DB."` and table_name='$table';");
		$count=$eq2->db->sql_numrows($result);
		while($data=$eq2->db->sql_fetchrow($result)) {
			$field[].=$data['COLUMN_NAME'];
		}

		$headerRow="					<tr>\n";
		for($i=0;$i<$count;$i++) {
			$headerRow.="						<td width=\"55\">$field[$i]</td>\n";
		}
		$headerRow.="						<td colspan=\"2\">&nbsp;</td>\n";
		$headerRow.="					</tr>\n";
		//$headerRow.="				</table>\n";
		$detailRow="
						<?php
						\$query=sprintf(\"select * from %s where item_id = %s\",\$table, \$id);
						\$result=\$eq2->db->sql_query(\$query);
						while(\$data=\$eq2->db->sql_fetchrow(\$result)) {
						?>\n";
		//$detailRow.="				<table>\n";
		$detailRow.="					<form method=\"post\" name=\"multiForm|<?php print(\$data['id']); ?>\" />\n";
		$detailRow.="					<tr>\n";
		for($i=0;$i<$count;$i++) {
			$style = ( $field[$i]=='id' ) ? " style=\"width:45px;  background-color:#ddd;\" readonly" : " style=\"width:45px;\"";
			$detailRow.="						<td>\n";
			$detailRow.="							<input type=\"text\" name=\"$table|$field[$i]\" value=\"<?php print(\$data['$field[$i]']) ?>\"$style />\n";
			$detailRow.="							<input type=\"hidden\" name=\"orig_$field[$i]\" value=\"<?php print(\$data['$field[$i]']) ?>\" />\n";
			$detailRow.="						</td>\n";
		}
		$detailRow.="						<td><?php if(\$eq2->CheckAccess(100)) { ?><input type=\"submit\" name=\"cmd\" value=\"Update\" style=\"font-size:10px; width:60px\" /><?php } ?></td>\n";
		$detailRow.="						<td><?php if(\$eq2->CheckAccess(100)) { ?><input type=\"submit\" name=\"cmd\" value=\"Delete\" style=\"font-size:10px; width:60px\" /><?php } ?></td>\n";
		$detailRow.="					</tr>\n";
		$detailRow.="					<input type=\"hidden\" name=\"objectName\" value=\"<?= \$objectName ?>\" />\n";
		$detailRow.="					<input type=\"hidden\" name=\"tableName\" value=\"<?= \$table ?>\" />\n";
		$detailRow.="					</form>\n";
		//$detailRow.="				</table>\n";
		$detailRow.="				<?php\n";
		$detailRow.="				}\n";
		$detailRow.="				?>\n";
		$detailRow.="				<?php if(\$eq2->CheckAccess(100)) { ?>\n";
		//$detailRow.="				<table>\n";
		$detailRow.="					<form method=\"post\" name=\"sdForm|new\" />\n";
		$detailRow.="					<tr>\n";
		$detailRow.="						<td align=\"center\"><strong>new</strong></td>\n";
		for($i=1;$i<$count;$i++) {
			$style = ( $field[$i]=='id' ) ? " style=\"width:45px;  background-color:#ddd;\" readonly" : " style=\"width:45px;\"";
			$detailRow.="						<td>\n";
			$detailRow.="							<input type=\"text\" name=\"$table|$field[$i]|new\" value=\"\"$style />\n";
			$detailRow.="						</td>\n";
		}	
		$detailRow.="						<td>\n";
		$detailRow.="							<input type=\"submit\" name=\"cmd\" value=\"Insert\" style=\"font-size:10px; width:60px\" />\n";
		$detailRow.="						</td>\n";
		$detailRow.="					</tr>\n";
		$detailRow.="					<input type=\"hidden\" name=\"tableName\" value=\"<?= \$table ?>\" />\n";
		$detailRow.="					</form>\n";
		$detailRow.="				<?php } ?>\n";
		$detailRow.="				</table>\n";
		$detailRow.="				</fieldset>\n";
		$detailRow.="			</td>\n";
		$detailRow.="		</tr>\n";
		$detailRow.="	</table>\n";
		$detailRow.="<?\n";
		$detailRow.="}\n";			
		print($headerRow);
		print($detailRow);
		break;
	default:
}
?>
