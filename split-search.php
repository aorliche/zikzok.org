<?php
    include_once('mysql.php');

    $query = $_GET['q'];

    $stmt = $mysqli->prepare('select 
        videos.name,
        videos.uniqid,
        timing.chunk_text,
        match(timing.chunk_text) against(?) as relevance 
        from timing join videos 
        on timing.uniqid = videos.uniqid 
        where match(timing.chunk_text) against(?) 
        order by relevance desc limit 5');
    $stmt->bind_param('ss', $query, $query);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $name = htmlentities($row['name']);
        $uniqid = htmlentities($row['uniqid']);
        $text = htmlentities($row['chunk_text']);
        $str = <<<EOT
            <div class='split-result' style='clear: both'>
                <img src='preview/$uniqid.png' style='float: left; padding-right: 5px;' height='80px'>
                <h4>$name</h4>
                $text<br>
                <a href='#' onclick='event.preventDefault()'>Split!</a>
            </div>
EOT;
        echo $str;
    }
?>
