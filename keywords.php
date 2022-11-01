<?php
    // Score words based on length
    // And penalize apostrophe words
    function scoreWord($word, $thresh=3, $gmult=0.2, $apopen=2) {
        $len = strlen($word);
        if (strpos($word,"'") !== false) {
            $len -= $apopen;
        }
        $fact = ($len-$thresh);
        return 1+$gmult*$fact;
    }
   
    function loadScores($file = 'wordcounts.json') {
        $counts_json = file_get_contents($file);
        $counts = json_decode($counts_json, JSON_UNESCAPED_SLASHES);
        $scores = array(); 
        foreach ($counts as $word => $count) {
            $scores[$word] = scoreWord($word)*$count;
        }
        arsort($scores);
        return $scores;
    }

    function outputKeywords($scores) {
        $count = 0;
        foreach ($scores as $word => $score) {
            if (rand(0,1) > 0) {
                continue;
            }
            if ($count++ == 7) {
                break;
            }
            $word = htmlentities($word);
            echo "<a href='search.php?w=$word'>$word</a> ($score)\n";
        }
    }
?>
