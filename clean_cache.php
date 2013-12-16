<?php
require("config.php");
$i = 0;
if ($handle = @opendir($config->cache_directory)) {
	while (false !== ($file = @readdir($handle))) {
		if ($file != '.' and $file != '..') {
			$file_created = ((@file_exists($file))) ? @filemtime($file) : 0;
			if (time() - config::cache_time < $file_created) {
				$i++;
				echo $file . ' deleted.<br>';
				@unlink(config::cache_directory . '/' . $file);
			}
		}
	}
	@closedir($handle);
}
if($i==0) echo 'Nothing to clean.';