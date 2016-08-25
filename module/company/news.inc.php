<?php 
defined('IN_DESTOON') or exit('Access Denied');
if($action == 'company') {//Company News
	include DT_ROOT.'/include/seo.inc.php';
	$seo_title = $L['news_title'].$seo_delimiter.$seo_page.$seo_modulename.$seo_delimiter.$seo_sitename;
	include template('news', $module);
	exit;
}
$table = $DT_PRE.'news';
$table_data = $DT_PRE.'news_data';
if($itemid) {
	$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
	if(!$item || $item['status'] < 3 || $item['username'] != $username) dheader($MENU[$menuid]['linkurl']);
	extract($item);
	$t = $db->get_one("SELECT content FROM {$table_data} WHERE itemid=$itemid");
	$content = $t['content'];	
	if(!$DT_BOT) $db->query("UPDATE LOW_PRIORITY {$table} SET hits=hits+1 WHERE itemid=$itemid", 'UNBUFFERED');
	$head_title = $title.$DT['seo_delimiter'].$head_title;
	$head_keywords = $title.','.$COM['company'];
	$head_description = get_intro($content, 200);
	if($EXT['mobile_enable']) $head_mobile = $EXT['mobile_url'].'index.php?moduleid=4&username='.$username.'&action='.$file.'&itemid='.$itemid;
} else {
	$typeid = isset($typeid) ? intval($typeid) : 0;
	$url = "file=$file";
	$condition = "username='$username' AND status=3";
	if($kw) {
		$condition .= " AND title LIKE '%$keyword%'";
		$url .= "&kw=$kw";
		$head_title = $kw.$DT['seo_delimiter'].$head_title;
	}
	if($typeid) {
		$MTYPE = get_type('news-'.$userid);
		$condition .= " AND typeid='$typeid'";
		$url .= "&typeid=$typeid";
		$head_title = $MTYPE[$typeid]['typename'].$DT['seo_delimiter'].$head_title;
	}
	$demo_url = userurl($username, $url.'&page={destoon_page}', $domain);

	$pagesize =intval($menu_num[$menuid]);
	if(!$pagesize || $pagesize > 100) $pagesize = 30;

	$offset = ($page-1)*$pagesize;
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE $condition", 'CACHE');
	$items = $r['num'];
	$pages = home_pages($items, $pagesize, $demo_url, $page);
	$lists = array();
	if($items) {
		$result = $db->query("SELECT * FROM {$table} WHERE $condition ORDER BY addtime DESC LIMIT $offset,$pagesize");
		while($r = $db->fetch_array($result)) {
			$r['alt'] = $r['title'];
			$r['title'] = set_style($r['title'], $r['style']);
			$r['linkurl'] = userurl($username, "file=$file&itemid=$r[itemid]", $domain);
			if($kw) $r['title'] = str_replace($kw, '<span class="highlight">'.$kw.'</span>', $r['title']);
			$lists[] = $r;
		}
		$db->free_result($result);
	}
	if($EXT['mobile_enable']) $head_mobile = $EXT['mobile_url'].'index.php?moduleid=4&username='.$username.'&action='.$file.($typeid ? '&typeid='.$typeid : '').($page > 1 ? '&page='.$page : '');
}
include template('news', $template);
?>