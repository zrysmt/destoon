<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<style type="text/css">
.t1 {width:200px;padding:3px 10px 3px 10px;color:#006699;}
.t2 {width:100px;padding:3px 10px 3px 10px;color:green;text-align:center;}
.t2 span {color:red;}
.t3 {padding:5px 10px 5px 10px;line-height:180%;}
</style>
<div class="tt">系统体检</div>
<table cellpadding="2" cellspacing="1" class="tb" style="line-height:200%;">
<tr>
<th>项目</th>
<th>值</th>
<th>说明</th>
</tr>
<?php
	if(strpos(get_env('self'), '/admin.php') !== false) {
?>
<tr>
<td class="t1">后台登录地址</td>
<td class="t2"><span>admin.php</span></td>
<td class="t3">
如果管理帐号泄漏，后台容易遭受攻击，为了系统安全，请修改根目录admin.php的文件名
</td>
</tr>
<?php } ?>
</tr>
<?php
	if(is_dir(DT_ROOT.'/5.0')) {
?>
<tr>
<td class="t1">升级备份目录</td>
<td class="t2"><span>5.0</span></td>
<td class="t3">
如果已经升级成功，建议将备份目录删除或备份至非站点目录
</td>
</tr>
<?php } ?>
<?php
	if(is_dir(DT_ROOT.'/upgrade')) {
?>
<tr>
<td class="t1">升级目录</td>
<td class="t2"><span>upgrade</span></td>
<td class="t3">
如果已经升级成功，建议将升级目录删除
</td>
</tr>
<?php } ?>
<?php
	if(is_dir(DT_ROOT.'/install')) {
?>
<tr>
<td class="t1">安装目录</td>
<td class="t2"><span>install</span></td>
<td class="t3">
如果已经安装成功，建议将安装目录删除
</td>
</tr>
<?php } ?>
<?php
	$D = is_write(DT_ROOT.'/file/') && is_write(DT_ROOT.'/file/cache/') && is_write(DT_ROOT.'/file/cache/tpl/') && is_write(DT_ROOT.'/file/upload/');
?>
<tr>
<td class="t1">file目录是否可写</td>
<td class="t2"><?php echo $D ? '可写' : '<span>不可写</span>';?></td>
<td class="t3">
file目录及所有子目录和子文件都必须设置可写，否则会出现以下问题：<br/>
系统缓存无法更新<br/>
后台无法登录<br/>
登录后台不显示密码输入框<br/>
前台页面无法正常显示<br/>
文件无法上传<br/>
</td>
</tr>
<?php
	$S = 0;
	foreach($MODULE as $v) {
		if($v['moduleid'] > 1 && $v['domain']) $S = 1;
	}
	if($CFG['com_domain']) $S = 1;
	if(!$S && $DT['city']) {
		$r = $db->get_one("SELECT areaid FROM {$DT_PRE}city WHERE domain<>''");
		if($r) $S = 1;
	}
	$D = $CFG['cookie_domain'];
	if($S) {
?>
<tr>
<td class="t1">Cookie作用域</td>
<td class="t2"><?php echo $D ? $D : '<span>未设置</span>';?></td>
<td class="t3">
当前系统使用过二级域名，未设置Cookie作用域会出现以下问题：<br/>
验证码/验证问题校验错误<br/>
会员登录状态显示错误<br/>
评论不显示<br/>
</td>
</tr>
<?php } ?>

<?php
	if($CFG['skin'] == $CFG['template'] && $CFG['template'] != 'default') {
?>
<tr>
<td class="t1">模板和风格目录</td>
<td class="t2"><span>同名</span></td>
<td class="t3">
模板和风格目录同名可能导致模板被下载，建议模板和风格使用不相同的目录名称
</td>
</tr>
<?php } ?>

<?php
	$dc->set('destoon', 'b2b', 3600);
	$D = $dc->get('destoon') == 'b2b' ? 1 : 0;
?>
<tr>
<td class="t1">系统缓存测试</td>
<td class="t2"><?php echo $D ? '成功' : '<span>失败</span>';?></td>
<td class="t3">
当前缓存类型为<?php echo $CFG['cache'];?>，<?php echo $D ? '缓存运行正常' : ($CFG['cache'] == 'file' ? '请检查file目录是否可写' : '请<a href="?file=setting&tab=2" target="_blank" class="t">立即更换</a>可用的缓存类型');?>
</td>
</tr>

<?php
	$D = ini_get('allow_url_fopen');
?>
<tr>
<td class="t1">允许使用URL打开文件<br/>allow_url_fopen</td>
<td class="t2"><?php echo $D ? 'On' : '<span>Off</span>';?></td>
<td class="t3">
建议设置为On，否则会出现以下问题：<br/>
远程图片无法保存<br/>
网络图片无法上传<br/>
一键登录无法登录<br/>
</td>
</tr>

<?php
	$D = ini_get('memory_limit');
?>
<tr>
<td class="t1">程序最多允许使用内存量<br/>memory_limit</td>
<td class="t2"><?php echo $D;?></td>
<td class="t3">
内存设置过小会导致部分操作无法进行，显示空白
</td>
</tr>

<?php
	$D = ini_get('post_max_size');
?>
<tr>
<td class="t1">POST最大字节数<br/>post_max_size</td>
<td class="t2"><?php echo $D;?></td>
<td class="t3">
大于<?php echo $D;?>的文件无法上传<br/>
大于<?php echo $D;?>的信息无法提交
</td>
</tr>

<?php
	$D = ini_get('upload_max_filesize');
?>
<tr>
<td class="t1">允许最大上传文件<br/>upload_max_filesize</td>
<td class="t2"><?php echo $D;?></td>
<td class="t3">
大于<?php echo $D;?>的文件无法上传
</td>
</tr>

<?php
	$D = function_exists('fsockopen');
?>
<tr>
<td class="t1">fsockopen</td>
<td class="t2"><?php echo $D ? '支持' : '<span>不支持</span>';?></td>
<td class="t3">
如果不支持，将会出现以下问题：<br/>
充值接口无法使用<br/>
手机短信无法发送<br/>
电子邮件无法发送<br/>
一键登录无法登录<br/>

</td>
</tr>

<?php
	$D = function_exists('curl_init');
?>
<tr>
<td class="t1">curl</td>
<td class="t2"><?php echo $D ? '支持' : '<span>不支持</span>';?></td>
<td class="t3">
如果不支持，将会出现以下问题：<br/>
一键登录无法登录<br/>
</td>
</tr>

<?php
	$D = function_exists('json_decode');
?>
<tr>
<td class="t1">json</td>
<td class="t2"><?php echo $D ? '支持' : '<span>不支持</span>';?></td>
<td class="t3">
如果不支持，将会出现以下问题：<br/>
一键登录无法登录<br/>
</td>
</tr>
<?php
	$D = function_exists('openssl_sign');
?>
<tr>
<td class="t1">OpenSSL</td>
<td class="t2"><?php echo $D ? '支持' : '<span>不支持</span>';?></td>
<td class="t3">
如果不支持，将会出现以下问题：<br/>
快钱接口无法使用<br/>
无法使用Gmail邮箱SMTP发信<br/>
</td>
</tr>
</table>
</div>
<script type="text/javascript">Menuon(0);</script>
<?php include tpl('footer');?>