<?php
    include_once('mysql.php');
    include_once('functions.php');

    $videos = array();

    function outputVideo($video, $query) {
        $uniqid = htmlentities($video['uniqid']);
        $name = htmlentities($video['name']);
        $created = htmlspecialchars($video['created']);
        $views = $video['views'];
        $likes = intval($video['likes'])+ord($name);
        $replies = $video['replies'];
        $comments = $video['comments'];
        $text = strtolower($video['transcript']);
        $positions = array();
        foreach ($query as $word) {
            $pos = strpos($text, $word);
            if ($pos !== false) {
                array_push($positions, $pos);
            }
            sort($positions);
        }
        $text = substr($text, $positions[0], 150);
        $text = htmlentities($text);
        foreach ($query as $word) {
            $word = htmlentities($word);
            $text = preg_replace("/($word)/i", "<b>$word</b>", $text);
        }
        $discussion = array();
        if ($replies) {
            array_push($discussion, "$replies replies");
        } 
        if ($likes) {
            $likes = likesToHunimal($likes);
            array_push($discussion, "<span class='hunimal-font'>$likes</span> likes");
        }
        if ($views) {
            array_push($discussion, "$views views");
        }
        if ($comments) {
            array_push($discussion, "$comments comments");
        }
        $discussion = implode(', ', $discussion);   
       echo <<<EOT
    <div class='video-search-block'>
        <div style='position: relative; float: left;'>
            <a href="video.php?v=$uniqid">
                <img alt="$uniqid" src="preview/$uniqid.png">
            </a>
        </div>
        <div>
            <a href="video.php?v=$uniqid"><span class="video-name">$name</span></a><br>
            $created<br>
            $discussion<br>
            <p class='video-search-quote'>"...$text..."</p>
        </div>
    </div>
EOT;
    }

    if ($_GET['w']) {
        $query = explode(' ', $_GET['w']);
        $query_joined = implode(',', $query);
        $stmt = $mysqli->prepare("select 
            videos.uniqid,videos.name,views,likes,
            transcripts.transcript,
            videos.created,unix_timestamp(videos.created) as created_unix,
            count(comments.id) as comments, 
            count(newvideo) as replies
            from videos
            left join transcripts on videos.uniqid = transcripts.uniqid
            left join comments on videos.uniqid = comments.uniqid
            left join replies on videos.uniqid = replies.oldvideo
            where match(transcripts.transcript) against (?) and videos.uniqid is not null
            group by uniqid 
            order by created desc");
        $stmt->bind_param('s', $query_joined);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            array_push($videos, $row);
        }
        shuffle($videos);
    }
?>
<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<title>ZikZok</title>
<link rel="icon" type="image/png" href="/images/icon.png">
<link rel='stylesheet' href='css/zikzok.css'>
<script src='/js/zikzok.js'></script>
<script src='/js/words.js'></script>
</head>
<body>
<div id='container'>
<?php
    include('header.php');
?>
    <div>
        Select a keyword type:
        <a id='random-words' href='#'>Random</a>
        <a id='high-score-words' href='#'>High Score</a>
        <a id='popular-words' href='#'>Popular</a>
        <a id='unpopular-words' href='#'>Unpopular</a>
        <br>
        <a id='safety' href='#'>Safety is On</a>
    </div>
    <div id='videos'>
<?php
    $num = count($videos);
    if ($num) {
        echo "<h4 class='small-margin'>$num videos</h4>\n";
        foreach ($videos as $video) {
            outputVideo($video, $query);
        }
    } else if ($_GET['w'] and $num == 0) {
        echo "<h4 class='small-margin'>No videos matching your search.</h4>\n";
    }
?>
    </div>
</div>
</body>
</html>
