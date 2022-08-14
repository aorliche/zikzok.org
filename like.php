<?php
    include('mysql.php');

    $stmt = $mysqli->prepare('update videos set likes = likes + 1 where uniqid = ?');
    $stmt->bind_param('s', $_GET['v']);
    $stmt->execute();

    echo 'ok';
?>
