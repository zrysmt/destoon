<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2016 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
class dcache {
	var $pre;
	var $obj;

    function __construct() {
		$this->obj = new Redis;
		include DT_ROOT.'/file/config/redis.inc.php';
		$num = count($RedisServer);
		$key = $num == 1 ? 0 : abs(crc32($GLOBALS['DT_IP']))%$num;
		$this->obj->connect($RedisServer[$key]['host'], $RedisServer[$key]['port']);
    }

    function dcache() {
		$this->__construct();
    }

	function get($key) {
        return $this->obj->get($this->pre.$key);
    }

    function set($key, $val, $ttl = 600) {
         return $ttl ? $this->obj->setex($this->pre.$key, $ttl, $val) : $this->obj->set($this->pre.$key, $val);
    }

    function rm($key) {
        return $this->obj->delete($this->pre.$key);
    }

    function clear() {
        return $this->obj->flushAll();
    }

	function expire() {
		return true;
	}
}
?>