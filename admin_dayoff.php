<?
require(__DIR__.'/lib/utilities.php');
check_auth(1);

$dayoff_rs = db()->query('SELECT `uid`, `date_start`, `date_end`, `reason` FROM `dayoff`');
$user_rs = db()->query('SELECT `id`, `account` FROM `user`');
$user = [];
foreach ($user_rs->data as $row){
    $id = $row->id;
    $account = $row->account;
    $user[$id] = $account;
}
?>
<!DOCTYPE html>
<html>

<head>
<title>人員請假記錄</title>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.16/datatables.min.css"/>
<link rel="stylesheet" href="css/main.css">
<style>
#dayoff_table {
    margin-bottom: 1rem !important;
    display: none;
}
@media screen and (max-width: 600px) {
    .mobile_hidden {
        display: none;   
    }
}
</style>
</head>

<body>
<?php include 'nav.php'; ?>

<h1>人員請假記錄</h1>

<table id="dayoff_table" class="table table-bordered" style="border-collapse: collapse !important; text-align: left !important;">
 	<thead>
		<tr>
            <th>名稱</th>
			<th>起始</th>
			<th>結束</th>
			<th class="mobile_hidden">事由</th>
		</tr>
	</thead>
	<tbody id="dayoff_tbody"></tbody>
</table>

<?php include 'nav_mobile.php'; ?>

<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.16/datatables.min.js"></script>
<script src="js/bootbox.min.js"></script>
<script src="js/utilities.js"></script>
<script>
var dayoffData = <?=json_encode($dayoff_rs)?>;
var userData = <?=json_encode($user)?>;
var showAtOnce = <?=$PAGE_SIZE?>;

//------------------------------------------------------------------------------
render(dayoffData);

//------------------------------------------------------------------------------
function render(dayoffData){
    var paging = true;

	$("#dayoff_tbody").empty();

    if (dayoffData.data.length<=showAtOnce) {
        paging = false;
    }

    for (var k in dayoffData.data) {
        var record = dayoffData.data[k];

        var tr = $('<tr>');
        tr.append($('<td>').html(userData[record.uid]))
        .append($('<td>').html(record.date_start))
        .append($('<td>').html(record.date_end))
        .append($('<td>').html(record.reason).addClass('mobile_hidden'));
        
        $("#dayoff_tbody").append(tr);
    }

    $(document).ready(function(){
        $('#dayoff_table').DataTable({
            "dom": "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12'p>>" +
                "<'row'<'col-sm-12'i>>",
            "info": paging,
            "paging": paging,
            "lengthMenu": [ [showAtOnce, 20, 50, -1], [showAtOnce, 20, 50, "全部"] ],
            "columnDefs": [
                { "orderable": false, "targets": [3] }
            ],
            "language": {
                "search": "搜尋",
                "paginate": {
                    "first": '<span aria-hidden="true">«</span>',
                    "last": "尾頁",
                    "next": '<span aria-hidden="true">»</span>',
                    "previous": '<span aria-hidden="true">«</span>'
                },
                "lengthMenu": "顯示 _MENU_ 筆",
                "info": "共_TOTAL_筆資料, 目前顯示_START_至_END_筆",
                "infoFiltered": "",
                "infoEmpty": "暫無紀錄",
                "emptyTable": "暫無紀錄"
            },
        });
    });
    $('#dayoff_table').fadeIn();
}

</script>
</body>
</html>