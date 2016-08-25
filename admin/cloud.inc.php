<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2016 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
$install = file_get(DT_CACHE.'/install.lock');
$url = decrypt('9c86mN4yUrDymsHosfI6kag1VdsjAXdpuY7C5UD3GsPI1P68R7SnbeT3X4qgVLxH0VyC3IcilPVjRchf8M3Zjl0', 'DESTOON').'?action='.$action.'&product=b2b&version='.DT_VERSION.'&release='.DT_RELEASE.'&lang='.DT_LANG.'&charset='.DT_CHARSET.'&install='.$install.'&os='.PHP_OS.'&soft='.urlencode($_SERVER['SERVER_SOFTWARE']).'&php='.urlencode(phpversion()).'&mysql='.urlencode($db->version()).'&url='.urlencode($DT_URL).'&site='.urlencode($DT['sitename']).'&auth='.strtoupper(md5($DT_URL.$install.$_SERVER['SERVER_SOFTWARE']));
if(isset($mfa)) $url .= '&mfa='.$mfa;
dheader($url);
?>