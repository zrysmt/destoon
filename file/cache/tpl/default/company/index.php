<?php defined('IN_DESTOON') or exit('Access Denied');?><?php $CSS = array('catalog');?>
<?php include template('header');?>
<div class="m">
<div class="m_l f_l">
<div class="left_box">
<div class="pos">当前位置: <a href="<?php echo $MODULE['1']['linkurl'];?>">首页</a> &raquo; <a href="<?php echo $MOD['linkurl'];?>"><?php echo $MOD['name'];?></a></div>
<div class="category">
<p><img src="<?php echo DT_SKIN;?>image/arrow.gif" width="17" height="12" alt=""/> <strong>按地区浏览</strong></p>
<div>
<table width="100%" cellpadding="3">
<?php $mainarea = get_mainarea(0)?>
<?php if(is_array($mainarea)) { foreach($mainarea as $k => $v) { ?>
<?php if($k%10==0) { ?><tr><?php } ?>
<td><a href="<?php echo $MOD['linkurl'];?>search.php?areaid=<?php echo $v['areaid'];?>" rel="nofollow"><?php echo $v['areaname'];?></a></td>
<?php if($k%10==9) { ?></tr><?php } ?>
<?php } } ?>
</table>
</div>
</div>
<?php if($page == 1) { ?><?php echo ad($moduleid,$catid,$kw,6);?><?php } ?>
<div style="padding:12px 10px 10px 15px;background:#F1F1F1;"><img src="<?php echo DT_SKIN;?>image/arrow.gif" width="17" height="12" alt=""/> <strong>按行业浏览</strong></div>
<div class="catalog" style="border:none;padding:0;">
<?php $mid = 4;?>
<?php include template('catalog', 'chip');?>
</div>
</div>
</div>
<div class="m_n f_l">&nbsp;</div>
<div class="m_r f_l">
<?php if($MOD['page_irec']) { ?>
<div class="box_head"><div><span class="f_r"><a href="<?php echo $MOD['linkurl'];?><?php echo rewrite('search.php?vip=1');?>" rel="nofollow">更多&raquo;</a></span><strong>名企推荐</strong></div></div>
<div class="box_body li_dot f_gray">
<?php echo tag("moduleid=$moduleid&condition=level>0 and catids<>''&areaid=$cityid&pagesize=".$MOD['page_irec']."&order=vip desc&template=list-com");?>
</div>
<div class="b10"> </div>
<?php } ?>
<?php if($MOD['page_ivip']) { ?>
<div class="box_head"><div><span class="f_r"><a href="<?php echo $MOD['linkurl'];?><?php echo rewrite('search.php?vip=1');?>" rel="nofollow">更多&raquo;</a></span><strong>最新<?php echo VIP;?></strong></div></div>
<div class="box_body li_dot f_gray">
<?php echo tag("moduleid=$moduleid&condition=vip>0 and catids<>''&areaid=$cityid&pagesize=".$MOD['page_ivip']."&order=fromtime desc&template=list-com");?>
</div>
<div class="b10"> </div>
<?php } ?>
<?php if($MOD['page_inews']) { ?>
<div class="box_head"><div><span class="f_r"><a href="<?php echo $MOD['linkurl'];?><?php echo rewrite('news.php?page=1');?>">更多&raquo;</a></span><strong>企业新闻</strong></div></div>
<div class="box_body li_dot f_gray">
<?php echo tag("table=news&condition=status=3 and level>0&pagesize=".$MOD['page_inews']."&datetype=2&order=addtime desc&target=_blank");?>
</div>
<div class="b10"> </div>
<?php } ?>
<?php if($MOD['page_inew']) { ?>
<div class="box_head"><div><span class="f_r"><a href="<?php echo $MOD['linkurl'];?><?php echo rewrite('search.php?page=1');?>" rel="nofollow">更多&raquo;</a></span><strong>最新加入</strong></div></div>
<div class="box_body li_dot f_gray">
<?php echo tag("moduleid=$moduleid&condition=groupid>5 and catids<>''&areaid=$cityid&pagesize=".$MOD['page_inew']."&order=userid desc&template=list-com");?>
</div>
<?php } ?>
</div>
</div>
<?php include template('footer');?>