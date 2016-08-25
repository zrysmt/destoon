<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2015 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
$api = $DT['trade_tp'] ? 2 : 1;
$aliapy_config['partner']      = $DT['trade_id'];
$aliapy_config['key']          = $DT['trade_pw'];
$aliapy_config['seller_email'] = $DT['trade_ac'];
$aliapy_config['return_url']   = DT_PATH.'api/trade/alipay/'.$api.'/return.php';
$aliapy_config['notify_url']   = DT_PATH.'api/trade/alipay/'.$api.'/'.($DT['trade_nu'] ? $DT['trade_nu'] : 'notify.php');
$aliapy_config['sign_type']    = 'MD5';
$aliapy_config['input_charset']= strtolower(DT_CHARSET);
$aliapy_config['transport']    = 'http';
?>