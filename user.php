<?php
    // Right now, just list the videos the user has liked

    include('mysql.php');

    // Get user info
    $stmt = $mysqli->prepare('select * from users where id = ?');
    $stmt->bind_param('s', $_GET['u']);
    $stmt->execute();
    $user_res = $stmt->get_result();
    if ($user_res->num_rows) {
        $user = $user_res->fetch_assoc();
        $user_display = $user['name'];
        if ($user['ai']) {
            $user_display .= ' (AI)';
        }
    }

    // Get likes
    $stmt = $mysqli->prepare('select * from likes
        inner join videos on likes.uniqid = videos.uniqid 
        where likes.userid = ?
        order by when_made desc');
    $stmt->bind_param('s', $_GET['u']);
    $stmt->execute();
    $likes_res = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<title>ZikZok</title>
<link rel="icon" type="image/png" href="/images/icon.png">
<!-- zikzok -->
<link rel='stylesheet' href='/css/zikzok.css'>
</head>
<body>
<div id='container'>
	<h1><a href='/'>ZikZok</a></h1>
    <h2>User: <?= $user_display ?></h2>
<?php
    if ($likes_res->num_rows) {
        echo <<<EOT
    <h3>Recent Activity</h3>
    EOT;
        $prev_dt = '';
        while ($row = $likes_res->fetch_assoc()) {
            $dt = new DateTime($row['when_made']);
            $dt = $dt->format('l, F j, o');
            if ($dt != $prev_dt) {
                if ($prev_dt != '') {
                    echo "</ul>";
                }
                echo <<<EOT
                <div>$dt</div>
                <ul>
                EOT;
                $prev_dt = $dt;
            } 
            $name = htmlspecialchars($row['name']);
            $uniqid = $row['uniqid'];
            $type = $row['type'];
            echo "<li><a href='/video.php?v=$uniqid'>$name</a> ($type)</li>";
        }
        echo "</ul>";
    } else {
        echo <<<EOT
    <p>No info</p>
    EOT;
    }
?>
</div>
</body>
</html>
