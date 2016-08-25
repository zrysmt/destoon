<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2015 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
$moduleid = 2;
require 'common.inc.php';
$DT['im_web'] or dheader('index.php?reload='.$DT_TIME);
$_userid or dheader('login.php?forward='.urlencode('chat.php'));
require DT_ROOT.'/include/module.func.php';
require DT_ROOT.'/include/post.func.php';
$chatid = (isset($chatid) && is_md5($chatid)) ? $chatid : '';
$table = $DT_PRE.'chat';
$chat_poll = intval($MOD['chat_poll']);
function get_chat_id($f, $t) {
	return md5(strcmp($f, $t) > 0 ? $f.'|'.$t : $t.'|'.$f);
}
function get_chat_file($chatid) {
	return DT_ROOT.'/file/chat/'.substr($chatid, 0, 2).'/'.$chatid.'.php';
}
switch($action) {
	case 'send':		
		$chatid or exit('ko');
		trim($word) or exit('ko');
		if($MOD['chat_maxlen'] && strlen($word) > $MOD['chat_maxlen']*3) exit('max');
		$word = convert($word, 'UTF-8', DT_CHARSET);
		$word = stripslashes(trim($word));
		$word = strip_tags($word);
		$word = dsafe($word);
		$word = nl2br($word);
		$word = strip_nr($word);
		$word = str_replace('|', ' ', $word);
		if($MOD['chat_file'] && $MG['upload']) clear_upload($word);
		$chat = $db->get_one("SELECT * FROM {$table} WHERE chatid='$chatid'");
		if($chat) {
			$lastmsg = addslashes(dsubstr($word, 50));
			if($chat['touser'] == $_username) {
				$sql = "fgettime=$DT_TIME,lasttime=$DT_TIME,lastmsg='$lastmsg'";
				if($DT_TIME - $chat['freadtime'] > $chat_poll) {
					$db->query("UPDATE {$DT_PRE}member SET chat=chat+1 WHERE username='$chat[fromuser]'");
					$sql .= ",fnew=fnew+1";
				}
				$db->query("UPDATE {$table} SET {$sql} WHERE chatid='$chatid'");
			} else if($chat['fromuser'] == $_username) {
				$sql = "tgettime=$DT_TIME,lasttime=$DT_TIME,lastmsg='$lastmsg'";
				if($DT_TIME - $chat['treadtime'] > $chat_poll) {
					$db->query("UPDATE {$DT_PRE}member SET chat=chat+1 WHERE username='$chat[touser]'");
					$sql .= ",tnew=tnew+1";
				}
				$db->query("UPDATE {$table} SET {$sql} WHERE chatid='$chatid'");
			} else {
				exit('ko');
			}
		} else {
			exit('ko');
		}
		$filename = get_chat_file($chatid);
		if(is_file($filename)) {
			if(filesize($filename) > 500*1024) {
				file_copy($filename, substr($filename, 0, -4).'-'.timetodate($DT_TIME, 'YmdHis').'.php');
				file_put($filename, '<?php exit;?>');
			}
		} else {
			file_put($filename, '<?php exit;?>');
		}
		$font_s = $font_s ? intval($font_s) : 0;
		$font_c = $font_c ? intval($font_c) : 0;
		$font_b = $font_b ? 1 : 0;
		$font_i = $font_i ? 1 : 0;
		$font_u = $font_u ? 1 : 0;
		$css = '';
		if($font_s) $css .= ' s'.$font_s;
		if($font_c) $css .= ' c'.$font_c;
		if($font_b) $css .= ' fb';
		if($font_i) $css .= ' fi';
		if($font_u) $css .= ' fu';
		if($css) $word = '<span class="'.trim($css).'">'.$word.'</span>';
		if($word && $fp = fopen($filename, 'a')) {
			fwrite($fp, $DT_TIME.'|'.$_username.'|'.$word."\n");
			fclose($fp);
			exit('ok');
		}
		exit('ko');
	break;
	case 'load':
		$chatid or exit;
		$filename = get_chat_file($chatid);
		$chat = $db->get_one("SELECT * FROM {$table} WHERE chatid='$chatid'");
		if($chat) {
			if($chat['touser'] == $_username) {
				$db->query("UPDATE {$table} SET treadtime=$DT_TIME,tnew=0 WHERE chatid='$chatid'");
			} else if($chat['fromuser'] == $_username) {
				$db->query("UPDATE {$table} SET freadtime=$DT_TIME,fnew=0 WHERE chatid='$chatid'");
			} else {
				exit('0');
			}
		} else {
			exit('0');
		}
		$chatlast = $_chatlast = intval($chatlast);
		$first = isset($first) ? intval($first) : 0;
		$i = $j = 0;
		$chat_lastuser = '';
		$chat_repeat = 0;
		$json = '';
		if($chatlast < @filemtime($filename)) {
			$data = file_get($filename);
			if($data) {
				$data = trim(substr($data, 13));
				if($data) {
					$time1 = 0;
					$data = explode("\n", $data);
					foreach($data as $d) {
						list($time, $name, $word) = explode("|", $d);
						if($_username == $name) { $chat_repeat++; } else {$chat_repeat = 0;}
						$chat_lastuser = $name;
						if($time > $chatlast && $word) {
							$chatlast = $time;
							$time2 = $time;
							if($time2 - $time1 < 600) {
								$date = '';
							} else {
								$date = timetodate($time2, 5);
								$time1 = $time2;
							}
							if($MOD['chat_url'] || $MOD['chat_img']) {
								if(preg_match_all("/([http|https]+)\:\/\/([a-z0-9\/\-\_\.\,\?\&\#\=\%\+\;]{4,})/i", $word, $m)) {
									foreach($m[0] as $u) {
										if($MOD['chat_img'] && preg_match("/^(jpg|jpeg|gif|png|bmp)$/i", file_ext($u)) && !preg_match("/([\?\&\=]{1,})/i", $u)) {
											$word = str_replace($u, '<img src="'.$u.'" onload="if(this.width>200)this.width=200;" onclick="window.open(this.src);"/>', $word);
										} else if($MOD['chat_url']) {
											$word = str_replace($u, '<a href="'.$u.'" target="_blank" rel="external" class="b">'.$u.'</a>', $word);
										}
									}
								}
							}
							if(preg_match_all("/\:([0-9]{3,})\)/i", $word, $m)) {
								foreach($m[0] as $u) {
									$f = 'face/'.substr($u, 1, -1).'.gif';
									if(is_file(DT_ROOT.'/'.$MOD['moduledir'].'/'.$f)) $word = str_replace($u, '<img src="'.$MOD['linkurl'].$f.'"/>', $word);
								}
							}
							$word = str_replace('"', '\"', $word);
							$self = $_username == $name ? 1 : 0;
							if($self) {
								//$name = 'Me';
							} else {
								$j++;
							}
							$json .= ($i ? ',' : '').'{time:"'.$time.'",date:"'.$date.'",name:"'.$name.'",word:"'.$word.'",self:"'.$self.'"}';
							$i = 1;
						}
					}
					if($_chatlast == 0) $j = 0;
				}
			}
		}
		$json = '{chat_msg:['.$json.'],chat_new:"'.$j.'",chat_last:"'.$chatlast.'"}';
		exit(convert($json, DT_CHARSET, 'UTF-8'));
	break;
	case 'list':
		$data = '';
		$new = 0;
		$result = $db->query("SELECT * FROM {$table} WHERE fromuser='$_username' OR touser='$_username' ORDER BY lasttime DESC LIMIT 100");
		while($r = $db->fetch_array($result)) {
			if($r['fromuser'] == $_username) {
				$r['user'] = $r['touser'];
				$r['new'] = $r['fnew'];
			} else {					
				$r['user'] = $r['fromuser'];
				$r['new'] = $r['tnew'];
			}
			$new += $r['new'];
			$r['last'] = timetodate($r['lasttime'], $r['lasttime'] > $today_endtime - 86400 ? 'H:i:s' : 'y-m-d');
			$r['online'] = online($r['user'], 1);
			$data .= '<div class="list-img list-chat">';
			$data .= '<a href="chat.php?chatid='.$r['chatid'].'&reload='.$DT_TIME.'"><img src="'.useravatar($r['user']).'" width="40" height="40"/>';
			$data .= '<ul>';
			$data .= '<li><span>'.$r['last'].'</span><strong>'.$r['user'].'</strong></li>';
			$data .= '<li>'.($r['new'] ? '<em>'.$r['new'].'</em>' : '').($r['online'] ? $L['chat_online'] : $L['chat_offline']).' '.$r['lastmsg'].'</li>';
			$data .= '</ul>';
			$data .= '</a></div>';
		}
		if($new != $_chat) {
			$db->query("UPDATE {$DT_PRE}member SET chat=$new WHERE userid=$_userid");
			$_chat = $new;
		}
		if(!$data) $data = '<div class="main"><div style="padding:40px 0;text-align:center;">'.$L['chat_empty'].'</div></div>';
		exit(convert($data, DT_CHARSET, 'UTF-8'));
	break;
	default:
		if(isset($touser)) {
			check_name($touser) or mobile_msg($L['chat_msg_user'], '?action=index');
			if($touser == $_username) mobile_msg($L['chat_msg_self'], '?action=index');
			$MG['chat'] or mobile_msg($L['chat_msg_no_rights']);
			$user = userinfo($touser);
			$user or mobile_msg($L['chat_msg_user'], '?action=index');
			if($user['black']) {
				$black = explode(' ', $user['black']);
				if(in_array($_username, $black)) mobile_msg($L['chat_msg_refuse'], '?action=index');
			}
			$online = online($user['userid']);
			$chatid = get_chat_id($_username, $touser);
			$chat_id = $chatid;
			$head_name = lang($L['chat_with'], array($user['username']));
			$head_title = $head_name.$DT['seo_delimiter'].$head_title;
			$forward = is_url($forward) ? addslashes(dhtmlspecialchars($forward)) : '';
			if(strpos($forward, $MOD['linkurl']) !== false) $forward = '';
			$chat = $db->get_one("SELECT * FROM {$table} WHERE chatid='$chatid'");
			if($chat) {
				$db->query("UPDATE {$table} SET forward='$forward' WHERE chatid='$chatid'");
			} else {
				$db->query("INSERT INTO {$table} (chatid,fromuser,touser,tgettime,forward) VALUES ('$chat_id','$_username','$touser','0','$forward')");
			}
			$type = 1;
		} else if(isset($chatid) && is_md5($chatid)) {
			$chat = $db->get_one("SELECT * FROM {$table} WHERE chatid='$chatid'");
			if($chat && ($chat['touser'] == $_username || $chat['fromuser'] == $_username)) {
				if($chat['touser'] == $_username) {
					$user = userinfo($chat['fromuser']);
				} else if($chat['fromuser'] == $_username) {
					$user = userinfo($chat['touser']);
				}
				$online = online($user['userid']);
				$chat_id = $chatid;
				$head_name = lang($L['chat_with'], array($user['username']));
				$head_title = $head_name.$DT['seo_delimiter'].$head_title;
			} else {
				dheader('?action=index');
			}
			$type = 2;
		} else {
			$head_name = $L['chat_title'];
			$head_title = $head_name.$DT['seo_delimiter'].$head_title;
			$type = 3;
		}
	break;
}
$foot = '';
include template('chat', 'mobile');
if(DT_CHARSET != 'UTF-8') toutf8();
?>