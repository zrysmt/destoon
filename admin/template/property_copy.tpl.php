<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?" onsubmit="return check();">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="catid" value="<?php echo $catid;?>"/>
<div class="tt">属性复制</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 复制方式</td>
<td>
	<input type="radio" name="type" value="1" id="t1" onclick="Ds('f1');Dh('f2');" checked/> <label for="t1">批量</label>&nbsp;&nbsp;
	<input type="radio" name="type" value="0" id="t2" onclick="Ds('f2');Dh('f1');"/> <label for="t2">单项</label>
</td>
</tr>
<tr id="f1" style="display:;">
<td class="tl"><span class="f_red">*</span> 来源分类</td>
<td>
<?php echo category_select('fromid', '选择分类', $CAT['parentid'], $CAT['moduleid'], 'size="2" style="width:200px;height:130px;"');?>
<span id="dfromid" class="f_red"></span>
</td>
</tr>
<tr id="f2" style="display:none;">
<td class="tl"><span class="f_red">*</span> 属性ID</td>
<td><input type="text" size="10" name="pid" id="pid"/><span id="dpid" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 同名过滤</td>
<td>
	<input type="radio" name="name" value="1" id="n1" checked/> <label for="n1">是</label>&nbsp;&nbsp;
	<input type="radio" name="name" value="0" id="n2"/> <label for="n2">否</label>
</td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value="复 制" class="btn"/>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value=" 关 闭 " class="btn" onclick="window.parent.location.reload();"/></div>
</form>
<script type="text/javascript">
function check() {
	if(Dd('t1').checked) {
		if(Dd('catid_1').value==0) {
			Dmsg('请选择来源分类', 'fromid');
			return false;
		}
		if(Dd('catid_1').value==<?php echo $catid;?>) {
			Dmsg('来源分类不能与当前分类相同', 'fromid');
			return false;
		}
	} else {
		if(Dd('pid').value=='') {
			Dmsg('请填写属性ID', 'pid');
			return false;
		}
	}
	return true;
}
</script>
<script type="text/javascript">Menuon(2);</script>
<?php include tpl('footer');?>