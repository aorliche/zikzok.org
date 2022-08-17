var $ = e => document.querySelector(e);
var $$ = e => document.querySelectorAll(e);

let selViews = null, selLikes = null;

window.addEventListener('load', e => {
    let madePred = false;
    
    const predViews = [...$$('#pred-views .pred-button')];
    const predLikes = [...$$('#pred-likes .pred-button')];

    [predViews, predLikes].forEach(pp => {
        pp.forEach(b => {
            b.addEventListener('click', e => {
                pp.forEach(c => {
                    c.classList.remove('selected');
                });
                b.classList.add('selected');
            });
        });
    });

    $('#pred-submit').addEventListener('click', e => {
        e.preventDefault();
        predViews.forEach(b => {
            if (b.classList.contains('selected')) selViews = b;
        });
        predLikes.forEach(b => {
            if (b.classList.contains('selected')) selLikes = b;
        });
        if (selViews == null || selLikes == null) {
            alert('Please make prediction!');
            return;
        }
        madePred = true;
        $('#pred-info').innerText = `Thank you! You predicted ${selViews.innerText} views and ${selLikes.innerText} likes`;
        $('#pred').style.display = 'none';
        $('#post-pred').style.display = 'block';
    });

});
