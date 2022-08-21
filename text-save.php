<?php

    include('mysql.php');
        
    $stmt = $mysqli->prepare(
        'insert into texts (id, uniqid, text, top, `left`, red, green, blue, size, start, end) 
            values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) 
            on duplicate key update 
            text = ?, top = ?, `left` = ?, red = ?, green = ?, blue = ?, size = ?, start = ?, end = ?');

    $draggables = json_decode(file_get_contents('php://input'), true);
    $ids = array();

    foreach ($draggables as $d) {
        $stmt->bind_param('issiiiiiiiisiiiiiiii', 
            $d['id'], $d['uniqid'], $d['text'], $d['top'], $d['left'], $d['red'], $d['green'], $d['blue'], $d['size'], $d['start'], $d['end'],
                                    $d['text'], $d['top'], $d['left'], $d['red'], $d['green'], $d['blue'], $d['size'], $d['start'], $d['end']);
        $stmt->execute();
        if ($stmt->errno) {
            echo json_encode(array('error' => $stmt->error));
            return;
        }
        array_push($ids, $stmt->insert_id);
    }

    echo json_encode($ids);

?>
