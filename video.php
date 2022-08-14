<?php
    include_once('mysql.php');
   
    if ($_GET['v']) {
        $stmt = $mysqli->prepare('select * from videos where uniqid = ?');
        $stmt->bind_param('s', $_GET['v']);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $video = '/videos/'.$row['uniqid'].'.'.$row['ext'];
        $preview = '/preview/'.$row['uniqid'].'.png';
        $name = $row['name'];
        $user = $row['userid'];
        $views = $row['views'];
        $likes = $row['likes'];
        if (!$user) $user = 'Anonymous';

        $stmt = $mysqli->prepare('update videos set views = views + 1 where id = ?');
        $stmt->bind_param('i', $row['id']);
        $stmt->execute();

        $views = $views+1;
    }
?>
<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<title>ZikZok</title>
<link rel="icon" type="image/png" href="/images/icon.png">
<!-- videojs -->
<script src="https://vjs.zencdn.net/7.20.1/video.min.js"></script>
<link href="https://vjs.zencdn.net/7.20.1/video-js.css" rel="stylesheet" />
<!-- zikzok -->
<link rel='stylesheet' href='/css/zikzok.css'>
<link rel='stylesheet' href='/css/video.css'>
<script src='/js/video.js'></script>
</head>
<body>
<div id='container'>
	<h1><a href='/'>ZikZok</a></h1>
	<div>
        <span id='video-title'>
        <?= htmlspecialchars($name)  ?></span><br>
        By: <?= $user ?>
    </div>
    <?php include('predict.php'); ?>
    <div id='video-stats'>
        Views: <span id='views'><?= $views ?></span>
        Likes: <span id='likes'><?= $likes ?></span><br>
        <div style='display: inline-block; margin-top: 7px;'>
            <video id='videoElt' class='video-js' controls playsinline>
                <source src='<?= $video ?>'>
            </video>
        </div>
        <div style='vertical-align: top; display: inline-block; position: relative;'>
            <a href='#' id='like' style='position: absolute; top: -22px; left: -48px;'>Like it<img src='/images/like.gif' height='48px'></a>
        </div>
    </div>
    <span id='pred-span'></span>
</div>
</body>
</html>
