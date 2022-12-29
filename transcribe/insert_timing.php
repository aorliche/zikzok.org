<?php
    include_once('../mysql.php');

    // Get timings already in db
    $stmt = $mysqli->prepare('select distinct(uniqid) from timing');
    $stmt->execute();
    $res = $stmt->get_result();
    $have = array();

    while ($row = $res->fetch_assoc()) {
        array_push($have, $row['uniqid']);
    }

    function to_ms($time) {
        $parts = array();
        preg_match('/\D*(\d+):(\d+):(\d+),(\d+)\D*/', $time, $parts);
        if (count($parts) == 5) {
            $h = intval($parts[1])*3600*1000;
            $m = intval($parts[2])*60*1000;
            $s = intval($parts[3])*1000;
            $ms = intval($parts[4]);
        }
        return $h+$m+$s+$ms;
    }

    function insert_timing($mysqli, $uniqid, $t1, $t2, $text) {
        $stmt = $mysqli->prepare('insert into timing (uniqid, chunk_start, chunk_end, chunk_text) values (?,?,?,?)');
        $stmt->bind_param('siis', $uniqid, $t1, $t2, $text);
        $stmt->execute();
        if ($mysqli->errno) {
            echo "An error occured for $uniqid\n";
            echo $mysqli->error . "\n";
        }
    }

    function read_and_insert($mysqli, $path, $uniqid) {
        $contents = file_get_contents($path);
        $chunks = preg_split(
            '/^\d+\R+|\R+\d+\R+/', 
            $contents, 
            -1, 
            PREG_SPLIT_NO_EMPTY);
        foreach ($chunks as $chunk) {
            $parts = preg_split('/\R+/', 
                $chunk, 
                -1, 
                PREG_SPLIT_NO_EMPTY);
            $time_parts = preg_split('/-->/', $parts[0]);
            $t1 = to_ms($time_parts[0]);
            $t2 = to_ms($time_parts[1]);
            //echo "$uniqid,$t1,$t2,".$parts[1]."\n";
            insert_timing($mysqli, $uniqid, $t1, $t2, $parts[1]);
        }
    }

    // Find local timing info
    $files = scandir('timing');
    $inserted = 0;
    $skipped = 0;

    foreach ($files as $file) {
        if (!preg_match('/^[a-f0-9]+.*\.srt$/', $file)) {
            continue;
        }
        $fullpath = 'timing/' . $file;
        $parts = pathinfo($fullpath);
        $bname = $parts['basename'];
        $ext = $parts['extension'];  
        $funiqid = explode('.', $bname)[0];
        if (!in_array($funiqid, $have)) {
            read_and_insert($mysqli, $fullpath, $funiqid);
            $inserted++;
        } else {
            $skipped++;
        }
    }

    echo "Inserted: $inserted\n";
    echo "Skipped: $skipped\n";
?>
