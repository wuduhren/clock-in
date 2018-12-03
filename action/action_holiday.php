<?php
require(__DIR__.'/../lib/utilities.php');
check_auth(0, true);

$action = get_request('action', true, 'import_holiday_csv');

if ($action == 'check_if_holiday')
    check_if_holiday();
else if ($action == 'remove_holiday')
	remove_holiday();
else if ($action == 'import_holiday_csv')
    import_holiday_csv();
die();

// -----------------------------------------------------------------------------
function check_if_holiday(){
    $rs = db()->query('SELECT `name` FROM `holiday` WHERE `date_holiday`=:date_holiday', date('Y-m-d'));
    die_success($rs->name);
}

function remove_holiday(){
	check_auth(1, true);
	
	$date = get_request('date');
	
	foreach ($date as $date_holiday) {
		db()->exec('DELETE FROM `holiday` WHERE `date_holiday`=:date_holiday', $date_holiday);
	}
	die_success();
}

function import_holiday_csv(){
	check_auth(1, true);

    if(empty($_FILES["holiday_file"]["name"])) {
        die_error('請選擇檔案');
    }
    if (!strpos($_FILES["holiday_file"]["name"], '.csv')) {
        die_error('請選擇.csv檔');
    }

    $skip = true; //skip first row
    $file_data = fopen($_FILES["holiday_file"]["tmp_name"], 'r');
    while($row = fgetcsv($file_data)) {
    	if ($skip) {
    		$skip = false;
    		continue;
    	}

    	$date_holiday = $row[0];
        $name = $row[1];
        $is_holiday = $row[2];
        $holiday_category = $row[3];
        $description = $row[4];

        if ($is_holiday=='否') continue; //補假, 不放假之紀念日等等

        if ($name=='') $name = $holiday_category;
        
        if (!check_date($date_holiday)) die_error('日期格式錯誤: '.$date_holiday);

        db()->exec('INSERT INTO `holiday` (`date_holiday`, `name`, `holiday_category`, `description`) VALUES(:date_holiday, :name, :holiday_category, :description) 
        	ON DUPLICATE KEY UPDATE `name`=:name, `holiday_category`=:holiday_category, `description`=:description', 
        	$date_holiday, $name, $holiday_category, $description,
        	$name, $holiday_category, $description
        );
    }

    die_success();
}

function check_date($date, $format='Y/n/j'){
	//format m: 01~12, d:01~31, n:1~12, j:1~31
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}


?>  