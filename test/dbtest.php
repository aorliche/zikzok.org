<?php
	require_once('../mysql.php');
    
    $stmt = $mysqli->prepare('select * from videos');
    $stmt->execute();
    $res = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>
<?=ini_get('post_max_size');?>
<?=ini_get('upload_max_filesize');?>
<?php
    while ($row = $res->fetch_assoc()) {
?>
    <div>Video: <?=$row['name']?> Id: <?=$row['id']?> User:<?=$row['userid']?> Uniqid: <?=$row['uniqid']?> </div>
<?php        
    }
?>
</body>
</html>
