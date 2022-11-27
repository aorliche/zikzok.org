<?php
	// php urlencode online doc comments
    // I don't think this is used anymore
	function escapeExtra($str) {
		return str_replace(array('=', '?'), array('%3D', '%3F'), $str);
	}
    
    function likesToHunimal($likes) {
        $hun = "";
        while ($likes > 0) {
            $digit = $likes % 100;
            if ($digit < 10) {
                $digit = "0$digit";
            }
            $hun = "&#x55$digit;$hun";
            $likes = intval($likes/100);
        }
        return $hun;
    }
?>
