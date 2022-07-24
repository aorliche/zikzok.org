<?php 
	require_once($_SERVER['DOCUMENT_ROOT'].'/functions.php');
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
<link rel='stylesheet' href='/zikzok.css'>
<script src='/video.js'></script>
</head>
<body>
<?php
	$video = $_GET['v'];
	$videoMp4 = "$video.mp4";
	$videoWebm = "$video.webm";
	if (file_exists($_SERVER['DOCUMENT_ROOT'].'/videos/'.$videoMp4)) {
		$src = $videoMp4;
	} else if (file_exists($_SERVER['DOCUMENT_ROOT'].'/videos/'.$videoWebm)) {
		$src = $videoWebm;
	} else {
		$src = '';
	}
?>
<div id='container'>
	<h1><a href='/'>ZikZok</a></h1>
	<h3><?= htmlspecialchars($_GET['v']) ?></h3>
	<video id='videoElt' class='video-js' controls playsinline>
		<source src='/videos/<?= escapeExtra(htmlspecialchars($src)) ?>'>
	</video>
</div>
</body>
</html>
