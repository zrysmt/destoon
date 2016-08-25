<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?" id="dform" onsubmit="return check();">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="catid" value="<?php echo $catid;?>"/>
<input type="hidden" name="post[catid]" value="<?php echo $catid;?>"/>
<input type="hidden" name="oid" value="<?php echo $oid;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<div class="tt"><?php echo $action=='add' ? '添加' : '修改';?>属性</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 属性名称</td>
<td><input name="post[name]" type="text"  size="30" id="name" value="<?php echo $name;?>"/> <span id="dname" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 添加方式</td>
<td>
<?php
foreach($TYPE as $k=>$v) { 
?>
<input type="radio" name="post[type]" value="<?php echo $k;?>" id="t_<?php echo $k;?>" onclick="c(<?php echo $k;?>)" <?php echo $k == $type ? 'checked' : '';?>/><label for="t_<?php echo $k;?>"> <?php echo $v;?></label>
<?php }?>
</td>
</tr>
<tr style="display:">
<td class="tl" id="v_l"><span class="f_hid">*</span> 默认值</td>
<td><textarea name="post[value]" style="width:98%;height:30px;overflow:visible;" id="value"><?php echo $value;?></textarea><br/><span id="v_r"></span></td>
</tr>
<tr style="display:none;" id="s_c">
<td class="tl"><span class="f_red">*</span> 参与搜索</td>
<td>
<input type="radio" name="post[search]" value="1" id="s_1" <?php echo $search == 1 ? 'checked' : '';?>/><label for="s_1"/> 是</label>&nbsp;&nbsp;
<input type="radio" name="post[search]" value="0" id="s_0" <?php echo $search == 0 ? 'checked' : '';?>/><label for="s_0"/> 否</label>
</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 是否必填</td>
<td>
<input type="radio" name="post[required]" value="1" id="r_1" <?php echo $required == 1 ? 'checked' : '';?>/><label for="r_1"/> 是</label>&nbsp;&nbsp;
<input type="radio" name="post[required]" value="0" id="r_0" <?php echo $required == 0 ? 'checked' : '';?>/><label for="r_0"/> 否</label>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 扩展代码</td>
<td><textarea name="post[extend]" style="width:98%;height:30px;overflow:visible;"><?php echo $extend;?></textarea></td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value=" 确 定 " class="btn"/>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value=" 关 闭 " class="btn" onclick="window.parent.location.reload();"/></div>
</form>
<script type="text/javascript">
function c(id) {
	if(id == 2 || id == 3) {
		Dd('v_l').innerHTML = '<span class="f_red">*</span> 备选值';
		Dd('v_r').innerHTML = '多个选项用 | 分隔，例如 红色|绿色(*)|蓝色 (*)表示默认选中';
		Ds('s_c');
	} else if(id == 0 || id == 1) {
		Dd('v_l').innerHTML = '<span class="f_hid">*</span> 默认值';
		Dd('v_r').innerHTML = '';
		Dh('s_c');
	}
}
c(<?php echo $type;?>);
function check() {
	var l;
	var f;
	f = 'name';
	l = Dd(f).value.length;
	if(l < 1) {
		Dmsg('请填写属性名称', f);
		return false;
	}
	return true;
}
</script>
<script type="text/javascript">Menuon(<?php echo $action=='add' ? 0 : 1;?>);</script>
<?php include tpl('footer');?>