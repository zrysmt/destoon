<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post">
<input type="hidden" name="forward" value="<?php echo $DT_URL;?>"/>
<input type="hidden" name="catid" value="<?php echo $catid;?>"/>
<div class="tt">属性参数</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<th width="40">排序</th>
<th>ID</th>
<th>名称</th>
<th>必填</th>
<th>搜索</th>
<th>添加方式</th>
<th>默认(备选)值</th>
<th width="50">操作</th>
</tr>
<?php foreach($lists as $k=>$v) { ?>
<tr onmouseover="this.className='on';" onmouseout="this.className='';" align="center">
<td><input type="text" size="2" name="listorder[<?php echo $v['oid'];?>]" value="<?php echo $v['listorder'];?>"/></td>
<td><?php echo $v['oid'];?></td>
<td><?php echo $v['name'];?></td>
<td><?php echo $v['required'] ? '<span class="f_red">是</span>' : '否';?></td>
<td><?php echo $v['search'] ? '<span class="f_red">是</span>' : '否';?></td>
<td><?php echo $TYPE[$v['type']];?></td>
<td><input type="text" style="width:300px;" value="<?php echo $v['value'];?>"/></td>
<td>
<a href="?file=<?php echo $file;?>&action=edit&catid=<?php echo $v['catid'];?>&oid=<?php echo $v['oid'];?>"><img src="admin/image/edit.png" width="16" height="16" title="修改" alt=""/></a>&nbsp;
<a href="?file=<?php echo $file;?>&action=delete&catid=<?php echo $v['catid'];?>&oid=<?php echo $v['oid'];?>" onclick="return _delete();"><img src="admin/image/delete.png" width="16" height="16" title="删除" alt=""/></a>
</td>
</tr>
<?php } ?>
</table>
<div class="btns">
<input type="submit" value=" 更新排序 " class="btn" onclick="this.form.action='?file=<?php echo $file;?>&catid=<?php echo $catid;?>&action=order';"/>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value=" 关 闭 " class="btn" onclick="window.parent.location.reload();"/>
</div>
</form>
<div class="pages"><?php echo $pages;?></div>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>