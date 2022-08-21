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
    <div id='video-main'>
        Views: <span id='views'><?= $views ?></span>
        Likes: <span id='likes'><?= $likes ?></span>
        <a href='/record/index.php?replyto=<?= $_GET['v'] ?>'>Reply</a>
        <a href='#like' id='like'>Like it<img src='/images/like.gif' height='32px'></a>
        <a href='#open-editor' id='open-editor'>Open editor</a>
        <br>
        <div style='display: inline-block; margin-top: 7px; position: relative;'>
            <div id='video-overlay'></div>
            <video id='video-elt' class='video-js' controls playsinline>
                <source src='<?= $video ?>'>
            </video>
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
    <div id='editor'>
        <input type='checkbox' id='display-overlay' checked>
            <span id='overlay-feedback1'>Editing (video play disabled)</span>
            <span id='overlay-feedback2'>Video controls enabled (not editing)</span><br>
        <button id='add-text'>Add text</button><br>
        <label for='text-font-size'>Font size:</label> <input type='range' id='text-font-size' min='8' max='80' step='8' value='24'><br>
        Color<br>
        <label for='text-font-red'>Red:</label> <input type='range' id='text-font-red' min='0' max='255' step='15' value='255'><br>
        <label for='text-font-green'>Green:</label> <input type='range' id='text-font-green' min='0' max='255' step='15' value='45'><br>
        <label for='text-font-blue'>Blue:</label> <input type='range' id='text-font-blue' min='0' max='255' step='15' value='45'><br>
        Timing<br>
        <label for='start-time'>Start:</label> <input type='range' id='start-time' min='0' max='100' step='1' value='0'><br>
        <label for='end-time'>End:</label> <input type='range' id='end-time' min='0' max='100' step='1' value='100'><br>
        <button id='delete-text'>Delete text</button><br>
        <a href='#close-editor' id='close-editor'>Close editor</a><br>
        <ol id='texts'></ol>
    </div>
</div>
</body>
</html>
