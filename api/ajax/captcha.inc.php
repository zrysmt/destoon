<?php
defined('IN_DESTOON') or exit('Access Denied');
if(strlen($captcha) < 4) exit('1');
$session = new dsession();
if(!isset($_SESSION['captchastr'])) exit('2');
$captcha = convert($captcha, 'UTF-8', DT_CHARSET);
if($_SESSION['captchastr'] != md5(md5(strtoupper($captcha).DT_KEY.$DT_IP))) exit('3');
exit('0');
?>