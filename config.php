<?php
class config {
	public static $use_cache = true;
	public static $cache_directory = './cache/';
	public static $cache_time = "86400"; // in seconds; 86400 seconds = 24h
	
	// default settings - ignored if url parameter has been passed
	public static $format = "json"; // "json", "jsonp" or "xml"
	public static $callback = "processShares"; // default 
	
}
