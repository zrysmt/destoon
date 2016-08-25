<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2015 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
switch($action) {
	case 'cache':
		cache_clear_tag(1);
		//cache_clear_sql(0);
		cache_clear('php', 'dir', 'tpl');
		cache_clear('cat');
		cache_category();
		cache_clear('area');
		cache_area();
		msg('缓存更新成功', '?file='.$file.'&action=module');
	break;
	case 'all':
		msg('全站更新成功');
	break;
	case 'index':
		tohtml('index');
		msg('网站首页生成成功', '?file='.$file.'&action=all');
	break;
	case 'back':
		$moduleids = 0;
		unset($MODULE[1]);
		unset($MODULE[2]);
		$KEYS = array_keys($MODULE);
		foreach($KEYS as $k => $v) {
			if($v == $mid) { $moduleids = $k; break; }
		}
		msg('['.$MODULE[$mid]['name'].'] 更新成功', '?file='.$file.'&action=module&moduleids='.($moduleids+1));
	break;
	case 'module':
		if(isset($moduleids)) {
			unset($MODULE[1]);
			unset($MODULE[2]);
			$KEYS = array_keys($MODULE);
			if(isset($KEYS[$moduleids])) {
				$bmoduleid = $moduleid = $KEYS[$moduleids];
				if(is_file(DT_ROOT.'/module/'.$MODULE[$moduleid]['module'].'/admin/html.inc.php')) {	
					msg('', '?moduleid='.$moduleid.'&file='.$file.'&action=all&one=1');
				} else {
					msg('['.$MODULE[$bmoduleid]['name'].'] 更新成功', '?file='.$file.'&action='.$action.'&moduleids='.($moduleids+1));
				}
			} else {
				msg('模块更新成功', '?file='.$file.'&action=index');
			}		
		} else {
			$moduleids = 0;
			msg('开始更新模块', '?file='.$file.'&action='.$action.'&moduleids='.$moduleids);
		}
	break;
	default:
		msg('正在开始更新全站', '?file='.$file.'&action=cache');
	break;
}
?>