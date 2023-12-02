<?php
    include_once('mysql.php');
    include_once('alphasongs.php');
    include_once('functions.php');

    if ($_POST['name'] && $_POST['comment']) {
        $name = $_POST['name'];
        $comment = $_POST['comment'];
        $stmt = $mysqli->prepare('insert into comments (uniqid, name, comment) values (?, ?, ?)');
        $stmt->bind_param('sss', $_GET['v'], $name, $comment);
        $stmt->execute();
    }
   
    if ($_GET['v']) {
        $stmt = $mysqli->prepare('select * from videos where uniqid = ?');
        $stmt->bind_param('s', $_GET['v']);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $video = '/videos/'.$row['uniqid'].'.'.$row['ext'];
        $preview = '/preview/'.$row['uniqid'].'.png';
        $id = $row['id'];
        $name = $row['name'];
        $user = $row['userid'];
        $views = $row['views'];
        $likes = intval($row['likes']);//+ord($name);
        $created = $row['created'];
        if (!$user) $user = 'Anonymous';

        $stmt = $mysqli->prepare('update videos set views = views + 1 where id = ?');
        $stmt->bind_param('i', $row['id']);
        $stmt->execute();

        $views = $views+1;

        // Is this a reply?
        $stmt = $mysqli->prepare('select oldvideo, name from replies
                            inner join videos on videos.uniqid = replies.oldvideo
                            where replies.newvideo = ?');
        $stmt->bind_param('s', $_GET['v']);
        $stmt->execute();
        $this_reply_res = $stmt->get_result();
    
        // Get replies
        $stmt = $mysqli->prepare('select newvideo, name, created, unix_timestamp(created) as created_unix from replies 
                            inner join videos on videos.uniqid = replies.newvideo 
                            where replies.oldvideo = ?');
        $stmt->bind_param('s', $_GET['v']);
        $stmt->execute();
        $replies_res = $stmt->get_result();

        // Get comments
        $stmt = $mysqli->prepare('select comments.id, name, comment, created, 
                            unix_timestamp(created) as created_unix from comments
                            where comments.uniqid = ? order by created desc');
        $stmt->bind_param('s', $_GET['v']);
        $stmt->execute();
        $comments_res = $stmt->get_result();

        // Get comment splits
        $stmt = $mysqli->prepare('select comment_splits.id, `before`, comment_splits.uniqid, `after`, split_which 
                            from comment_splits left join comments 
                            on comments.id = comment_splits.split_which where comments.uniqid = ?');
        $stmt->bind_param('s', $_GET['v']);
        $stmt->execute();
        $splits_res = $stmt->get_result();

        // Make map from comment to split for lookup later
        $split_map = array();
        while ($row = $splits_res->fetch_assoc()) {
            $split_map[$row['split_which']] = $row;
        }

        // Get likes
        $stmt = $mysqli->prepare('select * from likes left join users on likes.userid = users.id
                            where uniqid = ? order by when_made desc');
        $stmt->bind_param('s', $_GET['v']);
        $stmt->execute();
        $likes_res = $stmt->get_result();

        // Load existing overlays
        $stmt = $mysqli->prepare('select * from texts where uniqid = ? and deleted = false');
        $stmt->bind_param('s', $_GET['v']);
        $stmt->execute();
        $texts_res = $stmt->get_result();

        // Load related videos
        $stmt = $mysqli->prepare('select videos.uniqid, videos.name, connections.weight, connections.words
            from connections join videos 
            on connections.uniqid2 = videos.uniqid 
            where connections.uniqid1 = ? order by connections.weight desc');
        $stmt->bind_param('s', $_GET['v']);
        $stmt->execute();
        $related_res = $stmt->get_result();

        $top_related = array();
        while ($row = $related_res->fetch_assoc()) {
            array_push($top_related, $row);
            if (count($top_related) == 4) {
                break;
            }
        }
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
<!-- MSPs -->
<script src='/js/msp/build_common.js'></script>
<script src='/js/msp/build_flower.js'></script>
<script src='/js/msp/build_random.js'></script>
<script src='/js/msp/build_star.js'></script>
<script src='/js/msp/chains.js'></script>
<script src='/js/msp/draw.js'></script>
<script src='/js/msp/expand_common.js'></script>
<script src='/js/msp/expand_flower.js'></script>
<script src='/js/msp/expand_random.js'></script>
<script src='/js/msp/flips.js'></script>
<script src='/js/msp/hole.js'></script>
<script src='/js/msp/msp.js'></script>
<script src='/js/msp/rhombus.js'></script>
<script src='/js/msp/util.js'></script>
<script src='/js/msp/vertex.js'></script>
<!-- zikzok -->
<link rel='stylesheet' href='/css/zikzok.css'>
<link rel='stylesheet' href='/css/video.css'>
<script src='/js/video.js'></script>
<script src='/js/video_msp.js'></script>
<script src='/js/comments.js'></script>
<script src='/js/split-comment.js'></script>
</head>
<body>
<div id='container'>
<?php
    include('header.php');
?>
<?php
    if ($this_reply_res->num_rows) {
        $row = $this_reply_res->fetch_assoc();
        $olduniqid = $row['oldvideo'];
        $oldname = $row['name'];
        echo "<p>This is a reply to video <a href='/video.php?v=$olduniqid'>$oldname<img class='reply-thumb' src='/preview/$olduniqid.png'></a></p>";
    }
?>
    <div id='right'>
        <h3>Related Videos</h3>
<?php
    foreach ($top_related as $row) {
        $runiqid = $row['uniqid'];
        $rname = htmlentities($row['name']);
        $rwords = explode(', ', $row['words']);
        $rwords_text = array();
        foreach ($rwords as $rword) {
            $rword = htmlentities($rword);
            $rword = "<a href='/search.php?w=$rword'>$rword</a>";
            array_push($rwords_text, $rword);
        }
        $rwords_text = implode(', ', $rwords_text);
        echo <<<EOT
        <p>
            <a href='/video.php?v=$runiqid'>
                <strong>$rname</strong>
                <div class='img-holder'><img src='/preview/$runiqid.png' alt='$rname'></div>
            </a>
            <div style='font-size: smaller;'>$rwords_text</div>
        </p>
EOT;
    }
?>
    </div>
	<div id='video-header'>
        <div id='video-title-info'>
            <div id='video-title'><?= htmlspecialchars($name)  ?></div>
            <div id='video-info'>By: <?= $user ?> Created: <?= $created ?></div>
        </div>
        <canvas width=80 height=80></canvas>
        <!-- Video's AlphaSong: <span id='alphasong'><?= getAlphasongFromId($id) ?></span> -->
    </div>
    <div id='left'>
    <div id='video-main'>
        Views: <span id='views'><?= $views ?></span>
        Likes: <span id='likes' class='hunimal-font'><?= likesToHunimal($likes) ?></span>
        <a href='/record/index.php?replyto=<?= $_GET['v'] ?>'>Reply</a>
        <a href='#like' id='like'>Like it<img src='/images/like.gif' height='32px'></a>
        <a href='#disagree' id='disagree'>Disagree<img src='/images/unsure.jpg' height='32px'></a>
        <a href='#hate' id='dislike'>Dislike<img src='/images/disagree.png' height='28px'></a>
        <a href='#open-editor' id='open-editor'>Open editor</a>
        <br>
        <div style='display: inline-block; margin-top: 7px; position: relative;'>
            <div id='video-overlay'>
<?php
    if ($texts_res->num_rows) {
        foreach ($texts_res as $d) {
            $id = $d['id'];
            $text = $d['text'];
            $top = $d['top'];
            $left = $d['left'];
            $r = $d['red'];
            $g = $d['green'];
            $b = $d['blue'];
            $rgb = "rgb($r,$g,$b)";
            $size = $d['size'];
            $start = $d['start'];
            $end = $d['end'];
            echo <<<EOT
            <div class='draggable hunimal-font' 
                style='top: ${top}px; left: ${left}px; color: $rgb; font-size: ${size}px;' 
                data-start='$start' data-end='$end' data-id='$id'>$text</div>;
            EOT;
        }
    }
?>
            </div>
            <video id='video-elt' class='video-js' controls playsinline>
                <source src='<?= $video ?>'>
            </video>
            <br>
            <button id='split-button'>Split Video</button>
            <div id='split-uniqid' style='display: none;'><?= $_GET['v'] ?></div>
            <br>
        </div>
        <?php include('roulette.php'); ?>
        <br>
<?php
    if ($likes_res->num_rows) {
        echo <<<EOT
        <div id='likes-div'>
            <a id='blame-likes-a' href='#blame-likes'>Who liked this video?</a>
            <ul id='likes-ul' style='display: none;'>
        EOT;
        while ($row = $likes_res->fetch_assoc()) {
            $type = $row['type'];
            $name = htmlspecialchars($row['name']);
            $ip = $row['ip'];
            $when = $row['when_made'];
            $ai = $row['ai'] ? ' (AI)' : '';
            if ($name) {
                $userid = $row['userid'];
                $display = "<a href='/user.php?u=$userid'>$name</a>";
                if ($ai) {
                    $display .= ' (AI)';
                }
            } else {
                $display = $ip;
            }
            echo <<<EOT
                <li>$display ($when) $type</li>
                EOT;
        }
        echo <<<EOT
            </ul>
        </div>
        EOT;
    }
    // No Spiral tesselative boards comments
    if ($_GET['v'] != '651d7820e026c') {
?>
        <h4>Make a comment</h4>
        <form id='comment-form' method='post' action='/video.php?v=<?= $_GET['v'] ?>'>
            <label for='comment-name'>Name:</label>
            <input type='text' id='comment-name' name='name'><br>
            <label for='comment'>Comment:</label>
            <textarea id='comment' name='comment'></textarea>
            <button id='comment-submit'>Submit</button>
        </form>
        <div id='comments'>
<?php
    }
    if ($comments_res->num_rows) {
        $n = $comments_res->num_rows;
        echo "Comments: ($n)<br>";
        echo "<ol>";
    }
    while ($row = $comments_res->fetch_assoc()) {
        $name = htmlspecialchars($row['name']);
        $comment = htmlspecialchars($row['comment']);
        $created = htmlspecialchars($row['created']);
        $uniqid = htmlspecialchars($_GET['v']);
        $cid = htmlspecialchars($row['id']);
        if (array_key_exists($cid, $split_map)) {
            $split_row = $split_map[$cid];
            $before = htmlspecialchars($split_row['before']);
            $middle = htmlspecialchars($split_row['uniqid']);
            $after = htmlspecialchars($split_row['after']);
            echo "<li><strong>$name</strong> on $created: $before... <ul><li><a href='/video.php?v=$middle'><img src='/preview/$middle.png' width='100'></a></li></ul> ...$after";
        } else {
            echo "<li><strong>$name</strong> on $created: <span id='comment-$cid'>$comment</span> <a class='split-link' href='#' data-video='$uniqid' data-comment='$cid'>Split</a>
                <span class='hidden split-word-prompt'>Select word to split on:</span> 
                <input class='hidden split-word-input' type='range' min='0' max='0' value='0'> 
                <span class='hidden split-word-feedback'></span> 
                <select class='hidden split-select'></select></li>";
        }
        /*if ($other) {
            $other = "[Split By:] ";
        }*/
    }
    if ($comments_res->num_rows) {
        echo "</ol>";
    }
?>
        </div>
        <div id='replies'>
<?php
    if ($replies_res->num_rows) {
        $n = $replies_res->num_rows;
        echo "Replies: ($n)<br>";
        echo "<ol>";
    }
    while ($row = $replies_res->fetch_assoc()) {
        $name = $row['name'];
        $uniqid = $row['newvideo'];
        $created = $row['created'];
        echo "<li><div style='display: inline-block;'>
            <a href='/video.php?v=$uniqid'>$name</a><br>$created</div> 
            <a href='/video.php?v=$uniqid'><img class='reply-thumb' src='/preview/$uniqid.png'></a></li>";
    }
    if ($replies_res->num_rows) {
        echo "</ol>";
    }
?>
        </div>
    </div>
    <div id='editor'>
        <input type='checkbox' id='display-overlay'>
            <span id='overlay-feedback1'>Editing (video play disabled)</span>
            <span id='overlay-feedback2'>Video controls enabled (not editing)</span><br>
        <button id='add-text'>Add text</button>
        <button id='delete-text'>Delete text</button>
        <button id='save-all'>Save all</button><br>
        <small>Click on text, then use mouse, arrow keys, and keyboard to edit</small><br>
        <small>Remember to save</small><br>
<?php include('hunimal-select.php'); ?>
        <label for='text-font-size'>Font size:</label> <input type='range' id='text-font-size' min='8' max='80' step='8' value='24'><br>
        Color<br>
        <label for='text-font-red'>Red:</label> <input type='range' id='text-font-red' min='0' max='255' step='15' value='255'><br>
        <label for='text-font-green'>Green:</label> <input type='range' id='text-font-green' min='0' max='255' step='15' value='45'><br>
        <label for='text-font-blue'>Blue:</label> <input type='range' id='text-font-blue' min='0' max='255' step='15' value='45'><br>
        Timing<br>
        <label for='start-time'>Start:</label> <input type='range' id='start-time' min='0' max='100' step='1' value='0'><br>
        <label for='end-time'>End:</label> <input type='range' id='end-time' min='0' max='100' step='1' value='100'><br>
        <a href='#close-editor' id='close-editor'>Close editor</a><br>
        <ol id='texts' class='hunimal-font'></ol>
    </div>
    </div>
</div>
</body>
</html>
