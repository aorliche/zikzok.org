
var $ = q => document.querySelector(q);
var $$ = q => [...document.querySelectorAll(q)];

function shuffle(arr) {
	for (let i=0; i<arr.length; i++) {
		const j = Math.floor(Math.random()*arr.length);
		const k = Math.floor(Math.random()*arr.length);
		if (j != k) [arr[j],arr[k]] = [arr[k],arr[j]];
	}
}

function zip(a,b) {
    console.assert(a.length == b.length);
    return a.map((aelt, aidx) => [aelt, b[aidx]]);
}

class Word {
    constructor(word, count) {
        this.word = word;
        this.count = count;
        this.score = this.count*this.mult;
    }

    get mult() {
        let len = this.word.length;
        if (this.word.indexOf("'") != -1) {
            len -= 2;
        }
        return 1+0.3*(len-3);
    }
}

window.addEventListener('load', e => {
    let gotwords = false;
    let words;
    const nwords = 8;

    fetch('/wordcounts.json')
    .then(res => res.json())
    .then(json => {
        const w = Object.keys(json);
        const c = Object.values(json);
        words = zip(w, c).map(pair => new Word(pair[0], pair[1]));
        wordsSav = [...words];
        gotwords = true;
    })
    .catch(err => console.log(err));

    function populateKeywords(res, usecount) {
        $('#keywords').innerHTML = '';
        res.forEach(word => {
            const a = document.createElement('a');
            a.href = '/search.php?w=' + encodeURIComponent(word.word);
            a.innerText = word.word;
            const span = document.createElement('span');
            span.innerText = ` (${usecount ? word.count : word.score.toFixed(1)}) `;
            $('#keywords').appendChild(a);
            $('#keywords').appendChild(span);
        });
    }

    $('#random-words').addEventListener('click', e => {
        e.preventDefault();
        if (!gotwords) return;
        const res = [];
        const wordscp = [...words];
        for (let i=0; i<8; i++) {
            idx = Math.floor(Math.random()*(wordscp.length-i));
            res.push(wordscp.splice(idx,1)[0]);
        }
        populateKeywords(res);
    });
    
    $('#high-score-words').addEventListener('click', e => {
        e.preventDefault();
        if (!gotwords) return;
        const res = [];
        words.sort((a,b) => a.score < b.score);
        for (let i=0; i<8; i++) {
            res.push(words[i]);
        }
        populateKeywords(res);
    });
    
    $('#popular-words').addEventListener('click', e => {
        e.preventDefault();
        if (!gotwords) return;
        const res = [];
        shuffle(words);
        words.sort((a,b) => a.count < b.count);
        for (let i=0; i<8; i++) {
            res.push(words[i]);
        }
        populateKeywords(res, true);
    });
    
    $('#unpopular-words').addEventListener('click', e => {
        e.preventDefault();
        if (!gotwords) return;
        const res = [];
        shuffle(words);
        words.sort((a,b) => a.count > b.count);
        for (let i=0; i<8; i++) {
            res.push(words[i]);
        }
        populateKeywords(res, true);
    });

    $('#safety').addEventListener('click', e => {
        e.preventDefault();
        if (e.target.innerText == 'Safety is On') {
            e.target.innerText = 'Safety is Off';
            words = [...wordsSav];
        } else
            e.target.innerText = 'Safety is On';
    });

});
