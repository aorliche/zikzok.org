<?php

function flip($i1, $i2, $song = "abcdefghijklmnopqrstuvwxyz") {
    $letter = $song[$i1];
    $song[$i1] = $song[$i2];
    $song[$i2] = $letter;
    return $song;
}

function rotate($n, $song = "abcdefghijklmnopqrstuvwxyz") {
    return substr($song, $n) . substr($song, 0, $n);
}

function getAlphasongFromId($n) {
    $flips = intval(ceil($n/26));
    $rotations = $n%26;
    return rotate($rotations, flip(0, $flips));
}

?>
