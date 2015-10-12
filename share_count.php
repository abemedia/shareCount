<?php
require_once("config.php");
class shareCount {
    private $config;
    private $data;
    private $url;
    private $format;
    private $callback;
    private $cache;
    private $cache_directory;
    private $cache_time;
    
    function __construct() {
        $this->config = new Config;
        $this->cache = $this->config->cache;
        $this->cache_directory = $this->config->cache_directory;
        $this->cache_time = $this->config->cache_time;
        $this->data = new stdClass;
    }
    
    private function getVar($var, $strict = false) {
        if(array_key_exists($var, $_REQUEST) && $_REQUEST[$var] !== "") return $_REQUEST[$var];
        elseif($strict) return false;
        else return $this->config->$var;
    }
    
    public function get($url) {
        $this->url = $url;
        $this->data->url           = $this->url;
        $this->data->shares        = new stdClass;
        $this->data->shares->total = 0;
        
        return $this->getShares($url)->shares;
    }
    
    public function serve() {
        $this->url = $this->url ?: filter_input(INPUT_GET, 'url', FILTER_SANITIZE_URL);
        
        // kill the script if no URL provided
        if(!$this->url) die("Error: No URL specified.");
        
        $this->setFormat($this->format ?: $this->getVar('format'));
        $this->callback            = $this->callback ?: $this->getCallback();
        $this->data                = new stdClass;
        $this->data->url           = $this->url;
        $this->data->shares        = new stdClass;
        $this->data->shares->total = 0;
        
        $data = $this->getData();
        return $data;
    }
    
    // validate and return the callback
	private function getCallback(){
		return filter_var($this->getVar('callback'), FILTER_VALIDATE_REGEXP,
			array(
				"options" => array(
					"regexp"=>"/^\w+$/"
					)
				)
			);        
	}
	
    // set format of the output
    private function setFormat ($format) {
        switch($format) {
            case "xml":
                $this->format = 'xml';
                header ("Content-Type:text/xml"); 
                break;
            case "jsonp": 
                $this->format = 'jsonp';
                header ("Content-Type: application/javascript"); 
                break;
            case "json": // only here for reference
            default:
                if($this->getCallback()) {
                    $this->format = 'jsonp';
                    header ("Content-Type: application/javascript"); 
                }
                else {
                    $this->format = 'json';
                    header ("Content-Type:application/json");
                }
        }
        return $format;
    }
    
    // query API to get share counts
    public function getShares($url = '') {
        $url = $url ?: $this->url;
        
        $shareLinks = array(
            "facebook"    => "https://api.facebook.com/method/links.getStats?format=json&urls=",
            "twitter"     => "http://urls.api.twitter.com/1/urls/count.json?url=",
            "google"      => "https://plusone.google.com/_/+1/fastbutton?url=",
            "linkedin"    => "https://www.linkedin.com/countserv/count/share?format=json&url=",
            "pinterest"   => "http://api.pinterest.com/v1/urls/count.json?url=",
            "stumbleupon" => "http://www.stumbleupon.com/services/1.01/badge.getinfo?url=",
            "delicious"   => "http://feeds.delicious.com/v2/json/urlinfo/data?url=",
            "reddit"      => "http://www.reddit.com/api/info.json?&url=",
            "buffer"      => "https://api.bufferapp.com/1/links/shares.json?url=",
            "vk"          => "https://vk.com/share.php?act=count&index=1&url="
        );
        
        foreach($shareLinks as $service=>$provider) {
            @$this->getCount($service, $provider . $this->url);
        }
        
        switch($this->format) {
            case 'xml':
                return $this->generateValidXmlFromObj($this->data, "data");
            case 'json':
            case 'jsonp':
                return json_encode($this->data);
            default:
                return $this->data;
        }
    }
    
