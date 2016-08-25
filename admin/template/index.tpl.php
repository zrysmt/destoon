<?php
defined('DT_ADMIN') or exit('Access Denied');
$edition = edition(1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=<?php echo DT_CHARSET;?>"/>
<title>管理中心 - <?php echo $DT['sitename']; ?> - Powered By DESTOON B2B <?php echo $edition;?> V<?php echo DT_VERSION; ?> R<?php echo DT_RELEASE;?> <?php echo DT_CHARSET;?> <?php echo strtoupper(DT_LANG);?></title>
<meta name="robots" content="noindex,nofollow"/>
<meta name="generator" content="DESTOON B2B - www.destoon.com"/>
<meta http-equiv="x-ua-compatible" content="IE=8"/>
<link rel="shortcut icon" type="image/x-icon" href="<?php echo DT_STATIC;?>favicon.ico"/>
<link rel="bookmark" type="image/x-icon" href="<?php echo DT_STATIC;?>favicon.ico"/>
</head>
<noscript><br/><br/><br/><center><h1>您的浏览器不支持JAVASCRIPT,请更换支持JAVASCRIPT的浏览器</h1></center></noscript>
<noframes><br/><br/><br/><center><h1>您的浏览器不支持框架,请更换支持框架的浏览器</h1></center></noframes>
<script type="text/javascript" src="<?php echo DT_STATIC;?>file/script/config.js"></script>
<frameset cols="<?php echo $DT['admin_left'];?>,7,*" frameborder="no" border="0" framespacing="0" name="fra"> 
<frame name="left" noresize scrolling="auto" src="?action=left&rand=<?php echo $DT_TIME;?>">
<frame name="side" noresize scrolling="no" src="?action=side&rand=<?php echo $DT_TIME;?>">
<frame name="main" noresize scrolling="yes" src="?action=main&rand=<?php echo $DT_TIME;?>">
</frameset>
</html>