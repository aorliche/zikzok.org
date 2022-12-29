<style>
#split {
    width: 200px;
    font-size: 12px;
    display: inline-block;
    vertical-align: top;
}
/*#split > div {
    display: inline-block;
}*/
</style>
<div id='split'>
    <h3>Split</h3>
    <textarea id='split-search-area'>Type search text here</textarea>
    <button id='split-search-button'>Find Now</button>
    <div id='splits-results'>
    </div>
    <button id='split-button'>Split</button>
</div>
<script>
$('#split-search-button').addEventListener('click', e => {
    e.preventDefault();
    let text = $('#split-search-area').value;
    const results = $('#split-results');
    text = text.replace(/[^a-zA-Z0-9]+/g,',');
    text = encodeURIComponent(text);
    fetch(`/split-search.php?q=${text}`)
    .then(res => res.text())
    .then(txt => {
        results.innerHTML = txt;
    })
    .catch(err => {
        results.innerText = err;
        console.log(err));
    });
});
$('#split-button').addEventListener('click', e => {
    e.preventDefault();
});
</script>
