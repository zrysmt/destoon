<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<div class="tt">日志搜索</div>
<form action="?">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td>&nbsp;
<?php echo $fields_select;?>&nbsp;
<input type="text" size="25" name="kw" value="<?php echo $kw;?>" title="关键词"/>&nbsp;
<?php echo dcalendar('fromdate', $fromdate);?> 至 <?php echo dcalendar('todate', $todate);?>
&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" title="条/页"/>
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?file=<?php echo $file;?>');"/>
</td>
</tr>
</table>
</form>
<div class="tt">操作日志</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<th>URL</th>
<th>模块</th>
<th>文件</th>
<th>操作</th>
<th>ID</th>
<th>IP</th>
<th>地区</th>
<th>管理员</th>
<th>时间</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr onmouseover="this.className='on';" onmouseout="this.className='';" align="center">
<td title="<?php echo $v['qstring'];?>"><input type="text" size="30" value="<?php echo $v['qstring'];?>"/> <a href="?<?php echo $v['qstring'];?>" target="_blank"><img src="admin/image/link.gif" width="16" height="16" title="点击打开网址" alt="" align="absmiddle"/></a></td>
<td><?php echo $v['module'];?></td>
<td><?php echo $v['file'];?></td>
<td><?php echo $v['action'];?></td>
<td><a href="<?php echo DT_PATH;?>api/redirect.php?mid=<?php echo $v['mid'];?>&itemid=<?php echo $v['itemid'];?>" target="_blank"><?php echo $v['itemid'];?></a></td>
<td><a href="javascript:_ip('<?php echo $v['ip'];?>');"><?php echo $v['ip'];?></a></td>
<td><?php echo ip2area($v['ip']);?></td>
<td><a href="?file=<?php echo $file;?>&username=<?php echo $v['username'];?>"><?php echo $v['username'];?></a></td>
<td><?php echo $v['logtime'];?></td>
</tr>
<?php }?>
</table>
<div class="pages"><?php echo $pages;?></div>
<script type="text/javascript">Menuon(0);</script>
<?php include tpl('footer');?>