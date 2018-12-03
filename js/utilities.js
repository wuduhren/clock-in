function ajax(url, data, callback){
    $.ajax({
        url:url,
        type:'POST', 
        data:data,
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
                console.log(msg);

                if (!msg){
                    //DB error
                    console.log(data.msg);
                    return;
                }

                if (msg=='session_expired') {
                    bootbox.alert('閒置過久, 請重新登入.', function(){
                        window.location = 'index.php';
                    });
                } else if (msg=='wrong_pwd') {
                    bootbox.alert('密碼錯誤, 請重新登入.', function(){
                        window.location = 'index.php';
                    });
                } else if (msg=='unauthorized_request') {
                    bootbox.alert('權限不足.', function(){
                        window.location = 'index.php';
                    });
                } else {
                    bootbox.alert(msg);
                }
                return;
            }
            
            // 成功
            if (callback) callback(data.data);
            return;
        }
    });
}

// pagination
function renderPager(data){
    var pager = $('.pager');
    var maxPagerCount = 5;

    var pageCurrent = parseInt(data.page_curr);
    var pageTotal = parseInt(data.page_total);
    var recordTotal = parseInt(data.record_total);
    var recordBegin = (pageCurrent-1)*data.page_size+1;
    var recordEnd = recordBegin + data.page_size-1;

    var s = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';

    if (recordTotal==0 || pageTotal==1) {
        pager.hide();
        return;
    }
    if (recordTotal<recordEnd) {
        recordEnd = recordTotal;
    }

    //start making html string
    if (pageCurrent>1) {
        s += '<li class="page-item"><a class="page-link" data-page_number="'+(pageCurrent-1)+' aria-label="Previous"><span aria-hidden="true">&laquo;</span><span class="sr-only">上一頁</span></a></li>';
    }

    for (i=1; i<=pageTotal; i++) {
        if (i>(pageCurrent+(maxPagerCount-1)/2)+0.5) {
            continue;
        } else if (i<(pageCurrent-(maxPagerCount-1)/2)) {
            continue;
        }

        if (i==pageCurrent){
            s += '<li class="page-item active"><a class="page-link" data-page_number="'+i+'">'+i+'</a></li>';
        } else {
            s += '<li class="page-item"><a class="page-link" data-page_number="'+i+'">'+i+'</a></li>';
        }
    }

    if (pageCurrent<pageTotal) {
        s += '<li class="page-item"><a class="page-link" data-page_number="'+(pageCurrent+1)+'" aria-label="Next"><span aria-hidden="true">&raquo;</span><span class="sr-only">下一頁</span></a></li>';
    }
    if (pageTotal>maxPagerCount) {
        s += ' &nbsp;&nbsp;&nbsp;<li><a class="page-link page_jumper">跳頁</a></li>';
    }

    s += '</ul></nav>';
    s += '<span>共'+recordTotal+'筆資料, 目前顯示'+recordBegin+'至'+recordEnd+'筆</span>';
    pager.html(s);
}