<?
require(__DIR__.'/lib/utilities.php');
check_auth();

$rs = db()->query('SELECT `time_start`, `time_end` FROM `clock` WHERE `uid`=:uid AND `date_work`=:date_work', $_SESSION['uid'], date('Y-m-d'));
$time_clock_in = $rs->time_start;
$time_clock_out = $rs->time_end;
?>
<!DOCTYPE html>
<html>

<head>
<title>打卡</title>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<link rel="stylesheet" href="css/main.css">
<style>
.btn-xl {
	display: none;
	margin-top: 1.5rem;
    padding: 0.5rem 0.5rem;
    font-size: 4vw;
    border-radius: 10px;
    width: 20%;
}
.clock_time {
	font-size: 2.5vw;
}

#clock_out_btn {
	margin-top: 1rem;
}
@media screen and (max-width: 600px) {
	.btn-xl {
		margin-top: 1rem;
		width: 90%;
		padding: 1rem 1rem;
		font-size: 10vw;
	}

	.clock_time {
		font-size: 5vw;
	}

	#nav_mobile {
		margin-top: 4rem;
	}
}
</style>
</head>

<body>
<?php include 'nav.php'; ?>
<h1>打卡</h1>
<button id="clock_in_btn" class="btn btn-info btn-xl">上班</button>
<br>
<button id="clock_out_btn" class="btn btn-success btn-xl">下班</button>

<?php include 'nav_mobile.php'; ?>

<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="js/bootbox.min.js"></script>
<script src="js/utilities.js"></script>
<script>
var timeClockIn = '<?=$time_clock_in?>';
var timeClockOut = '<?=$time_clock_out?>';

//------------------------------------------------------------------------------
if (timeClockIn!='') {
	disableButton($('#clock_in_btn'), timeClockIn, '上班');
}
if (timeClockOut!='') {
	disableButton($('#clock_out_btn'), timeClockOut, '下班');
}

$('#clock_in_btn').fadeIn();
$('#clock_out_btn').fadeIn();
checkIfHoliday();

//------------------------------------------------------------------------------

$('#clock_in_btn').on('click', function(){
	ajax('action/action_clock.php', {
	    action: 'clock_in',
	}, function(timeClockIn){
		bootbox.alert('完成打卡: '+timeClockIn);
		disableButton($('#clock_in_btn'), timeClockIn, '上班');
	}); 
});

$('#clock_out_btn').on('click', function(){
	ajax('action/action_clock.php', {
	    action: 'clock_out',
	}, function(timeClockOut){
		bootbox.alert('完成打卡: '+timeClockOut);
		disableButton($('#clock_out_btn'), timeClockOut, '下班');
	});
});

function checkIfHoliday(){
	ajax('action/action_holiday.php', {
	    action: 'check_if_holiday',
	}, function(holidayName){
		if (holidayName!='') {
			bootbox.alert('恭喜! 今天放假: '+holidayName);
			disableButton($('#clock_in_btn'));
			disableButton($('#clock_out_btn'));
		}
	});
	return;
}

function disableButton(button, clockTime=null, complete='上班'){
	button.attr('disabled', '');
	if (clockTime) {
		button.html(complete+'<label class="clock_time">&nbsp;'+timeSlice(clockTime)+'</label>');
	}
	return;
}

function timeSlice(time){
	return time.toString().slice(11, 16);
}

</script>
</body>
</html>