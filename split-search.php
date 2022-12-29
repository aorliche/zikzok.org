<?php
    include_once('mysql.php');

    $query = $_GET['q'];

    $stmt = mysqli->prepare('select 
        videos.name,
        videos.uniqid,
        timing.chunk_text,
        match(timing.chunk_text) against(?) as relevance 
        from timing join videos 
        on timing.uniqid = videos.uniqid 
        where match(timing.chunk_text) against(?) 
        order by relevance desc limit 8');
    $stmt->bind_param('ss', $query, $query);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $name = $row['name'];
        $uniqid = $row['uniqid'];
        $text = $row['chunk_text'];
        $str = <<<EOT
            <div class='split-result'>
                <img src='preview/$uniqid.png'>
                <div style='display: inline-block;'>
                    <h4>$name</h4>
                    $text
                </div>
            </div>
EOT;
        echo $str;
    }
?>
