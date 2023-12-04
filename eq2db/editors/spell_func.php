<?php
define('IN_EDITOR', true);
include("header_short.php");
$showResults = ( isset($_GET['c']) || isset($_POST['cmdSearch']) ) ? true : false;
?>
<table width="100%" border="1" cellspacing="0" cellpadding="5">
	<tr>
		<td align="center" valign="top"><strong>Lookup Spell Effects</strong></td>
	</tr>
	<tr>
		<td align="center"><strong>Filters:</strong>
			<select name="luEffectCategory" onChange="dosub(this.options[this.selectedIndex].value)">
				<option>Pick a Category</option>
				<option value="spell_func.php?c=All">All</option>
			<?php
			$eq2->SQLQuery = "SELECT DISTINCT category FROM ".DEV_DB.".reference_spell_effects ORDER BY category";
			$categories = $eq2->RunQueryMulti();
			foreach($categories as $data) 
			{
			?>
				<option value="spell_func.php?c=<?= $data['category'] ?>"<?php if( $data['category']==$_GET['c'] ) echo " selected" ?>><?= $data['category'] ?></option>
			<? 
			} 
			?>	
			</select>&nbsp;
			<?php 
			if( !empty($_GET['c']) && $_GET['c'] != "All" )
			{
				$eq2->SQLQuery = "SELECT DISTINCT type FROM ".DEV_DB.".reference_spell_effects WHERE category = '" . $_GET['c']. "' ORDER BY type;";
				$types = $eq2->RunQueryMulti();
				foreach($types as $data) 
				{
					$rows[] = preg_replace("/[\r\n]+/i", "", $data['type']);
				}
			?>
			<select name="luEffectType" onChange="dosub(this.options[this.selectedIndex].value)">
				<option>Pick a Type</option>
				<option value="spell_func.php?c=<?= $_GET['c'] ?>">All</option>
				<?php
				foreach($rows as $row) 
					printf('<option value="spell_func.php?c=%s&t=%s"%s>%s</option>', $_GET['c'], $row, ( $row==$_GET['t'] ) ? " selected" : "", $row);
				?>	
			</select>&nbsp;
			<?php 
				if( !empty($_GET['t']) && $_GET['t'] != "All" )
				{
					$rows = array();
					
					$eq2->SQLQuery = "SELECT DISTINCT name FROM ".DEV_DB.".reference_spell_effects WHERE category = '" . $_GET['c']. "' AND type = '" . $_GET['t']. "' ORDER BY name;";
					$names = $eq2->RunQueryMulti();
					foreach($names as $data) 
					{
						$rows[] = preg_replace("/[\r\n]+/i", "", $data['name']);
					}
				?>
				<select name="luEffectName" onChange="dosub(this.options[this.selectedIndex].value)">
					<option>Pick a Type</option>
					<option value="spell_func.php?c=<?= $_GET['c'] ?>t=<?= $_GET['t'] ?>">All</option>
					<?php
					foreach($rows as $row) 
						printf('<option value="spell_func.php?c=%s&t=%s&n=%s"%s>%s</option>', $_GET['c'], $_GET['t'], $row, ( $row==$_GET['n'] ) ? " selected" : "", $row);
					?>	
				</select>&nbsp;
			<?php 
				} 
			?>
			</td>
			<?php
		}
		?>
	</tr>
	<form method="post">
	<tr>
		<td align="center"><strong>Lookup:</strong>
			<!--<input type="text" id="txtSearch" name="txtSearch" alt="Search Criteria" onkeyup="SpellLookupAJAX();" autocomplete="off" class="box" value="<?= $_POST['txtSearch'] ?>" onclick="this.value='';" />-->
			<input type="text" id="txtSearch" name="txtSearch" alt="Search Criteria" class="box" value="<?= $_POST['txtSearch'] ?>" onClick="this.value='';" />
			<input type="submit" id="cmdSearch" name="cmdSearch" value="Search" alt="Run Search" class="submit" />
			<input type="button" value="Clear" class="submit" onClick="dosub('spell_func.php?type=effects');" />
			<div id="search_suggest">
			</div>
		</td>
	</tr>
	</form>
</table>
<?php 
if( $showResults ) 
{ 
?>
<table width="640" border="1" cellspacing="0">
	<tr>
		<th>id</th>
		<th>category</th>
		<th>type</th>
		<th>name</th>
		<th>misc</th>
	</tr>
	<?php
	$search_text = $eq2->SQLEscape($_POST['txtSearch']);
	
	$query = "SELECT * FROM ".DEV_DB.".reference_spell_effects";
	
	if( $_POST['cmdSearch'] == 'Search' )
	{
		$query .= " WHERE (category RLIKE '".$search_text."') OR (type RLIKE '".$search_text."') OR (name RLIKE '".$search_text."') OR (misc RLIKE '".$search_text."')";
	}
	else
	{
		if( $_GET['c'] == "All" )
			$query .= " ORDER BY category, type";
		else
			$query .= " WHERE category RLIKE '" . $_GET['c'] . "'";
		
		if( !empty($_GET['t']) && $_GET['t'] != "All" )
			$query .= " AND type RLIKE '" . $_GET['t'] . "'";
	
		if( !empty($_GET['n']) && $_GET['n'] != "All" )
			$query .= " AND name RLIKE '" . $_GET['n'] . "'";
	}
	
	$eq2->SQLQuery = $query . " ORDER BY category, type, name, misc";
	
	$results = $eq2->RunQueryMulti();
	
	foreach($results as $data) 
	{
	?>
	<tr>
		<td><?= $data['id'] ?>&nbsp;</td>
		<td><?= $data['category'] ?>&nbsp;</td>
		<td><?= $data['type'] ?>&nbsp;</td>
		<td><?= $data['name'] ?>&nbsp;</td>
		<td><?= $data['misc'] ?>&nbsp;</td>
	</tr>
	<? 
	} 
	?>	
</table>
<?php 
} 
include("footer.php");
?>
