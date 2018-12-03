<?php
require(__DIR__.'/config.php');
require(__DIR__.'/db.php');
require(__DIR__.'/session.php');

// -----------------------------------------------------------------------------
// 設定全域錯誤處理函式
function exception_handler($exception){
    $msg = $exception->getMessage();
    die(json_encode(['code'=>0, 'msg'=>$msg], JSON_UNESCAPED_UNICODE));
}
set_exception_handler('exception_handler');

function error_handler($errno, $errstr, $errfile, $errline){
    $msg = '('.$errno.') '.$errstr.' in '.$errfile.' line:'.$errline;
    if (error_reporting() === 0) return true;  
    die(json_encode(['code'=>0, 'msg'=>$msg], JSON_UNESCAPED_UNICODE));
}
set_error_handler('error_handler');

// -----------------------------------------------------------------------------
// 回傳錯誤
function die_error($data=null, $code=0){
    if ($data==null) $data=[];
    die(json_encode(['code'=>$code, 'data'=>$data], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
}

function die_success($data=null, $code=1){
    if ($data==null) $data=[];
    die(json_encode(['code'=>$code, 'data'=>$data], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
}

// -----------------------------------------------------------------------------
function get_request($name, $optional=false, $default=null){
    if (!isset($_REQUEST[$name])) {
        if ($optional) return $default;
        die_error(['error'=>'get_request_failed:'.$name]);
    }
    return $_REQUEST[$name];
}

// -----------------------------------------------------------------------------
function check_auth($auth=0, $ajax=false){
    if (!isset($_SESSION['uid']) || !isset($_SESSION['auth'])) {
        if ($ajax===true) die_error('session_expired');
        header('Location: ./');
        exit;
    }
    if ($_SESSION['auth']<$auth) {
        if ($ajax===true) die_error('unauthorized_request');
        header('Location: ./');
        exit;
    }
    return;
}

// -----------------------------------------------------------------------------
// nav
$nav_list = ['clock.php'=>'打卡', 'clock_list.php'=>'記錄', 'dayoff.php'=>'請假', 'setting.php'=>'設定'];
$admin_nav_list = ['admin_clock.php'=>'人員打卡記錄', 'admin_dayoff.php'=>'人員請假記錄', 'admin_user.php'=>'人員管理', 'admin_schedule.php'=>'打卡排程','admin_holiday.php'=>'假日設定'];

function string_contain($string, $sub_string){
    if (strpos($string, $sub_string) !== false) return true;
    return false;
}

function at_page($file_url, $file){
    $file_url = substr($file_url, strrpos($file_url, '/') + 1);
    if ($file_url==$file) {
        return true;
    }
    return false;
}

function nav_mobile_render($file_url, $list){
    $html = '';
    foreach ($list as $file=>$name) {
        if (!at_page($file_url, $file)) {
            $s = '<a class="text-sm-center nav-link" href="'.$file.'">'.$name.'</a>';
            $html.=$s;
        }
    }
    return $html;
}

function nav_render($file_url, $list){
    $html = '';
    foreach ($list as $file=>$name) {
        if (at_page($file_url, $file)) {
            $s = '<a class="nav-item nav-link active" href="'.$file.'">'.$name.'</a>';
        } else {
            $s = '<a class="nav-item nav-link" href="'.$file.'">'.$name.'</a>';
        }
        $html.=$s;
    }
    return $html;
}




?>