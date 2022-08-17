<?php
    include_once('../mysql.php');

	function error($str) {
		echo json_encode(array('success' => false, 'error' => $str), JSON_UNESCAPED_SLASHES);
	}

	// Get video
	$name = $_POST['name'];
	$blob = $_FILES['recordedData'];
	$img = $_FILES['previewImage'];
    $pred_likes = $_POST['predLikes'];
    $pred_views = $_POST['predViews'];
    $replyto = $_POST['replyto'];
	if (!$name) {
		error('empty name');
		return;
	}
    $pred_likes = $pred_likes ? intval($pred_likes) : null;
    $pred_views = $pred_views ? intval($pred_views) : null;
    $uniqid = uniqid();

	$saveName = "../videos/$uniqid.mp4";
    $save_img_name = "../preview/$uniqid.png";
	$resp = array();
    $resp['name'] = $name;
	$resp['uniqid'] = $uniqid;
	$resp['size'] = $blob['size'];

	// Save video
	if (!move_uploaded_file($blob['tmp_name'], $saveName)) {
		error('cannot move uploaded file');
		return;
	}

	// Save preview
	if (!move_uploaded_file($img['tmp_name'], $save_img_name)) {
		error('cannot move uploaded preview image');
		return;
	}
	
	echo json_encode($resp, JSON_UNESCAPED_SLASHES);

    $stmt = $mysqli->prepare('insert into videos (uniqid, name, pred_views, pred_likes) values (?, ?, ?, ?)');
    $stmt->bind_param('ssii', $uniqid, $name, $pred_views, $pred_likes);
    $stmt->execute();

    if ($replyto) {
        $stmt = $mysqli->prepare('insert into replies (newvideo, oldvideo) values (?, ?)');
        $stmt->bind_param('ss', $uniqid, $replyto);
        $stmt->execute();
    }
?>
