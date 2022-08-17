<?php
    include_once('mysql.php');

    $stmt = $mysqli->prepare('select uniqid,name,created,count(newvideo) as nreplies from videos 
        left join replies on videos.uniqid = replies.oldvideo 
        where uniqid is not null 
        group by uniqid 
        order by created desc');
    $stmt->execute();
    $res = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<title>ZikZok</title>
<link rel="icon" type="image/png" href="/images/icon.png">
<!-- videojs -->
<!--<script src="https://vjs.zencdn.net/7.20.1/video.min.js"></script>
<link href="https://vjs.zencdn.net/7.20.1/video-js.css" rel="stylesheet" />-->
<!-- zikzok -->
<link rel='stylesheet' href='css/zikzok.css'>
<script src='js/zikzok.js'></script>
</head>
<body>
<div id='container'>
	<h1><a href='/'>ZikZok</a></h1>
	<div id='navigation'><a href='record/'>Record a video</a> <a href='signup.php'>Sign up</a></div>
	<h2>Recent Videos</h2>
	<div id='videos'>
<?php
    while ($row = $res->fetch_assoc()) {
        $name = $row['name'];
        $uniqid = $row['uniqid'];
        $created = $row['created'];
        $nreplies = $row['nreplies'];
?>
    <div class="video-block">
		<p>
            <a href="video.php?v=<?= $uniqid ?>"><span class="video-name"><?= htmlspecialchars($name) ?></span></a>
<?php
    if ($nreplies) {
        echo "<br>($nreplies replies)";
    }
?>
            <br>
		    <?= $created ?>
        </p>
		<a href="video.php?v=<?= $uniqid ?>">
            <img class="video-preview" alt="<?= $uniqid ?>" src="preview/<?= $uniqid ?>.png">
        </a>
	</div>
<?php
    }
?>
	</div>
</div>
</body>
</html>
