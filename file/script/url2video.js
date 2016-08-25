/*
	[Destoon B2B System] Copyright (c) 2008-2015 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
function url2video(url) {
	var video,t1,t2;
	if(url.indexOf('v.youku.com') != -1) {
		try	{
			t1 = url.split('id_');
			t2 = t1[1].split('.html');
			if(t2[0]) video = 'http://player.youku.com/player.php/sid/'+t2[0]+'/v.swf';					
		}
		catch(e){}
	} else if(url.indexOf('tudou.com/programs/view/') != -1) {
		try	{
			t1 = url.split('/view/');
			t2 = t1[1].split('/');
			if(t2[0]) video = 'http://www.tudou.com/v/'+t2[0]+'/v.swf';					
		}
		catch(e){}
	} else if(url.indexOf('v.qq.com') != -1) {
		try	{
			t1 = url.split('vid=');
			t2 = t1[1].split('&');
			if(t2[0]) video = 'http://static.video.qq.com/TPout.swf?vid='+t2[0]+'&auto=0';					
		}
		catch(e){}
	} else if(url.indexOf('www.56.com/') != -1) {
		try	{
			t1 = url.split('v_');
			t2 = t1[1].split('.html');
			if(t2[0]) video = 'http://player.56.com/v_'+t2[0]+'.swf';					
		}
		catch(e){}
	} else if(url.indexOf('v.ku6.com/show/') != -1) {
		try	{
			t1 = url.split('/show/');
			t2 = t1[1].split('.html');
			if(t2[0]) video = 'http://player.ku6.com/refer/'+t2[0]+'/v.swf';					
		}
		catch(e){}
	} else if(url.indexOf('youtube.com/watch?v=') != -1) {
		try	{
			t1 = url.split('watch?v=');
			if(t1[1]) video = 'http://www.youtube.com/v/'+t1[1];					
		}
		catch(e){}
	}
	return video ? video : '';
}