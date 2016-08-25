<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<script type="text/javascript">
var _del = 0;
</script>
<form method="post" action="?">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="page" value="<?php echo $page;?>"/>
<div class="tt">起价设置</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<th width="40">删除</th>
<th>关键词</th>
<th>供应起价</th>
<th>求购起价</th>
<th>公司起价</th>
<th>添加时间</th>
<th>操作人</th>
</tr>
<?php foreach($lists as $k=>$v) { ?>
<tr onmouseover="this.className='on';" onmouseout="this.className='';" align="center">
<td><input name="post[<?php echo $v['itemid'];?>][delete]" type="checkbox" value="1" onclick="if(this.checked){_del++;}else{_del--;}"/></td>

<td><input name="post[<?php echo $v['itemid'];?>][word]" type="text" size="10" value="<?php echo $v['word'];?>"/></td>

<td><input name="post[<?php echo $v['itemid'];?>][sell_price]" type="text" size="10" value="<?php echo $v['sell_price'];?>"/></td>
<td><input name="post[<?php echo $v['itemid'];?>][buy_price]" type="text" size="10" value="<?php echo $v['buy_price'];?>"/></td>
<td><input name="post[<?php echo $v['itemid'];?>][company_price]" type="text" size="10" value="<?php echo $v['company_price'];?>"/></td>


<td class="px11"><?php echo $v['edittime'];?></td>
<td><?php echo $v['editor'];?></td>
</td>
</tr>
<?php } ?>
<tr onmouseover="this.className='on';" onmouseout="this.className='';" align="center">
<td class="f_red">新增</td>
<td><input name="post[0][word]" type="text" size="10" value=""/></td>
<td><input name="post[0][sell_price]" type="text" size="10" value=""/></td>
<td><input name="post[0][buy_price]" type="text" size="10" value=""/></td>
<td><input name="post[0][company_price]" type="text" size="10" value=""/></td>
<td colspan="2"> </td>
</tr>
<tr>
<td> </td>
<td height="30" colspan="7">&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="submit" value=" 更 新 " onclick="if(_del && !confirm('提示:您选择删除'+_del+'个价格项目？确定要删除吗？')) return false;" class="btn"/></td>
</tr>
</table>
</form>
<div class="pages"><?php echo $pages;?></div>
<script type="text/javascript">Menuon(3);</script>
<?php include tpl('footer');?>