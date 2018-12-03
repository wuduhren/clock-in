<?
require(__DIR__.'/lib/utilities.php');
check_auth();

$rs = db()->paging($PAGE_SIZE, 1)->query('SELECT `date_start`, `date_end`, `reason` FROM `dayoff` WHERE `uid`=:uid ORDER BY `id` DESC', $_SESSION['uid']);
?>
<!DOCTYPE html>
<html>

<head>
<title>請假</title>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<link rel="stylesheet" href="css/main.css">
<style>
.application_form {
	margin-top: 2.5rem;
}

#dayoff_table {
	display: none;
}
</style>
</head>

<body>
<?php include 'nav.php'; ?>

<h1>請假</h1>

<div class="application_form">
	<label>起始日期</label>
	<input id="date_start" type="date" class="form-control">
	<label>結束日期</label>
	<input id="date_end" type="date" class="form-control">
	<label>事由</label>
	<textarea id="reason" class="form-control" rows="5"></textarea>
	<span class="tiny_info" style="float: right;">* 若只請一天, 起始/結束日期 請填同一天.</span><br>
    <div class="application_form_action">
    	<button id="leave" class="application_form_submit clock_in_button btn btn-primary">請假</button>
    </div>
</div>

<table id="dayoff_table" class="clock_in_table table table-bordered">
    <thead>
        <tr>
            <th>起始</th>
            <th>結束</th>
            <th width="40%">事由</th>
        </tr>
    </thead>
    <tbody id="dayoff_tbody"></tbody>
</table>
<div class="pager"></div>

<?php include 'nav_mobile.php'; ?>

<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="js/bootbox.min.js"></script>
<script src="js/utilities.js"></script>
<script>
var dayoffData = <?=json_encode($rs)?>;

//------------------------------------------------------------------------------
document.getElementById('date_start').valueAsDate = new Date();
document.getElementById('date_end').valueAsDate = new Date();
render(dayoffData);

//------------------------------------------------------------------------------
$('#leave').on('click', function(){
	var dateStart = $('#date_start').val();
	var dateEnd = $('#date_end').val();

	if (dateStart=='') {
		bootbox.alert('請輸入起始日期.');
		return;
	}
	if (dateEnd=='') {
		bootbox.alert('請輸入結束日期.');
		return;
	}

	if (new Date(dateStart) > new Date(dateEnd)) {
		bootbox.alert('起始日期大於結束日期.');
		return;
	}

	ajax('action/action_dayoff.php', {
	    action: 'leave',
	    date_start: $('#date_start').val(),
	    date_end: $('#date_end').val(),
	    reason: $('#reason').val()
	}, function(){
        query();
		bootbox.alert('請假已提交.');
	});
});

function render(dayoffData){
	$("#dayoff_tbody").empty();

    if (dayoffData.data.length==0) {
        return;
    }
    for (var k in dayoffData.data) {
        var record = dayoffData.data[k];

        var tr = $('<tr>');
        tr.append($('<td>').html(record.date_start))
        .append($('<td>').html(record.date_end))
        .append($('<td>').html(record.reason));
        
        $("#dayoff_tbody").append(tr);
    }
    renderPager(dayoffData.pager);
    $('#dayoff_table').fadeIn();
}

// -----------------------------------------------------------------------------
// pagination
function query(pageNumber=1){
    ajax('action/action_dayoff.php', {
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