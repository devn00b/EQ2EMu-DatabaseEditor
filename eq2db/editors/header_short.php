<?php
if (!defined('IN_EDITOR'))
{
	die("Hack attempt recorded.");
}

require "../class/dotenv.php";
DotEnv::load("../.env");
require("../config.php");

if( isset($_SESSION['cookieUserName']) ) 
	$eq2->chkUser($_SESSION['cookieUserName']);
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>EQ2DB Editor</title>
<link rel="stylesheet" href="<?php echo '../css/eq2.css?md5='.md5_file("../css/eq2.css"); ?>" />
<script src="../js/eq2editor.js?md5=<?php echo md5_file("../js/eq2editor.js"); ?>"></script>
<link rel="icon" href="../images/favicon.ico"/>
</head>
<?php

if( isset($_COOKIE['eq2db']) )
{
	$eq2->userdata = $eq2->GetCookie();
	if( $eq2->userdata['reset_password'] == 0 )
		$eq2->user_role = intval($eq2->userdata['role']);
	else
		$eq2->user_role = 0;
}

//print_r($GLOBALS['config']);
//print_r($eq2->role_list);
//print_r($eq2->userdata);

if( $GLOBALS['config']['debug_forms'] && isset($_POST['cmd']) )
	$eq2->AddDebugForm($_POST);

?>
<body>
<div id="site-container">
<div id="main-body">

