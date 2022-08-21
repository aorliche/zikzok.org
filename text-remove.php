<?php
    include('mysql.php');

    if ($_GET['id']) {
        $id = intval($_GET['id']);
        $stmt = $mysqli->prepare('update texts set deleted = true where id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        
        if ($stmt->errno) {
            echo $stmt->error;
        } else {
            echo 'Success';
        }
    } else {
        echo 'No id';
    }
?>
