# clock-in

根據勞基法的規定, 公司都需要有一套紀錄員工上下班時間的打卡系統.

clock-in就是一套免費簡易的打卡系統, 幫助公司紀錄員工上下班時間及請假紀錄. 其特點如下

1. 讓員工線上打卡, 請假. 管理員可以看到紀錄.
2. 同時支援手機及電腦版.
3. 擁有自動打卡的功能, 管理員可以設定「自動打卡」. 若開啟這項功能系統將會自動幫員工線上打卡, 即使員工忘了打卡, 還是會有紀錄以供勞保局查驗.

According to the law, all companies must have some sort of clock-in system to record employee's working hours.
clock-in is a free, easy-to-use clock-in system for start up or small companies to use. Feature:

1. Online clock-in or ask for leave. Admin can manage by those record and stat.
2. Responsive
3. Auto clock-in. Admin is able to set "auto clock-in". Once it is ON, the system will automatically clock-in for the user.(Its a cheat, if some start-up doesn't care the working hour).

# 截圖
### 使用者線上打卡(User clock-in/clock-out on mobile.)
![](https://imgur.com/Pv3yqBq.png)
<br/>
### 使用者查看打卡記錄(User check the history of clock-in/clock-out.)
![](https://imgur.com/N2cG3Vq.png)
<br/>
### 使用者線上請假(User ask for leave.)
![](https://imgur.com/iYonXZj.png)
<br/>
### 管理員擁有較多權限: 例如讀取使用者打卡及請假, 設定自動打卡.(Admin can do more things. Such as read statistic of all user, set auto clock-in.)
![](https://imgur.com/XexQEsX.png)
<br/>
### 電腦版(Desktop)
![](https://imgur.com/L5JkKMX.png)
<br/>
![](https://imgur.com/e7vEOkm.png)
<br/>
![](https://imgur.com/uOunska.png)
<br/>
![](https://imgur.com/uwm45B1.png)


# 安裝
1. 將程式碼上傳至`www`目錄, 並修改`lib/config.php`資訊
2. db中預設管理員帳密(default account and password):

	```
	#帳號(account): root
	#密碼(password): 帳號加上一二三四五(阿拉伯數字)
	#一般使用者需要等管理員新增帳號後才會取得帳密.
	```

3. 設定cronjob

# Cronjob設定
1. 腳本存在`_script`目錄下面
2. dryrun.php 為測試用(for test). 可以先把要給cron執行的指令測試一下

	```
	# macOS
	$ curl localhost/clock-in/_script/clock_in.php
	# Ubuntu
	curl https://csc-mgr.info/clock-in/_script/dryrun.php
	```
	若出現`admin account is xxxxx, dryrun success!`代表成功.

	cronjob會每15分鐘跑一次來幫使用者在預定時間打卡.`$ sudo crontab -e`.

	```
	# macOS
	*/15 * * * * curl localhost/clock-in/_script/clock_in.php
	# Ubuntu
	*/15 * * * * curl https://csc-mgr.info/clock-in/_script/clock_in.php
	```

# csv格式
在管理介面 `admin_holiday.php` 可匯入.csv來新增, 修改國定假日. 
第需要column名稱.
範例:

	```
	"2018/6/18","端午節","是","放假之紀念日及節日","全國各機關學校放假一日。"
	    .
	    .
	    .
	"2018/8/25","","是","星期六、星期日",""
	"2018/8/26","","是","星期六、星期日",""
	"2018/9/1","","是","星期六、星期日",""
	"2018/9/2","","是","星期六、星期日",""
	"2018/9/3","軍人節","是","特定節日","軍人依國防部規定辦理。"
	```

# PHP SESSION
1. session存在DB當中
2. 每位使用者(包含管理人員)都需要`uid`的session來認證.
3. 管理人員則會多一個`auth`的session來認證.

# die_error()預期錯誤
0. `die_error()`處理程式碼在`js/utilities.js`中的`ajax`函式.
1. `session_expired`: 找不到使用者`uid`的`session`能執行ajax, 代表`session`過期, 顯示`閒置過久, 請重新登入.`後轉跳至登入頁面.
2. `wrong_pwd`: 使用者輸入錯誤之密碼. 顯示`密碼錯誤, 請重新登入.`後轉跳至登入頁面. 通常在登入頁面或是修改個人資料時會發生.

# 套件
## Bootbox(4.4.0)
1. 有alert, confirm, prompt等功能, 提供較漂亮的介面與callback功能.
2. 還沒完全支援Bootstrap: 4.0.0, 但確定alert功能沒問題
3. 檔案放在js目錄底下的bootbox.min.js

## Bootstrap(4.0.0)
1. 包含css及js
2. 都使用CDN

## DataTables(1.10.16)
1. 強大的table套件, 可以自動生成搜尋, 排序, 分頁, 選取等功能
2. 唯若要更動它自動生成的dom還要另外include
3. 其功選取(select)需要額外include, 在`admin_holiday.php`有使用到, 其cdn不太一樣, 要特別注意.
4. 包含css及js, 目前都使用CDN
5. CDN使用<https://datatables.net/download/>來生成, 目前都使用`bootstrap4`+`DataTables`(+ `select`)
6. 目前`DataTables`的`paging功能`是假的全部載回來再分頁, 但比數不大沒差.

## jQuery(3.3.1)
```
1. 在js部分載入
2. 使用其CDN
```

