<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form action="?">
<div class="tt">记录搜索</div>
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="fid" value="<?php echo $fid;?>"/>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td>
&nbsp;<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" title="关键词"/>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" title="条/页"/>
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>&fid=<?php echo $fid;?>');"/>&nbsp;&nbsp;
<input type="button" value=" 关 闭 " class="btn" onclick="window.parent.cDialog();"/>
</td>
</tr>
</table>
</form>
<div class="tt">回复记录</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<th>IP</th>
<th>地区</th>
<th>会员名</th>
<th>回复时间</th>
<th>参数</th>
<th width="60">操作</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr onmouseover="this.className='on';" onmouseout="this.className='';" align="center">
<td><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>&fid=<?php echo $fid;?>&job=show&rid=<?php echo $v['rid'];?>" title="详情"><?php echo $v['ip'];?></a></td>
<td><?php echo ip2area($v['ip']);?></td>
<td><a href="javascript:_user('<?php echo $v['username'];?>');"><?php echo $v['username'];?></a></td>
<td class="px11"><?php echo $v['adddate'];?></td>
<td><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>&fid=<?php echo $fid;?>&item=<?php echo $v['item'];?>"><?php echo $v['item'];?></a></td>
<td>
<a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>&fid=<?php echo $fid;?>&job=show&rid=<?php echo $v['rid'];?>"><img src="admin/image/view.png" width="16" height="16" title="详情" alt=""/></a>&nbsp;&nbsp;
<a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>&fid=<?php echo $fid;?>&job=delete&rid=<?php echo $v['rid'];?>" onclick="return _delete();"><img src="admin/image/delete.png" width="16" height="16" title="删除" alt=""/></a></td>
</tr>
<?php }?>
</table>
<div class="pages"><?php echo $pages;?></div>
<br/>
<script type="text/javascript">Menuon(3);</script>
<?php include tpl('footer');?>