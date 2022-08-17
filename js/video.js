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

    /*player.on('play', e => {
        if (!madePred) {
            player.pause();
            alert('Please make prediction!');
        }
    });*/

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
