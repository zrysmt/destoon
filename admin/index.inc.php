<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2016 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
array('系统首页', '?action=main'),
array('修改密码', '?action=password'),
array('信息统计', '?file=count'),
array('商务中心', $MODULE[2]['linkurl'], 'target="_blank"'),
array('网站首页', DT_PATH, 'target="_blank"'),
array('安全退出', '?file=logout','target="_top" onclick="return confirm(\'确定要退出管理吗?\');"'),
);
if($_admin > 1) unset($menus[1]);
switch($action) {
	case 'cache':
		isset($step) or $step = 0;
		if($step == 1) {
			cache_clear('module');
			cache_module();
			msg('系统设置更新成功', '?action='.$action.'&step='.($step+1));
		} else if($step == 2) {
			cache_clear_tag(1);
			msg('标签调用缓存更新成功', '?action='.$action.'&step='.($step+1));
		} else if($step == 3) {
			cache_clear('php', 'dir', 'tpl');
			msg('模板缓存更新成功', '?action='.$action.'&step='.($step+1));
		} else if($step == 4) {
			cache_clear('cat');
			cache_category();
			msg('分类缓存更新成功', '?action='.$action.'&step='.($step+1));
		} else if($step == 5) {
			cache_clear('area');
			cache_area();
			msg('地区缓存更新成功', '?action='.$action.'&step='.($step+1));
		} else if($step == 6) {
			cache_clear('fields');
			cache_fields();
			cache_clear('option');
			msg('自定义字段更新成功', '?action='.$action.'&step='.($step+1));
		} else if($step == 7) {
			cache_clear_ad();
			tohtml('index');
			msg('全部缓存更新成功');
		} else {
			cache_clear('group');
			cache_group();
			cache_clear('type');
			cache_type();
			cache_clear('keylink');
			cache_keylink();
			cache_pay();
			cache_weixin();
			cache_banip();
			cache_banword();
			cache_bancomment();
			msg('正在开始更新缓存', '?action='.$action.'&step='.($step+1));
		}
	break;
	case 'cacheclear':
		if($CFG['cache'] == 'file') dheader('?action=update');
		$dc->clear();
		msg('缓存更新成功');
	break;
	case 'update':
		$job = 'php';
		if(isset($dir)) {
			isset($cf) or $cf = 0;
			isset($cd) or $cd = 0;
			if(preg_match("/^".$job."[0-9]{14}$/", $dir)) {
				$dirs = glob(DT_CACHE.'/'.$dir.'/*');
				if($dirs) {
					$sub = $dirs[array_rand($dirs)];
					file_del($sub.'/index.html');
					$files = glob($sub.'/*.php');
					if($files) {
						$i = 0;
						foreach($files as $f) {
							file_del($f);
							$cf++;
							$i++;
							if($i > 500) msg('已删除 '.$cd.' 个目录，'.$cf.' 个文件'.progress(0, $cd, $tt), '?action='.$action.'&dir='.$dir.'&cd='.$cd.'&cf='.$cf.'&job='.$job.'&tt='.$tt, 0);
						}
						dir_delete($sub);
						$cd++;
						msg('已删除 '.$cd.' 个目录，'.$cf.' 个文件'.progress(0, $cd, $tt), '?action='.$action.'&dir='.$dir.'&cd='.$cd.'&cf='.$cf.'&job='.$job.'&tt='.$tt, 0);
					} else {
						dir_delete($sub);
						$cd++;
						msg('已删除 '.$cd.' 个目录，'.$cf.' 个文件'.progress(0, $cd, $tt), '?action='.$action.'&dir='.$dir.'&cd='.$cd.'&cf='.$cf.'&job='.$job.'&tt='.$tt, 0);
					}
				} else {
					dir_delete(DT_CACHE.'/'.$dir);
					msg('缓存更新成功');
				}
			} else {
				msg('目录名错误');
			}
		} else {
			$dir = $job.timetodate($DT_TIME, 'YmdHis');
			if(rename(DT_CACHE.'/'.$job, DT_CACHE.'/'.$dir)) {
				dir_create(DT_CACHE.'/'.$job);
				file_del(DT_CACHE.'/'.$dir.'/index.html');
				$dirs = glob(DT_CACHE.'/'.$dir.'/*');
				$tt = count($dirs);
				msg('正在更新，此操作可能用时较长，请不要中断..', '?action='.$action.'&dir='.$dir.'&job='.$job.'&tt='.$tt);
			} else {
				msg('更新失败');
			}
		}
	break;
	case 'html':
		cache_clear_tag(1);
		$db->expires = $CFG['db_expires'] = $CFG['tag_expires'] = 0;
		tohtml('index');
		$filename = $CFG['com_dir'] ? DT_ROOT.'/'.$DT['index'].'.'.$DT['file_ext'] : DT_CACHE.'/index.inc.html';
		msg('首页更新成功 '.(is_file($filename) ? dround(filesize($filename)/1024).'Kb ' : '').'&nbsp;&nbsp;<a href="'.DT_PATH.'" target="_blank">点击查看</a>');
	break;
	case 'password':
		if($submit) {
			if(!$oldpassword) msg('请输入现有密码');
			if(!$password) msg('请输入新密码');
			if(strlen($password) < 6) msg('新密码最少6位，请修改');
			if($password != $cpassword) msg('两次输入的密码不一致，请检查');
			$r = $db->get_one("SELECT password,passsalt FROM {$DT_PRE}member WHERE userid='$_userid'");
			if($r['password'] != dpassword($oldpassword, $r['passsalt']))  msg('现有密码错误，请检查');
			if($password == $oldpassword) msg('新密码不能与现有密码相同');
			$passsalt = random(8);
			$password = dpassword($password, $passsalt);
			$db->query("UPDATE {$DT_PRE}member SET password='$password',passsalt='$passsalt' WHERE userid='$_userid'");
			userclean($_username);
			msg('管理员密码修改成功', '?action=main');
		} else {
			include tpl('password');
		}
	break;
	case 'side':
		include tpl('side');
	break;
	case 'cron':
		include DT_ROOT.'/api/cron.inc.php';
	break;
	case 'main':
		if($submit) {
			$note = '<?php exit;?>'.dhtmlspecialchars(stripslashes($note));
			file_put(DT_ROOT.'/file/user/'.dalloc($_userid).'/'.$_userid.'/note.php', $note);
			dmsg('更新成功', '?action=main');
		} else {
			$user = $db->get_one("SELECT loginip,logintime,logintimes FROM {$DT_PRE}member WHERE userid=$_userid");
			$note = DT_ROOT.'/file/user/'.dalloc($_userid).'/'.$_userid.'/note.php';
			$note = file_get($note);
			if($note) {
				$note = substr($note, 13);
			} else {
				$note = '';
			}
			$install = file_get(DT_CACHE.'/install.lock');
			if(!$install) {
				$install = $DT_TIME;
				file_put(DT_CACHE.'/install.lock', $DT_TIME);
			}
			$r = $db->get_one("SELECT item_value FROM {$DT_PRE}setting WHERE item='destoon' AND item_key='backtime'");
			$backtime = $r['item_value'];
			$backdays = intval(($DT_TIME - $backtime)/86400);
			$backtime = timetodate($backtime, 6);
			$notice_url = 'https://www.destoon.com/client.php?action=notice&product=b2b&version='.DT_VERSION.'&release='.DT_RELEASE.'&lang='.DT_LANG.'&charset='.DT_CHARSET.'&domain='.DT_DOMAIN.'&install='.$install.'&os='.PHP_OS.'&soft='.urlencode($_SERVER['SERVER_SOFTWARE']).'&php='.urlencode(phpversion()).'&mysql='.urlencode($db->version()).'&url='.urlencode($DT_URL).'&site='.urlencode($DT['sitename']).'&auth='.strtoupper(md5($DT_URL.$install.$_SERVER['SERVER_SOFTWARE']));
			$install = timetodate($install, 5);			
			$edition = edition(1);
			include tpl('main');
		}
	break;
	case 'left':
		$mymenu = cache_read('menu-'.$_userid.'.php');
		include tpl('left');
	break;
	default:
		include tpl('index');
	break;
}
?>