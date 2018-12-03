<?php
require(__DIR__.'/../lib/utilities.php');
check_auth(0, true);

$action = get_request('action');

if ($action == 'update')
	update();
die();

// -----------------------------------------------------------------------------
function update(){
	$time_start = get_request('time_start');
	$time_end = get_request('time_end');
	$time_range = get_request('time_range');

	check_time($time_start);
	check_time($time_end);

	if (!is_numeric($time_range)) {
		die_error('請輸入0~30之正整數');
	}
	if (intval($time_range<0) || intval($time_range >30)) {
		die_error('請輸入0~30之正整數');
	}

	db()->update('schedule', 'id=:id',[
		'id'=>1,
		'time_start'=>$time_start,
		'time_end'=>$time_end,
		'time_range'=>$time_range
	]);

	die_success();
}

function check_time($time){
	if (!preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]$/", $time)) {
		die_error('時間格式錯誤');
	}
	return;
}

?>










