/*
	[Destoon B2B System] Copyright (c) 2008-2015 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
function player(u, w, h, p, a) {
	var w = w ? w : 480;
	var h = h ? h : 400;
	var e = t = c = m = x = '';
	var UA = navigator.userAgent.toLowerCase();
	if(UA.indexOf('mac os')!=-1) m = 'Mac';
	if(UA.indexOf('ipad')!=-1) m = 'iPad';
	if(UA.indexOf('iphone')!=-1) m = 'iPhone';
	if(UA.indexOf('ipod')!=-1) m = 'iPod';
	if(UA.indexOf('android')!=-1) m = 'Android';
	x = ext_url(u);
	if(m) {
		u5 = '';
		if(x == 'mp4') {
			u5 = u;
		} else if(u.indexOf('.youku.com')!=-1) {
			u5 = youku_url5(u);
			if(u5) return html_url5(u5, w, h);
		} else if(u.indexOf('.tudou.com')!=-1) {
			u5 = tudou_url5(u);
			if(u5) return html_url5(u5, w, h);
		} else if(u.indexOf('static.video.qq.com')!=-1) {
			u5 = vqq_url5(u);
			if(u5) return html_url5(u5, w, h);
		} else if(u.indexOf('.56.com')!=-1) {
			u5 = v56_url5(u);
			if(u5) return html_url5(u5, w, h);
		} else if(u.indexOf('.ku6.com')!=-1) {
			u5 = ku6_url5(u);
		} else if(u.indexOf('.youtube.com')!=-1) {
			u5 = youtube_url5(u);
			if(u5) return html_url5(u5, w, h);
		}
		var h2 = parseInt(h/2)-21;
		var w2 = parseInt(w/2)-21;
		if(u5) {
			return m == 'Android' ? '<div style="width:'+w+'px;height:'+h+'px;text-align:center;background:#141516;margin:auto;"><a href="'+u5+'" target="_blank"><img src="'+DTPath+'/file/image/play.png" style="padding:'+h2+'px '+w2+'px '+h2+'px '+w2+'px;"/></a></div>' : '<video src="'+u5+'" width="'+w+'" height="'+h+'"'+(a ? ' autoplay="autoplay"' : '')+' controls="controls"></video>';
		} else {
			return '<div style="width:'+w+'px;height:'+h+'px;text-align:center;background:#000000;color:#FFFFFF;margin:auto;"><div style="padding-top:'+h2+'px;">'+m+L['iso_tips_video']+'</div></div>';
		}
	}
	if(p == 0) {
		e = 'swf';
	} else if(p == 1) {
		e = 'wma';
	} else if(p == 2) {
		e = 'rm';
	} else {
		e = x;
	}
	if(e == 'rm' || e == 'rmvb' || e == 'ram') {
		t = 'audio/x-pn-realaudio-extend';
	} else if(e == 'wma' || e == 'wmv') {
		t = 'application/x-mplayer2';
		c = 'controls="imagewindow,controlpanel,statusbar"';
	} else {
		if(x == 'mp4' || x == 'flv') return '<object type="application/x-shockwave-flash" data="'+DTPath+'file/flash/vcastr3.swf" width="'+w+'" height="'+h+'" id="vcastr3"><param name="movie" value="'+DTPath+'file/flash/vcastr3.swf"/><param name="FlashVars" value="xml=<vcastr><channel><item><source>'+u+'</source><duration></duration><title></title></item></channel><config><isAutoPlay>'+(a ? 'true' : 'false')+'</isAutoPlay><controlPanelBgColor>0x333333</controlPanelBgColor><isShowAbout>false</isShowAbout></config></vcastr>"/></object>';
		t = 'application/x-shockwave-flash';
		c = 'quality="high" extendspage="http://get.adobe.com/flashplayer/" allowfullscreen="true" allowscriptaccess="never"';
	}
	return '<embed src="'+u+'" width="'+w+'" height="'+h+'" type="'+t+'" autostart="'+(a ? 'true' : 'false')+'" '+c+'></embed>';
}
function ext_url(v) {return v.substring(v.lastIndexOf('.')+1, v.length).toLowerCase();}
function html_url5(u, w, h) {return '<iframe src="'+u5+'" width="'+w+'" height="'+h+'" frameborder="0" scrolling="no" allowfullscreen="true" allowtransparency="true"></iframe>';}
function youku_url5(u) {
	var t1,t2,t3;
	if(u.indexOf('/sid/') == -1 || u.indexOf('/v.sw') == -1) return '';
	t1 = u.split('/sid/');
	t2 = t1[1].split('/v.sw');
	t3 = t2[0];
	return t3 ? 'http://player.youku.com/embed/'+t3 : '';
}
function tudou_url5(u) {
	var t1,t2,t3;
	if(u.indexOf('/v/') == -1) return '';
	t1 = u.split('/v/');
	t2 = t1[1].split('/');
	t3 = t2[0];	
	return t3 ? 'http://www.tudou.com/programs/view/html5embed.action?code='+t3 : '';
}
function vqq_url5(u) {
	var t1,t2,t3;
	if(u.indexOf('vid=') == -1) return '';
	t1 = u.split('vid=');
	t2 = t1[1].split('&');
	t3 = t2[0];
	return t3 ? 'http://v.qq.com/iframe/player.html?vid='+t3+'&tiny=0&auto=0' : '';
	//return t3 ? 'http://vxml.56.com/m3u8/'+t3+'/' : '';
}
function v56_url5(u) {
	var t1,t2,t3;
	if(u.indexOf('/v_') == -1 || u.indexOf('.sw') == -1) return '';
	t1 = u.split('/v_');
	t2 = t1[1].split('.sw');
	t3 = t2[0];
	return t3 ? 'http://www.56.com/iframe/'+t3 : '';
}
function ku6_url5(u) {
	var t1,t2,t3;
	if(u.indexOf('refer/') == -1 || u.indexOf('v.sw') == -1) return '';
	t1 = u.split('refer/');
	t2 = t1[1].split('/v.sw');
	t3 = t2[0];
	return t3 ? 'http://v.ku6.com/fetchwebm/'+t3+'.m3u8' : '';
}
function youtube_url5(u) {
	var t1,t2,t3;
	if(u.indexOf('youtube.com/v/') == -1) return '';
	t1 = u.split('/v/');
	t3 = t1[1];
	return t3 ? 'http://www.youtube.com/embed/'+t3 : '';
}