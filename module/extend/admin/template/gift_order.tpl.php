<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
?>
<script type="text/javascript">
var _del = 0;
</script>
<form action="?">
<div class="tt">记录搜索</div>
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td>
&nbsp;
<?php echo $fields_select;?>&nbsp;
<input type="text" size="50" name="kw" value="<?php echo $kw;?>" title="关键词"/>
&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" title="条/页"/>
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>&itemid=<?php echo $itemid;?>');"/>
</td>
</tr>
</table>
</form>
<form method="post" action="?">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<div class="tt">订单列表</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<th width="30"><input type="checkbox" onclick="checkall(this.form);"/></th>
<th>下单时间</th>
<th>礼品</th>
<th><?php echo $DT['credit_name'];?></th>
<th>会员名</th>
<th>订单状态</th>
<th>备注信息</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr onmouseover="this.className='on';" onmouseout="this.className='';" align="center">
<td><input name="post[<?php echo $v['oid'];?>][delete]" type="checkbox" value="1" onclick="if(this.checked){_del++;}else{_del--;}"/><input name="post[<?php echo $v['oid'];?>][itemid]" type="hidden" value="<?php echo $v['itemid'];?>"/></td>
<td class="px11"><?php echo $v['adddate'];?></td>
<td align="left">&nbsp;<a href="<?php echo $v['linkurl'];?>" target="_blank" title="<?php echo $v['title'];?>"><?php echo dsubstr($v['title'], 30, '..');?></a></td>
<td class="px11"><?php echo $v['credit'];?></td>
<td title="IP:<?php echo $v['ip'];?>(<?php echo ip2area($v['ip']);?>)"><a href="javascript:_user('<?php echo $v['username'];?>');"><?php echo $v['username'];?></a></td>
<td><input name="post[<?php echo $v['oid'];?>][status]" type="text" size="10" value="<?php echo $v['status'];?>" id="status_<?php echo $v['oid'];?>"/>
<select onchange="if(this.value)Dd('status_<?php echo $v['oid'];?>').value=this.value;">
<option value="">备选状态</option>
<option value="处理中">处理中</option>
<option value="审核中">审核中</option>
<option value="已取消">已取消</option>
<option value="已发货">已发货</option>
<option value="已完成">已完成</option>
</select>
</td>
<td><input name="post[<?php echo $v['oid'];?>][note]" type="text" size="15" value="<?php echo $v['note'];?>"/></td>
</tr>
<?php }?>
<tr>
<td> </td>
<td height="30" colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="submit" value="更 新" onclick="if(_del && !confirm('提示:您选择删除'+_del+'个订单？确定要删除吗？')) return false;" class="btn"/>&nbsp;&nbsp;
<input type="button" value=" 关 闭 " class="btn" onclick="window.parent.cDialog();"/></td>
</tr>
</table>
</form>
<div class="pages"><?php echo $pages;?></div>
<script type="text/javascript">Menuon(2);</script>
<?php include tpl('footer');?>