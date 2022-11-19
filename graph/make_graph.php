<?php

include_once('../mysql.php');
include_once('../keywords.php');

function arr2push($aoa, $key, $val) {
    if (array_key_exists($key, $aoa)) {
        array_push($aoa[$key], $val);
    } else {
        $aoa[$key] = array($val);
    }
}

function arr_add($arr, $key, $val=1) {
    if (array_key_exists($key, $arr)) {
        $arr[$key] += $val;
    } else {
        $arr[$key] = $val;
    }
}

function get_keywords($text) {
    $banlist = file_get_contents('../banlist.txt');
    $banlist = preg_replace("'", '', $banlist);
	$banlist = preg_split('/\s/', $banlist);
    $text = preg_replace("/\?|,|'|\./", '', $text);
    $words = explode(' ', $text);
    $result = array();
    for ($words as $word) {
        if (!valid_word($word, $banlist)) {
            continue;
        }
        if (in_array($word, $result)) {
            continue;
        }
        array_push($result, $word);
    }
    return $result;
}

// Get transcripts
$stmt = $mysqli->prepare('select uniqid, transcript from transcripts');
$stmt->execute();
$res = $stmt->get_result();

// Create uniqid to words map and count all keywords
$uniqid2words = array();
$wordcounts = array();
while ($row = $res->fetch_assoc()) {
    $uniqid = $row['uniqid'];
    $transcript = $row['transcript'];
    $words = get_keywords($transcript);
    for ($words as $word) {
        arr2push($uniqid2words, $uniqid, $word);
        arr_add($wordcounts, $word);
    }
}

// Calculate weights
$weights = array();
for ($wordcounts as $word => $count) {
    if ($count > 1) {
        $weights[$word] = strlen($word)/$count;
    }
}

// Create keywords to uniqid map
$word2uniqids = array();
for ($keywords as $uniqid => $words) {
    for ($words as $word) {
        if (isset($kweights[$word])) {
            arr2push($word2uniqids, $word, $uniqid);
        }
    }
}

// Make connections between uniqids
$conns = array();
for ($word2uniqids as $word => $uniqids) {
    $len = count($uniqids);
    for ($i=0; $i<$len; $i++) {
        for ($j=0; $j<$len; $j++) {
            $key = $uniqids[$i] . '-' . $uniqids[$j];
            arr_add($conns, $key, $weights[$word]);
        }
    }
}

print_r($wordcounts);
print_r($uniqid2words);
print_r($word2uniqids);
print_r($weights);
print_r($conns);

?>