<?php
require(__DIR__.'/../lib/utilities.php');

$rs = db()->query('SELECT `account` FROM `user` WHERE `auth`=1 LIMIT 1');
var_dump('One of the admin account is '.$rs->account. ', dryrun success!');

die();

?>