<?php

include_once('../mysql.php');
include_once('../keywords.php');

function arr2push(&$aoa, $key, $val) {
    if (array_key_exists($key, $aoa)) {
        array_push($aoa[$key], $val);
    } else {
        $aoa[$key] = array($val);
    }
}

function arr_add(&$arr, $key, $val=1) {
    if (array_key_exists($key, $arr)) {
        $arr[$key] += $val;
    } else {
        $arr[$key] = $val;
    }
}

function get_keywords(&$text) {
    $banlist = file_get_contents('../banlist.txt');
    $banlist = preg_replace("/'/", '', $banlist);
	$banlist = preg_split('/\s/', $banlist);
    $text = preg_replace("/\?|,|'|\./", '', $text);
    $words = explode(' ', $text);
    $result = array();
    foreach ($words as $word) {
        if (!valid_word($word, $banlist)) {
            continue;
        }
        if (in_array($word, $result)) {
            continue;
        }
        array_push($result, strtolower($word));
    }
    return $result;
}

// Get transcripts
$stmt = $mysqli->prepare('select transcripts.uniqid, transcript from transcripts right join videos on transcripts.uniqid = videos.uniqid');
$stmt->execute();
$res = $stmt->get_result();

// Create uniqid to words map and count all keywords
$uniqid2words = array();
$wordcounts = array();
while ($row = $res->fetch_assoc()) {
    $uniqid = $row['uniqid'];
    $transcript = $row['transcript'];
    $words = get_keywords($transcript);
    foreach ($words as $word) {
        arr2push($uniqid2words, $uniqid, $word);
        arr_add($wordcounts, $word);
    }
}

// Calculate weights
$weights = array();
foreach ($wordcounts as $word => $count) {
    if ($count > 1) {
        $weights[$word] = strlen($word)/$count;
    }
}

// Create keywords to uniqid map
$word2uniqids = array();
foreach ($uniqid2words as $uniqid => $words) {
    foreach ($words as $word) {
        if (array_key_exists($word, $weights)) {
            arr2push($word2uniqids, $word, $uniqid);
        }
    }
}

// Make connections between uniqids
$conns = array();
foreach ($word2uniqids as $word => $uniqids) {
    $len = count($uniqids);
    for ($i=0; $i<$len; $i++) {
        for ($j=0; $j<$len; $j++) {
            if ($uniqids[$i] == $uniqids[$j]) {
                continue;
            }
            $key = $uniqids[$i] . '-' . $uniqids[$j];
            arr_add($conns, $key, $weights[$word]);
        }
    }
}
/*
print_r($wordcounts);
print_r($uniqid2words);
print_r($word2uniqids);
print_r($weights);
arsort($conns);
$n = 0;
foreach ($conns as $conn => $weight) {
    echo "$conn $weight\n";
    $parts = explode('-', $conn);
    print_r($uniqid2words[$parts[0]]);
    print_r($uniqid2words[$parts[1]]);
    if ($n++ > 0) {
        break;
    }
}*/
/*asort($conns);
print_r($conns);*/

// Get records from database
$stmt = $mysqli->prepare('select uniqid1, uniqid2, weight from connections');
$stmt->execute();
$res = $stmt->get_result();

$conns_exist = array();
while ($row = $res->fetch_assoc()) {
    $key = $row['uniqid1'] . '-' . $row['uniqid2'];
    $conns_exist[$key] = $row['weight'];
}

$updated = 0;
$inserted = 0;
foreach ($conns as $conn => $weight) {
    $parts = explode('-', $conn);
    if (array_key_exists($conn, $conns_exist)) {
        if (round($weight, 2) != round($conns_exist[$conn], 2)) {
            $stmt = $mysqli->prepare('update connections set weight = ? where uniqid1 = ? and uniqid2 = ?');
            $stmt->bind_param('dss', $weight, $parts[0], $parts[1]);
            $stmt->execute();
            $updated++;
        }
    } else {
        $stmt = $mysqli->prepare('insert into connections (weight, uniqid1, uniqid2) values (?, ?, ?)');
        $stmt->bind_param('dss', $weight, $parts[0], $parts[1]);
        $stmt->execute();
        $inserted++;
    }
}

echo "Updated $updated\n";
echo "Inserted $inserted\n";

?>
