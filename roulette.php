<!--<!DOCTYPE html>
<link rel="stylesheet" href="css/zikzok.css">-->
<style>
#chars {
    width: 180px;
    font-size: 12px;
    display: inline-block;
    vertical-align: top;
}
.char > span:first-child {
    display: inline-block;
    width: 90px;
}
.roulette {
    display: inline-block;
}
.roulette > div {
    display: inline-block;
}
.r-left, .r-right {
    width: 20px;
    text-align: center;
}
.r-numbers {
    width: 40px;
    display: flex;
    flex-wrap: nowrap;
    overflow-x: hidden;
}
.r-numbers span {
    padding-left: 2px;
    padding-right: 2px;
}
.invisible {
    color: rgba(0,0,0,0);
}
.pink, .yellow, .green {
    border-radius: 5px;
}
.pink {
    background-color: #f66;
}
.yellow {
    background-color: #ff6;
}
.green {
    background-color: #6f6;
}
</style>

<template id='roulette'>
<div class='roulette'>
    <div class='r-left'><a href='#'>&lt;</a></div>
    <div><div class='r-numbers'></div></div>
    <div class='r-right'><a href='#'>&gt;</a></div>
</div>
</template>

<div id='chars'>
    <h3>Video Characteristics</h3>
    <div class='char' data-name='Connecitivity'></div>
    <div class='char' data-name='Delta-V'></div>
    <div class='char' data-name='Entropy'></div>
    <div class='char' data-name='Fairness'></div>
    <div class='char' data-name='Height'></div>
    <div class='char' data-name='Kismet'></div>
    <div class='char' data-name='Monkey Island'></div>
    <div class='char' data-name='Pork'></div>
    <div class='char' data-name='Singularity'></div>
    <div class='char' data-name='Wu'></div>
    <div class='char' data-name='Wavelength'></div>
    <div class='char' data-name='Zero Crossings'></div>
</div>

<script>
var $ = q => document.querySelector(q);
var $$ = q => [...document.querySelectorAll(q)];
const template = $('#roulette');
$$('.char').forEach(c => {
    const clone = template.content.cloneNode(true); 
    const span = document.createElement('span');
    span.innerText = c.dataset.name;
    c.appendChild(span);
    c.appendChild(clone);
});
function decToHun(n) {
    if (n == 0) {
        return "\u{5500}";
    }
    let hun = "";
    while (n > 0) {
        const ones = (n % 10);
        const tens = (Math.floor(n/10) % 100);
        const digit = String.fromCharCode(0x5500+0x10*tens+ones);
        hun = `${digit}${hun}`;
        n = Math.floor(n/100);
    }
    return hun;
}
function makeSpace() {
    const span = document.createElement('span');
    span.innerText = 'x';
    span.classList.add('invisible');
    return span;
}
$$('.roulette').forEach(r => {
    const rn = r.querySelector('.r-numbers');
    const rl = r.querySelector('.r-left a');
    const rr = r.querySelector('.r-right a');
    const spans = [];
    spans.push(makeSpace());
    rn.appendChild(spans.at(-1));
    for (let i=0; i<10; i++) {
        const span = document.createElement('span');
        span.innerText = decToHun(i);
        span.dataset.num = i;
        span.classList.add('hunimal-font');
        if (i < 3) {
            span.classList.add('pink');
        } else if (i < 7) {
            span.classList.add('yellow');
        } else {
            span.classList.add('green');
        }
        rn.appendChild(span);
        spans.push(span);
    }
    let selected = spans[1+Math.floor((spans.length-1)*Math.random())];
    spans.push(makeSpace());
    rn.appendChild(spans.at(-1));
    function scrollTo(span) {
        span.scrollIntoView({behavior: 'smooth', block: 'nearest', inline: 'center'});
    }
    function action(e, lr) {
        e.preventDefault();
        const cur = parseInt(selected.dataset.num);
        if (lr == 'left' && cur == 0) return;
        if (lr == 'right' && cur == 9) return;
        const next = (lr == 'left') ? cur-1 : cur+1;
        selected = spans[next+1];
        scrollTo(selected);
    }
    const rect = rn.getBoundingClientRect();
    rn.scrollTo({behavior: 'smooth', left: selected.getBoundingClientRect().x-rect.x-12});
    rl.addEventListener('click', e => {
        action(e, 'left');
    });
    rr.addEventListener('click', e => {
        action(e, 'right');
    });
});
</script>
