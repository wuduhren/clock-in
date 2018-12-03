<?
require(__DIR__.'/lib/utilities.php');
check_auth(1);

$rs = db()->query('SELECT `id`, `account`, `status`, `auth`, `time_create` FROM `user` ORDER BY `auth` DESC, `status` DESC');
?>
<!DOCTYPE html>
<html>

<head>
<title>人員管理</title>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.16/datatables.min.css"/>
<link rel="stylesheet" href="css/main.css">

<style>
.dataTables_wrapper {
    margin-top: 1.5rem;
}

#user_table {
    display: none;
    margin-bottom: 1rem !important;
}
#user_table .selected {
    background-color: #4267b2;
    color: white;
}

#add_user {
    margin-top: 1.5rem;
}

.form-control {
    margin-bottom: 10px;
}
.form-control:focus {
    border-color: #7F7F7F;
    border-width: 0.2rem;
    box-shadow: none;
}

#hint {
    display: none;
}
@media screen and (max-width: 600px) {
    .user_action {
        margin: 0.5rem 0.2rem 0.5rem 0.2rem;
    }
}
</style>
</head>

<body>
<?php include 'nav.php'; ?>

<h1>人員管理</h1>

<button id="add_user" class="clock_in_button btn btn-info" data-toggle="modal" data-target="#add_user_modal">新增人員</button>
<table id="user_table" class="table table-bordered" style="border-collapse: collapse !important; text-align: left !important;">
    <thead>
        <tr>
            <th>名稱</th>
            <th>狀態</th>
        </tr>
    </thead>
    <tbody id="user_tbody"></tbody>
</table>
<span id="hint" class="tiny_info">提示: 點選列表可進行操作</span>


<div id="user_action_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="add_user_modal" aria-hidden="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button id="set_admin" class="user_action btn btn-outline-primary">設為管理員</button>
                <button id="unset_admin" class="user_action btn btn-outline-secondary">取消管理員</button>
                <button id="reactivate" class="user_action btn btn-outline-success">啟用</button>
                <button id="suspend" class="user_action btn btn-outline-danger">停用</button>
                <button id="change_pwd" class="user_action btn btn-outline-dark">修改密碼</button>
            </div>
        </div>
    </div>
</div>


<div id="add_user_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="add_user_modal" aria-hidden="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">新增人員</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div>
                    <input id="acc_inpput" class="form-control" type="text" placeholder="請輸入帳號">
                    <input id="email_input" class="form-control" type="email" placeholder="請輸入email">
                    <input id="pwd_input" class="form-control" type="text" placeholder="請輸入密碼">
                    <input id="pwd_confirm_input" class="form-control" type="text" placeholder="請再輸入一次密碼">
                    <span class="tiny_info" style="float: right;">* 帳密只可輸入英文及數字, 大小寫有別.</span>
                </div>
            </div>

            <div class="modal-footer">
                <button id="add_user_btn" class="btn btn-primary">新增</button>
                <button class="btn btn-secondary" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>

<div id="change_pwd_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="change_pwd_modal" aria-hidden="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">修改密碼</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div>
                    <input id="change_pwd_input" class="form-control" type="text" placeholder="請輸入密碼">
                    <input id="change_pwd_confirm_input" class="form-control" type="text" placeholder="請再輸入一次密碼">
                    <span class="tiny_info" style="float: right;">* 帳密只可輸入英文或數字, 大小寫有別.</span><br>
                </div>
            </div>

            <div class="modal-footer">
                <button id="admin_change_pwd" class="btn btn-primary">修改</button>
                <button class="btn btn-secondary" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>

<?php include 'nav_mobile.php'; ?>

<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.16/datatables.min.js"></script>
<script src="js/bootbox.min.js"></script>
<script src="js/utilities.js"></script>
<script>
var userData = <?=json_encode($rs->data)?>;
var showAtOnce = <?=$PAGE_SIZE?>;

//------------------------------------------------------------------------------
render(userData);
$('#user_table').fadeIn();
$('#hint').fadeIn();