    // query API to get share counts
    private function getCount($service, $url){
        if(function_exists('curl_version')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_USERAGENT, 'shareCount/1.1 by abemedia');
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1);
            $data = curl_exec($ch);
            curl_close($ch);
        }
        else {
            $data = @file_get_contents($url);
        }
        
        if ($data) {
            switch($service) {
            case "facebook":
                $data = json_decode($data);
                $count = (is_array($data) ? $data[0]->total_count : $data->total_count);
                break;
            case "google":
                preg_match( '/window\.__SSR = {c: (\d+(?:\.\d+)+)/', $data, $matches);
				if(isset($matches[0]) && isset($matches[1])) {
					$bits = explode('.',$matches[1]);
					$count = (int)( empty($bits[0]) ?: $bits[0]) . ( empty($bits[1]) ?: $bits[1] ); 
				}
                break;
            case "pinterest":
                $data = substr( $data, 13, -1);
            case "linkedin":
            case "twitter":
                $data = json_decode($data);
                $count = $data->count;
                break;
            case "stumbleupon":
                $data = json_decode($data);
                $count = $data->result->views;
                break;
            case "delicious":
                $data = json_decode($data);
                $count = $data[0]->total_posts;
                break;
            case "reddit":
                $data = json_decode($data);
                foreach($data->data->children as $child) {
                    $ups+= (int) $child->data->ups;
                    $downs+= (int) $child->data->downs;
                }
                $count = $ups - $downs;
                break;
            case "buffer":
                $data = json_decode($data);
                $count = $data->shares;
                break;
            case "vk":
                $data = preg_match('/^VK.Share.count\(\d+,\s+(\d+)\);$/i', $data, $matches);
                $count = $matches[1];
                break;
            default:
                // kill the script if trying to fetch from a provider that doesn't exist
                die("Error: Service not found");
            }
            $count = (int) $count;
            $this->data->shares->total += $count;
            $this->data->shares->$service = $count;
        } else{
            $this->data->shares->$service = '';
        }
        return;
    }
    
    // Get data and return it. If cache is active check for cached data and create it if unsuccessful.
    private function getData() {
        if($this->cache) $key = md5($this->url) . '.' . ($this->format == 'jsonp' ? 'json' : $this->format);
        switch($this->cache) {
            case 'memcache':
                if(!function_exists('memcache_connect')) 
                { 
                    die('Memcache isn\'t installed'); 
                } 
                else
                {
                    $memcache = new Memcache;
                    $memcache->addServer($this->config->cache_server, $this->config->cache_port, $this->config->cache_persistent);
                    if(!$memcache->connect($this->config->cache_server, $this->config->cache_port)) 
                    { 
                        die('Couldn\'t connect to Memcache host'); 
                    } 
                    $data = $memcache->get($key);
                    if ($data === false) {
                        $data = $this->getShares();
                        $memcache->set($key, $data, $this->cache_time);
                    }
                }
                break;
            case 'apc':
                if (apc_exists($key)) {
                    $data = apc_fetch($key);
                }
                else {
                    $data = $this->getShares();
                    apc_store($key, $data, $this->cache_time);
                }
                break;
            case 'file': 
                $data = $this->getCacheFile($key);
                break;
            default:
                $data = $this->getShares();
        }
        // if the format is JSONP wrap in callback function
        if($this->format == 'jsonp') $data = $this->callback . '(' . $data . ')';
        
        return $data;
    }
    
    // get cache file - create if doesn't exist
    private function getCacheFile($key) {
        if (!file_exists($this->cache_directory)) {
            mkdir($this->cache_directory, 0777, true);
        }
        $file = $this->cache_directory . $key;
        $file_created = ((@file_exists($file))) ? @filemtime($file) : 0;
        @clearstatcache();
        if (time() - $this->cache_time < $file_created) {
            return file_get_contents($file);
        }
        $data = $this->getShares();
        $fp = @fopen($file, 'w'); 
        @fwrite($fp, $data);
        @fclose($fp);
        return $data;
    }
    
    // Delete expired file cache. Use "kill" parameter to also flush the memory and delete all cache files.
    public function cleanCache($kill = null) {
        // flush memcache
        if($kill) {
            switch($this->cache) {
                case 'memcache':
                    $memcache = new Memcache;
                    $memcache->flush();
                    break;
                case 'apc':
                    apc_clear_cache();
                    apc_clear_cache('user');
                    apc_clear_cache('opcode');
                    break;
            }
        }
        // delete cache files
        if ($handle = @opendir($this->cache_directory)) {
            while (false !== ($file = @readdir($handle))) {
                if ($file != '.' and $file != '..') {
                    $file_created = ((@file_exists($file))) ? @filemtime($file) : 0;
                    if (time() - $this->cache_time < $file_created or $kill) {
                        echo $file . ' deleted.<br>';
                        @unlink($this->cache_directory . '/' . $file);
                    }
                }
            }
            @closedir($handle);
        }
    }
    
    // output share counts as XML
    // functions adopted from http://www.sean-barton.co.uk/2009/03/turning-an-array-or-object-into-xml-using-php/
    public static function generateValidXmlFromObj(stdClass $obj, $node_block='nodes', $node_name='node') {
        $arr = get_object_vars($obj);
        return self::generateValidXmlFromArray($arr, $node_block, $node_name);
    }

    public static function generateValidXmlFromArray($array, $node_block='nodes', $node_name='node') {
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>';
        $xml .= '<' . $node_block . '>';
        $xml .= self::generateXmlFromArray($array, $node_name);
        $xml .= '</' . $node_block . '>';
        return $xml;
    }

    private static function generateXmlFromArray($array, $node_name) {
        $xml = '';
        if (is_array($array) || is_object($array)) {
            foreach ($array as $key=>$value) {
                if (is_numeric($key)) {
                    $key = $node_name;
                }
                $xml .= '<' . $key . '>' . self::generateXmlFromArray($value, $node_name) . '</' . $key . '>';
            }
        } else {
            $xml = htmlspecialchars($array, ENT_QUOTES);
        }
        return $xml;
    }
}
