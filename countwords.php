<?php
    // Build the keywords map
    include_once('mysql.php');
    include_once('keywords.php');
    
    $banlist = file_get_contents('banlist.txt');
    $banlist = preg_replace("'", '', $banlist);
	$banlist = preg_split('/\s/', $banlist);

    $stmt = $mysqli->prepare('select transcript from transcripts');
    $stmt->execute();
    $res = $stmt->get_result();

    $counts = array();

    while ($row = $res->fetch_assoc()) {
        $text = $row['transcript'];
        $text = preg_replace("/\?|,|'|\./", '', $text);
        $words = explode(' ', $text);
        $counted = array();
        foreach ($words as $word) {
            $word = strtolower($word);
            if (!valid_word($word, $banlist)) {
                continue;
            }
            // Counted for this video
            if (in_array($word, $counted)) {
                continue;
            }
            if (array_key_exists($word, $counts)) {
                $counts[$word]++;
            } else {
                $counts[$word] = 1;
            }
            array_push($counted, $word);
        }
    }
   
    // Weight importance by the length of the word
    /*$score = array(); 
    foreach ($counts as $word => $count) {
        $score[$word] = scoreWord($word)*$count;
    }*/

    arsort($counts);
    //asort($score);

    $counts_json = json_encode($counts, JSON_UNESCAPED_SLASHES);

    echo "$counts_json\n";
    //print_r($score);
?>
