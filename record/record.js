function getVideoImage(path, secs, callback) {
  var me = this, video = document.createElement('video');
  video.onloadedmetadata = function() {
    if ('function' === typeof secs) {
      secs = secs(this.duration);
    }
    this.currentTime = Math.min(Math.max(0, (secs < 0 ? this.duration : 0) + secs), this.duration);
  };
  video.onseeked = function(e) {
    var canvas = document.createElement('canvas');
    canvas.height = video.videoHeight;
    canvas.width = video.videoWidth;
    var ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    var img = new Image();
    img.src = canvas.toDataURL();
    callback.call(me, img, this.currentTime, e);
  };
  video.onerror = function(e) {
    callback.call(me, undefined, undefined, e);
  };
  video.src = path;
}

function upload(video, img) {
	const nameInput = document.querySelector('#name');
	const data = new FormData();
	const name = nameInput.value.replace(/\//g, 'FSLASH').replace(/\\/g, 'BSLASH');
	data.append('recordedData', video);
	data.append('previewImage', img);
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
}
function dataURItoBlob(dataURI) {
    // convert base64 to raw binary data held in a string
    // doesn't handle URLEncoded DataURIs - see SO answer #6850276 for code that does this
    var byteString = atob(dataURI.split(',')[1]);

    // separate out the mime component
    var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];

    // write the bytes of the string to an ArrayBuffer
    var ab = new ArrayBuffer(byteString.length);
    var ia = new Uint8Array(ab);
    for (var i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i);
    }

    //Old Code
    //write the ArrayBuffer to a blob, and you're done
    //var bb = new BlobBuilder();
    //bb.append(ab);
    //return bb.getBlob(mimeString);

    //New Code
    return new Blob([ab], {type: mimeString});
}

let player, previewImg;

window.addEventListener('load', e => {
	const info = document.querySelector('#info');
	const ios = document.querySelector('#iosElt');
	ios.addEventListener('change', e => {
		const f = ios.files[0];
		const reader = new FileReader();
		info.innerText = 'Starting conversion...';
		reader.addEventListener('load', e => {
			const video = e.target.result;
			function cb(img, time, evt) {
				console.log(time);
				console.log(evt);
				console.log(img);
				const blob = dataURItoBlob(img.src);
				upload(f, blob);
			}
			getVideoImage(video, 0.1, cb);
		});
		reader.readAsDataURL(f);
	});
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
		upload(player.convertedData, previewImg);
		/*
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
		.catch(error => console.log(error));*/
	});
});
