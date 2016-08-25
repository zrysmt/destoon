/*
	[Destoon B2B System] Copyright (c) 2008-2015 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
if(destoon_oauth) {
	Ds('weibo_sync');
	if(destoon_oauth.indexOf('sina') != -1) {
		$('#weibo_show').append('<input type="hidden" name="post[sync_sina]" value="0" id="sync_sina_inp"/>');
		$('#weibo_show').append('<img src="'+DTPath+'file/image/sync_sina.gif" id="sync_sina_img" onclick="sync_site(\'sina\');" class="c_p" title="'+L['sync_sina']+'"/>&nbsp;&nbsp;');
		if(get_cookie('sina_token') && get_local('sina_sync')) {
			Dd('sync_sina_inp').value = 1;
			Dd('sync_sina_img').src = DTPath+'file/image/sync_sina_on.gif';
		}
	}
	if(destoon_oauth.indexOf('qq') != -1) {
		$('#weibo_show').append('<input type="hidden" name="post[sync_qq]" value="0" id="sync_qq_inp"/>');
		$('#weibo_show').append('<img src="'+DTPath+'file/image/sync_qq.gif" id="sync_qq_img" onclick="sync_site(\'qq\');" class="c_p" title="'+L['sync_qq']+'"/>&nbsp;&nbsp;');
		if(get_cookie('qq_token') && get_local('qq_sync')) {
			Dd('sync_qq_inp').value = 1;
			Dd('sync_qq_img').src = DTPath+'file/image/sync_qq_on.gif';
		}
	}
}

function sync_site(n) {
	if(Dd('sync_'+n+'_inp').value == 1) {
		Dd('sync_'+n+'_inp').value = 0;
		Dd('sync_'+n+'_img').src = DTPath+'file/image/sync_'+n+'.gif';
		set_local(n+'_sync', '');
	} else {
		if(!get_cookie(n+'_token')) {
			if(confirm(L['sync_login_'+n])) {
				window.open(DTPath+'api/oauth/'+n+'/connect.php');
			}
			return;
		}
		Dd('sync_'+n+'_inp').value = 1;
		Dd('sync_'+n+'_img').src = DTPath+'file/image/sync_'+n+'_on.gif';
		set_local(n+'_sync', 1);
	}
}