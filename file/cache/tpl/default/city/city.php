<?php defined('IN_DESTOON') or exit('Access Denied');?><?php include template('header');?>
<style>.cli {float:left;width:60px;padding:0 0 0 10px;font-size:14px;}</style>
<script type="text/javascript">
function gocity(s) {
Go('?action=go&'+s);
}
</script>
<div class="m">
<div class="tb">
<table>
<tr>
<th width="58">&nbsp;</th>
<th class="t_l px14">
<span class="f_r"><a href="javascript:gocity('auto=1');" title="智能选择">[智能]</a>&nbsp;</span>
&nbsp;&nbsp;<a href="javascript:gocity('');"<?php if($cityid == 0) { ?> class="f_b"<?php } ?>
><?php echo $L['allcity'];?></a>
</th>
</tr>
<?php if(is_array($lists)) { foreach($lists as $k => $v) { ?>
<tr>
<td class="f_b t_c px14"><?php echo $k;?></td>
<td>
<ul>
<?php if(is_array($v)) { foreach($v as $j => $s) { ?>
<li class="cli"><a href="<?php if($s['linkurl']) { ?><?php echo $s['linkurl'];?><?php } else { ?>javascript:gocity('areaid=<?php echo $s['areaid'];?>');<?php } ?>
"<?php if($cityid == $s['areaid']) { ?> class="f_b"<?php } ?>
><?php echo set_style($s['name'], $s['style']);?></a></li>
<?php } } ?>
</ul>
<div class="c_b"></div>
</td>
</tr>
<?php } } ?>
</table>
</div>
</div>
<?php include template('footer');?>