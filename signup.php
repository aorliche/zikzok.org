<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<title>Sign Up | ZikZok</title>
<link rel="icon" type="image/png" href="/images/icon.png">
<!-- zikzok -->
<link rel='stylesheet' href='zikzok.css'>
<script src='signup.js'></script>
</head>
<body>
<div id='container'>
	<h1><a href='/'>ZikZok</a></h1>
	<h2>Sign up</h2>
	<form action='finish.php'>
	<label for='email'>Email:</label><input type='text' name='email' id='email'><br>
	<label for='username'>Name:</label><input type='text' name='username' id='username'><br>
	<label for='password'>Password:</label><input type='password' name='username' id='password'><br>
	<label for='confirm'>Confirm Password:</label><input type='password' name='username' id='confirm'><br>
	<div id='infoDiv'></div>
	<button id='button'>Submit</button>
	</form>
</div>
</body>
</html>
