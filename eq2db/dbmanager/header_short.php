<?php
session_start();

include("../config.php");

if( isset($_SESSION['cookieUserName']) ) 
	$eq2->chkUser($_SESSION['cookieUserName']);
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>EQ2DB Editor</title>
<link type="text/css" rel="stylesheet" href="../css/eq2.css" />
<script language="javascript" type="text/javascript">

function dosub(subm) 
{ 
	if (subm != "") 
	{ 
		self.location=subm; 
	} 
}

</script>
</head>

<body>
<div id="site-container">
<div id="main-body">

