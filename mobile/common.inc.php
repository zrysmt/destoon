<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2016 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
define('DT_MOBILE', true);
require substr(str_replace("\\", '/', dirname(__FILE__)), 0, -7).'/common.inc.php';
require DT_ROOT.'/mobile/include/global.func.php';
if(is_pc()) dheader($EXT['mobile_url'].'mobile.php?action=device');
if(DT_CHARSET != 'UTF-8') header("Content-type:text/html; charset=utf-8");
include load('mobile.lang');
$EXT['mobile_enable'] or mobile_msg($L['msg_mobile_close']);
if($DT_BOT) $EXT['mobile_ajax'] = 0;
$_mobile = get_cookie('mobile');
if($_mobile == '' || $_mobile == 'pc') {
	set_cookie('mobile', 'touch', $DT_TIME + 30*86400);
}
$UA = strtoupper($_SERVER['HTTP_USER_AGENT']);
$back_link = $head_link = $head_name = '';
$mobile_modules = array('member', 'sell', 'buy', 'quote', 'company', 'exhibit', 'article', 'info', 'job', 'know', 'brand', 'mall', 'group', 'video', 'photo', 'club');
$pages = '';
$areaid = isset($areaid) ? intval($areaid) : 0;
$site_name = $head_title = $EXT['mobile_sitename'] ? $EXT['mobile_sitename'] : $DT['sitename'].$L['mobile_version'];
$kw = $kw ? strip_kw(decrypt($kw, DT_KEY.'KW')) : '';
if(strlen($kw) < $DT['min_kw'] || strlen($kw) > $DT['max_kw']) $kw = '';
$keyword = $kw ? str_replace(array(' ', '*'), array('%', '%'), $kw) : '';
$MURL = $MODULE[2]['linkurl'];
if($DT_MOB['browser'] == 'screen' && $_username) $MURL = 'mobile.php?action=sync&auth='.encrypt($_username.'|'.$DT_IP.'|'.$DT_TIME, DT_KEY.'SCREEN').'&goto=';
$_cart = (isset($MODULE[16]) && $_userid) ? intval(get_cookie('cart')) : 0;
$share_icon = ($DT_MOB['browser'] == 'weixin' || $DT_MOB['browser'] == 'qq') ? DT_PATH.'apple-touch-icon-precomposed.png' : '';
$MOB_MODULE = array();
foreach($MODULE as $v) {
	if(in_array($v['module'], $mobile_modules) && $v['module'] != 'member' && $v['ismenu']) $MOB_MODULE[] = $v;
}
$foot = 'channel';
?>