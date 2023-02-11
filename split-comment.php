<?php
include_once('mysql.php');

$c = intval($_GET['c']);
$stmt = $mysqli->prepare('insert into comment_splits (cid, uniqid) values (?, ?)');
$stmt->bind_param('is', $c, $_GET['v']);
$stmt->execute();

echo "OK";
?>
