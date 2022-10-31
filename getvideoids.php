<?php
    include_once('mysql.php');

    $stmt = $mysqli->prepare('select uniqid from videos');
    $stmt->execute();
    $res = $stmt->get_result();
    $uniqids = array();
    
    while ($row = $res->fetch_assoc()) {
        array_push($uniqids, $row['uniqid']);
    }

    header('Content-type: application/json; charset=utf-8');
    echo json_encode($uniqids, JSON_UNESCAPED_SLASHES);
?>

