<?php
defined('IN_DESTOON') or exit('Access Denied');
function im_web($id, $style = 0) {
	global $MODULE;
	return $id ? '<a href="'.$MODULE[2]['linkurl'].'chat.php?touser='.$id.'" target="_blank" rel="nofollow"><img src="'.DT_PATH.'api/online.png.php?username='.$id.'&style='.$style.'" title="点击交谈/留言" alt="" align="absmiddle" onerror="this.src=DTPath+\'file/image/web-off.gif\';"/></a>' : '';
}

function im_qq($id, $style = 0) {
	return $id ? '<a href="http://wpa.qq.com/msgrd?v=3&uin='.$id.'&site=qq&menu=yes" target="_blank" rel="nofollow"><img src="http://wpa.qq.com/pa?p=1:'.$id.':4" title="点击QQ交谈/留言" alt="" align="absmiddle" onerror="this.src=DTPath+\'file/image/qq-off.gif\';" onload="if(this.width==77){this.src=DTPath+\'file/image/qq-off.gif\';}else if(this.width==23){this.src=DTPath+\'file/image/qq.gif\';}"/></a>' : '';
}

function im_ali($id, $style = 0) {
	if($id) {
		$tb = 0;
		if(substr($id, 0, 3) == 'TB:') {
			$tb = 1;
			$id = substr($id, 3);
		}
		if(!check_name($id) && DT_CHARSET != 'UTF-8') $id = convert($id, 'GBK', 'UTF-8');
		$id = urlencode($id);
		return ($tb ? '<a href="http://www.taobao.com/webww/ww.php?ver=3&touid='.$id.'&siteid=cntaobao&status=2&charset=UTF-8" target="_blank" rel="nofollow"><img src="http://amos.alicdn.com/realonline.aw?v=2&uid='.$id.'&site=cntaobao&s=2&charset=UTF-8"' : '<a href="http://amos.alicdn.com/msg.aw?v=2&uid='.$id.'&site=cnalichn&s=6&charset=UTF-8" target="_blank" rel="nofollow"><img src="http://amos.alicdn.com/online.aw?v=2&uid='.$id.'&site=cnalichn&s=6&charset=UTF-8"').' title="点击旺旺交谈/留言" alt="" align="absmiddle" onerror="this.src=DTPath+\'file/image/ali-off.gif\';" onload="if(this.width>20)this.src=SKPath+\'image/ali-off.gif\';"/></a>';
	}
	return '';
}

function im_msn($id, $style = 0) {
	return $id ? '<a href="msnim:chat?contact='.$id.'" rel="nofollow"><img src="'.DT_PATH.'file/image/msn.gif" width="16" height="16" title="点击MSN交谈/留言" alt="" align="absmiddle"/></a>' : '';
}

function im_skype($id, $style = 0) {
	return $id ? '<a href="skype:'.$id.'" rel="nofollow"><img src="http://mystatus.skype.com/smallicon/'.$id.'" title="点击Skype通话" alt="" align="absmiddle" onerror="this.src=DTPath+\'file/image/skype-off.gif\';"/></a>' : '';
}
?>