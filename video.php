<?
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
<!-- zikzok -->
<link rel='stylesheet' href='/css/zikzok.css'>
<script src='/js/video.js'></script>
</head>
<body>
<div id='container'>
	<h1><a href='/'>ZikZok</a></h1>
	<h3 id='video-title'><?= htmlspecialchars($name)  ?></h3>
    <div id='video-info'>
        By: <?= $user ?>
    </div>
    <div id='video-stats'>
        Views: <span id='views'>0</span>
        Likes: <span id='likes'>0</span>
        <a href='#' id='like'><img src='/images/like.gif' style='margin-left: 10px; vertical-align: bottom;' height='24px'></a>
    </div>
	<video id='videoElt' class='video-js' controls playsinline>
		<source src='<?= $video ?>'>
	</video>
</div>
</body>
</html>
