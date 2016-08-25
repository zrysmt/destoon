<?php defined('IN_DESTOON') or exit('Access Denied');?><?php include template('header');?>
<script type="text/javascript">var module_id= <?php echo $moduleid;?>,item_id=<?php echo $itemid;?>,content_id='content',img_max_width=<?php echo $MOD['max_width'];?>;</script>
<div class="m">
<div class="left_box">
<div class="pos"><span class="f_r"><a href="<?php echo $MODULE['2']['linkurl'];?><?php echo $DT['file_my'];?>?mid=<?php echo $moduleid;?>&action=add&catid=<?php echo $catid;?>" rel="nofollow"><img src="<?php echo DT_SKIN;?>image/btn_add.gif" width="81" height="20" alt="发布信息" style="margin-top:6px;"/></a></span>当前位置: <a href="<?php echo $MODULE['1']['linkurl'];?>">首页</a> &raquo; <a href="<?php echo $MOD['linkurl'];?>"><?php echo $MOD['name'];?></a> &raquo; <?php echo cat_pos($CAT, ' &raquo; ');?> &raquo;</div>
<div class="b10 c_b"></div>
<table width="100%">
<tr>
<td width="10"> </td>
<td>
<table width="100%">
<tr>
<td colspan="3"><h1 class="title_trade" id="title"><?php echo $title;?></h1></td>
</tr>
<tr>
<td width="250" valign="top">
<div id="mid_pos"></div>
<div id="mid_div" onmouseover="SAlbum();" onmouseout="HAlbum();" onclick="PAlbum(Dd('mid_pic'));">
<img src="<?php echo $albums['0'];?>" width="240" height="180" id="mid_pic"/><span id="zoomer"></span>
</div>
<div class="b5"></div>
<div>
<?php if(is_array($thumbs)) { foreach($thumbs as $k => $v) { ?><img src="<?php echo $v;?>" width="60" height="60" onmouseover="if(this.src.indexOf('nopic60.gif')==-1)Album(<?php echo $k;?>, '<?php echo $albums[$k];?>');" class="<?php if($k) { ?>ab_im<?php } else { ?>ab_on<?php } ?>
" id="t_<?php echo $k;?>"/><?php } } ?>
</div>
<div class="b5"></div>
<div onclick="PAlbum(Dd('mid_pic'));" class="c_b t_c c_p"><img src="<?php echo DT_SKIN;?>image/ico_zoom.gif" width="16" height="16" align="absmiddle"/> 点击图片查看原图</div>
</td>
<td width="15"> </td>
<td valign="top">
<div id="big_div" style="display:none;"><img src="" id="big_pic"/></div>
<table width="100%" cellpadding="5" cellspacing="5">
<?php if($brand) { ?>
<tr>
<td class="f_dblue">品牌：</td>
<td><?php echo $brand;?></td>
</tr>
<?php } ?>
<?php if($n1 && $v1) { ?>
<tr>
<td class="f_dblue"><?php echo $n1;?>：</td>
<td><?php echo $v1;?></td>
</tr>
<?php } ?>
<?php if($n2 && $v2) { ?>
<tr>
<td class="f_dblue"><?php echo $n2;?>：</td>
<td><?php echo $v2;?></td>
</tr>
<?php } ?>
<?php if($n3 && $v3) { ?>
<tr>
<td class="f_dblue"><?php echo $n3;?>：</td>
<td><?php echo $v3;?></td>
</tr>
<?php } ?>
<tr>
<td class="f_dblue">单价：</td>
<td class="f_b f_orange"><?php if($price>0) { ?><?php echo $price;?><?php echo $DT['money_unit'];?>/<?php echo $unit;?><?php } else { ?>面议<?php } ?>
</td>
</tr>
<tr>
<td class="f_dblue">起订：</td>
<td class="f_b f_orange"><?php if($minamount) { ?><?php echo $minamount;?> <?php echo $unit;?><?php } ?>
</td>
</tr>
<tr>
<td class="f_dblue">供货总量：</td>
<td class="f_b f_orange"><?php if($amount) { ?><?php echo $amount;?> <?php echo $unit;?><?php } ?>
</td>
</tr>
<tr>
<td class="f_dblue">发货期限：</td>
<td>自买家付款之日起  <span class="f_b f_orange"><?php if($days) { ?><?php echo $days;?><?php } ?>
</span> 天内发货</td>
</tr>
<tr>
<td class="f_dblue">所在地：</td>
<td><?php echo area_pos($areaid, ' ');?></td>
</tr>
<tr>
<td class="f_dblue">有效期至：</td>
<td><?php if($todate) { ?><?php echo $todate;?><?php } else { ?>长期有效<?php } ?>
<?php if($expired) { ?> <span class="f_red">[已过期]</span><?php } ?>
</td>
</tr>
<tr>
<td class="f_dblue">最后更新：</td>
<td><?php echo $editdate;?></td>
</tr>
<tr>
<td width="80" class="f_dblue">浏览次数：</td>
<td><span id="hits"><?php echo $hits;?></span></td>
</tr>
<?php if($username && !$expired) { ?>
<tr>
<td colspan="2">
<?php if(SELL_ORDER && $price>0 && $minamount>0 && $amount>0 && $unit) { ?>
<img src="<?php echo DT_SKIN;?>image/btn_tobuy.gif" alt="购买" class="c_p" onclick="Go('<?php echo $MOD['linkurl'];?><?php echo rewrite('buy.php?itemid='.$itemid);?>');"/>
<?php } else { ?>
<img src="<?php echo DT_SKIN;?>image/btn_inquiry.gif" alt="询价" class="c_p" onclick="Go('<?php echo $MOD['linkurl'];?><?php echo rewrite('inquiry.php?itemid='.$itemid);?>');"/>
<?php } ?>
</td>
</tr>
<?php } ?>
</table>
</td>
</tr>
</table>
</td>
<td width="10"> </td>
<td width="300" valign="top">
<div class="contact_head">公司基本资料信息</div>
<div class="contact_body" id="contact"><?php include template('contact', 'chip');?></div>
<?php if(!$username) { ?>
<br/>
&nbsp;<strong class="f_red">注意</strong>:发布人未在本站注册，建议优先选择<a href="<?php echo $MODULE['2']['linkurl'];?>grade.php"><strong><?php echo VIP;?>会员</strong></a>
<?php } ?>
</td>
<td width="10"> </td>
</tr>
</table>
<div class="b10">&nbsp;</div>
</div>
</div>
<div class="m">
<div class="b10">&nbsp;</div>
<div class="box_head">
<strong>产品详细说明</strong>
</div>
<div class="box_body" style="padding:0;">
<?php if($CP) { ?><?php include template('property', 'chip');?><?php } ?>
<div class="content c_b" id="content"><?php echo $content;?></div>
<?php include template('comment', 'chip');?>
</div>
</div>
<?php if($username) { ?>
<div class="m">
<div class="b10">&nbsp;</div>
<div class="box_head"><div><span class="f_r"><a href="<?php echo userurl($username, 'file=sell');?>">更多&raquo;</a></span><strong>本企业其它产品</strong></div></div>
<div class="box_body">
<div class="thumb" style="padding:10px;">
<?php echo tag("moduleid=$moduleid&length=20&condition=status=3 and thumb<>'' and username='$username'&pagesize=8&order=edittime desc&width=80&height=80&cols=8&template=thumb-table");?>
</div>
</div>
</div>
<?php } ?>
<div class="m">
<br/>
<center>
[ <a href="<?php echo $MOD['linkurl'];?>search.php" rel="nofollow"><?php echo $MOD['name'];?>搜索</a> ]&nbsp;
[ <a href="javascript:SendFav();">加入收藏</a> ]&nbsp;
[ <a href="javascript:SendPage();">告诉好友</a> ]&nbsp;
[ <a href="javascript:Print();">打印本文</a> ]&nbsp;
[ <a href="javascript:SendReport();">违规举报</a> ]&nbsp;
[ <a href="javascript:window.close()">关闭窗口</a> ]
</center>
<br/>
</div>
<script type="text/javascript" src="<?php echo DT_STATIC;?>file/script/album.js"></script>
<?php if($content) { ?><script type="text/javascript" src="<?php echo DT_STATIC;?>file/script/content.js"></script><?php } ?>
<?php include template('footer');?>