<?php
defined('IN_DESTOON') or exit('Access Denied');
include IN_ROOT.'/header.tpl.php';
?>
<div class="head">
	<div>
		<strong>提示信息</strong><br/>
		如果对此提示信息有疑问，请访问www.destoon.com
	</div>
</div>
<div class="body">
<div>
	<br/><br/>
	<fieldset>
		<legend>&nbsp;提示信息&nbsp;</legend>
		<div><?php echo $msg;?></div>
	</fieldset>
	<br/><br/>
</div>
</div>
<div class="foot">
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td width="215">
<div class="progress">
<div id="progress"></div>
</div>
</td>
<td id="percent"></td>
<td height="40" align="right">
<input type="button" value=" 返回 " onclick="history.back(-1);"/>&nbsp;
<input type="button" value=" 关闭 " onclick="window.close();"/>&nbsp;
<?php
include IN_ROOT.'/footer.tpl.php';
?>