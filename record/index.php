<?php
    include('../mysql.php');

	header('Cross-Origin-Embedder-Policy: require-corp');
	header('Cross-Origin-Opener-Policy: same-origin');

    $iPod    = stristr($_SERVER['HTTP_USER_AGENT'],"iPod");
    $iPhone  = stristr($_SERVER['HTTP_USER_AGENT'],"iPhone");
    $iPad    = stristr($_SERVER['HTTP_USER_AGENT'],"iPad");
    $Android = stristr($_SERVER['HTTP_USER_AGENT'],"Android");
    $webOS   = stristr($_SERVER['HTTP_USER_AGENT'],"webOS");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<link rel="icon" type="image/png" href="/images/icon.png">
<title>Record Video | ZikZok</title>
<!-- RecordRTC -->
<script src="/lib/node_modules/recordrtc/RecordRTC.min.js"></script>
<!-- videojs -->
<script src="/lib/node_modules/video.js/dist/video.min.js"></script>
<link href="/lib/node_modules/video.js/dist/video-js.min.css" rel="stylesheet" />
<!-- ffmpeg.wasm -->
<script src="/lib/node_modules/@ffmpeg/ffmpeg/dist/ffmpeg.min.js"></script>
<!-- videojs-record -->
<script src='/lib/node_modules/videojs-record/dist/videojs.record.min.js'></script>
<link rel="stylesheet" href="/lib/node_modules/videojs-record/dist/css/videojs.record.min.css">
<!-- ffmpeg.wasm -->
<script src="/lib/node_modules/videojs-record/dist/plugins/videojs.record.ffmpeg-wasm.min.js"></script>
<!-- zikzok -->
<script src='/js/predict.js'></script>
<script type='module' src='record.js'></script>
<link rel='stylesheet' href='/css/zikzok.css'>
<link rel='stylesheet' href='/css/record.css'>
</head>
<body>
<div id='container'>
	<h1><a href='/'>ZikZok</a></h1>
	<h2>Record a Video</h2>
<?php
    $uniqid = $_GET['replyto'];
    $name = '';

    if ($uniqid) {
        $stmt = $mysqli->prepare('select * from videos where uniqid = ?');
        $stmt->bind_param('s', $uniqid);
        $stmt->execute();
        $res = $stmt->get_result();
       
        $row = $res->fetch_assoc();
        if ($row) {
            $name = $row['name'];
            echo "<p>You are replying to a video <a href='/video.php?v=$uniqid'>$name<img class='reply-thumb' src='/preview/$uniqid.png'></a></p>";
            $name = "RE: $name";
        }
    }
?>
	<p>Name: <input type='text' id='name' value='<?= $name ?>' data-replyto='<?= $uniqid ?>'></p>
    <?php include('../predict.php'); ?>
    <div id='post-pred'>
<?php
    if ($iPod || $iPhone || $iPad || $Android || $webOS || $_GET['alt']) {
?>
	<ul>
		<li>Click the record button</li>
		<li>Record your video</li>
		<li>Wait for your video to upload</li>
		<li>Videos are limited to 60s</li>
	</ul>
    <label for='mobile-elt' id='styled-mobile-elt'>Record Video</label>
	<input type='file' accept='video/mp4' capture='user' id='mobile-elt'>
<?php
    } else {
?>
	<ul>
		<li>Click the camera</li>
		<li>Allow browser permissions</li>
		<li>Record your video</li>
        <li>Wait for the video to convert and upload</li>
		<li>Videos are limited to 60s</li>
	</ul>
    <p><strong>If you're having trouble, <a href='/record/index.php?alt=true'>switch to the alternate view</a></strong></p>
	<video id='videoElt' controls playsinline preload='none' class='video-js vjs-default-skin'></video>
<?php
    }
?>
	<div id='info'></div>
    <div id='more-info'></div>
    </div>
</div>
</body>
</html>
