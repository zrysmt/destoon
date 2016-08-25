<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<div class="tt">注意事项</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td>&nbsp;&nbsp;&nbsp;- 如果进行了<span class="f_red">修改</span>或<span class="f_red">删除</span>分类操作，为了保证操作速度，系统不自动修复结构。请在<span class="f_red">管理完成</span>或<span class="f_red">操作失败</span>时，点更新缓存以修复分类结构至最新。</td>
</tr>
<tr>
<td>&nbsp;&nbsp;&nbsp;- <span class="f_red">删除分类</span>会将分类下的信息移至回收站，分类本身可以修改名称和上级分类，没有特殊情况不建议直接删除分类。</td>
</tr>
<tr>
<td>&nbsp;&nbsp;&nbsp;- 修改上级ID可以快速修改分类的上级分类，改变分类结构。</td>
</tr>
<?php if($mid == 9) { ?>
<tr>
<td>&nbsp;&nbsp;&nbsp;- 对于<?php echo $MODULE[9]['name'];?>模块，一级分类为行业，二级分类为职位。</td>
</tr>
<?php } ?>
<?php if($mid == 18) { ?>
<tr>
<td>&nbsp;&nbsp;&nbsp;- 对于<?php echo $MODULE[18]['name'];?>模块，建议只添加一级分类。</td>
</tr>
<?php } ?>
</table>

