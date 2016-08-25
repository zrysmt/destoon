<?php defined('IN_DESTOON') or exit('Access Denied');?><?php include template('header');?>
<div class="m">
<div class="m_l_1 f_l">
<div class="left_box">
<div class="pos">当前位置: <a href="<?php echo $MODULE['1']['linkurl'];?>">首页</a> &raquo; <a href="<?php echo $MOD['linkurl'];?>"><?php echo $MOD['name'];?></a> &raquo; <a href="<?php echo $MOD['linkurl'];?>search.php">搜索</a></div>
<div class="fsearch">
<?php if($MOD['sphinx']) { ?>
<form action="<?php echo $MOD['linkurl'];?>search.php" id="fsearch">
<input type="hidden" name="list" id="list" value="<?php echo $list;?>"/>
<table cellpadding="5" cellspacing="3">
<tr>
<td width="80" align="right">关 键 词：</td>
<td><input type="text" size="60" name="kw" value="<?php echo $kw;?>" class="pd3"/></td>
</tr>
<tr>
<td align="right">所属行业：</td>
<td><?php echo $category_select;?></td>
</tr>
<tr>
<td align="right">所在地区：</td>
<td><?php echo $area_select;?></td>
</tr>
<tr>
<td></td>
<td>
<input type="image" src="<?php echo DT_SKIN;?>image/btn_search.gif"/>&nbsp;&nbsp;
<a href="<?php echo $MOD['linkurl'];?>search.php"><img src="<?php echo DT_SKIN;?>image/btn_reset_search.gif"/></a>
</td>
</tr>
</table>
<?php } else { ?>
<form action="<?php echo $MOD['linkurl'];?>search.php" id="fsearch">
<input type="hidden" name="list" id="list" value="<?php echo $list;?>"/>
<table cellpadding="5" cellspacing="3">
<tr>
<td width="80" align="right">关 键 词：</td>
<td colspan="3"><input type="text" size="60" name="kw" value="<?php echo $kw;?>" class="pd3"/></td>
</tr>
<tr>
<td></td>
<td colspan="3">
<?php if(is_array($sfields)) { foreach($sfields as $k => $v) { ?>
<input type="radio" name="fields" value="<?php echo $k;?>" id="fd_<?php echo $k;?>"<?php if($fields==$k) { ?> checked<?php } ?>
/><label for="fd_<?php echo $k;?>"> <?php echo $v;?></label>&nbsp;
<?php } } ?>
</td>
</tr>
<tr>
<td align="right">更新日期：</td>
<td><?php echo dcalendar('fromdate', $fromdate, '');?> 至 <?php echo dcalendar('todate', $todate, '');?></td>
<td align="right">所属行业：</td>
<td><?php echo $category_select;?></td>
</tr>
<tr>
<td align="right">信息类型：</td>
<td>
<?php echo $type_select;?>&nbsp;
<input type="checkbox" name="price" id="price" value="1"<?php if($price) { ?> checked<?php } ?>
/>标价
<input type="checkbox" name="thumb" id="thumb" value="1"<?php if($thumb) { ?> checked<?php } ?>
/>图片
<input type="checkbox" name="vip" id="vip" value="1"<?php if($vip) { ?> checked<?php } ?>
/><?php echo VIP;?>
</td>
<td align="right">所在地区：</td>
<td><?php echo $area_select;?></td>
</tr>
<tr>
<td align="right">单价范围：</td>
<td>
<input type="text" size="10" name="minprice" value="<?php echo $minprice;?>"/> ~ <input type="text" size="10" name="maxprice" value="<?php echo $maxprice;?>"/></td>
<td align="right">排序方式：</td>
<td><?php echo $order_select;?></td>
</tr>
</tr>
<tr>
<td></td>
<td colspan="3">
<input type="image" src="<?php echo DT_SKIN;?>image/btn_search.gif"/>&nbsp;&nbsp;
<a href="<?php echo $MOD['linkurl'];?>search.php"><img src="<?php echo DT_SKIN;?>image/btn_reset_search.gif"/></a>
</td>
</tr>
</table>
<?php } ?>
<?php if($CP) { ?>
<?php if(is_array($PPT)) { foreach($PPT as $p) { ?>
<div class="ppt">
<table cellpadding="0" cellspacing="0" width="100%">
<tr>
<td class="ppt_l" valign="top">按<?php echo $p['name'];?></td>
<td class="ppt_r" valign="top">
<input type="hidden" name="ppt_<?php echo $p['oid'];?>" id="ppt_<?php echo $p['oid'];?>" value="<?php echo $p['select'];?>"/>
<a href="###" onclick="Dd('ppt_<?php echo $p['oid'];?>').value='';Dd('fsearch').submit();"><?php if($p['select']=='') { ?><span>全部</span><?php } else { ?>全部<?php } ?>
</a>
<?php if(is_array($p['options'])) { foreach($p['options'] as $o) { ?>
&nbsp;|&nbsp;<a href="###" onclick="Dd('ppt_<?php echo $p['oid'];?>').value='<?php echo $o;?>';Dd('fsearch').submit();"><?php if($p['select']==$o) { ?><span><?php echo $o;?></span><?php } else { ?><?php echo $o;?><?php } ?>
</a>
<?php } } ?>
</td>
</tr>
</table>
</div>
<?php } } ?>
<?php } ?>
</form>
</div>
<?php if($page==1 && $kw) { ?>
<?php echo ad($moduleid,$catid,$kw,6);?>
<?php echo load('m'.$moduleid.'_k'.urlencode($kw).'.htm');?>
<?php } ?>
<?php if($tags) { ?>
<form method="post">
<div class="sell_tip" id="sell_tip" style="display:none;" title="双击关闭" ondblclick="Dh(this.id);">
<div>
<p>您可以</p>
<input type="submit" value="对比选中" onclick="this.form.action='<?php echo $MOD['linkurl'];?>compare.php';" class="btn_1" onmouseover="this.className='btn_2'" onmouseout="this.className='btn_1'"/> 或 
<input type="submit" value="批量询价" onclick="this.form.action='<?php echo $MOD['linkurl'];?>inquiry.php';" class="btn_1" onmouseover="this.className='btn_2'" onmouseout="this.className='btn_1'"/>
</div>
</div>
<div class="img_tip" id="img_tip" style="display:none;">&nbsp;</div>
<div class="tool">
<table>
<tr height="30">
<td width="25" align="center" title="全选/反选"><input type="checkbox" onclick="checkall(this.form);"/></td>
<td>
<input type="submit" value="对比选中" onclick="this.form.action='<?php echo $MOD['linkurl'];?>compare.php';" class="btn_1" onmouseover="this.className='btn_2'" onmouseout="this.className='btn_1'"/>&nbsp;
<input type="submit" value="批量询价" onclick="this.form.action='<?php echo $MOD['linkurl'];?>inquiry.php';" class="btn_1" onmouseover="this.className='btn_2'" onmouseout="this.className='btn_1'"/>
</td>
<td align="right">
<?php if($list == 2) { ?>
<img src="<?php echo DT_SKIN;?>image/list_txt_on.gif" width="16" height="16" alt="文字列表" align="absmiddle"/>&nbsp;
<?php } else { ?>
<img src="<?php echo DT_SKIN;?>image/list_txt.gif" width="16" height="16" alt="文字列表" align="absmiddle" class="c_p" onclick="Dd('list').value=2;Dd('fsearch').submit();"/>&nbsp;
<?php } ?>
<?php if($list == 1) { ?>
<img src="<?php echo DT_SKIN;?>image/list_img_on.gif" width="16" height="16" alt="图片列表" align="absmiddle"/>&nbsp;
<?php } else { ?>
<img src="<?php echo DT_SKIN;?>image/list_img.gif" width="16" height="16" alt="图片列表" align="absmiddle" class="c_p" onclick="Dd('list').value=1;Dd('fsearch').submit();"/>&nbsp;
<?php } ?>
<?php if($list == 0) { ?>
<img src="<?php echo DT_SKIN;?>image/list_mix_on.gif" width="16" height="16" alt="图文列表" align="absmiddle"/>&nbsp;
<?php } else { ?>
<img src="<?php echo DT_SKIN;?>image/list_mix.gif" width="16" height="16" alt="图文列表" align="absmiddle" class="c_p" onclick="Dd('list').value=0;Dd('fsearch').submit();"/>&nbsp;
<?php } ?>
</td>
</tr>
</table>
</div>
<?php if($list==2) { ?>
<?php if(is_array($tags)) { foreach($tags as $k => $t) { ?>
<div class="list" id="item_<?php echo $t['itemid'];?>">
<table>
<tr align="center">
<td width="25">&nbsp;<input type="checkbox" id="check_<?php echo $t['itemid'];?>" name="itemid[]" value="<?php echo $t['itemid'];?>" onclick="sell_tip(this, <?php echo $t['itemid'];?>);"/> </td>
<td width="10"> </td>
<td align="left">
<h3><span class="f_r"><?php if($t['vip']) { ?><img src="<?php echo DT_SKIN;?>image/vip_<?php echo $t['vip'];?>.gif" alt="<?php echo VIP;?>" title="<?php echo VIP;?>:<?php echo $t['vip'];?>级"/><?php } ?>
</span><a href="<?php echo $t['linkurl'];?>" target="_blank"><?php echo $t['title'];?></a></h3>
<ul>
<li><span class="f_r px11"><?php echo timetodate($t['edittime'], $datetype);?>&nbsp;&nbsp;</span><?php echo cat_pos($t['catid'], '&nbsp;');?></li>
<li>
<span class="f_r f_gray">
<?php if($t['username'] && $DT['im_web']) { ?><?php echo im_web($t['username'].'&mid='.$moduleid.'&itemid='.$t['itemid']);?>&nbsp;<?php } ?>
<?php if($t['qq'] && $DT['im_qq']) { ?><?php echo im_qq($t['qq']);?>&nbsp;<?php } ?>
<?php if($t['ali'] && $DT['im_ali']) { ?><?php echo im_ali($t['ali']);?>&nbsp;<?php } ?>
<?php if($t['msn'] && $DT['im_msn']) { ?><?php echo im_msn($t['msn']);?>&nbsp;<?php } ?>
<?php if($t['skype'] && $DT['im_skype']) { ?><?php echo im_skype($t['skype']);?></a>&nbsp;<?php } ?>
[<?php echo area_pos($t['areaid'], '');?>]&nbsp;&nbsp;</span>
<a href="<?php echo userurl($t['username']);?>" target="_blank"><?php echo $t['company'];?></a>&nbsp;
<span class="f_gray">
<?php if($t['validated']) { ?><span class="f_green">[已核实]</span><?php } else { ?>[未核实]<?php } ?>
<?php if(!$t['username']) { ?> [未注册]<?php } ?>
</span>
</li>
</ul>
</td>
<td width="10"> </td>
<td width="120">
<?php if($t['unit'] && $t['price']>0) { ?>
<span class="f_red"><strong class="px14"><?php echo $t['price'];?></strong>/<?php echo $t['unit'];?></span><br/>
<?php echo $t['minamount'];?><?php echo $t['unit'];?>起订<br/>
<?php } else { ?>
<span class="f_gray">面议</span><br/>
<?php } ?>
<a href="<?php echo $path;?><?php echo rewrite('inquiry.php?itemid='.$t['itemid']);?>" class="l"><img src="<?php echo DT_SKIN;?>image/inquiry.gif" alt="询价" style="margin-top:10px;"/></a>
</td>
</tr>
</table>
</div>
<?php } } ?>
<?php if($pages) { ?><div class="pages"><?php echo $pages;?></div><?php } ?>
<?php } else if($list==1) { ?>
<table cellpadding="5" cellspacing="5" width="100%">
<?php if(is_array($tags)) { foreach($tags as $k => $t) { ?>
<?php if($k%5==0) { ?><tr><?php } ?>
<td valign="top" width="20%" id="item_<?php echo $t['itemid'];?>">
<table cellpadding="3" cellspacing="3" width="100%">
<tr align="center">
<td class="thumb"><a href="<?php echo $t['linkurl'];?>" target="_blank"><img src="<?php echo imgurl($t['thumb']);?>" width="80" height="80" alt="" onmouseover="img_tip(this, this.src);" onmouseout="img_tip(this, '');" class="bd"/></a></td>
</tr>
<tr align="center">
<td class="thumb">
<ul>
<li><input type="checkbox" id="check_<?php echo $t['itemid'];?>" name="itemid[]" value="<?php echo $t['itemid'];?>" onclick="sell_tip(this, <?php echo $t['itemid'];?>);"/> <a href="<?php echo $t['linkurl'];?>" target="_blank" class="px13 f_n"><?php echo $t['title'];?></a></li>
</ul>
<div style="padding:5px 0 0 0;">
<?php if($t['unit'] && $t['price']>0) { ?>
<span class="f_red"><strong class="px14"><?php echo $t['price'];?></strong>/<?php echo $t['unit'];?></span><br/>
<?php } else { ?>
<span class="f_gray">面议</span><br/>
<?php } ?>
<a href="<?php echo $path;?><?php echo rewrite('inquiry.php?itemid='.$t['itemid']);?>" class="l"><img src="<?php echo DT_SKIN;?>image/inquiry.gif" title="询价" style="border:none;"/></a>
</div>
<ul>
<li><?php if($t['vip']) { ?><img src="<?php echo DT_SKIN;?>image/vip_<?php echo $t['vip'];?>.gif" alt="<?php echo VIP;?>" title="<?php echo VIP;?>:<?php echo $t['vip'];?>级" style="border:none;" align="absmiddle"/>&nbsp;<?php } ?>
<a href="<?php echo userurl($t['username']);?>" target="_blank"><?php echo $t['company'];?></a></li>
</ul>
</td>
</tr>
<tr align="center">
<td>
<?php if($t['username'] && $DT['im_web']) { ?><?php echo im_web($t['username'].'&mid='.$moduleid.'&itemid='.$t['itemid']);?>&nbsp;<?php } ?>
<?php if($t['qq'] && $DT['im_qq']) { ?><?php echo im_qq($t['qq']);?>&nbsp;<?php } ?>
<?php if($t['ali'] && $DT['im_ali']) { ?><?php echo im_ali($t['ali']);?>&nbsp;<?php } ?>
<?php if($t['msn'] && $DT['im_msn']) { ?><?php echo im_msn($t['msn']);?>&nbsp;<?php } ?>
<?php if($t['skype'] && $DT['im_skype']) { ?><?php echo im_skype($t['skype']);?></a>&nbsp;<?php } ?>
</td>
</tr>
</table>
</td>
<?php if($k%5==4) { ?></tr><?php } ?>
<?php } } ?>
</table>
<?php if($pages) { ?><div class="pages"><?php echo $pages;?></div><?php } ?>
<?php } else { ?>
<?php include template('list-'.$module, 'tag');?>
<?php } ?>
<div class="tool">
<table>
<tr height="30">
<td width="25"></td>
<td>
<input type="submit" value="对比选中" onclick="this.form.action='<?php echo $MOD['linkurl'];?>compare.php';" class="btn_1" onmouseover="this.className='btn_2'" onmouseout="this.className='btn_1'"/>&nbsp;
<input type="submit" value="批量询价" onclick="this.form.action='<?php echo $MOD['linkurl'];?>inquiry.php';" class="btn_1" onmouseover="this.className='btn_2'" onmouseout="this.className='btn_1'"/>
</td>
<td></td>
</tr>
</table>
</div>
</form>
<?php } else { ?>
<?php include template('noresult', 'message');?>
<?php } ?>
</div>
</div>
<div class="m_n f_l">&nbsp;</div>
<div class="m_r_1 f_l">
<?php if($kw) { ?>
<div class="box_head"><div><strong>相关搜索</strong></div></div>
<div class="box_body">
<div class="sch_site">
<ul>
<?php if(is_array($MODULE)) { foreach($MODULE as $mod) { ?><?php if($mod['moduleid']>3 && $mod['moduleid']!=$moduleid && !$mod['islink']) { ?><li><a href="<?php echo $mod['linkurl'];?>search.php?kw=<?php echo urlencode($kw);?>">在 <span class="f_red"><?php echo $mod['name'];?></span> 找 <?php echo $kw;?></a></li><?php } ?>
<?php } } ?>
</ul>
</div>
<?php echo tag("moduleid=$moduleid&table=keyword&condition=moduleid=$moduleid and status=3 and word<>'$kw' and keyword like '%$keyword%'&pagesize=10&order=total_search desc&template=list-search_relate", -2);?>
</div>
<div class="b10">&nbsp;</div>
<?php } ?>
<div class="sponsor"><?php echo ad($moduleid,$catid,$kw,7);?></div>
<div class="box_head"><div><strong>今日搜索排行</strong></div></div>
<div class="box_body">
<div class="rank_list">
<?php echo tag("moduleid=$moduleid&table=keyword&condition=moduleid=$moduleid and status=3 and updatetime>$today_endtime-86400&pagesize=10&order=today_search desc&key=today_search&template=list-search_rank");?>
</div>
</div>
<div class="b10">&nbsp;</div>
<div class="box_head"><div><strong>本周搜索排行</strong></div></div>
<div class="box_body">
<div class="rank_list">
<?php echo tag("moduleid=$moduleid&table=keyword&condition=moduleid=$moduleid and status=3 and updatetime>$today_endtime-86400*7&pagesize=10&order=week_search desc&key=week_search&template=list-search_rank");?>
</div>
</div>
<div class="b10">&nbsp;</div>
<div class="box_head"><div><strong>本月搜索排行</strong></div></div>
<div class="box_body">
<div class="rank_list">
<?php echo tag("moduleid=$moduleid&table=keyword&condition=moduleid=$moduleid and status=3 and updatetime>$today_endtime-86400*30&pagesize=10&order=month_search desc&key=month_search&template=list-search_rank");?>
</div>
</div>
</div>
</div>
<?php include template('footer');?>