<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/mysql.php');

	$email = $_POST['email'];
	$name = $_POST['username'];
	$password = $_POST['password'];

	function exists($mysqli, $field, $value) {
		$stmt = $mysqli->prepare('select count(*) as total from users where users.'.$field.' = ?');
		$stmt->bind_param('s', $value);
		$stmt->execute();
		$res = $stmt->get_result();
		$row = $res->fetch_assoc();
		return $row['total'] != 0;
	}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<title>Finish Sign Up | ZikZok</title>
<link rel="icon" type="image/png" href="/images/icon.png">
<!-- zikzok -->
<link rel='stylesheet' href='zikzok.css'>
<script src='zikzok.js'></script>
</head>
<body>
<div id='container'>
	<h1><a href='/'>ZikZok</a></h1>
	<h2>Finish sign up</h2>
<?php
	if (!$email || !$name || !$password) {
?>
	<h2>Missing fields</h2>	
	<p>Missing email, username, or password</p>
<?php		
	} else if (exists($mysqli, 'email', $email)) {
?>
	<h2>Email exists</h2>
	<p>The email entered already exists</p>
<?php
	} else if (exists($mysqli, 'name', $name)) {
?>
	<h2>Username exists</h2>
	<p>The username entered already exists</p>
<?php
	} else {
		// Add user
		$secret = uniqid();
		$stmt = $mysqli->prepare('insert into users (email, name, password, secret, valid) values (?,?,?,?,false)');
		$stmt->bind_param('ssss', $email, $name, $password, $secret);
		$stmt->execute();
		if ($mysqli->errno) {
?>
	<h2>Database error</h2>
	<p>A database error occurred 
<?php
		} else {

			// Send email to user
			$url = "https://zikzok.org/activate.php?s=$secret";
			$to = $email;
			$subject = 'ZikZok account for '.$name;
			$message = "Click the following link to activate your account: <a href='$url'>$url</a>";
			$headers = "From: no-reply@zikzok.org\r\nX-Mailer: PHP/".phpversion();
			mail($to, $subject, $message, $headers);
?>	
	<h2>Finish signing up</h2>
	<p>Please check your email for the activation link</p>
<?php
		}
	}
?>
</div>
</body>
</html>
