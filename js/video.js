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
	const player = videojs('video-elt', options, function() {
		// print version information at startup
		const msg = 'Using video.js ' + videojs.VERSION;
		videojs.log(msg);
	});

    player.on('play', e => {
        if (selected) {
            selected.classList.remove('selected');
            selected = null;
        }
        getSelection().removeAllRanges();
        $('#video-overlay').style.display = 'block';
        [...$$('.draggable')].forEach(d => {
            d.style.display = 'none';
        });
    });

    player.on('timeupdate', e => {
        const time = player.currentTime();
        console.log(time);
        [...$$('.draggable')].forEach(d => {
            d.style.display = 'none';
            if (d.dataset.start*player.duration()/100 <= time && 
                d.dataset.end*player.duration()/100 >= time) {
                d.style.display = 'inline-block';
            }
        });
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

    let selected = null;
    const texts = [];

    function updateControls(d) {
        const style = getComputedStyle(d);
        const [r,g,b] = [...style.color.matchAll(/\d+/g)];
        $('#text-font-red').value = r;
        $('#text-font-green').value = g;
        $('#text-font-blue').value = b;
        $('#text-font-size').value = parseInt(style.fontSize);
        $('#start-time').value = d.dataset.start;
        $('#end-time').value = d.dataset.end;
    }

    function addDragableListener(d) {
        d.addEventListener('mousedown', e => {
            console.log('down');
            e.stopPropagation();
            if (!$('#display-overlay').checked) {
                return;
            }
            [...$$('.draggable')].forEach(f => {
                f.dragging = false;
                f.classList.remove('selected');
            });
            const rv = $('#video-overlay').getBoundingClientRect();
            const r = d.getBoundingClientRect();
            e.target.dragging = true;
            e.target.dx = r.left - e.clientX - rv.left;
            e.target.dy = r.top - e.clientY - rv.top;
            selected = d;
            d.classList.add('selected');
            updateControls(d);
        });
        d.addEventListener('keyup', e => {
            updateTexts();
        });
    }

    function updateTexts() {
        $('#texts').innerHTML = '';
        $$('#video-overlay .draggable').forEach(d => {
            const li = document.createElement('li');
            const a = document.createElement('a');
            a.innerText = d.innerText;
            a.href = '#';
            a.addEventListener('click', e => {
                e.preventDefault();
                d.click();
            });
            li.appendChild(a);
            $('#texts').appendChild(li);
        });
    }

    $('#video-overlay').addEventListener('mousemove', e => {
        if (!$('#display-overlay').checked) {
            return;
        }
        const d = [...$$('.draggable')].filter(d => d.dragging)[0];
        if (d) {
            const rv = $('#video-overlay').getBoundingClientRect();
            const r = d.getBoundingClientRect();
            let x = d.dx + e.clientX;
            let y = d.dy + e.clientY;
            if (x < 0) x = 0;
            if (x + r.width > rv.width) x = rv.width - r.width;
            if (y < 0) y = 0;
            if (y + r.height > rv.height) y = rv.height - r.height;
            d.style.left = x + 'px';
            d.style.top = y + 'px';
        }
    });
    $('#video-overlay').addEventListener('mousedown', e => {
        // Video playing
        if (!$('#display-overlay').checked) {
            $('#video-overlay').style.display = 'none';
            player.pause();
        }
        console.log('overlay down');
        /*if (!$('#display-overlay').checked) {
            return;
        }*/
        [...$$('.draggable')].forEach(d => d.dragging = false);
        if (selected) {
            selected.classList.remove('selected');
            selected = null;
        }
    });

    $('#video-overlay').addEventListener('mouseup', e => {
        if (!$('#display-overlay').checked) {
            return;
        }
        [...$$('.draggable')].forEach(d => d.dragging = false);
        console.log('up');
    });

    $('#video-overlay').addEventListener('mouseleave', e => {
        if (!$('#display-overlay').checked) {
            return;
        }
        [...$$('.draggable')].forEach(d => d.dragging = false);
        console.log('out');
    });

    $('#display-overlay').addEventListener('change', e => {
        const over = $('#video-overlay');
        if (e.target.checked) {
            over.style.display = 'block';
            $('#overlay-feedback1').style.display = 'inline';
            $('#overlay-feedback2').style.display = 'none';
            [...$$('.draggable')].forEach(d => {
                d.style.display = 'inline-block';
                d.contentEditable = true;
            });
        } else {
            over.style.display = 'none';
            $('#overlay-feedback1').style.display = 'none';
            $('#overlay-feedback2').style.display = 'inline';
            [...$$('.draggable')].forEach(d => {
                d.contentEditable = false;
            });
        }
    });

    $('#text-font-size').addEventListener('input', e => {
        if (selected) {
            selected.style.fontSize = e.target.value + 'px';
        }
    });

    [$('#text-font-red'), $('#text-font-green'), $('#text-font-blue')].forEach(range => {
        range.addEventListener('input', e => {
            if (selected) {
                const r = $('#text-font-red').value;
                const g = $('#text-font-green').value;
                const b = $('#text-font-blue').value;
                selected.style.color = `rgb(${r},${g},${b})`;
            }
        });
    });

    $('#add-text').addEventListener('click', e => {
        e.preventDefault();
        if (!$('#display-overlay').checked) {
            return;
        }
        const over = $('#video-overlay');
        const r = over.getBoundingClientRect();
        const d = document.createElement('div');
        d.innerText = 'Click to edit';
        d.style.top = Math.floor(r.height/2) + 'px';
        d.style.left = Math.floor(100*Math.random()) + 'px';
        d.classList.add('draggable');
        d.classList.add('selected');
        d.contentEditable = 'true';
        d.dataset.start = 0;
        d.dataset.end = 100;
        addDragableListener(d);
        if (selected) selected.classList.remove('selected');
        selected = d;
        over.appendChild(d);
        updateControls(d);
        updateTexts();
    });

    $('#delete-text').addEventListener('click', e => {
        e.preventDefault();
        if (selected) {
            selected.parentNode.removeChild(selected);
            selected = null;
            updateTexts();
        }
    });

    $('#start-time').addEventListener('input', e => {
        if (selected) {
            selected.dataset.start = e.target.value;
        }
    });

    $('#end-time').addEventListener('input', e => {
        if (selected) {
            selected.dataset.end = e.target.value;
        }
    });
    
    $('#open-editor').addEventListener('click', e => {
        e.preventDefault();
        $('#editor').style.visibility = 'visible';
        $('#display-overlay').checked = true;
        $('#display-overlay').dispatchEvent(new Event('change'));
    });

    $('#close-editor').addEventListener('click', e => {
        e.preventDefault();
        $('#editor').style.visibility = 'hidden';
        $('#display-overlay').checked = false;
        $('#display-overlay').dispatchEvent(new Event('change'));
    });
});
