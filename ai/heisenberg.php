<?php
    $dir = dirname(__FILE__);

    include("$dir/../mysql.php");

    function like_video($mysqli, $uniqid, $type) {
        if ($type == 'like') {
            $stmt = $mysqli->prepare('update videos set likes = likes+1 where uniqid = ?');
            $stmt->bind_param('s', $uniqid);
            $stmt->execute();
        }
        $stmt = $mysqli->prepare('insert into likes (uniqid, userid, type) values (?, 1, ?)');
        $stmt->bind_param('ss', $uniqid, $type);
        $stmt->execute();
    }

    // Like random videos
    // 2 Recent videos
    $stmt = $mysqli->prepare('select uniqid from videos 
        where unix_timestamp(created)+2*24*3600 > unix_timestamp(current_timestamp())
        order by rand() limit 2');
    $stmt->execute();
    $res = $stmt->get_result();
    $recent = array();
    while ($row = $res->fetch_assoc()) {
        array_push($recent, $row['uniqid']);
        like_video($mysqli, $row['uniqid'], 'like');
    }

    // 1 Recent video with no likes (comments + replies too hard)
    $stmt = $mysqli->prepare('select uniqid from videos 
        where unix_timestamp(created)+2*24*3600 > unix_timestamp(current_timestamp())
        and likes = 0
        order by rand()');
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        if (!in_array($row['uniqid'], $recent)) {
            like_video($mysqli, $row['uniqid'], 'like');
            break;
        }
    }

    // 2 Old time videos
    $stmt = $mysqli->prepare('select uniqid from videos 
        where unix_timestamp(created)+2*24*3600 < unix_timestamp(current_timestamp()) 
        order by rand() limit 2');
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        like_video($mysqli, $row['uniqid'], 'like');
    }

    // 2 Recent random videos are disagreed with
    $stmt = $mysqli->prepare('select uniqid from videos 
        where unix_timestamp(created)+2*24*3600 > unix_timestamp(current_timestamp())
        order by rand() limit 2');
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        like_video($mysqli, $row['uniqid'], 'disagree');
    }
?>
