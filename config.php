<?php
class Config {

	/* global cache settings */
	public $cache = 3;			// 0 = no cache, 1 = memcache, 2 = apc, 3 = filecache
	public $cache_time = "86400"; 		// in seconds; 86400 seconds = 24h
	
	/* memcache settings */
	public $cache_server = 'localhost';
	public $cache_port = 11211;
	public $cache_persistent = true;
	
	/* filecache settings */
	public $cache_directory = './cache/';	// needs trailing slash!
	
	/* default output settings - ignored if url parameter has been passed */
	public $format = "json"; 		// "json", "jsonp" or "xml"
	public $callback = "processShares"; 	// default jsonp callback function name 
	
}
