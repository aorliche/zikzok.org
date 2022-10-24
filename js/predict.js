var $ = e => document.querySelector(e);
var $$ = e => [...document.querySelectorAll(e)];

let selViews = null, selLikes = null;

window.addEventListener('load', e => {
    let madePred = false;
    
    const predViews = $$('#pred-views .pred-button');
    const predLikes = $$('#pred-likes .pred-button');
    
    const urlSearchParams = new URLSearchParams(window.location.search);
    const nam = urlSearchParams.get('name');
    const lik = urlSearchParams.get('predLikes');
    const view = urlSearchParams.get('predViews');

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
    
    if (nam) {
        $('#name').value = nam;
    }
    
    predViews.forEach(b => {
        if (b.innerText == view) {
            b.classList.add('selected');
        }
    });
    
    predLikes.forEach(b => {
        if (b.innerText == lik) {
            b.classList.add('selected');
        }
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
    
    if (urlSearchParams.get('alt')) {
        $('#pred-submit').dispatchEvent(new Event('click'));
    }
});
