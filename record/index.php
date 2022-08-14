<?php
	header('Cross-Origin-Embedder-Policy: require-corp');
	header('Cross-Origin-Opener-Policy: same-origin');

    $iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
    $iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
    $iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
    $Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
    $webOS   = stripos($_SERVER['HTTP_USER_AGENT'],"webOS");

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
<script type='module' src='record.js'></script>
<link rel='stylesheet' href='/css/zikzok.css'>
</head>
<body>
<div id='container'>
	<h1><a href='/'>ZikZok</a></h1>
	<h2>Record a Video</h2>
	<ul>
		<li>Click the camera</li>
		<li>Allow browser permissions</li>
		<li>Click the circle to record</li>
		<li>Click the square to stop and upload</li>
		<li>Videos are limited to 60s</li>
	</ul>
	<p>Name: <input type='text' id='name' value=''></p>
<?php
    if ($iPod || $iPhone || $iPad) {
?>
	<input type='file' accept='video/mp4' capture='user' id='iosElt'>
<?php
    } else {
?>
	<video id='videoElt' controls playsinline preload='none' class='video-js vjs-default-skin'></video>
<?php
    }
?>
	<div id='info'></div>
</div>
</body>
</html>
