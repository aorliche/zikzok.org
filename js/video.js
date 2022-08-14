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
});
