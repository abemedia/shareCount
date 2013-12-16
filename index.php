<?php
require("share_count.php");
$count = new shareCount;
$bla = $count->get();
print_r($bla);
?>