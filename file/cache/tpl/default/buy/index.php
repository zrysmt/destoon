<?php defined('IN_DESTOON') or exit('Access Denied');?><?php include template('header');?>
<div class="m">
<div class="m_l_1 f_l">
<div class="left_box">
<div class="pos">当前位置: <a href="<?php echo DT_PATH;?>">首页</a>
&raquo; <a href="<?php echo $MOD['linkurl'];?>"><?php echo $MOD['name'];?></a>
<?php if($typeid!=99) { ?>&raquo; <a href="<?php echo $MOD['linkurl'];?><?php echo rewrite('index.php?typeid='.$typeid);?>"><?php echo $TYPE[$typeid];?></a><?php } ?>
<?php if($catid) { ?>&raquo; <?php echo cat_pos($CAT, ' &raquo; ');?><?php } ?>
</div>
<div class="category">
<p><img src="<?php echo DT_SKIN;?>image/arrow.gif" width="17" height="12" alt=""/> <strong>按行业浏览</strong></p>
<div>
<table width="100%" cellpadding="3">
<?php if(is_array($maincat)) { foreach($maincat as $k => $v) { ?>
<?php if($k%$MOD['page_subcat']==0) { ?><tr><?php } ?>
<td<?php if($v['catid']==$catid) { ?> class="f_b"<?php } ?>
><a href="<?php echo $MOD['linkurl'];?><?php echo $v['linkurl'];?>"><?php echo set_style($v['catname'],$v['style']);?></a><?php if(!$cityid) { ?> <span class="f_gray px10">(<?php echo $v['item'];?>)</span><?php } ?>
</td>
<?php if($k%$MOD['page_subcat']==($MOD['page_subcat']-1)) { ?></tr><?php } ?>
<?php } } ?>
</table>
</div>
</div>
<div class="b10">&nbsp;</div>
<div class="type">
<a href="<?php echo $MOD['linkurl'];?>" class="<?php if($typeid==99) { ?>type_1<?php } else { ?>type_2<?php } ?>
">全部</a>
<?php if(is_array($TYPE)) { foreach($TYPE as $k => $v) { ?>
<a href="javascript:Go('<?php echo $MOD['linkurl'];?><?php echo rewrite('index.php?typeid='.$k.'&catid='.$catid);?>');" class="<?php if($typeid==$k) { ?>type_1<?php } else { ?>type_2<?php } ?>
"><?php echo $v;?></a>
<?php } } ?>
</div>
<div class="b10 c_b">&nbsp;</div>
<div class="img_tip" id="img_tip" style="display:none;">&nbsp;</div>
<?php if($page == 1) { ?><?php echo ad($moduleid,0,'',6);?><?php } ?>
<?php echo tag("moduleid=$moduleid&condition=status=3$dtype&catid=$catid&areaid=$cityid&pagesize=".$MOD['pagesize']."&page=$page&showpage=1&datetype=5&order=".$MOD['order']."&fields=".$MOD['fields']."&lazy=$lazy&template=list-buy");?>
<br/>
</div>
</div>
<div class="m_n f_l">&nbsp;</div>
<div class="m_r_1 f_l">
<div class="sponsor"><?php echo ad($moduleid,$catid,$kw,7);?></div>
<div class="box_head"><div><strong>本周搜索排行</strong></div></div>
<div class="box_body">
<div class="rank_list">
<?php echo tag("moduleid=$moduleid&table=keyword&condition=moduleid=$moduleid and status=3 and updatetime>$today_endtime-86400*7&pagesize=10&order=week_search desc&key=week_search&template=list-search_rank");?>
</div>
</div>
<div class="b10">&nbsp;</div>
<div class="box_head"><div><strong>按地区浏览</strong></div></div>
<div class="box_body">
<table width="100%" cellpadding="3">
<?php $mainarea = get_mainarea(0, $AREA)?>
<?php if(is_array($mainarea)) { foreach($mainarea as $k => $v) { ?>
<?php if($k%2==0) { ?><tr><?php } ?>
<td><a href="<?php echo $MOD['linkurl'];?>search.php?areaid=<?php echo $v['areaid'];?>&typeid=<?php echo $typeid;?>" rel="nofollow"><?php echo $v['areaname'];?></a></td>
<?php if($k%2==1) { ?></tr><?php } ?>
<?php } } ?>
</table>
</div>
</div>
</div>
<?php include template('footer');?>