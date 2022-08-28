var $ = q => document.querySelector(q);
var $$ = q => [...document.querySelectorAll(q)];

window.addEventListener('load', e => {

    $('#comment-submit').addEventListener('click', e => {
        if (!$('#comment-name').value) {
            alert('Need comment name');
            e.preventDefault();
            return;
        } else if (!$('#comment').value) {
            alert('Enter comment text');
            e.preventDefault();
            return;
        }
        $('#comment-form').submit();
    });

});
