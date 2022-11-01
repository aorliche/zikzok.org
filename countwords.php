<?php
    // Build the keywords map
    include_once('mysql.php');
    include_once('keywords.php');

    $stmt = $mysqli->prepare('select transcript from transcripts');
    $stmt->execute();
    $res = $stmt->get_result();

    $counts = array();

    while ($row = $res->fetch_assoc()) {
        $words = explode(' ', $row['transcript']);
        foreach ($words as $word) {
            $word = strtolower($word);
            // Ignore words equal to or less than this length
            if (strlen($word) < 4) {
                continue;
            }
            if (array_key_exists($word, $counts)) {
                $counts[$word]++;
            } else {
                $counts[$word] = 1;
            }
        }
    }
   
    // Weight importance by the length of the word
    /*$score = array(); 
    foreach ($counts as $word => $count) {
        $score[$word] = scoreWord($word)*$count;
    }*/

    arsort($counts);
    //arsort($score);

    $counts_json = json_encode($counts, JSON_UNESCAPED_SLASHES);

    echo "$counts_json\n";
    //print_r($score);
?>
