<?
require(__DIR__.'/lib/utilities.php');
check_auth(1);

$rs = db()->query('SELECT `date_holiday`, `name` FROM `holiday` ORDER BY `date_holiday` DESC');
?>
<!DOCTYPE html>
<html>

<head>
<title>假日設定</title>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.16/sl-1.2.5/datatables.min.css"/>
<link rel="stylesheet" href="css/main.css">
<style>
#upload_csv {
    margin-top: 1rem;
    margin-bottom: 2rem;
    display: none;
}

#holiday_table {
    display: none;
}
#holiday_table .selected {
    background-color: #4267b2;
}

#remove {
    display: none;
}
</style>
</head>

<body>
<?php include 'nav.php'; ?>

<h1>假日設定</h1>

<table id="holiday_table" class="table table-bordered" style="border-collapse: collapse !important; text-align: left !important;">
    <thead>
        <tr>
            <th>日期</th>
            <th>名稱</th>
        </tr>
    </thead>
    <tbody id="holiday_tbody"></tbody>
</table>

<form id="upload_csv" method="post" enctype="multipart/form-data">
    <input type="file" name="holiday_file"/>
    <input type="submit" value="上傳檔案" class="btn btn-info"/>
</form>
<button id="remove" class="btn btn-secondary">刪除假日</button>

<?php include 'nav_mobile.php'; ?>

<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.16/sl-1.2.5/datatables.min.js"></script>
<script src="js/bootbox.min.js"></script>
<script src="js/utilities.js"></script>
<script>
var holidayData = <?=json_encode($rs->data)?>;
var showAtOnce = <?=$PAGE_SIZE?>;

//------------------------------------------------------------------------------
render(holidayData);

//------------------------------------------------------------------------------
$('#upload_csv').on("submit", function(e){  
    e.preventDefault(); //form will not submitted
    
    uploadCSV(this, function(){
        bootbox.alert('上傳成功.', function(){
            location.reload();
        });
    });
});

function uploadCSV(formData, callback){
    $.ajax({
        url: 'action/action_holiday.php',
        type: 'POST', 
        data: new FormData(formData),
        contentType: false,
        cache: false,
        processData: false,
        error: function (request, status, error) {
            console.dir(request)
            alert(request.responseText);
        },
        success: function(ret) {
            try {
                data = JSON.parse(ret);
            } catch(error) {
                // 非預期錯誤
                console.log(ret);
                console.log(error);
                alert('抱歉，發生伺服器錯誤。');
                return;
            }
            
            // 預期錯誤
            if (data.code==0){
                var msg = data.data;
                if (!msg){
                    //DB error
                    console.log(data.msg);
                    bootbox.alert('資料格式錯誤');
                    return;
                }

                console.log(msg);
                bootbox.alert(msg);
                return;
            }
            
            // 成功
            if (callback) callback(data.data);
            return;
        }
    });
}

function render(holidayData){
    var paging = true;

    $("#holiday_tbody").empty();

    if (holidayData.length<=showAtOnce) {
        paging = false;
    }

    for (var k in holidayData) {
        var record = holidayData[k];

        var tr = $('<tr>');
        tr.attr('date', record.date_holiday)
        .append($('<td>').html(record.date_holiday))
        .append($('<td>').html(record.name));
        
        $("#holiday_tbody").append(tr);
    }

    $(document).ready(function(){
        $('#holiday_table').DataTable({
            "dom": "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12'p>>" +
                "<'row'<'col-sm-12'i>>",
            "info": paging,
            "paging": paging,
            "select": "multi",
            "lengthMenu": [ [showAtOnce, 20, 50, -1], [showAtOnce, 20, 50, "全部"] ],
            "order": [[0,"desc"]],
            "columnDefs": [
                { "orderable": false, "targets": [1] }
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
                "emptyTable": "暫無紀錄",
                "select": {
                    "rows": {
                        "_": "",
                        "0": "",
                        "1": ""
                    }
                }
            },
        });
    });

    $('#holiday_table').fadeIn();
    if ($(window).width()>=600) {
        $('#upload_csv').fadeIn().css('display', 'inline-block');
        $('#remove').fadeIn();
    }
}

$('#remove').on('click', function(){
    var date = [];

    $(".selected").each(function(){
        date.push($(this).attr('date'));
    });

    if (date.length==0) {
        bootbox.alert('請點選列表選取');
        return;
    }
    
    ajax('action/action_holiday.php', {
        action: 'remove_holiday',
        date: date
    }, function(){
        bootbox.alert('刪除成功', function(){
            location.reload();
        });
    });
});


</script>
</body>
</html>