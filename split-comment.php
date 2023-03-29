<?php
include_once('mysql.php');

$c = intval($_GET['c']);
$v = $_GET['v'];
$before = urldecode($_GET['before']);
$after = urldecode($_GET['after']);
$stmt = $mysqli->prepare('insert into comment_splits (split_which, uniqid, `before`, `after`) values (?, ?, ?, ?)');
$stmt->bind_param('isss', $c, $v, $before, $after);
$stmt->execute();

echo "OK";
?>
