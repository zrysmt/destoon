<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="dir" value="<?php echo $dir;?>"/>
<input type="hidden" name="dfileid" value="<?php echo $fileid;?>"/>
<div class="tt">模板修改</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td width="80">&nbsp;模板路径</td>
<td><?php echo $template_path.$fileid;?>.htm</td>
</tr>
<tr>
<td>&nbsp;模板名称</td>
<td><input type="text" size="20" name="name" value="<?php echo $name;?>"/> <span class="f_gray">可以为中文</span></td>
</tr>
<tr>
<td>&nbsp;文件名</td>
<td><input type="text" size="20" name="fileid" value="<?php echo $fileid;?>"/>.htm <span class="f_gray">只能为小写字母、数字、中划线、下划线</span></td>
</tr>
<tr>
<td colspan="2">
<textarea name="content" id="content" style="width:98%;height:300px;font-family:Fixedsys,verdana;overflow:visible;"><?php echo $content;?></textarea>
</td>
</tr>
<tr>
<td colspan="2"><input type="checkbox" name="backup" value="1"/> 保存时，创建一个备份文件&nbsp;&nbsp;<input type="submit" name="submit" value="保 存" class="btn"/>&nbsp;&nbsp;<input type="button" value="预 览" class="btn" onclick="Preview();"/>&nbsp;&nbsp;<input type="button" value="返 回" class="btn" onclick="if(window.parent.location.href.indexOf('&')==-1){window.history.go(-1);}else{window.parent.cDialog();}"/>&nbsp;&nbsp;<input type="reset" value="重 置" class="btn"/>&nbsp;&nbsp;<span id="FR" style="display:none;"><input type="button" value="替 换" class="btn" onclick="RP();"/>&nbsp;&nbsp;<input type="button" value="查 找" class="btn" onclick="FD();"/></span></td>
</tr>
</table>
</form>
<br/>
<form method="post" action="?file=<?php echo $file;?>&action=preview&dir=<?php echo $dir;?>" target="_blank" id="p">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="preview"/>
<input type="hidden" name="dir" value="<?php echo $dir;?>"/>
<input type="hidden" id="pcontent" name="content" value=""/>
</form>
<script type="text/javascript">
if(isIE) Ds('FR');
function FD(ff) {
	var f = ff ? ff : prompt('请填写需要查找的字符', '');
	if(f) {
		var r = Dd('content').createTextRange();
		var b = r.findText(f);
		if(b) { 
			r.select(); return b; 
		} else {
			if(!ff) confirm('没有查找到 '+f);
		}
	}
}
function RP() {
	var f = prompt('请填写需要查找的字符', '');
	var p = prompt('请填写需要替换的字符', '');
	if(f && p) {
		while(FD(f)) {
			Dd('content').focus();
			var r = document.selection.createRange(); 
			if(r.text.length <= 0) continue;
			r.text = p;
		}
		confirm(f+'已经替换为'+p);
		FD(p);
	}
}
function Preview() {
	if(Dd('content').value == '') {
		Dtip('模板内容为空');
	} else {
		Dd('pcontent').value = Dd('content').value;
		Dd('p').submit();
	}
}
</script>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>