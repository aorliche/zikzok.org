let player, previewImg;

window.addEventListener('load', e => {
	const info = document.querySelector('#info');
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
				debug: true,
				convertEngine: 'ffmpeg.wasm',
				convertWorkerURL: '/lib/node_modules/@ffmpeg/core/dist/ffmpeg-core.js',
				// convert recorded data to MP4 (and copy over audio data without encoding)
				convertOptions: ['-c:v', 'libx264', '-preset', 'slow', '-crf', '22', '-c:a', 'copy', '-f', 'mp4'],
				// specify output mime-type
				pluginLibraryOptions: {
					outputType: 'video/mp4'
				}
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
		info.innerText = 'Recording';
	});
	player.on('finishRecord', e => {
		info.innerText = 'Converting, please wait';
	});
	player.on('finishConvert', e => {
		console.log('Finished recording');
		console.log(player.convertedData.name);
		console.log(player.convertedData.size);
		const nameInput = document.querySelector('#name');
		const data = new FormData();
		const name = nameInput.value.replace(/\//g, 'FSLASH').replace(/\\/g, 'BSLASH');
		data.append('recordedData', player.convertedData);
		data.append('previewImage', previewImg);
		data.append('name', name);
		fetch('upload.php', {
			method: 'POST',
			body: data
		})
		.then(response => response.json())
		.then(result => {
			console.log(result);
			info.innerText = 'Finished converting, video available.';
		})
		.catch(error => console.log(error));
	});
});
