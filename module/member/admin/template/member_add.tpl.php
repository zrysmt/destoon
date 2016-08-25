<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
load('profile.js');
?>
<div class="tt">会员添加</div>
<form method="post" action="?" onsubmit="return Dcheck();">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 会员组</td>
<td>
<input type="radio" name="member[regid]" value="6" id="g_6"onclick="reg(1);" checked/><label for="g_6"> <?php echo $GROUP['6']['groupname'];?></label>&nbsp;&nbsp;&nbsp;&nbsp;
<?php if(is_array($GROUP)) { foreach($GROUP as $k => $v) { ?>
<?php if($k>6 && $v['vip']==0) { ?><input type="radio" name="member[regid]" value="<?php echo $k;?>" id="g_<?php echo $k;?>"onclick="reg(1);"/><label for="g_<?php echo $k;?>"> <?php echo $GROUP[$k]['groupname'];?></label>&nbsp;&nbsp;&nbsp;&nbsp;<?php } ?>
<?php } } ?>
<input type="radio" name="member[regid]" value="5" id="g_5"onclick="reg(0);"/><label for="g_5"> <?php echo $GROUP['5']['groupname'];?></label>
</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 会员登录名</td>
<td><input type="text" size="20" name="member[username]" id="username" onblur="validator('username');"/>&nbsp;<span id="dusername" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 昵称</td>
<td><input type="text" size="20" name="member[passport]" id="passport" onblur="validator('passport');"/> <span class="f_gray">[支持中文]</span>&nbsp;<span id="dpassport" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 登录密码</td>
<td><input type="password" size="20" name="member[password]" id="password" onblur="validator('password');" autocomplete="off"/>&nbsp;<span id="dpassword" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 重复输入密码</td>
<td><input type="password" size="20" name="member[cpassword]" id="cpassword" autocomplete="off"/>&nbsp;<span id="dcpassword" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> Email</td>
<td><input type="text" size="30" name="member[email]" id="email" onblur="validator('email');"/> <span class="f_gray">[不公开]</span>&nbsp;<span id="demail" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 真实姓名</td>
<td><input type="text" size="20" name="member[truename]" id="truename"/>&nbsp;<span id="dtruename" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 性别</td>
<td>
<input type="radio" name="member[gender]" value="1" checked="checked"/> 先生
<input type="radio" name="member[gender]" value="2"/> 女士
</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 所在地区</td>
<td><?php echo ajax_area_select('member[areaid]', '请选择', 0, '', 2);?>&nbsp;<span id="dareaid" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 部门</td>
<td><input type="text" size="20" name="member[department]" id="department"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 职位</td>
<td><input type="text" size="20" name="member[career]" id="career"/></td>
</tr><tr>
<td class="tl"><span class="f_hid">*</span> 手机号码</td>
<td><input type="text" size="20" name="member[mobile]" id="mobile"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> QQ</td>
<td><input type="text" size="20" name="member[qq]" id="qq"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 阿里旺旺</td>
<td><input type="text" size="20" name="member[ali]" id="ali"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> MSN</td>
<td><input type="text" size="30" name="member[msn]" id="msn"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> Skype</td>
<td><input type="text" size="20" name="member[skype]" id="skype"/></td>
</tr>
<?php echo $MFD ? fields_html('<td class="tl">', '<td>', array(), $MFD) : '';?>
</table>
<div id="company_detail">
<div class="tt">公司资料</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 公司名称</td>
<td><input type="text" size="60" name="member[company]" id="company" onblur="validator('company');"/>&nbsp;<span id="dcompany" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 公司类型</td>
<td><?php echo dselect($COM_TYPE, 'member[type]', '请选择', '', 'id="type"', 0);?>&nbsp;<span id="dtype" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 形象图片</td>
<td><input name="member[thumb]" type="text" size="60" id="thumb"/>&nbsp;&nbsp;<span onclick="Dthumb(<?php echo $moduleid;?>,<?php echo $MOD['thumb_width'];?>,<?php echo $MOD['thumb_height'];?>, Dd('thumb').value);" class="jt">[上传]</span>&nbsp;&nbsp;<span onclick="_preview(Dd('thumb').value);" class="jt">[预览]</span>&nbsp;&nbsp;<span onclick="Dd('thumb').value='';" class="jt">[删除]</span><br/>
<span class="f_gray">建议使用LOGO、办公环境等标志性图片，最佳大小为<?php echo $MOD['thumb_width'];?>px*<?php echo $MOD['thumb_height'];?>px</span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 主营行业</td>
<td>
<div id="catesch"></div><div id="cate"><?php echo ajax_category_select('', '', 0, 4, 'size="2" style="height:80px;width:160px;"');?></div>
<input type="button" value=" 添加↓ " class="btn" onclick="addcate(<?php echo $MOD['cate_max'];?>);"/>
<input type="button" value=" ×删除 " class="btn" onclick="delcate();"/>
<?php if($DT['schcate_limit']) { ?><input type="button" class="btn" value=" 搜索 " onclick="schcate(4);"/><?php } ?>
&nbsp;最多可添加 <strong class="f_red"><?php echo $MOD['cate_max'];?></strong> 个主营行业
<br/><select name="cates" id="cates" size="2" style="height:100px;width:380px;margin-top:5px;">
</select>
<input type="hidden" name="member[catid]" value="" id="catid"/><br/>
<span id="dcatid" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 主要经营范围</td>
<td><input type="text" size="80" name="member[business]" id="business"/>&nbsp;<span id="dbusiness" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 经营模式</td>
<td>
<span id="com_mode"><?php echo dcheckbox($COM_MODE, 'member[mode][]', '', 'onclick="check_mode(this,'.$MOD['mode_max'].');"', 0);?></span> <span class="f_gray">(最多可选<?php echo $MOD['mode_max'];?>种)</span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 公司规模</td>
<td><?php echo dselect($COM_SIZE, 'member[size]', '请选择规模', '', '', 0);?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 注册资本</td>
<td><?php echo dselect($MONEY_UNIT, 'member[regunit]', '', '', '', 0);?> <input type="text" size="6" name="member[capital]" id="capital"/> 万</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 公司成立年份</td>
<td><input type="text" size="15" name="member[regyear]" id="regyear"/>&nbsp;<span id="dregyear" class="f_red"></span> <span class="f_gray">(年份，如：2004)</span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 公司地址</td>
<td><input type="text" size="60" name="member[address]" id="address"/>&nbsp;<span id="daddress" class="f_red"></span></td>
</tr>

