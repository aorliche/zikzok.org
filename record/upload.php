<?php
	function error($str) {
		echo json_encode(array('success' => false, 'error' => $str), JSON_UNESCAPED_SLASHES);
	}

	// Get video
	$saveName = $_POST['name'];
	if (!$saveName) {
		error('empty filename');
		return;
	}
	$saveNameWebM = '../videos/'.$saveName.'.mp4';
	$saveNameImage = '../preview/'.$saveName.'.png';
	$blob = $_FILES['recordedData'];
	$img = $_FILES['previewImage'];
	$resp = array();
	$resp['saveName'] = $saveName;
	$resp['name'] = $blob['name'];
	$resp['size'] = $blob['size'];

	// Save video
	if (!move_uploaded_file($blob['tmp_name'], $saveNameWebM)) {
		error('cannot move uploaded file');
		return;
	}

	// Save preview
	if (!move_uploaded_file($img['tmp_name'], $saveNameImage)) {
		error('cannot move uploaded preview image');
		return;
	}
	
	echo json_encode($resp, JSON_UNESCAPED_SLASHES);
?>
