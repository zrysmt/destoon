<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<script type="text/javascript">
var _del = 0;
</script>
<form action="?">
<div class="tt">产品搜索</div>
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td>
&nbsp;产品名：
<input type="text" size="50" name="kw" value="<?php echo $kw;?>" title="关键词"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
</table>
</form>
<form method="post" action="?">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<div class="tt">产品管理</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<th width="40">删除</th>
<th>产品ID</th>
<th>排序</th>
<th>产品名称</th>
<th>计量单位</th>
<th>分类ID</th>
<th>所属分类</th>
<th>属性数量</th>
<th>属性参数</th>
</tr>
<?php foreach($lists as $k=>$v) { ?>
<tr onmouseover="this.className='on';" onmouseout="this.className='';" align="center">
<td><input name="post[<?php echo $v['pid'];?>][delete]" type="checkbox" value="1" onclick="if(this.checked){_del++;}else{_del--;}"/></td>
<td><?php echo $v['pid'];?></td>
<td><input name="post[<?php echo $v['pid'];?>][listorder]" type="text" size="3" value="<?php echo $v['listorder'];?>"/></td>
<td><input name="post[<?php echo $v['pid'];?>][title]" type="text" size="20" value="<?php echo $v['title'];?>"/></td>
<td><input name="post[<?php echo $v['pid'];?>][unit]" type="text" size="5" value="<?php echo $v['unit'];?>"/></td>
<td><input name="post[<?php echo $v['pid'];?>][catid]" type="text" size="5" value="<?php echo $v['catid'];?>"/></td>
<td><?php echo cat_pos($v['catid'], ' ', 1);?></td>
<td><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&pid=<?php echo $v['pid'];?>&action=manage"><?php echo $v['items'];?></a></td>
<td>
<a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&pid=<?php echo $v['pid'];?>&action=add"><img src="admin/image/new.png" width="16" height="16" title="添加属性" alt=""/></a>&nbsp;
<a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&pid=<?php echo $v['pid'];?>&action=manage"><img src="admin/image/child.png" width="16" height="16" title="管理属性" alt=""/></a>&nbsp;
</td>
</tr>
<?php } ?>
<tr onmouseover="this.className='on';" onmouseout="this.className='';" align="center">
<td class="f_red">新增</td>
<td></td>
<td><input name="post[0][listorder]" type="text" size="3" value=""/></td>
<td><input name="post[0][title]" type="text" size="20" value=""/></td>
<td><input name="post[0][unit]" type="text" size="5" value=""/></td>
<td colspan="5" align="left">&nbsp;&nbsp;<?php echo ajax_category_select('post[0][catid]', '选择分类', $catid, $moduleid);?></td>
</tr>
<tr>
<td> </td>
<td height="30" colspan="8">
&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="submit" value=" 更 新 " onclick="if(_del && !confirm('提示:您选择删除'+_del+'个产品类型？确定要删除吗？')) return false;" class="btn"/>
</td>
</tr>
</table>
</form>
<div class="pages"><?php echo $pages;?></div>
<form action="?" method="post" onsubmit="return check();">
<div class="tt">同步属性</div>
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="copy"/>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td>
&nbsp;源产品ID：<input type="text" size="5" name="fpid" id="fpid"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
目标产品ID：<input type="text" size="20" name="tpid" id="tpid"/>&nbsp;
<input type="submit" value="确定" class="btn"/>&nbsp;
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;<strong>使用说明</strong></td>
</tr>
<tr>
<td style="padding:10px;color:#666666;">
1、如果一个产品属于多个分类，首先按分类多次添加此产品，然后在任意一个同名产品下建立属性，属性建立之后，可以做为源产品ID将属性同步到其他指定的目标产品。<br/>
2、目标产品ID如果有多个，请用英文逗号(,)分隔开。目标产品没有而源产品有的属性，将被创建；目标产品和源产品同名的属性，将被更新。
</td>
</tr>
</table>
</form>
<script type="text/javascript">
function check() {
	if(Dd('fpid').value == '') {
		alert('请填写源产品ID');
		Dd('fpid').focus();
		return false;
	}
	if(Dd('tpid').value == '') {
		alert('请填写目标产品ID');
		Dd('tpid').focus();
		return false;
	}
	return confirm('确定要同步属性吗？此操作将不可撤销');
}
</script>
<script type="text/javascript">Menuon(0);</script>
<?php include tpl('footer');?>