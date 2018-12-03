<?
require(__DIR__.'/lib/utilities.php');
check_auth();

$rs = db()->query('SELECT `time_start`, `time_end`, `time_range` FROM `schedule` WHERE `id`=1');
?>
<!DOCTYPE html>
<html>

<head>
<title>打卡排程</title>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<link rel="stylesheet" href="css/main.css">
<style>

</style>
</head>

<body>
<?php include 'nav.php'; ?>

<h1>打卡排程</h1>

<div class="application_form">
	<label>上班時間</label>
	<input id="time_start" type="time" class="form-control" value="08:00">
	<label>下班時間</label>
	<input id="time_end" type="time" class="form-control" value="17:00">
	<label>誤差值(分鐘)</label>
	<input id="time_range" type="number" class="form-control" value="15" min="0" max="30">
	<span class="tiny_info" style="float: right;">* 設定誤差值可避免每天都在相同時間打卡. 0~30之正整數.</span>
	<div class="application_form_action" style="margin-top: 4rem;">
		<button id="schedule" class="clock_in_button btn btn-primary">設定時間</button>
	</div>
</div>

<?php include 'nav_mobile.php'; ?>

<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="js/bootbox.min.js"></script>
<script src="js/utilities.js"></script>
<script>
var timeStart = '<?=$rs->time_start?>';
var timeEnd = '<?=$rs->time_end?>';
var timeRange = '<?=$rs->time_range?>';

//------------------------------------------------------------------------------
$('#time_start').val(timeSlice(timeStart));
$('#time_end').val(timeSlice(timeEnd));
$('#time_range').val(timeRange);

//------------------------------------------------------------------------------
$('#schedule').on('click', function(){
	var timeStart = $('#time_start').val();
	var timeEnd = $('#time_end').val();
	var timeRange = $('#time_range').val();

	if (timeStart=='') {
		bootbox.alert('請輸入上班時間.');
		return;
	}
	if (timeEnd=='') {
		bootbox.alert('請輸入下班時間.');
		return;
	}
	if (!isNormalInteger(timeRange)) {
		bootbox.alert('請輸入誤差值');
		return;
	}
	
	ajax('action/action_schedule.php', {
	    action: 'update',
	    time_start: timeStart+':00',
	    time_end: timeEnd+':00',
	    time_range: timeRange
	}, function(data){
		bootbox.alert('設定成功');
	});
});

//正整數
function isNormalInteger(str) {
    var n = Math.floor(Number(str));
    return String(n) === str && n >= 0;
}

function timeSlice(time){
	return time.toString().slice(0, -3);
}

</script>
</body>
</html>