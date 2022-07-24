<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<link rel="icon" type="image/png" href="/images/icon.png">
<title>Record Video | ZikZok</title>
<!-- RecordRTC -->
<script src="https://www.WebRTC-Experiment.com/RecordRTC.js"></script>
<!-- videojs -->
<script src="https://vjs.zencdn.net/7.20.1/video.min.js"></script>
<link href="https://vjs.zencdn.net/7.20.1/video-js.css" rel="stylesheet" />
<!-- videojs-record -->
<script src='https://unpkg.com/videojs-record/dist/videojs.record.min.js'></script>
<link rel="stylesheet" href="https://unpkg.com/videojs-record/dist/css/videojs.record.min.css">
<!-- zikzok -->
<script src='record.js'></script>
<link rel='stylesheet' href='/zikzok.css'>
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
	<p>Name: <input type='text' id='name' value='<?= uniqid(); ?>'></p>
	<video id='videoElt' controls playsinline preload='none' class='video-js vjs-default-skin'></video>
</div>
</body>
</html>
