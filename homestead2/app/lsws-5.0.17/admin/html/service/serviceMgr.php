<?php

require_once('../includes/auth.php');

// this will force login
$product = PRODUCT::GetInstance();

$act = DUtil::getGoodVal(DUtil::grab_input("get",'act'));
$actId = DUtil::getGoodVal(DUtil::grab_input("get",'actId'));
$vl = DUtil::getGoodVal(DUtil::grab_input("get",'vl'));
$tk = DUtil::getGoodVal(DUtil::grab_input("get",'tk'));

// validate all inputs
$actions = array('', 'restart', 'upgrade', 'switchTo', // restart required
		'toggledbg', 'download', 'enable', 'disable', 'remove', 'validatelicense');

$vloptions = array('', '1','2','3','4');
if (!in_array($vl, $vloptions) || !in_array($act, $actions)) {
	echo "Illegal Entry Point!";
	return; // illegal entry
}
if ($act != '') {
	if ($tk != CLIENT::singleton()->getToken()) {
		echo "Illegal Entry Point!";
		return; // illegal entry
	}
}

$confCenter = ConfCenter::singleton();

$service = new Service();
$service->init();
$service->refreshConf($confCenter->ExportConf());

//check if require restart
if ( ($act == 'restart' && $actId == '') || $act == 'switchTo')
{
	header("location:restart.html");
	$service->process($act, $actId);
	return;
}
elseif ($act == 'upgrade')
{
	if ($service->download($actId))
	{
		header("location:restart.html");
		$service->process($act, $actId);
		return;
	}
	else
	{
		$error = "Failed to download release $actId! Please try upgrade manually.";
	}

}
elseif (in_array($act, array('toggledbg', 'enable', 'disable', 'restart', 'remove','validatelicense'))) //other no-restart actions
{
	if ($act == 'disable' || $act == 'enable') {
		$confCenter->enableDisableVh($act, $actId);
	}

	$service->process($act, $actId);
}

echo GUI::header($service->serv['name']);
echo GUI::top_menu();

switch($vl)
{
	case '1': include 'logViewer.php';
		break;
	case '2': include 'realtimeReport.php';
		break;
	case '3':
		if ($act == 'download') {
			include 'verMgrDownload.php';
		}
		else {
			include 'verMgrCont.php';
		}
		break;
	case '4': include 'realtimeReqReport.php';
		break;

	default: include 'homeCont.php';
		break;
}

echo GUI::footer();
