<?php
header('Content-type: application/json; charset=utf-8');
include_once('mysql.php');

$stmt = $mysqli->prepare('select name, uniqid from videos order by created desc');
$stmt->execute();
$res = $stmt->get_result();
$rows = array();

while ($row = $res->fetch_assoc()) {
    array_push($rows, $row); 
}

echo json_encode($rows, JSON_UNESCAPED_SLASHES);
?>
