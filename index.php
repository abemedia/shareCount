<?php
require("share_count.php");
$count = new shareCount;

// this starts the API server
echo $count->serve();

// use the following to get the shares as object
// $shares = $count->get('http://abemedia.co.uk');
// print_r($shares);