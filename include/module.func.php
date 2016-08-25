<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2016 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
function get_fee($item_fee, $mod_fee) {
	if($item_fee < 0) {
		$fee = 0;
	} else if($item_fee == 0) {
		$fee = $mod_fee;
	} else {
		$fee = $item_fee;
	}
	return $fee;
}

function keyword($kw, $items, $moduleid) {
	global $db, $DT_TIME, $DT;
	if(!$DT['search_kw'] || $items < 2 || strlen($kw) < 3 || strlen($kw) > 30 || strpos($kw, ' ') !== false || strpos($kw, '%') !== false) return;
	$kw = addslashes($kw);
	$r = $db->get_one("SELECT * FROM {$db->pre}keyword WHERE moduleid=$moduleid AND word='$kw' ORDER BY itemid ASC");
	if($r) {
		$items = $items > $r['items'] ? $items : $r['items'];
		$month_search = date('Y-m', $r['updatetime']) == date('Y-m', $DT_TIME) ? 'month_search+1' : '1';
		$week_search = date('W', $r['updatetime']) == date('W', $DT_TIME) ? 'week_search+1' : '1';
		$today_search = date('Y-m-d', $r['updatetime']) == date('Y-m-d', $DT_TIME) ? 'today_search+1' : '1';
		$db->query("UPDATE {$db->pre}keyword SET items='$items',updatetime='$DT_TIME',total_search=total_search+1,month_search=$month_search,week_search=$week_search,today_search=$today_search WHERE itemid=$r[itemid]");
		$db->query("DELETE FROM {$db->pre}keyword WHERE moduleid=$moduleid AND word='$kw' AND itemid>$r[itemid]");
	} else {
		$letter = trim(gb2py($kw));
		$status = $DT['search_check_kw'] ? 2 : 3;
		if(strlen($letter) < 2) $status = 2;
		$db->query("INSERT INTO {$db->pre}keyword (moduleid,word,keyword,letter,items,updatetime,total_search,month_search,week_search,today_search,status) VALUES ('$moduleid','$kw','$kw','$letter','$items','$DT_TIME','1','1','1','1','$status')");
	}
}

function money_add($username, $amount) {
	global $db;
	if($username && $amount) $db->query("UPDATE {$db->pre}member SET money=money+{$amount} WHERE username='$username'");
}

function money_record($username, $amount, $bank, $editor, $reason, $note = '') {
	global $db, $DT_TIME;
	if($username && $amount) {
		$r = $db->get_one("SELECT money FROM {$db->pre}member WHERE username='$username'");
		$balance = $r['money'];
		$reason = addslashes(stripslashes(strip_tags($reason)));
		$note = addslashes(stripslashes(strip_tags($note)));
		$db->query("INSERT INTO {$db->pre}finance_record (username,bank,amount,balance,addtime,reason,note,editor) VALUES ('$username','$bank','$amount','$balance','$DT_TIME','$reason','$note','$editor')");
	}
}

function credit_add($username, $amount) {
	global $db;
	if($username && $amount) $db->query("UPDATE {$db->pre}member SET credit=credit+{$amount} WHERE username='$username'");
}

function credit_record($username, $amount, $editor, $reason, $note = '') {
	global $db, $DT_TIME, $DT;
	if($DT['log_credit'] && $username && $amount) {
		$r = $db->get_one("SELECT credit FROM {$db->pre}member WHERE username='$username'");
		$balance = $r['credit'];
		$reason = addslashes(stripslashes(strip_tags($reason)));
		$note = addslashes(stripslashes(strip_tags($note)));
		$db->query("INSERT INTO {$db->pre}finance_credit (username,amount,balance,addtime,reason,note,editor) VALUES ('$username','$amount','$balance','$DT_TIME','$reason','$note','$editor')");
	}
}

function sms_add($username, $amount) {
	global $db;
	if($username && $amount) $db->query("UPDATE {$db->pre}member SET sms=sms+{$amount} WHERE username='$username'");
}

function sms_record($username, $amount, $editor, $reason, $note = '') {
	global $db, $DT_TIME;
	if($username && $amount) {
		$r = $db->get_one("SELECT sms FROM {$db->pre}member WHERE username='$username'");
		$balance = $r['sms'];
		$reason = addslashes(stripslashes(strip_tags($reason)));
		$note = addslashes(stripslashes(strip_tags($note)));
		$db->query("INSERT INTO {$db->pre}finance_sms (username,amount,balance,addtime,reason,note,editor) VALUES ('$username','$amount','$balance','$DT_TIME','$reason','$note','$editor')");
	}
}

