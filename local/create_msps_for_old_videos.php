<?php
    include_once('../mysql.php');

    $stmt = $mysqli->prepare('select * from videos where userid is NULL and uniqid is not NULL');
    $stmt->execute();
    $res = $stmt->get_result();
    $tocreate = array();

    while ($row = $res->fetch_assoc()) {
        $uniqid = $row['uniqid'];
        if (file_exists("../msps/$uniqid.msp.json")) {
            //echo "$uniqid exists\n";
        } else {
            //echo "$uniqid does not exist\n";
            array_push($tocreate, "'$uniqid'");
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Script to create MSPs</title>
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
    <script>
    window.addEventListener('load', e => {
        const tocreate = [<?= implode(',', $tocreate) ?>];
        canvas = document.querySelector('canvas');
        tocreate.forEach(uniqid => {
            msps = [];
            const msp = buildStarMSP(6, 20, false);
            msps.push(msp);
            repaint(canvas);
            const data = new FormData();
            data.append('uniqid', uniqid);
            data.append('msp', JSON.stringify(msp));
            fetch('/local/create_upload.php', {
                method: 'POST',
                body: data
            })
            .then(response => response.text())
            .then(text => {
                console.log(text);
            })
            .catch(error => console.log(error));
        });
    });
    </script>
</head>
<body>
<canvas width=80 height=80></canvas>
</body>
</html>
