<?php
require 'common.inc.php';
$foot = 'more';
$head_title = $L['tips_title'].$DT['seo_delimiter'].$head_title;
include template('tips', 'mobile');
if(DT_CHARSET != 'UTF-8') toutf8();
?>