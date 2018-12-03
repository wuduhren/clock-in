<?
require(__DIR__.'/lib/utilities.php');
check_auth();

$rs = db()->query('SELECT `schedule` FROM `user` WHERE `id`=:uid', $_SESSION['uid']);
?>
<!DOCTYPE html>
<html>

<head>
<title>設定</title>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<link rel="stylesheet" href="css/main.css">
<style>
h1 {
	margin-top: 2.5rem;
}
#set_schedule {
	margin-top: 1rem;
	margin-bottom: 1rem;
	font-size: 4rem;
}
.application_form {
	margin-top: 0rem;
}

@media screen and (max-width: 600px) {
	#set_schedule {
		font-size: 2rem;
	}
}
</style>
</head>

<body>
<?php include 'nav.php'; ?>

<h1>打卡排程</h1>
<button id="set_schedule" class="clock_in_button btn btn-secondary" schedule="0">排程關閉</button>
<br><span id="schedule_span" class="tiny_info">若開啟排程, 系統將會為您自動打卡.<br>目前為關閉狀態.</span>

<h1 style="margin-top: 3.5rem;">修改Email</h1>
<div class="application_form">
	<label>請輸入密碼</label>
	<input id="pwd" type="password" class="form-control">
	<label>請輸入新的Email</label>
	<input id="new_email" type="email" class="form-control">
	<div class="application_form_action">
		<button id="edit_user_info" class="application_form_submit clock_in_button btn btn-primary">修改</button>
	</div>
</div>

<h1 style="margin-top: 4rem;">修改密碼</h1>
<div class="application_form">
	<label>請輸入密碼</label>
	<input id="old_pwd" type="password" class="form-control">
	<label>請輸入新密碼</label>
	<input id="new_pwd" type="password" class="form-control">
	<label>再輸入一次新密碼</label>
	<input id="new_pwd_confirm" type="password" class="form-control">
	<div class="application_form_action">
		<button id="edit_user_pwd" class="application_form_submit clock_in_button btn btn-primary">修改</button>
	</div>
</div>

<?php include 'nav_mobile.php'; ?>

<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="js/bootbox.min.js"></script>
<script src="js/utilities.js"></script>
<script>
var schedule = <?=json_encode($rs->schedule)?>;
if (schedule==1) {
	scheduleOn();
}
//------------------------------------------------------------------------------
function scheduleOn(){
	$('#set_schedule').attr('schedule', '1');
	$('#set_schedule').removeClass('btn-secondary').addClass('btn-info').text('排程開啟');
	$('#schedule_span').html('若開啟排程, 系統將會為您自動打卡.<br>目前為開啟狀態.');
}

function scheduleOff(){
	$('#set_schedule').attr('schedule', '0');
	$('#set_schedule').removeClass('btn-info').addClass('btn-secondary').text('排程關閉');
	$('#schedule_span').html('若開啟排程, 系統將會為您自動打卡.<br>目前為關閉狀態.');
}

$('#set_schedule').on('click', function(){
	var schedule = parseInt($(this).attr('schedule'));
	if (schedule==0){
		schedule = 1;
		ajax('action/action_user.php', {
		    action: 'set_schedule',
		    schedule: schedule,
		}, function(){
			bootbox.alert('排程開啟');
			scheduleOn();
		});
	} else if (schedule==1){
		schedule = 0;
		ajax('action/action_user.php', {
		    action: 'set_schedule',
		    schedule: schedule,
		}, function(){
			bootbox.alert('排程關閉');
			scheduleOff();
		});
	}
	return;
});

$('#edit_user_info').on('click', function(){
	ajax('action/action_user.php', {
	    action: 'update',
	    password: $('#pwd').val(),
	    email: $('#new_email').val()
	}, function(){
		bootbox.alert('修改完成.');
	});
});

$('#edit_user_pwd').on('click', function(){
	if ($('#new_pwd').val()!=$('#new_pwd_confirm').val()){
		$('#old_pwd').val('');
		$('#new_pwd').val('');
        $('#new_pwd_confirm').val('');
        bootbox.alert('請重新輸入密碼');
		return;
	}

	ajax('action/action_user.php', {
	    action: 'change_pwd',
	    password: $('#old_pwd').val(),
	    new_password: $('#new_pwd').val()
	}, function(){
		bootbox.alert('修改完成, 請重新登入.', function(){
            window.location = 'index.php';
        });
	});
});

</script>
</body>
</html>