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
<link rel='stylesheet' href='zikzok.css'>
<script src='zikzok.js'></script>
</head>
<body>
<div id='container'>
	<h1><a href='/'>ZikZok</a></h1>
	<div id='navigation'><a href='record/'>Record a video</a> <a href='signup.php'>Sign up</a></div>
	<h2>Recent Videos</h2>
	<div id='videos'>
<?php
	function cmp($a, $b) {
		return $b-$a;
	}
	$images = array();
	$it = new FilesystemIterator('preview/');
	foreach ($it as $finfo) {
		if ($finfo->isFile()) {
			$fname = $finfo->getFileName();
			$ctime = $finfo->getCTime();
			$images[$fname] = $ctime;
		}
	}
	uasort($images, 'cmp');
	foreach ($images as $fname => $ctime) {
		$name = basename($fname, '.png');
		$url = urlencode($name);
		$aStart = '<a href="video.php?v='.$url.'">';
		echo '<div class="video-block">';
		echo '<p>'.$aStart.'<span class="video-name">'.htmlspecialchars($name).'</span></a><br>';
		echo gmdate('M d, Y, g:ia', $ctime).' GMT</p>';
		echo $aStart.'<img class="video-preview" src="preview/'
			.escapeExtra(htmlspecialchars($fname)).'"></a>';
		echo '</div>';
	}
?>
	</div>
</div>
</body>
</html>
