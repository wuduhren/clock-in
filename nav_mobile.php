<div id="nav_mobile">
    <div class="innerbox">
    	<? echo nav_mobile_render($_SERVER["SCRIPT_FILENAME"], $nav_list); ?>
    </div>
    <?
        if ($_SESSION['auth']>=1) 
            echo '<div class="innerbox">'.nav_mobile_render($_SERVER["SCRIPT_FILENAME"], $admin_nav_list).'</div>';
    ?>
    <div class="innerbox">
        <a class="text-sm-center nav-link" href="index.php">登出</a>
    </div>
</div>