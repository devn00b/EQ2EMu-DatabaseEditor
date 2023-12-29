<?php
// logtail.php
if( isset($_GET['type']) && isset($_GET['log']) )
{
	$ServerLogs = "/home/eq2emu_server/server/logs";
	$CompileLogs = "/home/eq2dev/webcompile.log";
	switch($_GET['type'])
	{
	case 'login':
		$cmd = "tail -100 /home/eq2emu_server/server/logs/".$_GET['log'];
		break;

        case 'patch':
		$cmd = "tail -100 /home/eq2emu_server/server/logs/".$_GET['log'];
            break;

        case 'world':
		$cmd = "tail -100 /home/eq2emu_server/server/logs/".$_GET['log'];
            break;

		case 'compile':
		$cmd = $CompileLogs;
			break;
	}

	exec("$cmd 2>&1", $output);
	foreach($output as $outputline) {
		echo ("$outputline\n");
	}
}
?>
