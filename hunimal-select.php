<link rel='stylesheet' href='/css/zikzok.css'>
<style>
#hunimal-container {
    position: relative;
    width: 200px;
}
#hunimal-select {
    border-collapse: collapse;
    position: absolute;
    top: 20px;
    left: 10px;
    z-index: 10;
    display: none;
    background-color: #fff;
}
#hunimal-select td {
    border: 1px solid black;
    padding: 2px;
    padding-left: 4px;
    padding-right: 4px;
}
#hunimal-select td:hover {
    background-color: #9cf;
    user-select: none;
    cursor: pointer;
}
</style>
<div id='hunimal-container' class='hunimal-font'>
Place hunimal &#x5500;,&#x5501;...
<table id='hunimal-select' class='hunimal-font'>
<?php

    for ($i = 0; $i <= 9; $i++) {
        echo "<tr>";
        for ($j = 0; $j <= 9; $j++) {
            echo "<td>&#x55$i$j;</td>";
        }
        echo "</tr>\n";
    }

?>
</table>
</div>
