<?php
defined('DT_ADMIN') or exit('Access Denied');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=<?php echo DT_CHARSET;?>"/>
<title>管理中心 - <?php echo $DT['sitename']; ?> - Powered By DESTOON B2B V<?php echo DT_VERSION; ?> R<?php echo DT_RELEASE;?></title>
<meta name="robots" content="noindex,nofollow"/>
<meta name="generator" content="DESTOON B2B - www.destoon.com"/>
<meta http-equiv="x-ua-compatible" content="IE=8"/>
<link rel="stylesheet" href="admin/image/style.css" type="text/css"/>
<?php if(!DT_DEBUG) { ?><script type="text/javascript">window.onerror= function(){return true;}</script><?php } ?>
<script type="text/javascript" src="<?php echo DT_STATIC;?>lang/<?php echo DT_LANG;?>/lang.js"></script>
<script type="text/javascript" src="<?php echo DT_STATIC;?>file/script/config.js"></script>
<script type="text/javascript" src="<?php echo DT_STATIC;?>file/script/jquery.js"></script>
<script type="text/javascript" src="<?php echo DT_STATIC;?>file/script/common.js"></script>
<script type="text/javascript" src="<?php echo DT_STATIC;?>file/script/admin.js"></script>
<base target="main"/>
<style type="text/css">html{overflow-x:hidden;overflow-y:auto;}</style>
</head>
<body>
<?php
include DT_ROOT.'/admin/menu.inc.php';
if($_admin == 2) {
?>
<table cellpadding="0" cellspacing="0" width="<?php echo $DT['admin_left'];?>" height="100%">
<tr>
<td id="bar" class="bar" valign="top" align="center"><img src="admin/image/bar2on.gif" width="25" height="85" alt="" /><img src="admin/image/barnav.gif" width="25" height="1" alt=""/></td>
<td valign="top" class="barmain">
<div class="bartop">
<table cellpadding="0" cellspacing="0" width="100%">
<tr height="20">
<td width="5"></td>
<td id="name">我的面板</td>
<td width="60" align="right">
<a href="<?php echo DT_PATH;?>" target="_blank"><img src="admin/image/home.gif" width="8" height="8" title="网站首页"/></a>&nbsp;
<a href="?action=start" target="_top"><img src="admin/image/reload.gif" width="8" height="8" title="刷新(返回后台起始页)"/></a>&nbsp;
<a href="?file=search" target="main"><img src="admin/image/search.gif" width="8" height="8" title="后台功能搜索"/></a>&nbsp;
<a href="?file=logout" target="_top" onclick="if(!confirm('确实要注销登录吗?')) return false;"><img src="admin/image/quit.gif" width="8" height="8" title="注销登录"/></a>
</td>
<td width="5"></td>
</tr>
</table>
</div>
<div id="menu">
	<dl>
	<dt onclick="s(this)" onmouseover="this.className='dt_on';" onmouseout="this.className='';">我的面板</dt>
	<dd onclick="c(this);"><a href="?action=main">系统首页</a></dd>
	<dd onclick="c(this);"><a href="?file=mymenu">定义面板</a></dd>
	<?php
		foreach($mymenu as $menu) {
	?>
	<dd onclick="c(this);"><a href="<?php echo substr($menu['url'], 0, 1) == '?' ? $menu['url'] : DT_PATH.'api/redirect.php?url='.$menu['url'].'" target="_blank';?>"><?php echo set_style($menu['title'], $menu['style']);?></a></dd>
	<?php
		}
	?>
	</dl>
</div>
</td>
</tr>
</table>
<?php } else { ?>
<table cellpadding="0" cellspacing="0" width="<?php echo $DT['admin_left'];?>" height="100%">
<tr>
<td id="bar" class="bar" valign="top" align="center"><img src="admin/image/bar1on.gif" width="25" height="85" alt="" id="b_1" onclick="show(1);"/><img src="admin/image/barnav.gif" width="25" height="1" alt="" id="n_1"/><img src="admin/image/bar2.gif" width="25" height="85" alt="" id="b_2" onclick="show(2);"/><img src="admin/image/barnav.gif" width="19" height="1" alt="" id="n_2"/><img src="admin/image/bar3.gif" width="25" height="85" alt="" id="b_3" onclick="show(3);"/><img src="admin/image/barnav.gif" width="19" height="1" alt="" id="n_3"/><img src="admin/image/bar4.gif" width="25" height="85" alt="" id="b_4" onclick="show(4);"/><img src="admin/image/barnav.gif" width="19" height="1" alt="" id="n_4"/></td>
<td valign="top" class="barmain">
<div class="bartop">
<table cellpadding="0" cellspacing="0" width="100%">
<tr height="20">
<td width="5"></td>
<td id="name">&nbsp;</td>
<td width="60" align="right">
<a href="<?php echo DT_PATH;?>" target="_blank"><img src="admin/image/home.gif" width="8" height="8" title="网站首页"/></a>&nbsp;
<a href="?action=start" target="_top"><img src="admin/image/reload.gif" width="8" height="8" title="刷新(返回后台起始页)"/></a>&nbsp;
<a href="?file=search" target="main"><img src="admin/image/search.gif" width="8" height="8" title="后台功能搜索"/></a>&nbsp;
<a href="?file=logout" target="_top" onclick="if(!confirm('确实要注销登录吗?')) return false;"><img src="admin/image/quit.gif" width="8" height="8" title="注销登录"/></a>
</td>
<td width="5"></td>
</tr>
</table>
</div>
<div id="menu">&nbsp;</div>
</td>
</tr>
</table>
<div style="display:none;">
	<div id="m_1">
	<?php if($_founder) { ?>
	<dl> 
	<dt onclick="s(this)" onmouseover="this.className='dt_on';" onmouseout="this.className='';">系统设置</dt> 
	<?php
		foreach($menu_system as $m) {
			echo '<dd onclick="c(this);"><a href="'.$m[1].'">'.$m[0].'</a></dd>';
		}
	?>
	</dl>
	<?php } ?>
	<dl> 
	<dt onclick="s(this)" onmouseover="this.className='dt_on';" onmouseout="this.className='';">更新数据</dt>
	<dd onclick="c(this);"><a href="?action=html">生成首页</a></dd>
	<?php if($_founder) { ?>
	<dd onclick="c(this);"><a href="?action=cache">更新缓存</a></dd>
	<dd onclick="c(this);"><a href="?moduleid=3&file=html">更新扩展</a></dd>
	<dd onclick="c(this);"><a href="?file=html" onclick="return confirm('确定要开始更新全站页面吗？此操作比较耗费服务器资源和时间 ');">更新全站</a></dd>
	<?php } ?>
	</dl>
	<dl> 
	<dt onclick="s(this)" onmouseover="this.className='dt_on';" onmouseout="this.className='';">系统工具</dt>
	<?php
		foreach($menu as $m) {
			echo '<dd onclick="c(this);"><a href="'.$m[1].'">'.$m[0].'</a></dd>';
		}
	?>
	</dl>
	</div>
	<div id="m_2">
	<dl>
	<dt onclick="s(this)" onmouseover="this.className='dt_on';" onmouseout="this.className='';">使用帮助</dt>
	<?php
		foreach($menu_help as $m) {
			echo '<dd onclick="c(this);" style="display:none;"><a href="'.$m[1].'">'.$m[0].'</a></dd>';
		}
	?>
	</dl>
	<dl>
	<dt onclick="s(this)" onmouseover="this.className='dt_on';" onmouseout="this.className='';">我的面板</dt>
	<dd onclick="c(this);"><a href="?action=main">系统首页</a></dd>
	<dd onclick="c(this);"><a href="?file=mymenu">定义面板</a></dd>
	<?php
		foreach($mymenu as $menu) {
	?>
	<dd onclick="c(this);"><a href="<?php echo substr($menu['url'], 0, 1) == '?' ? $menu['url'] : DT_PATH.'api/redirect.php?url='.$menu['url'].'" target="_blank';?>"><?php echo set_style($menu['title'], $menu['style']);?></a></dd>
	<?php
		}
	?>
	</dl>
	</div>
	<div id="m_3">
	<?php
		$menuinc = DT_ROOT.'/module/'.$MODULE[3]['module'].'/admin/menu.inc.php';
		if(is_file($menuinc)) {
			extract($MODULE[3]);
			include $menuinc;
			echo '<dl id="dl_'.$moduleid.'">';
			echo '<dt onclick="m('.$moduleid.');" onmouseover="this.className=\'dt_on\';" onmouseout="this.className=\'\';">扩展功能</dt>';
			foreach($menu as $m) {
				echo '<dd onclick="c(this);" style="display:none;"><a href="'.$m[1].'">'.$m[0].'</a></dd>';
			}
			echo '</dl>';
		}
	?>
	<?php
	foreach($MODULE as $v) {
		if($v['moduleid'] > 3) {
			$menuinc = DT_ROOT.'/module/'.$v['module'].'/admin/menu.inc.php';
			if(is_file($menuinc)) {
				extract($v);
				include $menuinc;
				echo '<dl id="dl_'.$moduleid.'">';
				echo '<dt onclick="m('.$moduleid.');" onmouseover="this.className=\'dt_on\';" onmouseout="this.className=\'\';">'.$name.'管理</dt>';
				foreach($menu as $m) {
					echo '<dd onclick="c(this);" style="display:none;"><a href="'.$m[1].'">'.$m[0].'</a></dd>';
				}
				echo '</dl>';
			}
		}
	}
	?>
	</div>	
	<div id="m_4">
	<?php
		$menuinc = DT_ROOT.'/module/'.$MODULE[2]['module'].'/admin/menu.inc.php';
		if(is_file($menuinc)) {
			extract($MODULE[2]);
			include $menuinc;
			echo '<dl id="dl_'.$moduleid.'">';
			echo '<dt id="dt_'.$moduleid.'" onclick="s(this);" onmouseover="this.className=\'dt_on\';" onmouseout="this.className=\'\';">'.$name.'管理</dt>';
			foreach($menu as $m) {
				echo '<dd onclick="c(this);"><a href="'.$m[1].'">'.$m[0].'</a></dd>';
			}
			echo '</dl>';
		}
	?>
	<?php
		$menuinc = DT_ROOT.'/module/'.$MODULE[4]['module'].'/admin/menu.inc.php';
		if(is_file($menuinc)) {
			extract($MODULE[4]);
			include $menuinc;
			echo '<dl id="dl_'.$moduleid.'">';
			echo '<dt id="dt_'.$moduleid.'" onclick="s(this);h(Dd(\'dt_pay\'));h(Dd(\'dt_oth\'));" onmouseover="this.className=\'dt_on\';" onmouseout="this.className=\'\';">'.$name.'管理</dt>';
			foreach($menu as $m) {
				echo '<dd onclick="c(this);" style="display:none;"><a href="'.$m[1].'">'.$m[0].'</a></dd>';
			}
			echo '</dl>';
		}
	?>
	<dl id="dl_pay"> 
	<dt id="dt_pay" onclick="s(this);h(Dd('dt_oth'));h(Dd('dt_4'));" onmouseover="this.className='dt_on';" onmouseout="this.className='';">财务管理</dt>
	<?php
		foreach($menu_finance as $m) {
			echo '<dd onclick="c(this);"><a href="'.$m[1].'">'.$m[0].'</a></dd>';
		}
	?>
	</dl>
	<dl id="dl_oth"> 
	<dt id="dt_oth" onclick="s(this);h(Dd('dt_pay'));h(Dd('dt_4'));" onmouseover="this.className='dt_on';" onmouseout="this.className='';">会员相关</dt> 
	<?php
		foreach($menu_relate as $m) {
			echo '<dd onclick="c(this);" style="display:none;"><a href="'.$m[1].'">'.$m[0].'</a></dd>';
		}
	?>
	</dl>
	</div>
</div>
<script type="text/javascript">
var names = ['', '系统维护', '我的面板', '功能模块', '会员管理'];
function show(ID) {
	var imgdir = 'admin/image/';
	Dd('menu').innerHTML = Dd('m_'+ID).innerHTML;
	Dd('name').innerHTML = names[ID];
	for(i=1;i<names.length;i++) {
		if(i==ID) {
			Dd('b_'+i).src = imgdir+'bar'+i+'on.gif';
			if(i==1) {Dd('n_1').style.width = '25px';} else {Dd('n_'+i).style.width = '25px';Dd('n_'+(i-1)).style.width = '25px';}
		} else {
			Dd('b_'+i).src = imgdir+'bar'+i+'.gif';
			if(ID == 1) {Dd('n_'+i).style.width = '19px';} else if(i!=(ID-1) && i!=(ID+1)) {Dd('n_'+i).style.width = '19px';}
		}
		Dd('b_'+i).title = names[i];
	}
}
show(2);
</script>
<?php } ?>
<script type="text/javascript">
function c(o) {
	var dds = Dd('menu').getElementsByTagName('dd');
	for(var i=0;i<dds.length;i++) {
		dds[i].className = dds[i] == o ? 'dd_on' : '';
		if(dds[i] == o) o.firstChild.blur();
	}
}
function s(o) {
	var dds = o.parentNode.getElementsByTagName('dd');
	for(var i=0;i<dds.length;i++) {
		dds[i].style.display = dds[i].style.display == 'none' ? '' : 'none';
	}
}
function h(o) {
	var dds = o.parentNode.getElementsByTagName('dd');
	for(var i=0;i<dds.length;i++) {
		dds[i].style.display = 'none';
	}
}
function m(ID) {
	var dls = Dd('m_3').getElementsByTagName('dl');
	for(var i=0;i<dls.length;i++) {
		var dds = Dd(dls[i].id).getElementsByTagName('dd');
		for(var j=0;j<dds.length;j++) {
			dds[j].style.display = dls[i].id == 'dl_'+ID ? dds[j].style.display == 'none' ? '' : 'none' : 'none';
		}
	}
}
</script>
</body>
</html>