<?php
    // Build the keywords map
    include_once('mysql.php');
    include_once('keywords.php');

	$banlist = <<<EOT
then
know
make
your
don't
that's
what
want
like
it's
with
just
have
this
because
that
they
them
about
from
other
there
their
some
think
okay
right
alright
here's
does
let's
EOT;
	$banlist = preg_split('/\s/', $banlist);

    $stmt = $mysqli->prepare('select transcript from transcripts');
    $stmt->execute();
    $res = $stmt->get_result();

    $counts = array();

    while ($row = $res->fetch_assoc()) {
        $text = $row['transcript'];
        $text = preg_replace('/\?|,|\./', '', $text);
        $words = explode(' ', $text);
        foreach ($words as $word) {
            $word = strtolower($word);
            // Ignore words equal to or less than this length
            if (strlen($word) < 4) {
                continue;
            }
            // Ignore words that are greater than this length (David singing)
            if (strlen($word) > 18) {
                continue;
            }
            // Ignore words on the banlist
            if (in_array($word, $banlist)) {
                continue;
            }
            if (array_key_exists($word, $counts)) {
                $counts[$word]++;
                break;
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
    //asort($score);

    $counts_json = json_encode($counts, JSON_UNESCAPED_SLASHES);

    echo "$counts_json\n";
    //print_r($score);
?>
