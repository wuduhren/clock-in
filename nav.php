<nav class="navbar navbar-expand-sm navbar-light bg-light">
    <a class="navbar-brand" href="clock.php">打卡系統</a>
    <div class="collapse navbar-collapse">
        <div class="navbar-nav">
            <? 
                echo nav_render($_SERVER["SCRIPT_FILENAME"], $nav_list);
                if ($_SESSION['auth']>=1) echo nav_render($_SERVER["SCRIPT_FILENAME"], $admin_nav_list);
            ?>
        </div>
        <div class="navbar-nav ml-auto">
            <a class="nav-item nav-link" href="index.php">登出</a>
        </div>
    </div>
</nav>