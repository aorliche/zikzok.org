window.addEventListener('load', e => {
    const $ = q => document.querySelector(q);
    const $$ = q => [...document.querySelectorAll(q)];

    fetch('/video-names-uniqids.php')
    .then(resp => resp.json())
    .then(json => {
        $$('.split-link').forEach(a => {
            a.addEventListener('click', ee => {
                ee.preventDefault();
                const comment = $(`#comment-${a.dataset.comment}`).innerText;
                const wordPrompt = a.parentElement.querySelector('.split-word-prompt');
                const wordInp = a.parentElement.querySelector('.split-word-input');
                const wordFeedback = a.parentElement.querySelector('.split-word-feedback');
                const commentText = comment.split(/\s+/);
                wordFeedback.style.display = 'inline';
                wordInp.style.display = 'inline-block';
                wordFeedback.style.display = 'inline';
                wordInp.max = commentText.length-1;
                wordFeedback.innerText = commentText[0];
                wordInp.addEventListener('change', eee => {
                    wordFeedback.innerText = commentText[wordInp.value];
                });
                const sel = a.parentElement.querySelector('.split-select');
                sel.style.display = 'inline-block';
                const opt = document.createElement('option');
                opt.value = 'none';
                opt.innerText = 'Select a video';
                sel.appendChild(opt);
                for (let i=0; i<json.length; i++) {
                    const opt = document.createElement('option');
                    opt.value = json[i].uniqid;
                    opt.innerText = json[i].name;
                    sel.appendChild(opt);
                }
                sel.addEventListener('change', eee => {
                    /*console.log(sel.options[sel.selectedIndex].value);
                    console.log(a.dataset.video);
                    console.log(a.dataset.comment);*/
                    const vid = sel.options[sel.selectedIndex].value;
                    fetch(`/split-comment.php?c=${a.dataset.comment}&v=${vid}&before=${before}&after=${after}`)
                    .then(resp => resp.text())
                    .then(text => {
                        if (text != 'OK') {
                            console.log(text);
                        } else {
                            document.location.href = `/video.php?v=${a.dataset.video}`;
                        }
                    })
                    .catch(err => console.log(err));
                });
            });
        });
    })
    .catch(err => console.log(err));
});
