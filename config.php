<?php
class Config {
	public $use_cache = false;
	public $cache_directory = './cache/';
	public $cache_time = "86400"; // in seconds; 86400 seconds = 24h
	
	// default settings - ignored if url parameter has been passed
	public $format = "json"; // "json", "jsonp" or "xml"
	public $callback = "processShares"; // default 
	
}
