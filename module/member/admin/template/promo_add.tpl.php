<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?" id="dform" onsubmit="return check();">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<div class="tt">生成优惠码</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td class="tl"><span class="f_hid">*</span> 优惠码前缀</td>
<td><input name="prefix" id="prefix" type="text" size="20" value="<?php echo $prefix;?>"/> <a href="javascript:" onclick="window.location.reload();" class="t">[刷新]</a> <span id="dprefix" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 优惠码组成</td>
<td><input name="number_part" id="number_part" type="text" size="50" value="0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"/>
<select onchange="Dd('number_part').value=this.value">
<option value="0123456789">数字</option>
<option value="abcdefghijklmnopqrstuvwxyz">小写字母</option>
<option value="ABCDEFGHIJKLMNOPQRSTUVWXYZ">大写字母</option>
<option value="0123456789abcdefghijklmnopqrstuvwxyz">数字和小写字母</option>
<option value="0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ" selected>数字和大写字母</option>
</select>
<span id="dnumber_part" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 优惠码长度</td>
<td><input name="number_length" id="number_length" type="text" size="20" value="10"/> 推荐8-15位之间 <span id="dnumber_length" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 优惠用途</td>
<td>
<input type="radio" name="type" value="0" id="t_0" onclick="Dd('am').innerHTML='<?php echo $DT['money_unit'];?>';" checked/><label for="t_0"> 抵金额</label>&nbsp;&nbsp;
<input type="radio" name="type" value="1" id="t_1" onclick="Dd('am').innerHTML='天 <span class=f_gray>自使用时间开始计算</span>';"/><label for="t_1"> 有效期</label>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 优惠额度</td>
<td><input name="amount" id="amount" type="text" size="5" value="30"/> <span id="am"><?php echo $DT['money_unit'];?></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 有效期至</td>
<td><?php echo dcalendar('totime', $totime);?> <span id="dtotime" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 重复使用</td>
<td><input type="radio" name="reuse" value="1" id="r_1"/><label for="r_1"> 是</label>&nbsp;&nbsp;
<input type="radio" name="reuse" value="0" id="r_0" checked/><label for="r_0"> 否</label></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 生成数量</td>
<td><input name="total" id="total" type="text" size="5" value="100"/> <span id="dtotal" class="f_red"></span></td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value=" 确 定 " class="btn"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="reset" name="reset" value=" 重 置 " class="btn"/></div>
</form>
<script type="text/javascript">
function check() {
	return confirm('确定要生成吗？');
}
</script>
<script type="text/javascript">Menuon(0);</script>
<?php include tpl('footer');?>