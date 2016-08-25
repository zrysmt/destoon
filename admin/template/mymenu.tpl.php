<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<div class="tt">我的面板管理</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<th width="40">删除</th>
<th>排序</th>
<th>名称</th>
<th>地址</th>
</tr>
<?php foreach($dmenus as $k=>$v) {?>
<tr onmouseover="this.className='on';" onmouseout="this.className='';" align="center">
<td><input name="right[<?php echo $v['adminid'];?>][delete]" type="checkbox" value="1"/></td>
<td><input name="right[<?php echo $v['adminid'];?>][listorder]" type="text" size="3" value="<?php echo $v['listorder'];?>"/></td>
<td><input name="right[<?php echo $v['adminid'];?>][title]" type="text" size="12" value="<?php echo $v['title'];?>"/> <?php echo dstyle('right['.$v['adminid'].'][style]', $v['style']);?></td>
<td><input name="right[<?php echo $v['adminid'];?>][url]" type="text" size="60" value="<?php echo $v['url'];?>"/></td>
</tr>
<?php }?>
<tr onmouseover="this.className='on';" onmouseout="this.className='';" align="center">
<td class="f_red">新增</td>
<td><input name="right[0][listorder]" type="text" size="3" value=""/></td>
<td><input name="right[0][title]" type="text" size="12" value=""/> <?php echo dstyle('right[0][style]');?></td>
<td><input name="right[0][url]" type="text" size="60" value=""/>
</td>
</tr>
<tr>
<td> </td>
<td height="30" colspan="4"><input type="submit" name="submit" value="更 新" class="btn"/>&nbsp;&nbsp;&nbsp;&nbsp;提示：复制左侧栏的操作链接，删除“?”之前的地址即为对应操作的地址</td>
</tr>
</table>
</form>
<script type="text/javascript">Menuon(0);</script>
<?php if(isset($update)) { ?>
<script type="text/javascript">window.parent.frames[0].location.reload();</script>
<?php } ?>
<br/>
<?php include tpl('footer');?>