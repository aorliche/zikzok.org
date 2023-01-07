
<?php
    include_once('mysql.php');
    include_once('alphasongs.php');
    include_once('functions.php');
    
    if ($_GET['v']) {
        $stmt = $mysqli->prepare('select * from videos where uniqid = ?');
        $stmt->bind_param('s', $_GET['v']);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $video = '/videos/'.$row['uniqid'].'.'.$row['ext'];
        $preview = '/preview/'.$row['uniqid'].'.png';
        $id = $row['id'];
        $name = $row['name'];
        $user = $row['userid'];
        $views = $row['views'];
        $likes = intval($row['likes']);//+ord($name);
        $created = $row['created'];
        if (!$user) $user = 'Anonymous';
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
<!-- MSPs -->
<script src='/js/msp/build_common.js'></script>
<script src='/js/msp/build_flower.js'></script>
<script src='/js/msp/build_random.js'></script>
<script src='/js/msp/build_star.js'></script>
<script src='/js/msp/chains.js'></script>
<script src='/js/msp/draw.js'></script>
<script src='/js/msp/expand_common.js'></script>
<script src='/js/msp/expand_flower.js'></script>
<script src='/js/msp/expand_random.js'></script>
<script src='/js/msp/flips.js'></script>
<script src='/js/msp/hole.js'></script>
<script src='/js/msp/msp.js'></script>
<script src='/js/msp/rhombus.js'></script>
<script src='/js/msp/util.js'></script>
<script src='/js/msp/vertex.js'></script>
<!-- zikzok -->
<link rel='stylesheet' href='/css/zikzok.css'>
<link rel='stylesheet' href='/css/video.css'>
<script src='/js/video_split.js'></script>
<script src='/js/video_msp.js'></script>
</head>
<body>
<div id='container'>
<?php
    include('header.php');
?>
	<div id='video-header'>
        <div id='video-title-info'>
            <div id='video-title'><?= htmlspecialchars($name)  ?></div>
            <div id='video-info'>By: <?= $user ?> Created: <?= $created ?></div>
        </div>
        <canvas width=80 height=80></canvas>
    </div>
    <div id='video-main'>
        Views: <span id='views'><?= $views ?></span>
        Likes: <span id='likes' class='hunimal-font'><?= likesToHunimal($likes) ?></span>
        <video id='video-elt-1' class='video-js' controls playsinline>
            <source src='<?= $video ?>'>
        </video>
        <textarea>
        These two videos are connected because...
        </textarea>
        <button id='post-split-button'>Finished</button>
        <video id='video-elt-2' class='video-js' controls playsinline>
            <source src='<?= $video ?>'>
        </video>
        <div id='my-time'><?= $_GET['t'] ?></div>
    </div>
</div>
</body>
</html>
