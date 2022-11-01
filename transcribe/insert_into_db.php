<?php
    include_once('../mysql.php');

    // Whisper doesn't know what "Zikzok" is and transcribes it as 
    // zigzag or other things    
    function fixZikZok($str) {
        $words = preg_split('/\s|\.|,/', $str);
        $fixed = array();
        foreach ($words as $word) {
            if (preg_match('/z.+z.+/i', $word)) {
                array_push($fixed, 'Zikzok');
            } else {
                array_push($fixed, $word);
            }
        }
        return implode(' ', $fixed);
    }

    // Get uniqids for transcripts we already have in db
    $stmt = $mysqli->prepare('select videos.uniqid,ext,transcripts.id from videos 
        left join transcripts on transcripts.uniqid = videos.uniqid');
    $stmt->execute();
    $res = $stmt->get_result();

    // Get filenames of already inserted video transcripts
    $wanted = array();
    $have = array();
    while ($row = $res->fetch_assoc()) {
        $bname = $row['uniqid'] . '.' . $row['ext'] . '.txt';
        if ($row['id']) {
            array_push($have, $bname);
        } else {
            array_push($wanted, $bname);
        }
    }

    // Get available filenames
    // Fix zikzok transcription
    // And insert into database
    $files = scandir('tmpvideos');
    $count = 0;

    foreach ($files as $file) {
        $fullpath = 'tmpvideos/' . $file;
        $parts = pathinfo($fullpath);
        $bname = $parts['basename'];
        $ext = $parts['extension'];  
        $funiqid = explode('.', $bname)[0];
        if (in_array($bname, $wanted)) {
            $transcript = file_get_contents($fullpath);
            $transcript = fixZikZok($transcript);
            $stmt = $mysqli->prepare('insert into transcripts 
                (uniqid, transcript) values (?, ?)');
            $stmt->bind_param('ss', $funiqid, $transcript);
            $stmt->execute();
            if ($mysqli->errno) {
                echo "An error occured for $funiqid, $transcript:\n";
                echo $mysqli->error . "\n";
            } else {
                $count += $mysqli->affected_rows;
            }
        }
    }

    echo "Inserted $count transcripts\n";
?>
