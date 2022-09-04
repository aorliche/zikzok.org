<?php
    $msp = $_POST['msp'];
    $uniqid = $_POST['uniqid'];
    $save_msp_name = "../msps/$uniqid.msp.json";
    
    // Save msp
    if (!file_put_contents($save_msp_name, $msp)) {
        echo 'failed to write msp data to file';
        return;
    }

    echo $_POST['uniqid'];
?>
