<?php
require 'common.inc.php';
set_cookie('mobile', 'pc', $DT_TIME + 30*86400);
$foot = '';
$head_title = $L['device_title'].$DT['seo_delimiter'].$head_title;
include template('device', 'mobile');
if(DT_CHARSET != 'UTF-8') toutf8();
?>