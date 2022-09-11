<?php
    include('mysql.php');
    
    // Update simple likes
    if ($_GET['type'] == 'like') {
        $stmt = $mysqli->prepare('update videos set likes = likes + 1 where uniqid = ?');
        $stmt->bind_param('s', $_GET['v']);
        $stmt->execute();
    }

    // Get user info
    $ip = $_SERVER['REMOTE_ADDR'];

    // Update detailed likes
    $stmt = $mysqli->prepare('insert into likes (uniqid, ip, type) values (?, ?, ?)');
    $stmt->bind_param('sss', $_GET['v'], $ip, $_GET['type']);
    $stmt->execute();

    echo 'ok';
?>
