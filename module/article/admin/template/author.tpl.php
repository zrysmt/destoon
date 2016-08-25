<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
?>
<form action="?">
<div class="tt">作者搜索</div>
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td>
&nbsp;
<input type="text" size="60" name="kw" value="<?php echo $kw;?>" title="关键词"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>&nbsp;
<input type="button" value="关 闭" class="btn" onclick="parent.cDialog();"/>
</td>
</tr>
</table>
</form>
<div class="tt">作者列表</div>
<table cellpadding="3" cellspacing="1" class="tb">
<?php 
foreach($lists as $k=>$v) {
	if($k%5==0) { echo '<tr>';}
?>
<td width="20%">&nbsp;&nbsp;<a href="javascript:TopUseBack('<?php echo $v['author'];?>');"><?php echo $v['author'];?></a></td>
<?php 
	if($k%5==4) { echo '</tr>';}
}
?>
</table>
<script type="text/javascript">
function TopUseBack(v) {
	parent.Dd('author').value = v;
	parent.cDialog();
}
</script>
<?php include tpl('footer');?>