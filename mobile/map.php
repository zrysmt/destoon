<?php
require 'common.inc.php';
isset($auth) or $auth = '';
$addr = $auth ? decrypt($auth, DT_KEY.'MAP') : '';
include DT_ROOT.'/api/map/baidu/config.inc.php';
$map_key or $map_key = 'waKl9cxyGpfdPbon7PXtDXIf';
$head_title = $L['map_title'].$DT['seo_delimiter'].$head_title;
$foot = '';
include template('map', 'mobile');
if(DT_CHARSET != 'UTF-8') toutf8();
?>