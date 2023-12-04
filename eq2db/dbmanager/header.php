<?php
session_start();
include("../config.php");

if( isset($_SESSION['cookieUserName']) ) 
	$eq2->chkUser($_SESSION['cookieUserName']);

if( $eq2->mLev < 100 ) 
	header("Location: /editors/index.php");
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>EQ2EmuDB Project Manager</title>
<link type="text/css" rel="stylesheet" href="../css/eq2.css" />
<script>
function dosub(subm) 
{ 
	if (subm != "") 
	{ 
		self.location=subm; 
	} 
}
</script>
<link rel="shortcut icon" href="images/favicon.ico"/>
</head>

<body>
<div id="site-container">
<div id="site-banner">EQ2Emulator Database Project Manager
    <div id="db-picker">
    <?php 
        $eq2->DBPicker(); 
        $db_name = isset($_SESSION['current_database']) ? $_SESSION['current_database'] : "eq2dev";		
    ?>
    </div>
</div>
<!-- top menu -->
<div id="top-menu-mgr">
<table width="100%" cellspacing="0" border="0">
	<tr>
		<td><a href="index.php">Home</a></td>
		<td><a href="project.php">Project</a></td>
		<td><a href="scripting.php">Scripting</a></td>
		<td><a href="db_tools.php">DB Tools</a></td>
		<td><a href="file_manager.php">File Manager</a></td>
		<td><a href="reports.php">Reports</a></td>
		<td nowrap="nowrap"><a href="../editors/index.php">DB Editor</a></td>
		<td width="100%">&nbsp;</td>
	</tr>
</table>
</div>
<div id="main-body">
