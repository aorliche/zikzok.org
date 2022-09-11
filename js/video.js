var $ = e => document.querySelector(e);
var $$ = e => document.querySelectorAll(e);

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

    function likeOrDisagree(type) {
        fetch(`/like.php?v=${new URL(document.location).searchParams.get('v')}&type=${type}`)
        .then(resp => resp.text())
        .then(text => {
            if (text == 'ok') {
                if (!type || type == 'like') {
                    const n = parseInt($('#likes').innerText);
                    $('#likes').innerText = n+1;
                } 
            } else {
                alert(text);
            }
        })
        .catch(err => console.log(err));

    }

    // Like video
    $('#like').addEventListener('click', e => {
        e.preventDefault();
        likeOrDisagree('like');
    });

    // Disagree with video
    $('#disagree').addEventListener('click', e => {
        e.preventDefault();
        likeOrDisagree('disagree');
    });
    
    // Disagree with video
    $('#hate').addEventListener('click', e => {
        e.preventDefault();
        likeOrDisagree('hate');
    });

    // View likes
    $('#blame-likes-a').addEventListener('click', e => {
        e.preventDefault();
        $('#likes-ul').style.display = 'block';
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

    // Listeners for all text/hunimal overlay elements
    function addDraggableListener(d) {
        function downOrTouchStart(clientX, clientY) {
            if (!$('#display-overlay').checked) {
                return;
            }
            [...$$('.draggable')].forEach(f => {
                f.dragging = false;
                f.classList.remove('selected');
            });
            const rv = $('#video-overlay').getBoundingClientRect();
            const r = d.getBoundingClientRect();
            d.dragging = true;
            d.dx = r.left - clientX - rv.left;
            d.dy = r.top - clientY - rv.top;
            selected = d;
            d.classList.add('selected');
            updateControls(d);
        }
        d.addEventListener('mousedown', e => {
            e.stopPropagation();
            downOrTouchStart(e.clientX, e.clientY);
        });
        d.addEventListener('touchstart', e => {
            e.stopPropagation();
            downOrTouchStart(e.touches[0].clientX, e.touches[0].clientY);
        });
        d.addEventListener('keyup', e => {
            e.stopPropagation();
            updateTexts();
        });
    }

    // Add listeners to texts already saved in the db for the video
    [...$$('.draggable')].forEach(d => addDraggableListener(d));

    // And update the list
    updateTexts(); 

    // This is an existing text and we want to delete it from the db
    function removeFromDB(d) {
        fetch(`/text-remove.php?id=${d.dataset.id}`)
        .then(resp => resp.text())
        .then(text => {
            if (text != 'Success') console.log(text);
        })
        .catch(err => console.log(err));
    }

    function updateTexts() {
        $('#texts').innerHTML = '';
        $$('#video-overlay .draggable').forEach(d => {
            const li = document.createElement('li');
            const a = document.createElement('a');
            const aRemove = document.createElement('a');
            a.innerText = d.innerText;
            a.href = '#';
            a.addEventListener('click', e => {
                e.preventDefault();
                d.dispatchEvent(new Event('mousedown'));
            });
            aRemove.innerText = "Remove";
            aRemove.href = '#';
            aRemove.addEventListener('click', e => {
                e.preventDefault();
                if (d == selected) selected = null;
                d.parentNode.removeChild(d);
                updateTexts();
                if (d.dataset.id) {
                    removeFromDB(d);
                }
            });
            aRemove.style.paddingLeft = '5px';
            li.appendChild(a);
            li.appendChild(aRemove);
            $('#texts').appendChild(li);
        });
    }

    function moveOrTouchMove(clientX, clientY) {
        if (!$('#display-overlay').checked) {
            return;
        }
        const d = [...$$('.draggable')].filter(d => d.dragging)[0];
        if (d) {
            console.log([clientX, clientY]);
            const rv = $('#video-overlay').getBoundingClientRect();
            const r = d.getBoundingClientRect();
            let x = d.dx + clientX;
            let y = d.dy + clientY;
            if (x < 0) x = 0;
            if (x + r.width > rv.width) x = rv.width - r.width;
            if (y < 0) y = 0;
            if (y + r.height > rv.height) y = rv.height - r.height;
            d.style.left = x + 'px';
            d.style.top = y + 'px';
        }
    }

    $('#video-overlay').addEventListener('mousemove', e => {
        moveOrTouchMove(e.clientX, e.clientY);
    });
    
    $('#video-overlay').addEventListener('touchmove', e => {
        moveOrTouchMove(e.touches[0].clientX, e.touches[0].clientY);
    });

    function deselectText() {
        // Video playing
        if (!$('#display-overlay').checked) {
            $('#video-overlay').style.display = 'none';
            player.pause();
        }
        [...$$('.draggable')].forEach(d => d.dragging = false);
        if (selected) {
            selected.classList.remove('selected');
            selected = null;
        }
    }

    $('#video-overlay').addEventListener('mousedown', e => {
        deselectText();
    });
    
    $('#video-overlay').addEventListener('touchstart', e => {
        deselectText();
    });

    function upLeaveOrTouchEnd() {
        if (!$('#display-overlay').checked) {
            return;
        }
        [...$$('.draggable')].forEach(d => d.dragging = false);
        console.log('up');
    }

    $('#video-overlay').addEventListener('mouseup', e => {
        upLeaveOrTouchEnd();
    });

    $('#video-overlay').addEventListener('mouseleave', e => {
        upLeaveOrTouchEnd();
    });
    
    $('#video-overlay').addEventListener('touchend', e => {
        upLeaveOrTouchEnd();
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

    // Add a new piece of draggable text to be displayed
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
        d.classList.add('hunimal-font');
        d.classList.add('selected');
        d.contentEditable = 'true';
        d.dataset.start = 0;
        d.dataset.end = 100;
        addDraggableListener(d);
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

    // Placing hunimal digits
    $('#hunimal-container').addEventListener('mouseover', e => {
        $('#hunimal-select').style.display = 'table';
    });
    
    $('#hunimal-container').addEventListener('mouseout', e => {
        $('#hunimal-select').style.display = 'none';
    });
    
    $('#hunimal-select').addEventListener('mouseout', e => {
        $('#hunimal-select').style.display = 'none';
    });

    let rememberedOffset = null;

    [...$$('#hunimal-select td')].forEach(td => {
        td.addEventListener('click', e => {
            const sel = getSelection();
            let off = null;
            if (sel && sel.focusNode instanceof Text && sel.focusNode.parentNode == selected) {
                off = sel.anchorOffset;
            } else if (selected && rememberedOffset) {
                off = rememberedOffset;
            } else {
                $('#add-text').dispatchEvent(new Event('click'));
                selected.innerText = '';
                off = 0;
            }
            if (off || off === 0) {
                const before = selected.innerText.substr(0,off);
                const after = selected.innerText.substr(off);
                selected.innerText = before + td.innerText + after;
                updateTexts();
                rememberedOffset = off+1;
            }
        });
    });

    // Push to database
    $('#save-all').addEventListener('click', e => {
        e.preventDefault();
        const orig = [...$$('.draggable')];
        const draggables = orig.map(d => {
            const style = getComputedStyle(d);
            const [r,g,b] = [...style.color.matchAll(/\d+/g)].map(part => parseInt(part));
            return {
                id: d.dataset.id ?? null,
                uniqid: new URL(document.location).searchParams.get('v'),
                text: d.innerText,
                top: parseInt(d.style.top),
                left: parseInt(d.style.left),
                red: r,
                green: g,
                blue: b,
                size: parseInt(style.fontSize),
                start: parseInt(d.dataset.start),
                end: parseInt(d.dataset.end)
            };
        });
        fetch('/text-save.php', {
            method: 'post', 
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(draggables)
        })
        .then(resp => resp.json())
        .then(json => {
            if (json.error) {
                console.log(json.error);
                return;
            }
            for (let i=0; i<draggables.length; i++) {
                if (!draggables[i].id) {
                    orig[i].dataset.id = json[i];
                }
            }
        })
        .catch(err => console.log(err));
    });
});
