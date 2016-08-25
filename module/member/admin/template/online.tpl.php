<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form action="?">
<div class="tt">会员搜索</div>
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td>&nbsp;
<?php echo $fields_select;?>&nbsp;
<input type="text" size="40" name="kw" value="<?php echo $kw;?>" title="关键词"/>&nbsp;
<select name="online">
<option value="2"<?php if($online==2) echo ' selected';?>>状态</option>
<option value="1"<?php if($online==1) echo ' selected';?>>在线</option>
<option value="0"<?php if($online==0) echo ' selected';?>>隐身</option>
</select>&nbsp;
<?php echo module_select('mid', '模块', $mid);?>&nbsp;
<?php echo $order_select;?>
&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" title="条/页"/>
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>');"/>
</td>
</tr>
</table>
</form>
<div class="tt">在线会员</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<th width="60">头像</th>
<th>会员名</th>
<th>状态</th>
<th>所在模块</th>
<th>IP</th>
<th>IP所在地</th>
<th width="130">访问时间</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr onmouseover="this.className='on';" onmouseout="this.className='';" align="center">
<td><img src="<?php echo useravatar($v['username']);?>" style="padding:5px;" width="48" height="48"/></td>
<td><a href="javascript:_user('<?php echo $v['username'];?>')"><?php echo $v['username'];?></a></td>
<td><?php echo $v['online'] ? '<span class="f_green">在线</span>' : '<span class="f_gray">隐身</span>';?></td>
<td><a href="<?php echo $MODULE[$v['moduleid']]['linkurl'];?>" target="_blank"><?php echo $MODULE[$v['moduleid']]['name'];?></a></td>
<td><?php echo $v['ip'];?></td>
<td><?php echo ip2area($v['ip']);?></td>
<td><?php echo $v['lasttime'];?></td>
</tr>
<?php }?>
</table>
<div class="pages"><?php echo $pages;?></div>
<br/>
<script type="text/javascript">Menuon(0);</script>
<?php include tpl('footer');?>