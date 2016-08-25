<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<?php if($user) { ?>
<div class="tt">会员信息</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td class="tl"><span class="f_hid">*</span> 会员名</td>
<td><a href="javascript:_user('<?php echo $username;?>');" class="t"><?php echo $username;?></a></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 公司名</td>
<td><a href="javascript:_user('<?php echo $username;?>');" class="t"><?php echo $user['company'];?></a></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 会员组</td>
<td class="f_blue"><?php echo $GROUP[$user['groupid']]['groupname'];?></td>
</tr>
</table>
<?php } ?>
<div class="tt">升级申请</div>
<table cellpadding="2" cellspacing="1" class="tb">
<tr>
<td class="tl"><span class="f_hid">*</span> 会员组</td>
<td class="f_red"><?php echo $GROUP[$groupid]['groupname'];?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 公司名</td>
<td><?php echo $company;?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 联系人</td>
<td><?php echo $truename;?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 联系电话</td>
<td><?php echo $telephone;?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 联系手机</td>
<td><?php echo $mobile;?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> Email</td>
<td><?php echo $email;?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> QQ</td>
<td><?php echo $qq ? im_qq($qq).' '.$qq : '';?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 阿里旺旺</td>
<td><?php echo $ali ? im_ali($ali).' '.$ali : '';?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> MSN</td>
<td><?php echo $msn ? im_msn($msn).' '.$msn : '';?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> Skype</td>
<td><?php echo $skype ? im_skype($skype).' '.$skype : '';?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 附言</td>
<td><?php echo nl2br($content);?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 已付金额</td>
<td class="f_b f_red"><?php echo $amount;?></td>
</tr>
<?php if($promo_code) { ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 优惠码</td>
<td><?php echo $promo_code;?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 优惠幅度</td>
<td class="f_blue"><?php echo $promo_amount;?> <?php echo $promo_type ? '天' : $DT['money_unit'];?></td>
</tr>
<?php } ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 申请时间</td>
<td><?php echo $addtime;?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 申请IP</td>
<td><?php echo $ip;?> - <?php echo ip2area($ip);?></td>
</tr>
</table>
<div class="tt">申请受理</div>
<form method="post" action="?" id="dform" onsubmit="return check();">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<table cellpadding="2" cellspacing="1" class="tb">
<?php if($status == 2) { ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 受理状态</td>
<td>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<input type="radio" name="post[status]" value="3" id="s_3" onclick="S(this.value);"/><label for="s_3"> 通过</label>
<input type="radio" name="post[status]" value="2" id="s_2" onclick="S(this.value);" checked/><label for="s_2"> 待审</label>
<input type="radio" name="post[status]" value="1" id="s_1" onclick="S(this.value);"/><label for="s_1">  拒绝</label>
</td>
</tr>
<tbody id="pass" style="display:none;">
<?php if($user && $fee) { ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 会员组年费</td>
<td class="f_b f_red"><?php echo $fee;?> <?php echo $DT['money_unit'];?></td>
</tr>
<?php if($promo_amount && $promo_type == 1) { ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 优惠幅度</td>
<td class="f_blue"><?php echo $promo_amount;?> 天</td>
</tr>
<?php } ?>
<?php if($promo_amount && $promo_type == 0) { ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 优惠幅度</td>
<td class="f_blue"><?php echo $promo_amount;?> <?php echo $DT['money_unit'];?></td>
</tr>
<?php } ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 会员已支付</td>
<td class="f_blue"><?php echo $amount;?> <?php echo $DT['money_unit'];?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 需支付金额</td>
<td class="f_blue"><input type="text" name="post[pay]" size="5" value="<?php echo $pay;?>"/> <?php echo $DT['money_unit'];?>&nbsp;&nbsp;&nbsp;<a href="?moduleid=2&file=record&action=add&username=<?php echo $username;?>" class="t" target="_blank">[<?php echo $DT['money_name'];?>管理]</a>&nbsp;&nbsp;<span class="f_gray">(会员当前账户余额:<?php echo $user['money'];?><?php echo $DT['money_unit'];?>)</span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 服务有效期</td>
<td><?php echo dcalendar('post[fromtime]', $fromtime);?> 至 <?php echo dcalendar('post[totime]', $totime);?></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 企业资料是否通过认证</td>
<td>
<input type="radio" name="post[validated]" value="1"/> 是&nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" name="post[validated]" value="0" checked/> 否
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 认证名称或机构</td>
<td><input type="text" name="post[validator]" size="30"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 认证日期</td>
<td><?php echo dcalendar('post[validtime]', $fromtime);?></td>
</tr>
<?php } ?>
</tbody>
<tbody id="send" style="display:none;">
<?php if($user) { ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 发送通知</td>
<td>
<input type="radio" name="post[message]" value="1"/> 是&nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" name="post[message]" value="0" checked/> 否
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 通知内容</td>
<td>
<textarea name="post[content]" rows="4" cols="60" id="content"></textarea>
<textarea id="c_3" style="display:none;">
尊敬的<?php echo $truename;?>:
您的<?php echo $GROUP[$groupid]['groupname'];?>升级申请已经通过。
</textarea>
<textarea id="c_1" style="display:none;">
尊敬的<?php echo $truename;?>:
您的<?php echo $GROUP[$groupid]['groupname'];?>升级申请失败。
原因如下：

</textarea>
</td>
</tr>
<?php } ?>
</tbody>
<tr>
<td class="tl"><span class="f_hid">*</span> 受理备注</td>
<td><textarea name="post[note]" rows="4" cols="60"><?php echo $note;?></textarea></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 注意事项</td>
<td class="f_gray">
- 如果通过申请，系统会尝试扣除需支付金额，如果会员余额不足，将操作失败<br/>
- 如果拒绝申请，系统会返还会员已支付的金额<br/>
</td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value=" 确 定 " class="btn">&nbsp;&nbsp;&nbsp;&nbsp;<input type="reset" name="reset" value=" 重 置 " class="btn"/></div>
</form>
<?php } else { ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 受理状态</td>
<td><?php echo $status == 1 ? '已拒绝' : '已通过';?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 受理通知</td>
<td><?php echo $message == 1 ? '已发送' : '未通知';?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 受理人</td>
<td><?php echo $editor;?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 受理时间</td>
<td><?php echo $edittime;?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 备注</td>
<td><?php echo $note;?></td>
</tr>
</table>
<?php } ?>
<script type="text/javascript">
function check() {
	return confirm('确定要执行此操作吗？');
}
function S(i) {
	if(i==1) {
		Dh('pass');Ds('send');
		try{Dd('content').value=Dd('c_1').value;}catch(e){}
	} else if(i==2) {
		Dh('pass');Dh('send');
	} else if(i==3) {
		Ds('pass');Ds('send');
		try{Dd('content').value=Dd('c_3').value;}catch(e){}
	}
}
</script>
<script type="text/javascript">Menuon(<?php echo $menuon[$status];?>);</script>
<?php include tpl('footer');?>