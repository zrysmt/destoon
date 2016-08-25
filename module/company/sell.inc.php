<?php 
defined('IN_DESTOON') or exit('Access Denied');
$could_inquiry = check_group($_groupid, $MOD['group_inquiry']);
if($username == $_username || $domain) $could_inquiry = true;
$moduleid = 5;
$module = 'sell';
$MOD = cache_read('module-'.$moduleid.'.php');
$table = $DT_PRE.$module.'_'.$moduleid;
$table_data = $DT_PRE.$module.'_data_'.$moduleid;
if($itemid) {
	$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
	if(!$item || $item['status'] < 3 || $item['username'] != $username) dheader($MENU[$menuid]['linkurl']);
	unset($item['template']);
	extract($item);
	$CAT = get_cat($catid);
	$content_table = content_table($moduleid, $itemid, $MOD['split'], $table_data);
	$t = $db->get_one("SELECT content FROM {$content_table} WHERE itemid=$itemid");
	$content = $t['content'];
	$CP = $MOD['cat_property'] && $CAT['property'];
	if($CP) {
		require DT_ROOT.'/include/property.func.php';
		$options = property_option($catid);
		$values = property_value($moduleid, $itemid);
	}
	$adddate = timetodate($addtime, 5);
	$editdate = timetodate($edittime, 5);
	$todate = $totime ? timetodate($totime, 3) : 0;
	$expired = $totime && $totime < $DT_TIME ? true : false;
	$linkurl = $MOD['linkurl'].$linkurl;
	$thumbs = get_albums($item);
	$albums =  get_albums($item, 1);
	$album_js = 1;
	$amount = number_format($amount, 0, '.', '');
	$inquiry_url = $MODULE[4]['linkurl'].'home.php?action=message&job=inquiry&&itemid='.$itemid.'&template='.$template.'&skin='.$skin.'&title='.rawurlencode($title).'&username='.$username.'&sign='.crypt_sign($itemid.$template.$skin.$title.$username);
	$order_url = $MODULE[4]['linkurl'].'home.php?action=message&job=order&&itemid='.$itemid.'&template='.$template.'&skin='.$skin.'&title='.rawurlencode($title).'&username='.$username.'&sign='.crypt_sign($itemid.$template.$skin.$title.$username);
	$update = '';
	include DT_ROOT.'/include/update.inc.php';
	$head_canonical = $linkurl;
	$head_title = $title.$DT['seo_delimiter'].$head_title;
	$head_keywords = $keyword;
	$head_description = $introduce ? $introduce : $title;
	if($EXT['mobile_enable']) $head_mobile = $EXT['mobile_url'].mobileurl($moduleid, 0, $itemid, $page);;
} else {
	$typeid = isset($typeid) ? intval($typeid) : 0;
	$view = isset($view) ? 1 : 0;
	$url = "file=$file";
	$condition = "username='$username' AND status=3";
	if($typeid) {
		$MTYPE = get_type('product-'.$userid);
		$condition .= " AND mycatid='$typeid'";
		$url .= "&typeid=$typeid";
		$head_title = $MTYPE[$typeid]['typename'].$DT['seo_delimiter'].$head_title;
	}
	if($kw) {
		$condition .= " AND keyword LIKE '%$keyword%'";
		$url .= "&kw=$kw";
		$head_title = $kw.$DT['seo_delimiter'].$head_title;
	}
	if($view) {
		$url .= "&view=$view";
	}
	$demo_url = userurl($username, $url.'&page={destoon_page}', $domain);
	$pagesize =intval($menu_num[$menuid]);
	if(!$pagesize || $pagesize > 100) $pagesize = 16;
	if($view) $pagesize = ceil($pagesize/2);
	$offset = ($page-1)*$pagesize;
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE $condition", 'CACHE');
	$items = $r['num'];
	$pages = home_pages($items, $pagesize, $demo_url, $page);
	$lists = array();
	if($items) {
		$result = $db->query("SELECT ".$MOD['fields']." FROM {$table} WHERE $condition ORDER BY edittime DESC LIMIT $offset,$pagesize");
		while($r = $db->fetch_array($result)) {
			$r['alt'] = $r['title'];
			$r['title'] = set_style($r['title'], $r['style']);
			$r['linkurl'] = $homeurl ? $MOD['linkurl'].$r['linkurl'] : userurl($username, "file=$file&itemid=$r[itemid]", $domain);
			if($kw) {
				$r['title'] = str_replace($kw, '<span class="highlight">'.$kw.'</span>', $r['title']);
				$r['introduce'] = str_replace($kw, '<span class="highlight">'.$kw.'</span>', $r['introduce']);
			}
			$lists[] = $r;
		}
		$db->free_result($result);
	}
	if($EXT['mobile_enable']) $head_mobile = $EXT['mobile_url'].'index.php?moduleid=4&username='.$username.'&action='.$file.($typeid ? '&typeid='.$typeid : '').($page > 1 ? '&page='.$page : '');
}
include template('sell', $template);
?>