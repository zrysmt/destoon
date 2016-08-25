<?php
defined('IN_DESTOON') or exit('Access Denied');
if(strlen($answer) < 1) exit('1');
$answer = stripslashes($answer);
$answer = convert($answer, 'UTF-8', DT_CHARSET);
$session = new dsession();
if(!isset($_SESSION['answerstr'])) exit('2');
if($_SESSION['answerstr'] != md5(md5($answer.DT_KEY.$DT_IP))) exit('3');
exit('0');
?>