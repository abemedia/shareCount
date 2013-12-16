<?php
require("config.php");
class shareCount {
	public $shares;
	public $url;
	public $callback;
	public $format;
	private $config;
	
	function __construct() {
		$this->config 		 = new config;
	}
	
	public function get() {
		$this->format 		 = $this->getFormat();
		$this->shares 		 = new stdClass;
		$this->shares->count = new stdClass;
		$this->shares->count->total = 0;
		$this->shares->url = $this->url;
		if(!$this->callback) $this->callback = (string) (isset($_REQUEST['callback']) ? $_REQUEST['callback'] : $this->config->callback);
		
		if($this->config->use_cache) $this->getCache();
		else $this->getShares();
	}
	
	// set format of the output
	private function getFormat () {
		if(!$this->format) $format = ($_REQUEST['format']?$_REQUEST['format']:$this->config->format);
		elseif(!$_REQUEST['format'])
		switch($_REQUEST['format']) {
			case "xml":
				$format = 'xml';
				header ("Content-Type:text/xml"); 
				break;
			case "jsonp": 
				if(!$this->callback) $this->callback = $this->config->callback;
			case "json": // only here for reference
			default:
				if($this->callback) {
					$format = 'jsonp';
					header ("Content-Type: application/javascript"); 
				}
				else {
					$format = 'json';
					header ("Content-Type:application/json");
				}
				break;
		}
		return $format;
	}
	
	// query API to get share counts
	private function getShares() {
		$this->url = ($this->url?$this->url:$_REQUEST['url']);
		$shareLinks = array(
			"facebook"		=> "https://api.facebook.com/method/links.getStats?format=json&urls=",
			"twitter"		=> "http://urls.api.twitter.com/1/urls/count.json?url=",
			"google"		=> "https://plusone.google.com/_/+1/fastbutton?url=",
			"reddit"		=> "http://www.reddit.com/api/info.json?&url=",
			"linkedin"		=> "http://www.linkedin.com/countserv/count/share?format=json&url=",
			/*"digg"			=> "http://widgets.digg.com/buttons/count?url=",*/
			"delicious"		=> "http://feeds.delicious.com/v2/json/urlinfo/data?url=",
			"stumbleupon"	=> "http://www.stumbleupon.com/services/1.01/badge.getinfo?url=",
			"pinterest"		=> "http://widgets.pinterest.com/v1/urls/count.json?source=6&url="
		);
		foreach($shareLinks as $service=>$url) {
			$this->getCount($service, $url);
		}
		
		if($this->format == 'xml') $data = $this->generateValidXmlFromObj($this->shares, "shares");
		elseif($this->format == 'jsonp') $data = $this->callback . "(" . json_encode($this->shares) . ")";
		else $data = json_encode($this->shares);
		
		return $data;
	}
	
	// query API to get share counts
	private function getCount($service, $url){
		$count = 0;
		$data = @file_get_contents($url . ($this->url));
		if ($data) {
			switch($service) {
				case "facebook":
					$data 		= json_decode($data);
					$count	 	= (is_array($data) ? $data[0]->total_count : $data->total_count);
					break;
				case "google":
					preg_match( '/window\.__SSR = {c: ([\d]+)/', $data, $matches );
					if(isset($matches[0])) $count = str_replace( 'window.__SSR = {c: ', '', $matches[0] );
					break;
				case "pinterest":
					$data 		= substr( $data, 13, -1);
				case "linkedin":
				case "twitter":
					$data 		= json_decode($data);
					$count 		= $data->count;
					break;
				case "reddit":
					$data 		= json_decode($data);
					if(count($data->data->children))
						$count 	= $data->data->children[0]->data->score;
					break;
				case "delicious":
					$data 		= json_decode($data);
					$count 		= $data[0]->total_posts;
					break;
				case "stumbleupon":
					$data 		= json_decode($data);
					$count 		= $data->result->views;
					break;
			}
			$count =  (int)$count;
			$this->shares->count->total += $count;
			$this->shares->count->$service = $count;
		} 
		return;
	}
	
	// get cached output - create if doesn't exist
	private function getCache() {
		if (!file_exists($this->config->cache_directory)) {
    		mkdir($this->config->cache_directory, 0777, true);
		}
		$URi = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$cachefile = $this->config->cache_directory . md5($URi) . '.' . $this->format;
		$cachefile_created = ((@file_exists($cachefile))) ? @filemtime($cachefile) : 0;
		@clearstatcache();
		if (time() - $this->config->cache_time < $cachefile_created) {
			//ob_start('ob_gzhandler');
			@readfile($cachefile);
			//ob_end_flush();
			exit();
		}
		ob_start();
		echo $this->getShares();
		$fp = @fopen($cachefile, 'w'); 
		@fwrite($fp, ob_get_contents());
		@fclose($fp); 
		ob_end_flush(); 
	}
	
	// delete expired cache
	public function cleanCache($kill = null) {
		$i = 0;
		if ($handle = @opendir($config->cache_directory)) {
			while (false !== ($file = @readdir($handle))) {
				if ($file != '.' and $file != '..') {
					$file_created = ((@file_exists($file))) ? @filemtime($file) : 0;
					if (time() - $this->config->cache_time < $file_created or $kill) {
						$i++;
						echo $file . ' deleted.<br>';
						@unlink($this->config->cache_directory . '/' . $file);
					}
				}
			}
			@closedir($handle);
		}
		if($i==0) echo 'Nothing to clean.';
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
    }}