<tr>
<td class="tl"><span class="f_hid">*</span> 邮政编码</td>
<td><input type="text" size="8" name="member[postcode]" id="postcode"/></td>
</tr>

<tr>
<td class="tl"><span class="f_red">*</span> 公司电话</td>
<td><input type="text" size="20" name="member[telephone]" id="telephone"/>&nbsp;<span id="dtelephone" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 公司传真</td>
<td><input type="text" size="20" name="member[fax]" id="fax"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 公司Email</td>
<td><input type="text" size="30" name="member[mail]" id="mail"/> <span class="f_gray">[公开]</span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 公司网址</td>
<td><input type="text" size="30" name="member[homepage]" id="homepage"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 销售的产品（服务）</td>
<td><input type="text" size="50" name="member[sell]" id="sell"/> <span class="f_gray">多个产品或服务请用'|'号隔开</span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 采购的产品（服务）</td>
<td><input type="text" size="50" name="member[buy]" id="buy"/> <span class="f_gray">多个产品或服务请用'|'号隔开</span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 公司介绍</td>
<td><textarea name="member[content]" id="content" class="dsn"></textarea>
<?php echo deditor($moduleid, 'content', $MOD['editor'], '100%', 300);?><br/><span id="dcontent" class="f_red"></span></td>
</tr>
<?php echo $CFD ? fields_html('<td class="tl">', '<td>', array(), $CFD) : '';?>
</table>
</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td class="tl"><span class="f_hid">*</span> 会员资料是否完整</td>
<td>
<input type="radio" name="member[edittime]" value="1"  checked/> 是&nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" name="member[edittime]" value="0"/> 否&nbsp;&nbsp;
<span class="f_gray">如果选择是，系统将不再提示会员完善资料</span>
</td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value=" 确 定 " class="btn"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="reset" name="reset" value=" 重 置 " class="btn"/></div>
</form>
<script type="text/javascript">
var vid = '';
function validator(id) {
	if(!Dd(id).value) return false;
	vid = id;
	makeRequest('moduleid=<?php echo $moduleid;?>&action=member&job='+id+'&value='+Dd(id).value, AJPath, '_validator');
}

function _validator() {
	if(xmlHttp.readyState==4 && xmlHttp.status==200) {
		Dd('d'+vid).innerHTML = xmlHttp.responseText ? '<img src="'+SKPath+'image/check_error.gif" align="absmiddle"/> '+xmlHttp.responseText : '';
	}
}

function reg(type) {
	if(type) {
		Ds('company_detail');
	} else {
		Dh('company_detail');
	}
}
function Dcheck() {
	if(Dd('username').value == '') {
		Dmsg('请填写会员登录名', 'username');
		return false;
	}
	if(Dd('password').value == '') {
		Dmsg('请填写会员登录密码', 'password');
		return false;
	}
	if(Dd('cpassword').value == '') {
		Dmsg('请重复输入密码', 'cpassword');
		return false;
	}
	if(Dd('password').value != Dd('cpassword').value) {
		Dmsg('两次输入的密码不一致', 'password');
		return false;
	}
	if(Dd('email').value == '') {
		Dmsg('请填写电子邮箱', 'email');
		return false;
	}
	if(Dd('truename').value == '') {
		Dmsg('请填写真实姓名', 'truename');
		return false;
	}
	if(Dd('areaid_1').value == 0) {
		Dmsg('请选择所在地', 'areaid');
		return false;
	}
	<?php echo $MFD ? fields_js($MFD) : '';?>
	if(Dd('g_5').checked == false) {
		<?php echo $CFD ? fields_js($CFD) : '';?>
		if(Dd('company').value == '') {
			Dmsg('请填写公司名称', 'company');
			return false;
		}
		if(Dd('type').value == '') {
			Dmsg('请选择公司类型', 'type');
			return false;
		}
		if(Dd('catid').value.length < 2) {
			Dmsg('请选择公司主营行业', 'catid');
			return false;
		}
		if(Dd('business').value.length < 2) {
			Dmsg('请填写主要经营范围', 'business');
			return false;
		}
		if(Dd('regyear').value.length < 4) {
			Dmsg('请填写公司成立年份', 'regyear');
			return false;
		}
		if(Dd('address').value.length < 2) {
			Dmsg('请填写公司地址', 'address');
			return false;
		}
		if(Dd('telephone').value.length < 6) {
			Dmsg('请填写公司电话', 'telephone');
			return false;
		}
		if(FCKLen('content') < 10) {
			Dmsg('公司介绍最少10字，当前已经输入'+FCKLen('content')+'字', 'content');
			return false;
		}
	}
	return true;
}
</script>
<script type="text/javascript">Menuon(0);</script>
<?php include tpl('footer');?>