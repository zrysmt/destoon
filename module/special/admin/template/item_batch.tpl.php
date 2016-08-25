<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form action="?">
<div class="tt">信息筛选</div>
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="specialid" value="<?php echo $specialid;?>"/>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td>&nbsp;
<select name="mid">
<option value="0">请选择</option>
<?php
foreach($MODULE as $m) {
	if(!$m['islink'] && $m['moduleid'] > 4 && $m['moduleid'] != $moduleid) echo '<option value="'.$m['moduleid'].'"'.($mid == $m['moduleid'] ? ' selected' : '').'>'.$m['name'].'</option>';
}
?>
</select>&nbsp;
<?php if($mid) echo category_select('catid', '请选择分类', $catid, $mid).'&nbsp;';?>
<input type="text" size="50" name="kw" value="<?php echo $kw;?>" title="关键词"/>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" title="条/页"/>
<input type="submit" value="筛 选" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>&specialid=<?php echo $specialid;?>&mid=21');"/>
</td>
</tr>
</table>
</form>
<form method="post" action="?" id="dform">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="mid" value="<?php echo $mid;?>"/>
<input type="hidden" name="specialid" value="<?php echo $specialid;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<div class="tt"><?php echo $tname;?></div>
<table cellpadding="2" cellspacing="1" class="tb">
<?php if($lists) { ?>
<tr>
<th width="30"><input type="checkbox" onclick="checkall(this.form);"/></th>
<th>分类</th>
<th width="14"> </th>
<th>标题</th>
<th width="130">添加时间</th>
<th>浏览</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr onmouseover="this.className='on';" onmouseout="this.className='';" align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td><a href="<?php echo $v['caturl'];?>" target="_blank"><?php echo $v['catname'];?></a></td>
<td><?php if($v['level']) {?><img src="admin/image/level_<?php echo $v['level'];?>.gif" title="<?php echo $v['level'];?>级" alt=""/><?php } ?></td>
<td align="left">&nbsp;<a href="<?php echo $v['linkurl'];?>" target="_blank"><?php echo $v['title'];?></a></td>
<td class="px11"><?php echo $v['adddate'];?></td>
<td class="px11"><?php echo $v['hits'];?></td>
</tr>
<?php } ?>
<?php } else { ?>
<tr>
<td colspan="6" class="f_red">&nbsp;&nbsp;没有找到可用的信息，请重新筛选</td>
</tr>
<?php } ?>
<tr>
<td colspan="6">&nbsp;&nbsp;
<?php echo type_select($tid, 0, 'typeid', '请选择分类', 0, 'id="typeid"');?>&nbsp;&nbsp;
 <?php echo level_select('level', '级别', 0, 'id="level"');?>&nbsp;&nbsp;
<input type="submit" name="submit" value=" 添 加 " class="btn"/></td>
</tr>
</table>
</form>
<div class="pages"><?php echo $pages;?></div>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>