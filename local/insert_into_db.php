<?php
    // Put all old videos into db with only title and give unique ids

    include_once('mysql.php');

    $files = scandir('videos');
    foreach ($files as $file) {
        $time = filemtime("videos/$file");
        $uniqid = uniqid();
        $stmt = $mysqli->prepare('insert into videos (uniqid, name, created) values (?, ?, from_unixtime(?))');
        $stmt->bind_param('ssi', $uniqid, $file, $time);
        $stmt->execute();
    }
?>
