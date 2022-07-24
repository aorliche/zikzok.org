<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<title>ZikZok</title>
<link rel="icon" type="image/png" href="/images/icon.png">
<link rel='stylesheet' href='/zikzok.css'>
</head>
<body>
<div id='container'>
	<h1><a href='/'>ZikZok</a></h1>
	<h3><?= htmlspecialchars($_GET['v']) ?></h3>
	<video controls playsinline width='320px'>
		<source src='/videos/<?= htmlspecialchars($_GET['v']) ?>.webm'>
	</video>
</div>
</body>
</html>
