<?
require(__DIR__.'/lib/utilities.php');
check_auth();

$rs = db()->paging($PAGE_SIZE, 1)->query('SELECT `date_work`, `time_start`, `time_end` FROM `clock` WHERE `uid`=:uid ORDER BY `date_work` DESC', $_SESSION['uid']);
?>
<!DOCTYPE html>
<html>

<head>
<title>記錄</title>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<link rel="stylesheet" href="css/main.css">
</head>

<body>
<?php include 'nav.php'; ?>

<h1>記錄</h1>

<table id="clock_table" class="clock_in_table table table-bordered">
 	<thead>
		<tr>
			<th>日期</th>
			<th>上班</th>
			<th>下班</th>
		</tr>
	</thead>
	<tbody id="clock_tbody"></tbody>
</table>
<div class="pager"></div>

<?php include 'nav_mobile.php'; ?>

<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="js/bootbox.min.js"></script>
<script src="js/utilities.js"></script>
<script>
var clockData = <?=json_encode($rs)?>;

//------------------------------------------------------------------------------
render(clockData);

//------------------------------------------------------------------------------
function render(clockData){
	$("#clock_tbody").empty();

    if (clockData.data.length==0) {
        $("#clock_table").replaceWith('<label style="margin-top: 4rem;">暫無紀錄</label>');
        return;
    }

    for (var k in clockData.data) {
        var record = clockData.data[k];

        var tr = $('<tr>');
        tr.append($('<td>').html(record.date_work))
        .append($('<td>').html(getDate(record.time_start)))
        .append($('<td>').html(getDate(record.time_end)));

        $("#clock_tbody").append(tr);
    }
    renderPager(clockData.pager);
}

function getDate(datetime){
	if (datetime==null) {
		return '-';
	}

	// 2018-03-12 19:10:44 to 19:10
    var timeWithSecond = datetime.split(' ')[1];
    var time = timeWithSecond.substr(0, timeWithSecond.lastIndexOf(':'));
    return time;
}

// -----------------------------------------------------------------------------
// pagination
function query(pageNumber=1){
    ajax('action/action_clock.php', {
    	action: 'query',
        page_number: pageNumber,
    }, function(data) {
    	render(data);
    });
}

$('.pager').unbind().on('click', 'a', function(){
    var e = $(this);
    if (!e.hasClass('page_jumper')) {
        query(e.attr('data-page_number'));
        return false;
    }

    var pageNumber = prompt('請輸入頁碼:', '1');
    if (pageNumber) {
        query(pageNumber);
    }
    return false;
});

</script>
</body>
</html>