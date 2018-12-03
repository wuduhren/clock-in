<?php
require(__DIR__.'/../lib/utilities.php');
check_auth(0, true);

$action = get_request('action');

if ($action == 'clock_in')
	clock_in();
else if ($action == 'clock_out')
	clock_out();
else if ($action == 'query')
	query();
die();

// -----------------------------------------------------------------------------
function clock_in(){
	$date_work = date('Y-m-d');

	$rs = db()->query('SELECT `time_start` FROM `clock` WHERE `uid`=:uid AND `date_work`=:date_work', $_SESSION['uid'], $date_work);
	if (isset($rs->time_start)) die_error('上班打卡已完成');
	
	$id = db()->insert('clock', [
		'uid'=>$_SESSION['uid'],
		'date_work'=>$date_work,
		'time_start'=>date("Y-m-d H:i:s")
	]);
	$rs = db()->query('SELECT `time_start` FROM `clock` WHERE `uid`=:uid AND `date_work`=:date_work', $_SESSION['uid'], $date_work);
	die_success($rs->time_start);
}

function clock_out(){
	$date_work = date('Y-m-d');

	$rs = db()->query('SELECT `time_start`, `time_end` FROM `clock` WHERE `uid`=:uid AND `date_work`=:date_work', $_SESSION['uid'], $date_work);
	if (!isset($rs->time_start)) die_error('上班打卡未完成');
	if (isset($rs->time_end)) die_error('下班打卡已完成');
	
	db()->update('clock', 'uid=:uid AND date_work=:date_work',[
		'uid'=>$_SESSION['uid'],
		'date_work'=>$date_work,
		'time_end'=>date("Y-m-d H:i:s")
	]);

	$rs = db()->query('SELECT `time_end` FROM `clock` WHERE `uid`=:uid AND `date_work`=:date_work', $_SESSION['uid'], $date_work);
	die_success($rs->time_end);
}

function query(){
	global $PAGE_SIZE;
	$page_number = get_request('page_number');
	$rs = db()->paging($PAGE_SIZE, $page_number)->query('SELECT `date_work`, `time_start`, `time_end` FROM `clock` WHERE `uid`=:uid ORDER BY `date_work` DESC', $_SESSION['uid']);
	die_success($rs);
}










?>