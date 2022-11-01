<?php
    include_once('keywords.php');

    $scores = loadScores();
?>
<h1><a href='/'>ZikZok</a></h1>
<div id='navigation'>
    <a href='record/'>Record a video</a>
    <a href='signup.php'>Sign up</a>
    <a href='user.php?u=1'>Heisenberg's reserve</a>
</div>
<div id='search'>
    <form method='get' action='/search.php'>
        <input id='search-input' name='w' type='text' value='Search videos'>
        <button>Go</button>
        <a href='/search.php'>I want to see more</a>
    </form>
</div>
<div id='keywords'>
<?php
    outputKeywords($scores);
?>
</div>
