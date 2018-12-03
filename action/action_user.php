<?php
require(__DIR__.'/../lib/utilities.php');
$action = get_request('action');

if ($action == 'login') {
	login();
	die();
}

check_auth(0, true);

if ($action == 'insert')
	insert();
else if ($action == 'update')
	update();
else if ($action == 'change_pwd')
	change_pwd();
else if ($action == 'admin_change_pwd')
	admin_change_pwd();
else if ($action == 'suspend')
	suspend();
else if ($action == 'reactivate')
	reactivate();
else if ($action == 'set_admin')
	set_admin();
else if ($action == 'unset_admin')
	unset_admin();
else if ($action == 'set_schedule')
	set_schedule();
die();
// -----------------------------------------------------------------------------
function login(){
	$account = get_request('account');
	$password = get_request('password');

	$rs = db()->query('SELECT `id`, `password`, `status`, `auth` FROM `user` WHERE `account`=:account LIMIT 1', $account);

	if (count($rs->data)==0){
		die_error('帳號: '.$account.' 不存在');
	}

	if ($rs->password != md5($password)) {
		die_error('wrong_pwd');
	}
	if ($rs->status != 1) {
		die_error('帳號已停權, 請洽管理人員');
	}

	$_SESSION['uid'] = intval($rs->id);
	$_SESSION['auth'] = intval($rs->auth);

	die_success();
	
}

function insert(){
	check_auth(1, true);
	
	$account = get_request('account');
	$password = get_request('password');
	$email = get_request('email');

	check_account($account);
	check_password($password);
	check_email($email);

	$id = db()->insert('user', [
		'account'=>$account,
		'password'=>md5($password),
		'email'=>$email,
		'status'=>1
	]);
	
	die_success();
}

function update(){
	$password = get_request('password');
	$email = get_request('email');

	check_email($email);

	$rs = db()->query('SELECT `password` FROM `user` WHERE `id`=:id', $_SESSION['uid']);
	if ($rs->password==md5($password)) {
		db()->update('user', 'id=:id',[
			'id'=>$_SESSION['uid'],
			'email'=>$email
		]);
		die_success();
	} else {
		die_error('wrong_pwd');
	}
}

function change_pwd(){
	$password = get_request('password');
	$new_password = get_request('new_password');

	check_password($new_password);

	$rs = db()->query('SELECT `password` FROM `user` WHERE `id`=:id', $_SESSION['uid']);
	if ($rs->password==md5($password)) {
		db()->update('user', 'id=:id',[
			'id'=>$_SESSION['uid'],
			'password'=>md5($new_password)
		]);
		die_success();
	} else {
		die_error('wrong_pwd');
	}
}

function admin_change_pwd(){
	check_auth(1, true);

	$uid = get_request('uid');
	$password = get_request('password');

	db()->update('user', 'id=:id',[
		'id'=>$uid,
		'password'=>md5($password)
	]);
	die_success();
}

function suspend(){
	check_auth(1, true);

	$uid = get_request('uid');

	//check if last admin
	$rs = db()->query('SELECT COUNT(*) FROM `user` where `auth` > 0');
	$admin_count = intval($rs['COUNT(*)']);
	$rs = db()->query('SELECT `auth` FROM `user` where `id`=:id', $uid);
	$auth = intval($rs->auth);
	if ($admin_count<=1 && $auth>=1) die_error('停用此管理員將導致系統鎖死.');

	db()->update('user', 'id=:id',[
		'id'=>$uid,
		'status'=>0
	]);
	die_success();
}

function reactivate(){
	check_auth(1, true);

	$uid = get_request('uid');
	db()->update('user', 'id=:id',[
		'id'=>$uid,
		'status'=>1
	]);
	die_success();
}

function set_admin(){
	check_auth(1, true);

	$uid = get_request('uid');
	db()->update('user', 'id=:id',[
		'id'=>$uid,
		'auth'=>1
	]);
	die_success();
}

function unset_admin(){
	check_auth(1, true);
	
	//check if last admin
	$rs = db()->query('SELECT COUNT(*) FROM `user` where `auth` > 0');
	$admin_count = intval($rs['COUNT(*)']);
	if ($admin_count <=1) die_error('取消此管理員將導致系統鎖死.');


	$uid = get_request('uid');
	db()->update('user', 'id=:id',[
		'id'=>$uid,
		'auth'=>0
	]);
	die_success();
}

function set_schedule(){
	$schedule = get_request('schedule');

	if ($schedule!=0 && $schedule!=1) {
		die_error('錯誤的狀態');
	}
	
	db()->update('user', 'id=:id',[
		'id'=>$_SESSION['uid'],
		'schedule'=>$schedule
	]);
	die_success();
}

// -----------------------------------------------------------------------------
function check_account($account){
	if (!ctype_alnum($account)) die_error('帳號只可輸入英文或數字, 大小寫有別.');

	$rs = db()->query('SELECT EXISTS(SELECT 1 FROM `user` WHERE `account`=:account)', $account);
	if (reset($rs[0])) die_error('帳號已存在');
}

function check_password($password){
	if (!ctype_alnum($password)) die_error('密碼只可輸入英文或數字, 大小寫有別.');
}

function check_email($email){
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) die_error('email有誤.');
}




?>