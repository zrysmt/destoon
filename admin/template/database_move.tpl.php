<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?" onsubmit="return check();">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<div class="tt">数据互转</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 转移类型</td>
<td class="c_p">
<table cellpadding="3" cellspacing="3" width="100%">
<tr onclick="Dd('t_3').checked=true;">
<td><input type="radio" name="type" value="3" id="t_3"/></td>
<td>
<select name="afid" id="afid">
<option value="0">文章</option>
<?php
foreach($MODULE as $m) {
	if($m['module'] == 'article') {
		echo '<option value="'.$m['moduleid'].'">'.$m['name'].'</option>';
	}
}
?>
</select>
&rarr;
<select name="atid" id="atid" onchange="loadc(this.value);">
<option value="0">文章</option>
<?php
foreach($MODULE as $m) {
	if($m['module'] == 'article') {
		echo '<option value="'.$m['moduleid'].'">'.$m['name'].'</option>';
	}
}
?>
</select>
</td>
</tr>
<tr onclick="Dd('t_4').checked=true;">
<td><input type="radio" name="type" value="4" id="t_4"/></td>
<td>
<select name="ifid" id="ifid">
<option value="0">信息</option>
<?php
foreach($MODULE as $m) {
	if($m['module'] == 'info') {
		echo '<option value="'.$m['moduleid'].'">'.$m['name'].'</option>';
	}
}
?>
</select>
&rarr;
<select name="itid" id="itid" onchange="loadc(this.value);">
<option value="0">信息</option>
<?php
foreach($MODULE as $m) {
	if($m['module'] == 'info') {
		echo '<option value="'.$m['moduleid'].'">'.$m['name'].'</option>';
	}
}
?>
</select>
</td>
</tr>
<tr onclick="Dd('t_1').checked=true;loadc(6);">
<td width="20"><input type="radio" name="type" value="1" id="t_1"/></td>
<td><?php echo $MODULE[5]['name'];?> &rarr; <?php echo $MODULE[6]['name'];?></td>
</tr>
<tr onclick="Dd('t_2').checked=true;loadc(5);">
<td><input type="radio" name="type" value="2" id="t_2"/></td>
<td><?php echo $MODULE[6]['name'];?> &rarr; <?php echo $MODULE[5]['name'];?></td>
</tr>
<tr onclick="Dd('t_5').checked=true;loadc(16);">
<td width="20"><input type="radio" name="type" value="5" id="t_5" checked/></td>
<td><?php echo $MODULE[5]['name'];?> &rarr; <?php echo $MODULE[16]['name'];?></td>
</tr>
<tr onclick="Dd('t_6').checked=true;loadc(5);">
<td width="20"><input type="radio" name="type" value="6" id="t_6"/></td>
<td><?php echo $MODULE[16]['name'];?> &rarr; <?php echo $MODULE[5]['name'];?></td>
</tr>
</table>
</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 转移条件</td>
<td class="f_gray">&nbsp;
<input type="text" name="condition" value="" size="80" id="condition"/>
<br/>
&nbsp;- 如果转移单条信息，则直接填写信息ID，例如 <span class="f_blue">123</span><br/>
&nbsp;- 如果转移多条信息，则填用,分隔信息ID，例如 <span class="f_blue">123,124,125</span> (结尾和开头不需要,)<br/>
&nbsp;- 可直接写SQL调用条件，必须以and开头<br/>
&nbsp;&nbsp;例如 <span class="f_blue">and catid=123</span> 表示调用分类ID为123的信息<br/>
&nbsp;&nbsp;例如 <span class="f_blue">and itemid>0</span> 表示调用源模块所有信息<br/>
&nbsp;&nbsp;例如 <span class="f_blue">and price>0</span> 表示调用有价格的信息(一般为供应)<br/>
</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 新分类</td>
<td>&nbsp;
<?php echo ajax_category_select('catid', '请选择', 0, 16, 'size="2" style="height:120px;width:180px;"');?>
<?php tips('数据将被转移到此分类下');?>
</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 删除源数据</td>
<td>&nbsp;
<input type="radio" name="delete" value="1" id="d_1"/> 是&nbsp;&nbsp;&nbsp;
<input type="radio" name="delete" value="0" id="d_0" checked/> 否
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 注意事项</td>
<td class="f_gray">
&nbsp;- 转移成功后请进入目标模型管理，更新新转移的信息，如果模型内容设置生成HTML，需要生成一下<br/>
&nbsp;- 可能需要按信息ID降序搜索才可以看到新转移的信息<br/>
&nbsp;- 如果待转移的数据较多，请设置条件分批转移，以免转移程序卡死<br/>
</td>
</tr>
<tr>
<td class="tl">&nbsp;</td>
<td>&nbsp;<input type="submit" name="submit" value="执 行" class="btn"/></td> 
</tr>
</table>
</form>
<script type="text/javascript">
function loadc(i) {
	if(i) {
		category_moduleid[1] = i;
		load_category(0, 1);
	}
}
function getid() {
	var mid;
	if(Dd('t_1').checked) {
		mid = 6;
	} else if(Dd('t_2').checked) {
		mid = 5;
	} else if(Dd('t_5').checked) {
		mid = 16;
	} else if(Dd('t_6').checked) {
		mid = 5;
	} else if(Dd('t_3').checked) {
		mid = Dd('atid').value;
		if(mid == 0) {
			alert('请选择文章目标模型');
			Dd('atid').focus();
			return;
		}
	} else if(Dd('t_4').checked) {
		mid = Dd('itid').value;
		if(mid == 0) {
			alert('请选择信息目标模型');
			Dd('itid').focus();
			return;
		}
	} else {
		alert('请选择转移类型');
		return;
	}
	window.open('?file=category&mid='+mid);
}
function check() {
	if(Dd('t_1').checked) {
		//
	} else if(Dd('t_2').checked) {
		//
	} else if(Dd('t_5').checked) {
		//
	} else if(Dd('t_6').checked) {
		//
	} else if(Dd('t_3').checked) {
		if(Dd('afid').value == 0) {
			alert('请选择文章来源模型');
			Dd('afid').focus();
			return false;
		}
		if(Dd('atid').value == 0) {
			alert('请选择文章目标模型');
			Dd('atid').focus();
			return false;
		}
		if(Dd('afid').value == Dd('atid').value) {
			alert('文章来源模型和目标模型不能相同');
			Dd('atid').focus();
			return false;
		}
	} else if(Dd('t_4').checked) {
		if(Dd('ifid').value == 0) {
			alert('请选择信息来源模型');
			Dd('ifid').focus();
			return false;
		}
		if(Dd('itid').value == 0) {
			alert('请选择信息目标模型');
			Dd('itid').focus();
			return false;
		}
		if(Dd('ifid').value == Dd('itid').value) {
			alert('信息来源模型和目标模型不能相同');
			Dd('itid').focus();
			return false;
		}
	} else {
		alert('请选择转移类型');
		return false;
	}
	if(Dd('condition').value.length < 1) {		
		alert('请填写转移条件');
		Dd('condition').focus();
		return false;
	}
	if(Dd('catid_1').value == 0) {		
		alert('请选择新分类');
		return false;
	}
	return confirm('确定要转移吗？此操作将不可恢复');
}
</script>
<script type="text/javascript">Menuon(5);</script>
<?php include tpl('footer');?>