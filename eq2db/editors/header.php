<?php
/*  
    EQ2Editor:  Everquest II Database Editor v1.0
    Copyright (C) 2008-2013  EQ2Emulator Development Team (http://eq2emulator.net)

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
if (!defined('IN_EDITOR')) { die("Hack attempt recorded."); }

require "../class/dotenv.php";
DotEnv::load("../.env");

require "../config.php";

if (isset($_REQUEST['cmd'])) {
switch($_REQUEST['cmd']) 
{
	case "Set Password":
		$eq2->SavePassword();
		break;
		
	case "Login":
		if( !empty($_POST['lName']) && !empty($_POST['lPass']) )
		{
			if( $eq2->LoginUser() )
				header("Location: index.php"); /* Redirect browser */
		}
		else
			$eq2->AddStatus("Invalid login information.");
		break;

	case "Logout":
		unset($eq2->userdata);
		$eq2->DeleteCookie();
		header("Location: index.php"); /* Redirect browser */
		break;
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>EQ2DB Editor</title>

    <link rel="stylesheet" href="<?php echo '../css/eq2.css?md5='.md5_file("../css/eq2.css"); ?>" />
    <script src="../js/eq2editor.js?md5=<?php echo md5_file("../js/eq2editor.js"); ?>"></script>
    <link rel="icon" href="../images/favicon.ico"/>
</head>
<?php

if (isset($_COOKIE['eq2db'])) {
	$eq2->userdata = $eq2->GetCookie();
	if ($eq2->userdata['reset_password'] == 0)
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
    <div id="site-banner"><?php printf("%s %s", $GLOBALS['config']['app_name'], $GLOBALS['config']['app_version']); ?>
        <div id="user-info">
            <?php
            if( is_array($eq2->userdata) )
            {
                printf('Logged in as: %s [%s] (%s messages) <a href="settings.php" target="_self"><u>My Settings</u></a><br />', ( strlen($eq2->userdata['displayname']) > 0 ) ? $eq2->userdata['displayname'] : $eq2->userdata['username'], $eq2->userdata['title'], 0);
                if( $GLOBALS['config']['readonly'] )
                    print('<font class="warning">READ-ONLY Mode!</font>');
                if (env("DEBUG"))
                    print('&nbsp;<font class="warning">Debug ON!</font>');
            }
    ?>
        </div>
    </div>
    <?php /*?>do not show menu unless user is validated<?php */?>
    <?php if (!empty($eq2->userdata)) { ?>
        <!-- top menu -->
        <div id="top-menu">
        <table width="100%" cellspacing="0" border="0">
            <tr align="center">
                <?php 
                $current_script = $eq2->GetPHPScriptName();
                // Always display Home tab
                printf('<td class="%s"><a href="index.php">Home</a></td>', ( $current_script == "index.php" ) ? "tabOn" : "tabOff");
                
                $devTabs = [
                    M_CHARACTERS => "Characters|characters.php",
                    M_GUILDS     => "Guilds|guilds.php",
                    M_ITEMS      => "Items|items.php",
                    M_QUESTS     => "Quests|quests.php",
                    M_SCRIPTS    => "Scripts|scripts.php",
                    M_SPELLS     => "Spells|spells.php",
                    M_SPAWNS     => "Spawns|spawns.php",
                    M_SERVER     => "Server|server.php",
                    M_ZONES      => "Zones|zones.php",
                    M_ADMIN      => "Admin|_admin.php"
                ];
                
                $empty_cell = 0;

                foreach ($devTabs as $flag=>$tab) {
                    if ($eq2->user_role & $flag) {
                        $info = explode('|', $tab);
                        printf('<td class="%s"><a href="%s">%s</a></td>', ( $current_script == $info[1] ) ? "tabOn" : "tabOff", $info[1], $info[0]);
                    }
                    else $empty_cell++;
                }
                    
                if( $empty_cell ) {
                    for( $i = 0; $i < $empty_cell; $i++ ) {
                        print('<td class="tabOff">&nbsp;</td>');
                    }
                }
                ?>
                <td class="tabOff"><a href="index.php?cmd=Logout">&nbsp;Logout</a>&nbsp;</td>
            </tr>
        </table>
        </div>
    <?php } else { ?>
        <div id="login-box">
        <table cellspacing="0" align="center">
            <?php if (!empty($eq2->Status)) { ?>
                <tr>
                    <td colspan="2" class="warning"><?= $eq2->DisplayStatus(); ?></td>
                </tr>
            <?php } ?>
            <form action="index.php" method="post" name="Login">
            <tr>
                <td colspan="2" class="title">EQ2DB Login</td>
            </tr>
            <tr>
                <td class="label">Username:</td>
                <td><input type="text" name="lName" value="" class="text" /></td>
            </tr>
            <tr>
                <td class="label">Password:</td>
                <td><input type="password" name="lPass" value="" class="text" /></td>
            </tr>
            <tr>
                <td align="center" colspan="2"><input type="submit" name="cmd" value="Login" class="submit" /></td>
            </tr>
            <?php /*?><tr>
                <td align="center" colspan="2">( <a href="index.php">Guest</a> )</td>
            </tr><?php */?>
            </form>
        </table>
        </div>
    <?php
        include('footer.php');
        exit;
    }

    /* Reset Password check */
    if( $eq2->userdata['reset_password'] == 1 )
    {
        $eq2->ResetPasswordForm();
        include('footer.php');
        exit;
    }

    /* if logged in, continue with main body */
    ?>
    <div id="main-body">
