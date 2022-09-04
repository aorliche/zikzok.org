function getVideoImage(path, secs, callback) {
    var me = this, video = document.createElement('video');
    video.onloadedmetadata = function() {
        if ('function' === typeof secs) {
            secs = secs(this.duration);
        }
        this.currentTime = Math.min(Math.max(0, (secs < 0 ? this.duration : 0) + secs), this.duration);
    };
    video.onseeked = function(e) {
        var canvas2 = document.createElement('canvas');
        canvas2.height = video.videoHeight;
        canvas2.width = video.videoWidth;
        var ctx = canvas2.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas2.width, canvas2.height);
        var img = new Image();
        img.src = canvas2.toDataURL();
        callback.call(me, img, this.currentTime, e);
    };
    video.onerror = function(e) {
        callback.call(me, undefined, undefined, e);
    };
    video.src = path;
}

function upload(video, img, mspStr) {
	const nameInput = document.querySelector('#name');
	const data = new FormData();
	const name = nameInput.value;
	data.append('recordedData', video);
	data.append('previewImage', img);
	data.append('name', name);
    data.append('predLikes', selLikes.innerText);
    data.append('predViews', selViews.innerText);
    data.append('replyto', nameInput.dataset.replyto);
    data.append('msp', mspStr);
	fetch('upload.php', {
		method: 'POST',
		body: data
	})
	.then(response => response.json())
	.then(json => {
		console.log(json);
		info.innerText = 'Finished converting, video available.';
        window.location.href = `/video.php?v=${json['uniqid']}`;
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

    //New Code
    return new Blob([ab], {type: mimeString});
}

let player, previewImg;

window.addEventListener('load', e => {
    // MSP-specific, canvas declared in draw.js
    canvas = document.querySelector('canvas');
    const msp = buildStarMSP(6, 20, false);
    msps.push(msp);
    repaint(canvas);
    // Video
	const info = document.querySelector('#info');
	const moreInfo = document.querySelector('#more-info');
    const mobileElt = document.querySelector('#mobile-elt');
    const nameInput = document.querySelector('#name');
    const reader = new FileReader();
    let vf = null;
    reader.addEventListener('load', e => {
        const video = e.target.result;
        function cb(img, time, evt) {
            /*console.log(time);
            console.log(evt);
            console.log(img);*/
            const blob = dataURItoBlob(img.src);
            upload(vf, blob, JSON.stringify(msp));
        }
        getVideoImage(video, 0.1, cb);
    });
    if (mobileElt) {
        mobileElt.addEventListener('click', e => {
            if (nameInput.value.length == 0) {
                alert('Enter a video name');
                e.preventDefault();
            }
        });
        mobileElt.addEventListener('change', e => {
            vf = mobileElt.files[0];
            info.innerText = 'Starting conversion...';
            reader.readAsDataURL(vf);
        });
    } else {
        let options = {
            // video.js options
            controls: true, bigPlayButton: false, loop: false, fluid: false, width: 640,
            plugins: {
                // videojs-record plugin options
                record: {
                    image: false, audio: true, video: true, maxLength: 60, displayMilliseconds: true, debug: true,
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
            if (nameInput.value.length == 0) {
                alert('Enter a video name');
                player.record().reset();
                return;
            }
            info.innerText = 'Recording';
        });
        player.on('finishRecord', e => {
            info.innerText = 'Converting, please wait';
        });
        player.on('finishConvert', e => {
            info.innerText = 'Finished converting, grabbing screen preview';
            vf = player.convertedData;
            reader.readAsDataURL(vf);
        });
    }
});
