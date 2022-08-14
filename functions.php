<?php
	// php urlencode online doc comments
	function escapeExtra($str) {
		return str_replace(array('=', '?'), array('%3D', '%3F'), $str);
	}
?>
