<?php 
defined('DT_ADMIN') or exit('Access Denied');
$edition = edition(1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=<?php echo DT_CHARSET; ?>" />
<meta name="robots" content="noindex,nofollow"/>
<title>管理员登录 - Powered By Destoon B2B <?php echo $edition;?></title>
<meta name="generator" content="Destoon B2B,www.destoon.com"/>
<link rel="stylesheet" href="admin/image/login.css" type="text/css" />
<script type="text/javascript" src="<?php echo DT_STATIC;?>lang/<?php echo DT_LANG;?>/lang.js"></script>
<script type="text/javascript" src="<?php echo DT_STATIC;?>file/script/config.js"></script>
<script type="text/javascript" src="<?php echo DT_STATIC;?>file/script/jquery.js"></script>
<script type="text/javascript" src="<?php echo DT_STATIC;?>file/script/common.js"></script>
</head>
<body>
<noscript><br/><br/><br/><center><h1>您的浏览器不支持JavaScript,请更换支持JavaScript的浏览器</h1></center></noscript>
<noframes><br/><br/><br/><center><h1>您的浏览器不支持框架,请更换支持框架的浏览器</h1></center></noframes>
<table cellpadding="0" cellspacing="0" width="400"  align="center">
<tr>
<td height="100"></td>
</tr>
<tr>
<td>
	<div class="msg">
		<div class="head"><div class="mr">&nbsp;</div><div class="ml">管理员登录 IP:<?php echo $DT_IP;?></div></div>
		<div class="content">
		<form method="post" action="?"  onsubmit="return Dcheck();">
		<input type="hidden" name="file" value="<?php echo $file;?>"/>
		<input name="forward" type="hidden" value="<?php echo $forward;?>"/>
		<table cellpadding="2" cellspacing="1" width="100%">
		<tr>
		<td colspan="2" height="50"><a href="http://www.destoon.com/" target="_blank"><img src="admin/image/spacer.gif" width="290" height="30" title="Powered By www.destoon.com" alt=""/></a></td>
		</tr>
		<tr>
		<td height="20" colspan="2" class="tip"><img src="admin/image/lock.gif"/> 您尚未登录或登录超时，请登录后继续操作...</td>
		</tr>
		<tr>
		<td>&nbsp;户&nbsp;&nbsp;&nbsp;名</td>
		<td><input name="username" type="text" id="username" class="inp" style="width:140px;" value="<?php echo $username;?>"/></td>
		</tr>
		<tr>
		<td>&nbsp;密&nbsp;&nbsp;&nbsp;码</td>
		<td><input name="password" type="password" class="inp" style="width:140px;" id="password"<?php if(isset($password)) { ?> value="<?php echo $password;?>"<?php } ?>/>&nbsp;
		<script type="text/javascript" src="<?php echo DT_STATIC;?>file/script/keyboard.js"></script>
		<img src="<?php echo DT_STATIC;?>file/image/keyboard.gif" title="密码键盘" alt="" class="c_p" onclick="_k('password', 'kb', this);"/>
		<div id="kb" style="display:none;"></div>
		<script type="text/javascript" src="<?php echo DT_STATIC;?>file/script/md5.js"></script>
		<script type="text/javascript">init_md5();</script>
		</td>
		</tr>
		<?php if($DT['captcha_admin']) { ?>
		<tr>
		<td>&nbsp;验证码</td>
		<td><?php include template('captcha', 'chip');?></td>
		</tr>
		<?php } ?>
		<tr>
		<td></td>
		<td><input type="submit" name="submit" value="登 录" class="btn" tabindex="4" id="submit"/>&nbsp;&nbsp;<input type="button" value="退 出" class="btn" onclick="top.Go('<?php echo DT_PATH;?>');"/>
		</td>
		</tr>
		</table>
		</form>
		</div>
	</div>
	<?php if(strpos(get_env('self'), '/admin.php') !== false || is_file(DT_ROOT.'/admin.php')) { ?>
	<div style="margin:10px 40px 0 40px;border:#FF8D21 1px solid;background:#FFFFDD;padding:8px;"><img src="admin/image/notice.gif" align="absmiddle"/> 提示：为了系统安全，请立即修改后台文件名 <a href="http://help.destoon.com/use/34.html" target="_blank" style="color:#006699;">帮助&#187;</a></div>
	<script type="text/javascript">
	try{Dd('username').disabled=true;Dd('password').disabled=true;Dd('submit').disabled=true;$('.c_p').hide();<?php if($DT['captcha_admin']) { ?>Dd('captcha').disabled=true;<?php } ?>}catch(e){}
	</script>
	<?php } ?>
</td>
</tr>
</table>
<script type="text/javascript">
if(Dd('username').value == '') {
	Dd('username').focus();
} else {
	Dd('password').focus();
}
function Dcheck() {
	if(Dd('username').value == '') {
		confirm('请填写会员名');
		Dd('username').focus();
		return false;
	}
	if(Dd('password').value == '') {
		confirm('请填写密码');
		Dd('password').focus();
		return false;
	}
	<?php if($DT['captcha_admin']) { ?>
	if(!is_captcha(Dd('captcha').value)) {
		confirm('请填写验证码');
		Dd('captcha').focus();
		return false;
	}
	<?php } ?>
	return true;
}
</script>
</body>
</html>