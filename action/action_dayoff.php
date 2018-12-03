<?php
require(__DIR__.'/../lib/utilities.php');
check_auth(0, true);

$action = get_request('action');

if ($action == 'leave')
	leave();
else if ($action == 'query')
	query();
die();

// -----------------------------------------------------------------------------
function leave(){
	$date_start = get_request('date_start');
	$date_end = get_request('date_end');
	$reason = get_request('reason');

	if (!check_date($date_start)) die_error('日期格式錯誤');
	if (!check_date($date_end)) die_error('日期格式錯誤');

	$id = db()->insert('dayoff', [
		'uid'=>$_SESSION['uid'],
		'date_start'=>$date_start,
		'date_end'=>$date_end,
		'reason'=>$reason
	]);
	die_success();
}

function query(){
	global $PAGE_SIZE;
	$page_number = get_request('page_number');
	$rs = db()->paging($PAGE_SIZE, $page_number)->query('SELECT `date_start`, `date_end`, `reason` FROM `dayoff` WHERE `uid`=:uid ORDER BY `id` DESC', $_SESSION['uid']);
	die_success($rs);
}

function check_date($date, $format='Y-m-d'){
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

?>