function secondstodate($seconds) {
	include load('include.lang');
	$date = '';
	if($seconds > 0) {
		$t = floor($seconds/86400);
		if($t) {
			$date .= $t.$L['mod_day'];
			$seconds = $seconds%86400;
		}
		$t = floor($seconds/3600);
		if($t) {
			$date .= $t.$L['mod_hour'];
			$seconds = $seconds%3600;
		}
		$t = floor($seconds/60);
		if($t) {
			$date .= $t.$L['mod_minute'];
			$seconds = $seconds%60;
		}
		if($seconds) {
			$date .= $seconds.$L['mod_second'];
		}
	}
	return $date;
}

function get_intro($content, $length = 0) {
	if($length) {
		$intro = trim(strip_tags($content));
		$intro = preg_replace("/&([a-z]{1,});/", '', $intro);
		$intro = str_replace(array("\r", "\n", "\t", '  '), array('', '', '', ''), $intro);
		return dsubstr($intro, $length);
	} else {
		return '';
	}
}

function get_description($content, $length) {
	if($length) {
		$content = str_replace(array(' ', '[pagebreak]'), array('', ''), $content);
		return nl2br(dsubstr(trim(strip_tags($content)), $length, '...'));
	} else {
		return '';
	}
}

function get_module_setting($moduleid, $key = '') {
	$M = cache_read('module-'.$moduleid.'.php');
	return $key ? $M[$key] : $M;
}

function anti_spam($string) {
	global $DT;
	if($DT['anti_spam'] && preg_match("/^[a-z0-9_@\-\s\/\.\,\(\)\+]+$/i", $string)) {
		return '<img src="'.DT_PATH.'api/image.png.php?auth='.encrypt($string, DT_KEY.'SPAM').'" align="absmddle"/>';
	} else {
		return $string;
	}
}

function hide_ip($ip, $sep = '*') {
	if(!preg_match("/[\d\.]{7,15}/", $ip)) return $ip;
	$tmp = explode('.', $ip);
	return $tmp[0].'.'.$tmp[1].'.'.$sep.'.'.$sep;
}

function hide_name($name, $sep = '*') {
	$len = strlen($name);
	$str = '';
	for($i = 0; $i < $len; $i++) {
		$str .= ($i == 0 || $i == $len - 1) ? $name{$i} : $sep;
	}
	return $str;
}

function check_pay($moduleid, $itemid) {
	global $db, $_username, $DT_TIME, $MOD;
	$condition = "moduleid=$moduleid AND itemid=$itemid AND username='$_username'";
	if($MOD['fee_period']) $condition .= " AND paytime>".($DT_TIME - $MOD['fee_period']*60);
	return $db->get_one("SELECT itemid FROM {$db->pre}finance_pay WHERE $condition");
}

function check_sign($string, $sign) {
	return $sign == crypt_sign($string);
}

function crypt_sign($string) {
	global $DT_IP;
	return strtoupper(md5(md5($DT_IP.$string.DT_KEY)));
}

function cache_hits($moduleid, $itemid) {
	if(@$fp = fopen(DT_CACHE.'/hits-'.$moduleid.'.php', 'a')) {
		flock($fp, LOCK_EX);
		fwrite($fp, $itemid.' ');
		flock($fp, LOCK_UN);
		fclose($fp);
	}
}

function update_hits($moduleid, $table) {
	global $db, $DT_TIME;
	$hits = trim(file_get(DT_CACHE.'/hits-'.$moduleid.'.php'));
	file_put(DT_CACHE.'/hits-'.$moduleid.'.php', ' ');
	file_put(DT_CACHE.'/hits-'.$moduleid.'.dat', $DT_TIME);
	if($hits) {
		$tmp = array_count_values(explode(' ', $hits));
		$arr = array();
		foreach($tmp as $k=>$v) {
			$arr[$v] .= $k ? ','.$k : '';
		}
		$id = $moduleid == 4 ? 'userid' : 'itemid';
		foreach($arr as $k=>$v) {
			$db->query("UPDATE LOW_PRIORITY {$table} SET `hits`=`hits`+".$k." WHERE `$id` IN (0".$v.")", 'UNBUFFERED');
		}
	}
}

