<?php
require("share_count.php");
$count = new shareCount;
$count->cleanCache(isset($_REQUEST['kill']) ? true : false);
