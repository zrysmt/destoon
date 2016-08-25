<?php defined('IN_DESTOON') or exit('Access Denied');?><?php if($user_status == 3) { ?>
<ul>
<?php if($member) { ?>
<li class="f_b t_c" style="padding:3px 0 5px 0;font-size:14px;"><a href="<?php echo $member['linkurl'];?>" target="_blank" title="<?php echo $member['company'];?>&#10;<?php echo $member['mode'];?>"><?php echo $member['company'];?></a></li>
<?php if($member['vip']) { ?>
<li class="f_orange" style="padding:5px 0 0 12px;"><img src="<?php echo DT_SKIN;?>image/vip_<?php echo $member['vip'];?>.gif" alt="<?php echo VIP;?>" title="<?php echo VIP;?>:<?php echo $member['vip'];?>级" align="absmiddle"/> [<?php echo VIP;?>第<?php echo vip_year($member['fromtime']);?>年] 指数:<?php echo $member['vip'];?></li>
<?php } ?>
<?php if($member['validated'] || $member['vcompany'] || $member['vtruename'] || $member['vbank'] || $member['vmobile'] || $member['vemail']) { ?>
<li class="f_green" style="padding-top:6px;padding-bottom:6px;">
<?php if($member['vcompany']) { ?>&nbsp;<img src="<?php echo $MODULE['2']['linkurl'];?>image/v_company.gif" width="16" height="16" align="absmiddle" title="通过工商认证"/><?php } ?>
<?php if($member['vtruename']) { ?>&nbsp;<img src="<?php echo $MODULE['2']['linkurl'];?>image/v_truename.gif" width="16" height="16" align="absmiddle" title="通过实名认证"/><?php } ?>
<?php if($member['vbank']) { ?>&nbsp;<img src="<?php echo $MODULE['2']['linkurl'];?>image/v_bank.gif" width="16" height="16" align="absmiddle" title="通过银行帐号认证"/><?php } ?>
<?php if($member['vmobile']) { ?>&nbsp;<img src="<?php echo $MODULE['2']['linkurl'];?>image/v_mobile.gif" width="16" height="16" align="absmiddle" title="通过手机认证"/><?php } ?>
<?php if($member['vemail']) { ?>&nbsp;<img src="<?php echo $MODULE['2']['linkurl'];?>image/v_email.gif" width="16" height="16" align="absmiddle" title="通过邮件认证"/><?php } ?>
<?php if($member['validated']) { ?>&nbsp;<img src="<?php echo DT_SKIN;?>image/check_right.gif" align="absmiddle"/> 通过<?php echo $member['validator'];?>认证<?php } ?>
&nbsp;<a href="<?php echo userurl($member['username'], 'file=credit');?>">[诚信档案]</a>
</li>
<?php } ?>
<?php if($member['deposit']) { ?>
<li class="f_green">已缴纳 <strong><?php echo $member['deposit'];?></strong> <?php echo $DT['money_unit'];?>保证金</li>
<?php } ?>
<li style="padding-top:6px;padding-bottom:6px;">
<span>联系人</span><?php echo $member['truename'];?>(<?php echo gender($member['gender']);?>)&nbsp;<?php echo $member['career'];?>&nbsp;
<?php if($member['username'] && $DT['im_web']) { ?><?php echo im_web($member['username'].'&mid='.$moduleid.'&itemid='.$itemid);?>&nbsp;<?php } ?>
<?php if($member['qq'] && $DT['im_qq']) { ?><?php echo im_qq($member['qq']);?>&nbsp;<?php } ?>
<?php if($member['ali'] && $DT['im_ali']) { ?><?php echo im_ali($member['ali']);?>&nbsp;<?php } ?>
<?php if($member['msn'] && $DT['im_msn']) { ?><?php echo im_msn($member['msn']);?>&nbsp;<?php } ?>
<?php if($member['skype'] && $DT['im_skype']) { ?><?php echo im_skype($member['skype']);?>&nbsp;<?php } ?>
</li>
<li><span>会员</span> [<?php if(online($member['userid'])==1) { ?><font class="f_red">当前在线</font><?php } else { ?><font class="f_gray">当前离线</font><?php } ?>
] <a href="<?php echo $MODULE['2']['linkurl'];?>friend.php?action=add&username=<?php echo $member['username'];?>" rel="nofollow">[加为商友]</a> <a href="<?php echo $MODULE['2']['linkurl'];?>message.php?action=send&touser=<?php echo $member['username'];?>" rel="nofollow">[发送信件]</a></li>
<?php if($member['mail']) { ?><li><span>邮件</span><?php echo anti_spam($member['mail']);?></li><?php } ?>
<?php if($member['telephone']) { ?><li><span>电话</span><?php echo anti_spam($member['telephone']);?></li><?php } ?>
<?php if($member['mobile']) { ?><li><span>手机</span><?php echo anti_spam($member['mobile']);?><?php if($DT['sms'] && $member['vmobile']) { ?>&nbsp;&nbsp;<a href="<?php echo $MODULE['2']['linkurl'];?>sms.php?action=add&auth=<?php echo encrypt($member['mobile'], DT_KEY.'SMS');?>" target="_blank" rel="nofollow">[发送短信]</a><?php } ?>
</li><?php } ?>
<li><span>地区</span><?php echo area_pos($member['areaid'], '-');?></li>
<?php if($member['address']) { ?><li title="<?php echo $member['address'];?>"><span>地址</span><?php echo $member['address'];?></li><?php } ?>
<?php } else { ?>
<li class="f_b t_c" style="font-size:14px;"><a href="<?php echo userurl('');?>" target="_blank"><?php echo $item['company'];?></a></li>
<li style="padding-top:3px;">
<span>联系人</span><?php echo $item['truename'];?>&nbsp;
<?php if($item['username'] && $DT['im_web']) { ?><?php echo im_web($item['username'].'&mid='.$moduleid.'&itemid='.$itemid);?>&nbsp;<?php } ?>
<?php if($item['qq'] && $DT['im_qq']) { ?><?php echo im_qq($item['qq']);?>&nbsp;<?php } ?>
<?php if($item['ali'] && $DT['im_ali']) { ?><?php echo im_ali($item['ali']);?>&nbsp;<?php } ?>
<?php if($item['msn'] && $DT['im_msn']) { ?><?php echo im_msn($item['msn']);?>&nbsp;<?php } ?>
<?php if($item['skype'] && $DT['im_skype']) { ?><?php echo im_skype($item['skype']);?>&nbsp;<?php } ?>
&nbsp;&nbsp;<strong class="f_red">未注册</strong>
</li>
<?php if($item['email']) { ?><li><span>邮件</span><?php echo anti_spam($item['email']);?></li><?php } ?>
<?php if($item['telephone']) { ?><li><span>电话</span><?php echo anti_spam($item['telephone']);?></li><?php } ?>
<?php if($item['mobile']) { ?><li><span>手机</span><?php echo anti_spam($item['mobile']);?></li><?php } ?>
<li><span>地区</span><?php echo area_pos($item['areaid'], '&nbsp;');?></li>
<?php if($item['address']) { ?><li title="<?php echo $item['address'];?>"><span>地址</span><?php echo $item['address'];?></li><?php } ?>
</li>
<?php } ?>
</ul>
<?php } else if($user_status == 2) { ?>
<div class="px13 t_c">
<table cellpadding="5" cellspacing="5" width="100%">
<tr>
<td class="f_b"><div style="padding:3px;border:#40B3FF 1px solid;background:#E5F5FF;">查看该信息联系方式需支付<?php echo $name;?> <strong class="f_red"><?php echo $fee;?></strong> <?php echo $unit;?></div></td>
</tr>
<tr>
<td>我的<?php echo $name;?>余额 <strong class="f_blue"><?php if($currency=='money') { ?><?php echo $_money;?><?php } else { ?><?php echo $_credit;?><?php } ?>
</strong> <?php echo $unit;?></td>
</tr>
<tr>
<td>请点击支付按钮支付后查看</td>
</tr>
<?php if($MOD['fee_period']) { ?>
<tr>
<td>支付后可查看<strong class="f_red"><?php echo $MOD['fee_period'];?></strong>分钟，过期重新计费</td>
</tr>
<?php } ?>
<tr>
<td>
<a href="<?php echo $pay_url;?>" rel="nofollow"><img src="<?php echo DT_SKIN;?>image/btn_pay.gif" alt="立即支付"/></a>
&nbsp;&nbsp;&nbsp;
<a href="<?php echo $MODULE['2']['linkurl'];?><?php if($currency=='money') { ?>charge.php?action=pay&reason=pay|<?php echo $mid;?>|<?php echo $itemid;?><?php } else { ?>credit.php?action=buy<?php } ?>
" rel="nofollow"><img src="<?php echo DT_SKIN;?>image/btn_charge.gif" alt="帐户充值"/></a>
</td>
</tr>
</table>
</div>
<?php } else if($user_status == 1) { ?>
<div class="px13 t_c">
<table cellpadding="5" cellspacing="5" width="100%">
<tr>
<td class="f_b"><div style="padding:3px;border:#FFC600 1px solid;background:#FFFEBF;">您的会员级别没有查看联系方式的权限</div></td>
</tr>
<tr>
<td>获得更多商业机会，建议<span class="f_red">升级</span>会员级别</td>
</tr>
<?php if($DT['telephone']) { ?>
<tr>
<td>咨询电话：<?php echo $DT['telephone'];?></td>
</tr>
<?php } ?>
<tr>
<td>
<a href="<?php echo $MODULE['2']['linkurl'];?>grade.php" rel="nofollow"><img src="<?php echo DT_SKIN;?>image/btn_upgrade.gif" width="100" height="30" alt="现在升级"/></a>&nbsp;&nbsp;
<a href="<?php echo $MODULE['2']['linkurl'];?>grade.php" rel="nofollow"><img src="<?php echo DT_SKIN;?>image/btn_detail.gif" width="100" height="30" alt="了解详情"/></a>
</td>
</tr>
</table>
</div>
<?php } else if($user_status == 0) { ?>
<div class="user_warn"><img src="<?php echo DT_SKIN;?>image/no.gif" align="absmiddle"/> 您还没有登录，请登录后查看联系方式</div>
<div class="user_login">
<form action="<?php echo $MODULE['2']['linkurl'];?><?php echo $DT['file_login'];?>" method="post" onsubmit="return user_login();">
<input type="hidden" name="submit" value="1"/>
<input name="username" id="user_name" type="text" value="会员名/Email" onfocus="if(this.value=='会员名/Email')this.value='';" class="user_input"/>&nbsp; 
<input name="password" id="user_pass" type="password" value="password" onfocus="if(this.value=='password')this.value='';" class="user_input"/>&nbsp; 
<input type="image" src="<?php echo DT_SKIN;?>image/user_login.gif" align="absmiddle"/>
</form>
</div>
<div class="user_tip">免费注册为会员后，您可以...</div>
<div class="user_can">
<table cellpadding="0" cellspacing="0" width="100%">
<tr>
<td><img src="<?php echo $MODULE['2']['linkurl'];?>image/ico_edit.gif" align="absmiddle"/> 发布供求信息</td>
<td><img src="<?php echo $MODULE['2']['linkurl'];?>image/ico_product.gif" align="absmiddle"/> 推广企业产品</td>
</tr>
<tr>
<td><img src="<?php echo $MODULE['2']['linkurl'];?>image/ico_homepage.gif" align="absmiddle"/> 建立企业商铺</td>
<td><img src="<?php echo $MODULE['2']['linkurl'];?>image/ico_message.gif" align="absmiddle"/> 在线洽谈生意</td>
</tr>
</table>
</div>
<div class="user_reg_c"><a href="<?php echo $MODULE['2']['linkurl'];?><?php echo $DT['file_register'];?>" rel="nofollow"><img src="<?php echo DT_SKIN;?>image/user_reg.gif" width="260" height="26" alt="还不是会员，立即免费注册"/></a></div>
<?php } else { ?>
<br/><br/><br/>
<center><img src="<?php echo DT_SKIN;?>image/load.gif"/></center>
<br/><br/><br/>
<?php } ?>
