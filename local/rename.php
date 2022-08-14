<?php

    include_once('mysql.php');

    $stmt = $mysqli->prepare('select * from videos where userid is NULL and uniqid is not NULL');
    $stmt->execute();
    $res = $stmt->get_result();
    
    while ($row = $res->fetch_assoc()) {
        $name = $row['name'];
        $uniqid = $row['uniqid'];
        $ext = pathinfo($name)['extension'];
        if ($ext !== 'webm' && $ext !== 'mp4') continue;
        rename("videos/$name", "videos/$uniqid.$ext");
    }

?>
