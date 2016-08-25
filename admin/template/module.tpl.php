<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form action="?" method="post">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="order"/>
<div class="tt">模块管理</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<th width="40">排序</th>
<th width="40">ID</th>
<th>名称</th>
<th width="100">目录</th>
<th width="60">类型</th>
<th width="60">导航</th>
<th width="100">模型</th>
<th width="100">安装日期</th>
<th width="100">管理</th>
<th width="50">状态</th>
</tr>
<?php foreach($modules as $k=>$v) {?>
<tr align="center" onmouseover="this.className='on';" onmouseout="this.className='';">
<td><input type="text" size="2" name="listorder[<?php echo $v['moduleid'];?>]" value="<?php echo $v['listorder'];?>"/></td>
<td><?php echo $v['moduleid'];?></td>
<td><a href="<?php echo $v['linkurl'];?>" target="_blank"><?php echo set_style($v['name'], $v['style']);?></a></td>
<td><a href="<?php echo $v['linkurl'];?>" target="_blank"><?php echo $v['moduledir'] ? $v['moduledir'] : '--';?></a></td>
<td><?php echo $v['islink'] ? '<span class="f_red">外链</span>' : '内置';?></td>
<td><img src="admin/image/<?php echo $v['ismenu'] ? 'yes' : 'no'; ?>.gif"/></td>
<td title="<?php echo $v['module'];?>"><?php echo $v['modulename'];?></td>
<td><?php echo $v['installdate'];?></td>
<td><a href="?file=<?php echo $file;?>&action=edit&modid=<?php echo $v['moduleid'];?>"><img src="admin/image/edit.png" width="16" height="16" title="修改" alt=""/></a>&nbsp;&nbsp;<a href="?file=<?php echo $file;?>&action=delete&modid=<?php echo $v['moduleid'];?>" onclick="return _delete();"><img src="admin/image/delete.png" width="16" height="16" title="删除" alt=""/></a>&nbsp;&nbsp;<a href="?file=<?php echo $file;?>&action=remkdir&modid=<?php echo $v['moduleid'];?>"><img src="admin/image/remkdir.png" width="16" height="16" title="重建目录" alt=""/></a>&nbsp;&nbsp;<a href="javascript:Dwidget('?file=setting&moduleid=<?php echo $v['moduleid'];?>', '[<?php echo $v['modulename'];?>] 模块设置');"><img src="admin/image/set.png" width="16" height="16" title="设置" alt=""/></a></td>
<td>
<?php if($v['disabled']) {?>
<a href="?file=<?php echo $file;?>&action=disable&value=0&modid=<?php echo $v['moduleid'];?>"><img src="admin/image/stop.png" width="16" height="16" title="已禁用,点击启用" alt=""/></a>
<?php } else {?>
<a href="javascript:Dconfirm('确定要禁用[<?php echo $v['name'];?>]模块吗?', '?file=<?php echo $file;?>&action=disable&value=1&modid=<?php echo $v['moduleid'];?>');"><img src="admin/image/start.png" width="16" height="16" title="正常运行,点击禁用" alt=""/></a>
<?php } ?>
</td>
</tr>
<?php }?>
</table>
<?php if($_modules) { ?>
<div class="tt">禁用模块</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<th width="40">排序</th>
<th width="40">ID</th>
<th>名称</th>
<th width="100">目录</th>
<th width="60">类型</th>
<th width="60">导航</th>
<th width="100">模型</th>
<th width="100">安装日期</th>
<th width="100">管理</th>
<th width="50">状态</th>
</tr>
<?php foreach($_modules as $k=>$v) {?>
<tr align="center" onmouseover="this.className='on';" onmouseout="this.className='';">
<td><input type="text" size="2" name="listorder[<?php echo $v['moduleid'];?>]" value="<?php echo $v['listorder'];?>"/></td>
<td><?php echo $v['moduleid'];?></td>
<td><a href="<?php echo $v['linkurl'];?>" target="_blank"><?php echo set_style($v['name'], $v['style']);?></a></td>
<td><a href="<?php echo $v['linkurl'];?>" target="_blank"><?php echo $v['moduledir'] ? $v['moduledir'] : '--';?></a></td>
<td><?php echo $v['islink'] ? '<span class="f_red">外链</span>' : '内置';?></td>
<td><img src="admin/image/<?php echo $v['ismenu'] ? 'yes' : 'no'; ?>.gif"/></td>
<td title="<?php echo $v['module'];?>"><?php echo $v['modulename'];?></td>
<td><?php echo $v['installdate'];?></td>
<td><a href="?file=<?php echo $file;?>&action=edit&modid=<?php echo $v['moduleid'];?>"><img src="admin/image/edit.png" width="16" height="16" title="修改" alt=""/></a>&nbsp;&nbsp;<a href="?file=<?php echo $file;?>&action=delete&modid=<?php echo $v['moduleid'];?>" onclick="return _delete();"><img src="admin/image/delete.png" width="16" height="16" title="删除" alt=""/></a>&nbsp;&nbsp;<a href="?file=<?php echo $file;?>&action=remkdir&modid=<?php echo $v['moduleid'];?>"><img src="admin/image/remkdir.png" width="16" height="16" title="重建目录" alt=""/></a>&nbsp;&nbsp;<a href="?file=setting&moduleid=<?php echo $v['moduleid'];?>"><img src="admin/image/set.png" width="16" height="16" title="设置" alt=""/></a></td>
<td>
<?php if($v['disabled']) {?>
<a href="?file=<?php echo $file;?>&action=disable&value=0&modid=<?php echo $v['moduleid'];?>"><img src="admin/image/stop.png" width="16" height="16" title="已禁用,点击启用" alt=""/></a>
<?php } else {?>
<a href="javascript:Dconfirm('确定要禁用[<?php echo $v['name'];?>]模块吗?', '?file=<?php echo $file;?>&action=disable&value=1&modid=<?php echo $v['moduleid'];?>');"><img src="admin/image/start.png" width="16" height="16" title="正常运行,点击禁用" alt=""/></a>
<?php } ?>
</td>
</tr>
<?php }?>
</table>
<?php } ?>
<div class="btns">
<input type="submit" value=" 更新排序 " class="btn"/>&nbsp;
</div>
</form>
<div class="tt">系统模型</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<th>模型</th>
<th width="100">目录</th>
<th width="60">可复制</th>
<th width="60">可卸载</th>
<th width="100">作者</th>
<th width="260">官方网站</th>
</tr>
<?php foreach($sysmodules as $k=>$v) {?>
<tr align="center" onmouseover="this.className='on';" onmouseout="this.className='';">
<td align="left">&nbsp;<img src="admin/image/folder.gif" align="absmiddle"/> <?php echo $v['name'];?>(<?php echo $v['module'];?>)</td>
<td title="位于./module/<?php echo $v['module'];?>/"><?php echo $v['module'];?></td>
<td><img src="admin/image/<?php echo $v['copy'] ? 'yes' : 'no'; ?>.gif"/></td>
<td><img src="admin/image/<?php echo $v['uninstall'] ? 'yes' : 'no'; ?>.gif"/></td>
<td><?php echo $v['author'];?></td>
<td><a href="<?php echo 'http://'.$v['homepage'];?>" target="_blank"><?php echo $v['homepage'];?></a></td>
</tr>
<?php
}
?>
</table>
<script type="text/javascript">Menuon(1);</script>
<?php if(isset($update)) { ?>
<script type="text/javascript">window.parent.frames[0].location.reload();</script>
<?php } ?>
<?php include tpl('footer');?>