<form action="?">
<input type="hidden" name="mid" value="<?php echo $mid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<div class="tt">分类搜索</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" title="关键词"/>&nbsp;
<input type="submit" name="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 搜" class="btn" onclick="Go('?mid=<?php echo $mid;?>&file=<?php echo $file;?>');"/>&nbsp;
</td>
</tr>
</table>
</div>
</form>
<div class="tt"><?php if($parentid) echo $CATEGORY[$parentid]['catname'];?>分类管理</div>
<form method="post">
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<th width="25"><input type="checkbox" onclick="checkall(this.form);"/></th>
<th>排序</th>
<th>ID</th>
<th>上级ID</th>
<th>分类名</th>
<th>分类目录</th>
<th>索引</th>
<th>级别</th>
<th colspan="2">信息数量</th>
<th>子类</th>
<th>属性</th>
<th width="80">操作</th>
</tr>
<?php foreach($DTCAT as $k=>$v) {?>
<tr onmouseover="this.className='on';" onmouseout="this.className='';" align="center">
<td><input type="checkbox" name="catids[]" value="<?php echo $v['catid'];?>"/></td>
<td><input name="category[<?php echo $v['catid'];?>][listorder]" type="text" size="3" value="<?php echo $v['listorder'];?>"/></td>
<td>&nbsp;<a href="<?php echo $MODULE[$mid]['linkurl'].$v['linkurl'];?>" target="_blank"><?php echo $v['catid'];?></a>&nbsp;</td>
<td><input name="category[<?php echo $v['catid'];?>][parentid]" type="text" size="5" value="<?php echo $v['parentid'];?>"/></td>
<td>
<input name="category[<?php echo $v['catid'];?>][catname]" type="text" value="<?php echo $v['catname'];?>" style="width:100px;color:<?php echo $v['style'];?>"/>
<?php echo dstyle('category['.$v['catid'].'][style]', $v['style']);?>
</td>
<td><input name="category[<?php echo $v['catid'];?>][catdir]" type="text" value="<?php echo $v['catdir'];?>" size="10"/></td>
<td>
<input name="category[<?php echo $v['catid'];?>][letter]" type="text" value="<?php echo $v['letter'];?>" size="1"/>
</td>
<td>
<input name="category[<?php echo $v['catid'];?>][level]" type="text" value="<?php echo $v['level'];?>" size="1"/>
</td>
<td><script type="text/javascript">perc(<?php echo $v['item'];?>,<?php echo $total;?>,'80px');</script></td>
<td><?php echo $v['item'];?></td>
<td title="管理子分类"><a href="?file=<?php echo $file;?>&mid=<?php echo $mid;?>&parentid=<?php echo $v['catid'];?>"><?php echo $v['childs'];?></a></td>
<td title="管理属性"><a href="###" onclick="Dwidget('?file=property&catid=<?php echo $v['catid'];?>', '[<?php echo $v['catname'];?>]扩展属性');"><?php echo $v['property'];?></a></td>
<td>
<a href="?file=<?php echo $file;?>&action=add&mid=<?php echo $mid;?>&parentid=<?php echo $v['catid'];?>"><img src="admin/image/add.png" width="16" height="16" title="添加子分类" alt=""/></a>&nbsp;
<a href="?file=<?php echo $file;?>&action=edit&mid=<?php echo $mid;?>&catid=<?php echo $v['catid'];?>"><img src="admin/image/edit.png" width="16" height="16" title="修改" alt=""/></a>&nbsp;
<a href="?file=<?php echo $file;?>&action=delete&mid=<?php echo $mid;?>&catid=<?php echo $v['catid'];?>&parentid=<?php echo $parentid;?>" onclick="return _delete();"><img src="admin/image/delete.png" width="16" height="16" title="删除" alt=""/></a></td>
</tr>
<?php }?>
</table>
<div class="btns">
<span class="f_r">
分类总数:<strong class="f_red"><?php echo count($CATEGORY);?></strong>&nbsp;&nbsp;
当前目录:<strong class="f_blue"><?php echo count($DTCAT);?></strong>&nbsp;&nbsp;
</span>
<input type="submit" name="submit" value="更新分类" class="btn" onclick="this.form.action='?mid=<?php echo $mid;?>&file=<?php echo $file;?>&parentid=<?php echo $parentid;?>&action=update'"/>&nbsp;
<input type="submit" value="删除选中" class="btn" onclick="if(confirm('确定要删除选中分类吗？此操作将不可撤销')){this.form.action='?mid=<?php echo $mid;?>&file=<?php echo $file;?>&parentid=<?php echo $parentid;?>&action=delete'}else{return false;}"/>
<?php if($parentid) {?>&nbsp;
<input type="button" value="上级分类" class="btn" onclick="Go('?file=<?php echo $file;?>&mid=<?php echo $mid;?>&parentid=<?php echo $CATEGORY[$parentid]['parentid'];?>');"/>
<?php }?>
</div>
</form>
<form method="post" action="?">
<div class="tt">快捷操作</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr align="center">
<td>
<div style="float:left;padding:10px;">
<?php echo category_select('cid', '分类结构', $parentid, $mid, 'size="2" style="width:200px;height:130px;"');?></div>
<div style="float:left;padding:10px;">
	<table>
	<tr>
	<td><input type="submit" value="管理分类" class="btn" onclick="this.form.action='?mid=<?php echo $mid;?>&file=<?php echo $file;?>&parentid='+Dd('catid_1').value;"/></td>
	</tr>
	<tr>
	<td><input type="submit" value="添加分类" class="btn" onclick="this.form.action='?mid=<?php echo $mid;?>&file=<?php echo $file;?>&action=add&parentid='+Dd('catid_1').value;"/></td>
	</tr>
	<tr>
	<td><input type="submit" value="修改分类" class="btn" onclick="this.form.action='?mid=<?php echo $mid;?>&file=<?php echo $file;?>&action=edit&catid='+Dd('catid_1').value;"/></td>
	</tr>
	<tr>
	<td><input type="submit" value="删除分类" class="btn" onclick="if(confirm('确定要删除选中分类吗？此操作将不可撤销')){this.form.action='?mid=<?php echo $mid;?>&file=<?php echo $file;?>&action=delete&catid='+Dd('catid_1').value;}else{return false;}"/></td>
	</tr>
	</table>
</div>
</td>
</tr>
</table>
</div>
</form>
<script type="text/javascript">
function Prop(t, n) {
	mkDialog('', '<iframe src="?file=property&catid='+n+'" width="700" height=300" border="0" vspace="0" hspace="0" marginwidth="0" marginheight="0" framespacing="0" frameborder="0" scrolling="yes"></iframe>', '['+t+']扩展属性', 720, 0, 0);
}
</script>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>