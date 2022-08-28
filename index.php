<?php
    include_once('mysql.php');

    $NTOP = 8;
    $NRECENT = 20;

    // Get videos + number replies
    $stmt = $mysqli->prepare('select uniqid,name,likes,created,unix_timestamp(created) as created_unix,count(newvideo) as nreplies from videos 
        left join replies on videos.uniqid = replies.oldvideo 
        where uniqid is not null 
        group by uniqid 
        order by created desc');
    $stmt->execute();
    $res = $stmt->get_result();

    $top = array();
    $rest = array();

    function compareTop($a, $b) {
        $ascore = round($a['nreplies']*pow(2, (intval($a['created_unix'])-time())/(3600*24))*10000);
        $bscore = round($b['nreplies']*pow(2, (intval($b['created_unix'])-time())/(3600*24))*10000);
        return $bscore-$ascore;
    }

    while ($row = $res->fetch_assoc()) {
        // Get number of comments for each video
        $uniqid = $row['uniqid'];
        $stmt = $mysqli->prepare('select uniqid,count(id) as ncomments from comments where uniqid = ? group by uniqid order by created desc');
        $stmt->bind_param('s', $uniqid);
        $stmt->execute();
        $res2 = $stmt->get_result();
        $crow = $res2->fetch_assoc();
        $row['ncomments'] = $crow['ncomments'] ? $crow['ncomments'] : 0;

        // Add to top and rest (rest is actually all)
        array_push($rest, $row);
        array_push($top, $row);
    }

    uasort($top, 'compareTop');
    $top = array_slice($top, 0, $NTOP);
    
    if (!$_GET['all']) {
        $rest = array_slice($rest, 0, $NRECENT);
    }

    function outputVideo($row) {
        $name = htmlspecialchars($row['name']);
        $uniqid = htmlspecialchars($row['uniqid']);
        $created = htmlspecialchars($row['created']);
        $likes = htmlspecialchars($row['likes']);
        $nreplies = $row['nreplies'];
        $ncomments = $row['ncomments'];
        echo <<<EOT
    <div class="video-block">
		<p>
            <a href="video.php?v=$uniqid"><span class="video-name">$name</span></a>
EOT;
        $discussion = array();
        if ($nreplies) {
            array_push($discussion, "$nreplies replies");
        } 
        if ($likes) {
            array_push($discussion, "$likes likes");
        }
        if ($ncomments) {
            array_push($discussion, "$ncomments comments");
        }
        if (count($discussion)) {
            echo '<br>(' . implode(', ', $discussion) . ')';
        }
        echo <<<EOT
            <br>
		    $created
        </p>
		<a href="video.php?v=$uniqid">
            <img class="video-preview" alt="$uniqid" src="preview/$uniqid.png">
        </a>
	</div>
EOT;
    }

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
    <h2>Top Videos</h2>
    <div id='top-videos'>
<?php
    foreach ($top as $row) {
        outputVideo($row);
    }
?>
    </div>
	<h2>Recent Videos 
        <span style='font-size: 16px; font-weight: normal;'>
            (<?= count($rest) ?> videos)
            <a href='/index.php?all=true'>Show all</a>
            <a href='/'>Show fewer</a>
        </span>
    </h2>
	<div id='videos'>
<?php
    foreach ($rest as $row) {
        outputVideo($row);
    }
?>
	</div>
</div>
</body>
</html>
