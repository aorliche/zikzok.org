
var $ = e => document.querySelector(e);
var $$ = e => document.querySelectorAll(e);
	
window.addEventListener('load', e => {
    const options = {};
    const myTime = $('#time-elt').innerText;

	const player1 = videojs('video-elt-1', options, function() {
		// print version information at startup
		const msg = 'Using video.js ' + videojs.VERSION;
		videojs.log(msg);
	});
	
    const player2 = videojs('video-elt-2', options, function() {
		// print version information at startup
		const msg = 'Using video.js ' + videojs.VERSION;
		videojs.log(msg);
	});

    function resizeMe(eltIda) {
        let eltId = $(eltIda);
        let rect = eltId.getBoundingClientRect();
        if (rect.width > 600) {
            const h = rect.height*600/rect.width;
            eltId.style.width = '600px';
            eltId.style.height = h + 'px';
        }
        eltId = $(eltIda);
        rect = eltId.getBoundingClientRect();
        if (rect.height > 400) {
            const w = rect.width*400/rect.height;
            eltId.style.width = w + 'px';
            eltId.style.height = '400px';
        }
    }
    
    player1.on('loadeddata', e => {
        resizeMe('#video-elt-1');
    });

    player2.on('loadeddata', e => {
        resizeMe('#video-elt-2');
        player2.currentTime(parseInt(myTime));
    });

    /*player2.on('canplaythrough', e => {
        player2.currentTime(parseInt(myTime));
    });*/

});
