<?php
require(__DIR__.'/lib/utilities.php');
$_SESSION = [];
?>
<!DOCTYPE html>
<html>

<head>
<title>登入</title>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<link rel="stylesheet" href="css/main.css">
<link rel="stylesheet" href="css/login.css"/>
</head>

<body>

<div class="loginmodal-container">
    <h1>故事系統登入</h1><br>
    <div>
        <input placeholder="請輸入帳號" id="acc_input" class="login_input" type="text">
        <input placeholder="請輸入密碼" id="pwd_input" class="login_input" type="password">
        <button id="login_btn" class="login_btn">登入</button>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="js/bootbox.min.js"></script>
<script src="js/utilities.js"></script>
<script>

$('#login_btn').on('click', function(){
    ajax('action/action_user.php', {
        action: 'login',
        account: $('#acc_input').val(),
        password: $('#pwd_input').val()
    }, function(){
        window.location = 'clock.php';
    });
});

</script>
</body>
</html>