//------------------------------------------------------------------------------
$('#add_user_btn').on('click', function(){

    if ($('#pwd_input').val()!=$('#pwd_confirm_input').val()){
        $('#pwd_input').val('');
        $('#pwd_confirm_input').val('');
        bootbox.alert('請重新輸入密碼');
        return;
    }

    ajax('action/action_user.php', {
        action: 'insert',
        account: $('#acc_inpput').val(),
        password: $('#pwd_input').val(),
        email: $('#email_input').val()
    }, function(){
        bootbox.alert('新增成功', function(){
            location.reload();
        });
    });
});

$('tr').on('click', function(){
    $('.selected').removeClass('selected');
    $(this).addClass('selected');
    $('#user_action_modal').modal();
});

$('#user_action_modal').on('hidden.bs.modal', function(){
    $('.selected').removeClass('selected');
});

$('#reactivate').on('click', function(){
    var uid = $(".selected").attr('user_id');
    var account = $(".selected").attr('account');

    if (uid===undefined) {
        bootbox.alert('請點選列表選取');
        return;
    }
    ajax('action/action_user.php', {
        action: 'reactivate',
        uid: uid
    }, function(){
        bootbox.alert(account+'已重新啟用', function(){
            location.reload();
        });
    });
});

$('#suspend').on('click', function(){
    var uid = $(".selected").attr('user_id');
    var account = $(".selected").attr('account');

    if (uid===undefined) {
        bootbox.alert('請點選列表選取');
        return;
    }
    ajax('action/action_user.php', {
        action: 'suspend',
        uid: uid
    }, function(){
        bootbox.alert(account+'已被停權', function(){
            location.reload();
        });
    });
});

$('#set_admin').on('click', function(){
    var uid = $(".selected").attr('user_id');
    var account = $(".selected").attr('account');

    if (uid===undefined) {
        bootbox.alert('請點選列表選取');
        return;
    }
    ajax('action/action_user.php', {
        action: 'set_admin',
        uid: uid
    }, function(){
        bootbox.alert(account+'已成為管理員', function(){
            location.reload();
        });
    });
});

$('#unset_admin').on('click', function(){
    var uid = $(".selected").attr('user_id');
    var account = $(".selected").attr('account');

    if (uid===undefined) {
        bootbox.alert('請點選列表選取');
        return;
    }
    ajax('action/action_user.php', {
        action: 'unset_admin',
        uid: uid
    }, function(){
        bootbox.alert(account+'已成為一般人員', function(){
            location.reload();
        });
    });
});

$('#change_pwd').on('click', function(){
    var uid = $(".selected").attr('user_id');
    if (uid===undefined) {
        bootbox.alert('請點選列表選取');
        return;
    }
    $('#change_pwd_modal').modal();
});

$('#admin_change_pwd').on('click', function(){
    var uid = $(".selected").attr('user_id');
    var account = $(".selected").attr('account');

    if (uid===undefined) {
        bootbox.alert('請點選列表選取');
        return;
    }

    if ($('#change_pwd_input').val()!=$('#change_pwd_confirm_input').val()){
        $('#change_pwd_input').val('');
        $('#change_pwd_confirm_input').val('');
        bootbox.alert('請重新輸入密碼');
        return;
    }
    var password = $('#change_pwd_input').val();

    ajax('action/action_user.php', {
        action: 'admin_change_pwd',
        uid: uid,
        password: password
    }, function(){
        bootbox.alert(account+'密碼已更改');
        $('#change_pwd_modal').modal('toggle');
    });
});

function render(userData){
    var paging = true;

    $("#user_tbody").empty();

    if (userData.length<=showAtOnce) {
        paging = false;
    }

    for (var k in userData) {
        var record = userData[k];
        var tr = $('<tr>');
        var status;

        if (record.status==0) {
            status = '停權';
        } else if (record.status==1) {
            status = '一般';
            if (record.auth==1) {
                status = '管理員';
            }
        }

        tr.attr('user_id', record.id).attr('account', record.account)
        .append($('<td>').html(record.account))
        .append($('<td>').html(status));

        $("#user_tbody").append(tr);
    }

    $(document).ready(function(){
        $('#user_table').DataTable({
            "dom": "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12'p>>" +
                "<'row'<'col-sm-12'i>>",
            "info": false,
            "paging": paging,
            "lengthMenu": [ [showAtOnce, 20, 50, -1], [showAtOnce, 20, 50, "全部"] ],
            "ordering": false,
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
            }
        });
    });
}

</script>
</body>
</html>