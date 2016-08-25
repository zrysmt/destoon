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
<input type="hidden" name="pollid" value="<?php echo $pollid;?>"/>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td>
&nbsp;<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" title="关键词"/>&nbsp;
<select name="itemid">
<option value="0">投票选项</option>
<?php
foreach($I as $k=>$v) {
?>
<option value="<?php echo $k;?>" <?php echo $k == $itemid ? ' selected' : '';?>><?php echo $v['title'];?></option>
<?php
}
?>
</select>
&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" title="条/页"/>
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>&pollid=<?php echo $pollid;?>&itemid=<?php echo $itemid;?>');"/>&nbsp;&nbsp;
<input type="button" value=" 关 闭 " class="btn" onclick="window.parent.cDialog();"/>
</td>
</tr>
</table>
</form>
<div class="tt">投票记录</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<th>IP</th>
<th>地区</th>
<th>会员名</th>
<th>投票时间</th>
<th>选项</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr onmouseover="this.className='on';" onmouseout="this.className='';" align="center">
<td><?php echo $v['ip'];?></td>
<td><?php echo ip2area($v['ip']);?></td>
<td><a href="javascript:_user('<?php echo $v['username'];?>');"><?php echo $v['username'];?></a></td>
<td class="px11"><?php echo $v['polldate'];?></td>
<td><?php echo $I[$v['itemid']]['title'];?></td>
</tr>
<?php }?>
</table>
<div class="pages"><?php echo $pages;?></div>
<br/>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>