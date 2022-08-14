const $ = e => document.querySelector(e);
const $$ = e => document.querySelectorAll(e);

window.addEventListener('load', e => {
	/*let options = {
		// video.js options
		controls: true,
		bigPlayButton: false,
		loop: false,
		fluid: false,
		width: 320,
		height: 240,
		plugins: {}
	};*/
	const options = {};
	const player = videojs('videoElt', options, function() {
		// print version information at startup
		const msg = 'Using video.js ' + videojs.VERSION;
		videojs.log(msg);
	});
    
    let madePred = false;

    player.on('play', e => {
        if (!madePred) {
            player.pause();
            alert('Please make prediction!');
        }
    });

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
        let selViews = null, selLikes = null;
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
        pred.innerHTML = '';
        $('#pred-span').innerText = `Thank you! You predicted ${selViews.innerText} views and ${selLikes.innerText} likes`;
    });

    $('#like').addEventListener('click', e => {
        fetch(`/like.php?v=${new URL(document.location).searchParams.get('v')}`)
        .then(resp => resp.text())
        .then(text => {
            if (text == 'ok') {
                const n = parseInt($('#likes').innerText);
                $('#likes').innerText = n+1;
            } else {
                alert(text);
            }
        })
        .catch(err => console.log(err));
    });
});
