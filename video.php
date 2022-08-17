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

        // Is this a reply?
        $stmt = $mysqli->prepare('select oldvideo, name from replies
                            inner join videos on videos.uniqid = replies.oldvideo
                            where replies.newvideo = ?');
        $stmt->bind_param('s', $_GET['v']);
        $stmt->execute();
        $this_reply_res = $stmt->get_result();
    
        // Get replies
        $stmt = $mysqli->prepare('select newvideo, name, created from replies 
                            inner join videos on videos.uniqid = replies.newvideo 
                            where replies.oldvideo = ?');
        $stmt->bind_param('s', $_GET['v']);
        $stmt->execute();
        $replies_res = $stmt->get_result();
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
<?php
    if ($this_reply_res->num_rows) {
        $row = $this_reply_res->fetch_assoc();
        $olduniqid = $row['oldvideo'];
        $oldname = $row['name'];
        echo "<p>This is a reply to video <a href='/video.php?v=$olduniqid'>$oldname<img class='reply-thumb' src='/preview/$olduniqid.png'></a></p>";
    }
?>
	<p>
        <span id='video-title'>
        <?= htmlspecialchars($name)  ?></span><br>
        By: <?= $user ?>
    </p>
    <div id='video-stats'>
        Views: <span id='views'><?= $views ?></span>
        Likes: <span id='likes'><?= $likes ?></span><br>
        <div style='display: inline-block; margin-top: 7px;'>
            <video id='videoElt' class='video-js' controls playsinline>
                <source src='<?= $video ?>'>
            </video>
        </div>
        <div style='vertical-align: top; display: inline-block; position: relative;'>
            <div style='position: absolute; top: -39px; left: -145px; width: 145px;'>
                <a href='/record/index.php?replyto=<?= $_GET['v'] ?>'>Reply</a>
                <a href='#' id='like'>
                    Like it
                    <img src='/images/like.gif' height='32px'>
                </a>
            </div>
        </div>
    </div>
    <div id='replies'>
<?php
    if ($replies_res->num_rows) {
        $n = $replies_res->num_rows;
        echo "Replies: ($n)<br>";
        echo "<ol>";
    }
    while ($row = $replies_res->fetch_assoc()) {
        $name = $row['name'];
        $uniqid = $row['newvideo'];
        $created = $row['created'];
        echo "<li><div style='display: inline-block;'>
            <a href='/video.php?v=$uniqid'>$name</a><br>$created</div> 
            <a href='/video.php?v=$uniqid'><img class='reply-thumb' src='/preview/$uniqid.png'></a></li>";
    }
    if ($replies_res->num_rows) {
        echo "</ol>";
    }
?>
    </div>
</div>
</body>
</html>
