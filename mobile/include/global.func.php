<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2016 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
function mobile_msg($msg, $forward = '') {
	if(!$msg && $forward) dheader($forward);
	extract($GLOBALS, EXTR_SKIP);
	include template('msg', 'mobile');
	if(DT_CHARSET != 'UTF-8') toutf8();
	exit();
}

function mobile_login() {
	global $_userid, $DT_URL;
	$_userid or dheader('login.php?forward='.rawurlencode($DT_URL));
}

function mobile_pages($total, $page = 1, $perpage = 20, $demo = '') {
	global $DT_URL, $DT, $CFG, $L;
	if($total <= $perpage) return '';
	$total = ceil($total/$perpage);
	$page = intval($page);
	if($page < 1 || $page > $total) $page = 1;
	if($demo) {
		$demo_url = $demo;
	} else {
		if(substr($DT_URL, -5) == '.html') {
			$demo_url = preg_replace("/[0-9]{1,}\.html/", "{destoon_page}.html", $DT_URL);
		} else {
			$demo_url = preg_replace("/(.*)([&?]page=[0-9]*)(.*)/i", "\\1\\3", $DT_URL);
			$s = strpos($demo_url, '?') === false ? '?' : '&';
			$demo_url = $demo_url.$s.'page={destoon_page}';
			$demo_url = urldecode($demo_url);
		}
	}
	$pages = '<a href="javascript:GoPage('.$total.', \''.$demo_url.'\');"><b>'.$page.'</b>/'.$total.'</a> ';
	$_page = $page >= $total ? 1 : $page + 1;
	$url = str_replace('{destoon_page}', $_page, $demo_url);
	$pages .= '<a href="'.$url.'" data-transition="none" id="page-next">'.$L['next_page'].'</a> ';

	$_page = $page <= 1 ? $total : ($page - 1);
	$url = str_replace('{destoon_page}', $_page, $demo_url);
	$pages .= '<a href="'.$url.'" data-transition="none" id="page-prev">'.$L['prev_page'].'</a> ';

	$_page = 1;
	$url = str_replace('{destoon_page}', $_page, $demo_url);
	$pages .= '<a href="'.$url.'" data-transition="none" id="page-home">'.$L['home_page'].'</a> ';

	$_page = $total;
	$url = str_replace('{destoon_page}', $_page, $demo_url);
	$pages .= '<a href="'.$url.'" id="page-last">'.$L['last_page'].'</a> ';
	return $pages;
}

function input_trim($wd) {
	return trim(urldecode(str_replace('%E2%80%86', '', urlencode($wd))));
}

function toutf8() {
	$data = ob_get_contents();
	ob_end_clean();
	echo convert($data, DT_CHARSET, 'UTF-8');
}

function video5($content) {
	if(strpos($content, '</embed>') !== false) {
		if(!preg_match_all("/<embed[^>]*>(.*?)<\/embed>/i", $content, $matches)) return $content;
		foreach($matches[0] as $m) {
			$content = str_replace($m, video5_player(video5_url($m)), $content);
		}
		return $content;
	} else {
		return $content;
	}
}

function video5_url($content) {
	$url = '';
	if(strpos($content, 'vcastr3.swf') !== false) {
		$t1 = explode('source&gt;', $content);
		$url = str_replace('&lt;/', '', $t1[1]);
	} else if(strpos($content, 'src="') !== false) {
		$t1 = explode('src="', $content);
		$t2 = explode('"', $t1[1]);
		$url = $t2[0];
	}
	return $url;
}

function video5_frame($url, $w, $h) {
	return '<iframe src="'.$url.'" width="'.$w.'" height="'.$h.'" frameborder="0" scrolling="no" allowfullscreen="true" allowtransparency="true"></iframe>';
}

function video5_player($url, $w = 280, $h = 210, $a = 0) {
	$ext = file_ext($url);
	$u5 = '';
	if($ext == 'mp4') {
		$u5 = $url;
	} else if(strpos($url, '.youku.com') !== false) {
		if(strpos($url, '/sid/') !== false && strpos($url, '/v.sw') !== false) {
			$t1 = explode('/sid/', $url);
			$t2 = explode('/v.sw', $t1[1]);
			$t3 = $t2[0];
			if($t3) return video5_frame('http://player.youku.com/embed/'.$t3, $w, $h);
		}
	} else if(strpos($url, '.tudou.com') !== false) {
		if(strpos($url, '/v/') !== false) {
			$t1 = explode('/v/', $url);
			$t2 = explode('/', $t1[1]);
			$t3 = $t2[0];
			if($t3) return video5_frame('http://www.tudou.com/programs/view/html5embed.action?code='.$t3, $w, $h);
		}
	} else if(strpos($url, 'static.video.qq.com') !== false) {
		if(strpos($url, 'vid=') !== false) {
			$t1 = explode('vid=', $url);
			$t2 = explode('&', $t1[1]);
			$t3 = $t2[0];
			if($t3) return video5_frame('http://v.qq.com/iframe/player.html?vid='.$t3.'&tiny=0&auto=0', $w, $h);
		}
	} else if(strpos($url, '.56.com') !== false) {
		if(strpos($url, '/v_') !== false && strpos($url, '.sw') !== false) {
			$t1 = explode('/v_', $url);
			$t2 = explode('.sw', $t1[1]);
			$t3 = $t2[0];
			if($t3) return video5_frame('http://www.56.com/iframe/'.$t3, $w, $h);
		}
	} else if(strpos($url, '.ku6.com') !== false) {
		if(strpos($url, 'refer/') !== false && strpos($url, 'v.sw') !== false) {
			$t1 = explode('refer/', $url);
			$t2 = explode('v.sw', $t1[1]);
			$t3 = $t2[0];
			if($t3) $u5 = 'http://v.ku6.com/fetchwebm/'.$t3.'.m3u8';
		}
	} else if(strpos($url, '.youtube.com') !== false) {
		if(strpos($url, 'youtube.com/v/') !== false) {
			$t1 = explode('/v/', $url);
			$t3 = $t1[1];
			if($t3) return video5_frame('http://www.youtube.com/embed/'.$t3, $w, $h);
		}
	}
	if($u5) return '<video src="'.$u5.'" width="'.$w.'" height="'.$h.'"'.($a ? ' autoplay="autoplay"' : '').' controls="controls"></video><center><a href="'.$u5.'" target="_blank" rel="external">Click To Play</a></center>';
	return '';
}

function is_pc() {
	if(DT_DEBUG || is_robot()) return false;
	$UA = strtoupper($_SERVER['HTTP_USER_AGENT']);
	if(strpos($UA, 'WINDOWS NT') !== false) {
		global $DT_URL;
		if(strpos($DT_URL, 'plg_') !== false) return false;//QQ
		return true;
	}
	return false;
}

function share_icon($thumb, $content) {
	if(strpos($thumb, '.thumb.') !== false) return substr($thumb, 0, strpos($thumb, '.thumb.'));
	if($thumb) return $thumb;
	if(strpos($content, '<img') !== false) return 'auto';
	return DT_PATH.'apple-touch-icon-precomposed.png';
}
?>