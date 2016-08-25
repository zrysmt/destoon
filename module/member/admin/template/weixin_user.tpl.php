<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form action="?">
<div class="tt">用户搜索</div>
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td>&nbsp;
<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" title="关键词"/>&nbsp;
<select name="sex">
<option value="-1">性别</option>
<?php
foreach($SEX as $k=>$v) {
	echo '<option value="'.$k.'" '.($sex == $k ? 'selected' : '').'>'.$v.'</option>';
}
?>
</select>&nbsp;
<select name="subscribe">
<option value="-1">状态</option>
<?php
foreach($SUBSCRIBE as $k=>$v) {
	echo '<option value="'.$k.'" '.($subscribe == $k ? 'selected' : '').'>'.strip_tags($v).'</option>';
}
?>
</select>&nbsp;
<?php echo $order_select;?>
<input type="checkbox" name="thumb" value="1"<?php echo $thumb ? ' checked' : '';?>/>头像&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" title="条/页"/>
<input type="submit" value="搜 索" class="btn" onclick="Dd('export').value=0;"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
</table>
</form>
<div class="tt">用户列表</div>
<form method="post">
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<th width="25"><input type="checkbox" onclick="checkall(this.form);"/></th>
<th width="68">头像</th>
<th>昵称</th>
<th width="50">性别</th>
<th>来自</th>
<th>会员名</th>
<th width="90">关注状态</th>
<th width="130">关注时间</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr onmouseover="this.className='on';" onmouseout="this.className='';" align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td><a href="javascript:Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&openid=<?php echo $v['openid'];?>&action=chat', '与[<?php echo $v['nickname'];?>]交谈中...', 520, 490);"><img src="<?php echo $v['headimgurl'];?>" width="46" style="margin:5px 0 5px 0;"/></a></td>
<td><a href="javascript:Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&openid=<?php echo $v['openid'];?>&action=chat', '与[<?php echo $v['nickname'];?>]交谈中...', 520, 490);"><?php echo $v['nickname'];?></a></td>
<td><?php echo $v['gender'];?></td>
<td><?php echo $v['province'].$v['city'];?></td>
<td><a href="javascript:_user('<?php echo $v['username'];?>')"><?php echo $v['username'];?></a></td>
<td><?php echo $v['status'];?></td>
<td class="px11"><?php echo $v['adddate'];?></td>
</tr>
<?php }?>
</table>
<div class="btns">
<input type="submit" value=" 解除绑定 " class="btn" onclick="if(confirm('确定要解除会员绑定吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=unbind'}else{return false;}"/>&nbsp;
<input type="button" value="同步用户" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=sync');" title="同步微信平台上的用户信息"/>
</div>
</form>
<div class="pages"><?php echo $pages;?></div>
<br/>
<script type="text/javascript">Menuon(2);</script>
<?php include tpl('footer');?>