function keylink($content, $item) {
	global $KEYLINK;
	$KEYLINK or $KEYLINK = cache_read('keylink-'.$item.'.php');
	if(!$KEYLINK) return $content;
	$data = $content;
	foreach($KEYLINK as $k=>$v) {
		$quote = str_replace(array("'", '-'), array("\'", '\-'), preg_quote($v['title']));
		$data = preg_replace('\'(?!((<.*?)|(<a.*?)|(<strong.*?)))('.$quote.')(?!(([^<>]*?)>)|([^>]*?</a>)|([^>]*?</strong>))\'si', '<a href="'.$v['url'].'" target="_blank"><strong class="keylink">'.$v['title'].'</strong></a>', $data, 1);
		if($data == '') $data = $content;
	}
	return $data;
}

function gender($gender, $type = 0) {
	global $L;
	if($type) return $gender == 1 ? $L['man'] : $L['woman'];
	return $gender == 1 ? $L['sir'] : $L['lady'];
}

function online($user, $type = 0) {
	global $db;
	$r = $db->get_one("SELECT online FROM {$db->pre}online WHERE `".($type ? 'username' : 'userid')."`='$user'");
	if($r) return $r['online'] ? 1 : -1;
	return 0;
}

function fix_link($url) {
	if(strlen($url) < 10) return '';
	return strpos($url, '://') === false  ? 'http://'.$url : $url;
}

function vip_year($fromtime) {
	global $DT_TIME;
	return $fromtime ? intval(($DT_TIME - $fromtime)/86400/365) + 1 : 1;
}

function get_albums($item, $type = 0) {
	$imgs = array();
	if($item['thumb'] && !preg_match("/^[a-z0-9\-\.\:\/]{30,}$/i", $item['thumb'])) $item['thumb'] = '';
	if($item['thumb1'] && !preg_match("/^[a-z0-9\-\.\:\/]{30,}$/i", $item['thumb1'])) $item['thumb1'] = '';
	if($item['thumb2'] && !preg_match("/^[a-z0-9\-\.\:\/]{30,}$/i", $item['thumb2'])) $item['thumb2'] = '';
	if($type == 0) {
		$nopic = DT_SKIN.'image/nopic60.gif';
		$imgs[] = $item['thumb'] ? $item['thumb'] : $nopic;
		$imgs[] = $item['thumb1'] ? $item['thumb1'] : $nopic;
		$imgs[] = $item['thumb2'] ? $item['thumb2'] : $nopic;
	} else if($type == 1) {
		$nopic = DT_SKIN.'image/nopic240.gif';
		$imgs[] = $item['thumb'] ? str_replace('.thumb.', '.middle.', $item['thumb']) : $nopic;
		$imgs[] = $item['thumb1'] ? str_replace('.thumb.', '.middle.', $item['thumb1']) : $nopic;
		$imgs[] = $item['thumb2'] ? str_replace('.thumb.', '.middle.', $item['thumb2']) : $nopic;
	}
	return $imgs;
}

function xml_linkurl($linkurl, $modurl = '') {
	if(strpos($linkurl, '://') === false) $linkurl = $modurl.$linkurl;
	return str_replace('&', '&amp;', $linkurl);
}

function img_lazy($content) {
	return preg_replace("/src=([\"|']?)([^ \"'>]+\.(jpg|jpeg|gif|png|bmp))\\1/i", "src=\"".DT_SKIN."image/lazy.gif\" class=\"lazy\" original=\"\\2\"", $content);
}

function sort_type($TYPE) {
	$p = $c = array();
	foreach($TYPE as $v) {
		if($v['parentid']) {
			$c[$v['parentid']][] = $v;
		} else {
			$p[] = $v;
		}
	}
	return array($p, $c);
}

function update_user($member, $item, $fileds = array('groupid','vip','validated','company','areaid','truename','telephone','mobile','address','qq','msn','ali','skype')) {
	$update = '';
	foreach($fileds as $v) {
		if(isset($item[$v]) && $item[$v] != $member[$v]) $update .= ",$v='".addslashes($member[$v])."'";
	}
	if(isset($item['email']) && $item['email'] != $member['mail']) $update .= ",email='".addslashes($member['mail'])."'";
	return $update;
}

function highlight($str) {
	return '<span class="highlight">'.$str.'</span>';
}
?>