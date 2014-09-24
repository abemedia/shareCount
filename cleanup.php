<?php
require("share_count.php");
$count = new shareCount;
$count->cleanCache(isset($_REQUEST['kill']));
echo 'Cache has been ' . (isset($_REQUEST['kill']) ? 'deleted' : 'cleaned') . '.';