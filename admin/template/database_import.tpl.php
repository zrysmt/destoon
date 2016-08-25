<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?" id="dform">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="delete"/>
<div class="tt">备份系列</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<th width="40"><input type="checkbox" onclick="checkall(this.form);"/></th>
<th>备份系列</th>
<th width="100">文件大小(M)</th>
<th width="150">备份时间</th>
<th width="50">分卷</th>
<th width="100">操作</th>
</tr>
<?php foreach($dbaks as $k=>$v) {?>
<tr align="center" onmouseover="this.className='on';" onmouseout="this.className='';">
<td><input type="checkbox" name="filenames[]" value="<?php echo $v['filename'];?>"></td>
<td align="left">&nbsp;<img src="admin/image/folder.gif" width="16" height="14" alt="" align="absmiddle"/> <a href="?file=<?php echo $file;?>&action=open&dir=<?php echo $v['filename'];?>"><?php echo $v['filename'];?></a></td>
<td><?php echo $v['filesize'];?></td>
<td title="修改时间:<?php echo $v['mtime'];?>"><?php echo $v['btime'];?></td>
<td><?php echo $v['number'];?></td>
<td>
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&filepre=<?php echo $v['pre'];?>&tid=<?php echo $v['number'];?>&import=1" onclick="return confirm('确定要导入此系列文件吗？现有数据将被覆盖，此操作将不可恢复');"><img src="admin/image/import.png" width="16" height="16" title="导入本系列备份文件" alt=""/></a>&nbsp;&nbsp;<a href="?file=<?php echo $file;?>&action=open&dir=<?php echo $v['filename'];?>"><img src="admin/image/save.png" width="16" height="16" title="下载" alt=""/></a>&nbsp;&nbsp;<a href="?file=<?php echo $file;?>&action=delete&filenames=<?php echo $v['filename'];?>" onclick="return _delete();"><img src="admin/image/delete.png" width="16" height="16" title="删除" alt=""/></a></td>
</tr>
<?php }?>
</table>
<?php if($dsqls || $sqls) {?>
<div class="tt">其他SQL文件</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<th width="40">-</th>
<th>SQL文件</th>
<th width="100">文件大小(M)</th>
<th width="150">修改时间</th>
<th width="50">分卷</th>
<th width="100">操作</th>
</tr>
<?php if($dsqls) {?>
<?php foreach($dsqls as $k=>$v) {?>
<tr align="center"<?php if($v['class']) echo ' class="on"';?>>
<td><input type="checkbox" name="filenames[]" value="<?php echo $v['filename'];?>"></td>
<td align="left">&nbsp;<img src="admin/image/sql.gif" width="16" height="16" alt="" align="absmiddle"/> <a href="<?php DT_PATH;?>file/backup/<?php echo $v['filename'];?>" title="点鼠标右键另存为保存此文件" target="_blank"><?php echo $v['filename'];?></a></td>
<td><?php echo $v['filesize'];?></td>
<td title="修改时间:<?php echo $v['mtime'];?>"><?php echo $v['btime'];?></td>
<td><?php echo $v['number'];?></td>
<td>
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&filepre=<?php echo $v['pre'];?>&import=1" onclick="return confirm('确定要导入此系列文件吗？现有数据将被覆盖，此操作将不可恢复');"><img src="admin/image/import.png" width="16" height="16" title="导入本系列备份文件" alt=""/></a>&nbsp;&nbsp;<a href="?file=<?php echo $file;?>&action=download&filename=<?php echo $v['filename'];?>"><img src="admin/image/save.png" width="16" height="16" title="下载" alt=""/></a>&nbsp;&nbsp;<a href="?file=<?php echo $file;?>&action=delete&filenames=<?php echo $v['filename'];?>" onclick="return _delete();"><img src="admin/image/delete.png" width="16" height="16" title="删除" alt=""/></a></td>
</tr>
<?php }?>
<?php }?>

<?php if($sqls) {?>
<?php foreach($sqls as $k=>$v) {?>
<tr align="center" onmouseover="this.className='on';" onmouseout="this.className='';">
<td><input type="checkbox" name="filenames[]" value="<?php echo $v['filename'];?>"></td>
<td align="left">&nbsp;<img src="admin/image/sql.gif" width="16" height="16" alt="" align="absmiddle"/> <a href="<?php DT_PATH;?>file/backup/<?php echo $v['filename'];?>" title="点鼠标右键另存为保存此文件" target="_blank"><?php echo $v['filename'];?></a></td>
<td><?php echo $v['filesize'];?></td>
<td><?php echo $v['mtime'];?></td>
<td> -- </td>
<td><a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&filename=<?php echo $v['filename'];?>&import=1"><img src="admin/image/import.png" width="16" height="16" title="导入SQL文件" alt=""/></a>&nbsp;&nbsp;<a href="?file=<?php echo $file;?>&action=download&filename=<?php echo $v['filename'];?>"><img src="admin/image/save.png" width="16" height="16" title="下载" alt=""/></a>&nbsp;&nbsp;<a href="?file=<?php echo $file;?>&action=delete&filenames=<?php echo $v['filename'];?>" onclick="return _delete();"><img src="admin/image/delete.png" width="16" height="16" title="删除" alt=""/></a></td>
</tr>
<?php }?>
<?php }?>
</table>
<?php } ?>
<div class="btns">
<span class="f_r">
<a href="javascript:" onclick="checkall(Dd('dform'), 1);" class="t">反选</a>&nbsp;&nbsp;
<a href="javascript:" onclick="checkall(Dd('dform'), 2);" class="t">全选</a>&nbsp;&nbsp;
<a href="javascript:" onclick="checkall(Dd('dform'), 0);" class="t">全不选</a>&nbsp;&nbsp;
</span>
&nbsp;
<input type="submit" name="submit" value="删除文件" class="btn" onclick="return confirm('确定要删除所选文件吗？此操作将不可恢复');"/>
</div>
</form>
<script type="text/javascript">Menuon(1);</script>
<?php if(count($dbaks) > 10) { ?>
<script type="text/javascript">Dalert('备份系列超 10 个，建议清理或转移过期备份')</script>
<?php } ?>
<?php include tpl('footer');?>