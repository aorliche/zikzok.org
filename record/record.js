let player, previewImg;

window.addEventListener('load', e => {
	let options = {
		// video.js options
		controls: true,
		bigPlayButton: false,
		loop: false,
		fluid: false,
		width: 320,
		height: 240,
		plugins: {
			// videojs-record plugin options
			record: {
				image: false,
				audio: true,
				video: true,
				maxLength: 60,
				displayMilliseconds: true,
				debug: true
			}
		}
	};
	player = videojs('videoElt', options, function() {
		// print version information at startup
		const msg = 'Using video.js ' + videojs.VERSION +
			' with videojs-record ' + videojs.getPluginVersion('record');
		videojs.log(msg);
		console.log("videojs-record is ready!");
	});
	player.on('startRecord', e => {
		player.record().exportImage()
			.then(res => previewImg = res)
			.catch(error => console.log(`Error in preview image ${error}`));
	});
	player.on('finishRecord', e => {
		console.log('Finished recording');
		console.log(player.recordedData.name);
		console.log(player.recordedData.size);
		const nameInput = document.querySelector('#name');
		const data = new FormData();
		const name = nameInput.value.replace('/\//g', 'FSLASH').replace('/\\/g', 'BLASH');
		data.append('recordedData', player.recordedData);
		data.append('previewImage', previewImg);
		data.append('name', name);
		fetch('finish.php', {
			method: 'POST',
			body: data
		})
		.then(response => response.json())
		.then(result => console.log(result))
		.catch(error => console.log(error));
	});
});
