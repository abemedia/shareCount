<?php
class Config {
	
	/* cache settings */
	public $cache = 2;						// 0 = no cache, 1 = memcache with filecache fallback, 2 = memcache only, 3 = filecache only
	public $cache_time = "86400"; 			// in seconds; 86400 seconds = 24h
	public $cache_directory = './cache/';	// needs trailing slash!
	
	/* default output settings - ignored if url parameter has been passed */
	public $format = "json"; 				// "json", "jsonp" or "xml"
	public $callback = "processShares"; 	// default jsonp callback function name 
	
}
