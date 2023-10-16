<?php
    include_once('mysql.php');
    include_once('functions.php');

    $NTOP = 8;
    $NRECENT = 20;
    $NCOMMENTS = 5;

    // Get latest comments
    $stmt = $mysqli->prepare('select comments.uniqid, comments.name, comments.comment, videos.name from comments 
        left join videos on comments.uniqid = videos.uniqid order by comments.id desc limit 5');
    $stmt->execute();
    $res = $stmt->get_result();

    $latest_comments = array();

    while ($row = $res->fetch_assoc()) {
        array_push($latest_comments, $row);
    }

    // Get deleted videos
    $stmt = $mysqli->prepare('select uniqid from deleted');
    $stmt->execute();
    $res = $stmt->get_result();
    $deleted = array();

    while ($row = $res->fetch_assoc()) {
        array_push($deleted, $row['uniqid']);
    }

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
        $ascore = round(($a['nreplies']+$a['ncomments']/3+$a['likes']/10)*pow(2, (intval($a['created_unix'])-time())/(2*3600*24))*10000);
        $bscore = round(($b['nreplies']+$b['ncomments']/3+$b['likes']/10)*pow(2, (intval($b['created_unix'])-time())/(2*3600*24))*10000);
        return $bscore-$ascore;
    }

    while ($row = $res->fetch_assoc()) {
        // Get number of comments for each video
        $uniqid = $row['uniqid'];
        // Skip deleted videos
        if (in_array($uniqid, $deleted)) {
            continue;
        }
        $stmt = $mysqli->prepare('select uniqid,count(id) as ncomments from comments where uniqid = ? group by uniqid order by created desc');
        $stmt->bind_param('s', $uniqid);
        $stmt->execute();
        $res2 = $stmt->get_result();
        if ($res2->num_rows) {
            $crow = $res2->fetch_assoc();
            $row['ncomments'] = $crow['ncomments'] ? $crow['ncomments'] : 0;
        } else {
            $row['ncomments'] = 0;
        }
        
        // Get Heisenberg's likes
        /*$stmt = $mysqli->prepare('select type from likes where userid = 1 and uniqid = ?');
        $stmt->bind_param('s', $uniqid);
        $stmt->execute();
        $likes_res = $stmt->get_result();
        $row['hscore'] = 0;
        while ($lrow = $likes_res->fetch_assoc()) {
            $type = $lrow['type'];
            if ($type == 'like') {
                $row['hscore'] += 10;
            } else if ($type == 'dislike') {
                $row['hscore'] += 3;
            } else {
                $row['hscore'] += 1;
            }
        }*/

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
        $likes = $row['likes'] != 0 ? likesToHunimal(intval($row['likes'])) : false;//+ord($name));
        $nreplies = $row['nreplies'];
        $ncomments = $row['ncomments'];
        //$hscore = $row['hscore'] < 99 ? sprintf('%02d', $row['hscore']) : 99;
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
            array_push($discussion, "<span class='hunimal-font'>$likes</span> likes");
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
            <div style='position: relative; display: inline-block;'>
        EOT;
        /*if ($hscore > 0) {
            echo <<<EOT
            <div class='hoverlay-back' style='position: absolute; display: inline-block; z-index: 8; width: 100%; text-align: center; 
                background-color: white;'></div>
            <div style='position: absolute; display: inline-block; z-index: 10; font-size: 80px; font-weight: bold; width: 100%;
                text-align: center; background: url(/preview/$uniqid.png); background-size: cover; background-clip: text; 
                -webkit-background-clip: text; -webkit-text-fill-color: transparent;' class='hunimal-font hoverlay'>&#x55$hscore</div>
            <img alt="$uniqid" src="preview/$uniqid.png"></div>
            EOT;
        } else {*/
            echo <<<EOT
            <img alt="$uniqid" src="preview/$uniqid.png"></div>
            EOT;
        //}
        echo <<<EOT
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
<?php
    include('header.php');
?>
    <h2>Latest Comments</h2>
    <ul id='latest-comments'>
<?php
    foreach ($latest_comments as $row) {
        $cname = htmlspecialchars($row['comments.name']);
        $vname = htmlspecialchars($row['videos.name']);
        $uniqid = htmlspecialchars($row['uniqid']);
        $comment = htmlspecialchars($row['comments.comment']);
        echo <<<EOT
        <li>$cname on <a href="video.php?v=$uniqid"><b>$vname</b></a>: $comment</li>
EOT;
    }
?>
    </ul>
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
