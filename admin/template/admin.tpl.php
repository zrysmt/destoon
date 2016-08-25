<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<div class="tt">管理员搜索</div>
<form action="?">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td>&nbsp;
<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" title="关键词"/>&nbsp;
<select name="type">
<option value="0">管理员类型</option>
<option value="1"<?php echo $type == 1 ? ' selected' : '';?>>超级管理员</option>
<option value="2"<?php echo $type == 2 ? ' selected' : '';?>>普通管理员</option>
</select>&nbsp;
<?php echo ajax_area_select('areaid', '所属分站', $areaid);?>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" title="条/页"/>
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?file=<?php echo $file;?>');"/>
</td>
</tr>
</table>
</form>
<form method="post" action="?">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="add"/>
<div class="tt">管理员管理</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<th>姓名</th>
<th>用户名</th>
<th>管理级别</th>
<th>管理角色</th>
<th>所属分站</th>
<th>上次登录时间</th>
<th>登录IP</th>
<th>登录地区</th>
<th>登录次数</th>
<th width="150">管理</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center" onmouseover="this.className='on';" onmouseout="this.className='';">
<td><?php echo $v['truename'];?></td>
<td><a href="javascript:_user('<?php echo $v['username'];?>');"><?php echo $v['username'];?></a></td>
<td><?php echo $v['adminname'];?></td>
<td><?php echo $v['role'];?></td>
<td><?php echo $v['aid'] ? $AREA[$v['aid']]['areaname'] : '';?></td>
<td class="px11"><?php echo $v['logintime'];?></td>
<td class="px11"><a href="javascript:_ip('<?php echo $v['loginip'];?>');"><?php echo $v['loginip'];?></a></td>
<td><?php echo ip2area($v['loginip']);?></td>
<td><?php echo $v['logintimes'];?></td>
<td>
<a href="?file=<?php echo $file;?>&action=edit&userid=<?php echo $v['userid'];?>" title="修改管理级别、角色、分站">修改</a> | 
<a href="javascript:Dwidget('?file=<?php echo $file;?>&action=right&userid=<?php echo $v['userid'];?>', '[<?php echo $v['username'];?>]分配权限和管理面板');" title="分配权限 / 管理面板">权限/面板</a> | 
<a href="?file=<?php echo $file;?>&action=delete&username=<?php echo $v['username'];?>" onclick="return _delete();" title="撤销管理员">撤销</a>
</td>
</tr>
<?php }?>
</table>
<div class="pages"><?php echo $pages;?></div>
<br/>
<?php if(isset($id) && isset($tm) && $id && $tm > $DT_TIME) { ?>
<script type="text/javascript">Dwidget('?file=<?php echo $file;?>&action=right&userid=<?php echo $id;?>', '请分配权限和管理面板');</script>
<?php } ?>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>