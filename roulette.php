<!DOCTYPE html>
<link rel="stylesheet" href="css/zikzok.css">
<style>
#chars {
    width: 280px;
    font-size: 12px;
}
.char > span:first-child {
    display: inline-block;
    width: 120px;
}
.roulette {
    display: inline-block;
    width: 142px;
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
</style>

<template id='roulette'>
<div class='roulette'>
    <div class='r-left'><a href='#'>&lt;</a></div>
    <div><div class='r-numbers'></div></div>
    <div class='r-right'><a href='#'>&gt;</a></div>
</div>
</template>

<div id='chars'>
    <div class='char' data-name='Algorithm'></div>
    <div class='char' data-name='Connecitivity'></div>
    <div class='char' data-name='Delta-V'></div>
    <div class='char' data-name='Entropy'></div>
    <div class='char' data-name='Hive'></div>
    <div class='char' data-name='Kismet'></div>
    <div class='char' data-name='Monkey Island'></div>
    <div class='char' data-name='Porkus'></div>
    <div class='char' data-name='Singularity'></div>
    <div class='char' data-name='Tian'></div>
    <div class='char' data-name='Wavelength'></div>
    <div class='char' data-name='Zorro'></div>
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
        rn.appendChild(span);
        spans.push(span);
    }
    let selected = spans[1+Math.floor((spans.length-1)*Math.random())];
    spans.push(makeSpace());
    rn.appendChild(spans.at(-1));
    function scrollTo(span) {
        span.scrollIntoView({behavior: 'smooth', inline: 'center'});
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
    scrollTo(selected);
    rl.addEventListener('click', e => {
        action(e, 'left');
    });
    rr.addEventListener('click', e => {
        action(e, 'right');
    });
});
</script>
