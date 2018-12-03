<?php
require(__DIR__.'/../lib/utilities.php');

check_if_holiday();

$schedule = db()->query('SELECT `time_start`, `time_end`, `time_range` FROM `schedule` WHERE `id`=1');
$time_start = $schedule->time_start;
$time_end = $schedule->time_end;
$time_range = $schedule->time_range;


$user = db()->query('SELECT `id`, `schedule` FROM `user`');
foreach ($user->data as $u){
	if ($u->schedule != 1) continue;
	
	$uid = $u->id;
	check_if_dayoff($uid);

	$rs = db()->query('SELECT `time_start`, `time_end` FROM `clock` WHERE `uid`=:uid AND `date_work`=:date_work', $uid, date('Y-m-d'));

	//沒打卡且排程時間已到
	if (!isset($rs->time_start) && time()>strtotime($time_start)) {
		clock_in($uid, $time_start, $time_range);
	}
	if (!isset($rs->time_end) && time()>strtotime($time_end)) {
		clock_out($uid, $time_end, $time_range);
	}
}

die();

// -----------------------------------------------------------------------------
function check_if_holiday(){
    $rs = db()->query('SELECT `name` FROM `holiday` WHERE `date_holiday`=:date_holiday', date('Y-m-d'));
    if (count($rs->data)!=0) die();
    return;
}

function check_if_dayoff($uid){
    $rs = db()->query('SELECT `date_start`, `date_end` FROM `dayoff` WHERE `uid`=:uid', $uid);
    foreach ($rs as $dayoff) {
    	$today = date('Y-m-d');
    	$date_start = date('Y-m-d', strtotime($dayoff->date_start));
    	$date_end = date('Y-m-d', strtotime($dayoff->date_end));

    	if (($today>=$date_start) && ($today<=$date_end)) die();
    }
    return;
}

function clock_in($uid, $time_start, $time_range){
	$id = db()->insert('clock', [
		'uid'=>$uid,
		'date_work'=>date('Y-m-d'),
		'time_start'=>datetime($time_start, $time_range)
	]);
	return;
}

function clock_out($uid, $time_end, $time_range){
	db()->update('clock', 'uid=:uid AND date_work=:date_work',[
		'uid'=>$uid,
		'date_work'=>date('Y-m-d'),
		'time_end'=>datetime($time_end, $time_range)
	]);
	return;
}

function datetime($time, $time_range){
	$time = strtotime(date('Y-m-d').' '.$time);
	$time = $time+rand(-$time_range, $time_range)*60;

	$date = new \DateTime();
	$date->setTimestamp($time);

	return $date->format('Y-m-d H:i:s');
}


?>