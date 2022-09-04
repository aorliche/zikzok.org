
// canvas declared in draw.js
window.addEventListener('load', e => {
    canvas = document.querySelector('canvas');
    const uniqid = new URL(document.location).searchParams.get('v');
    fetch(`/msps/${uniqid}.msp.json`)
    .then(resp => resp.json())
    .then(json => {
        msps.push(json);
        repaint(canvas);
    })
    .catch(err => console.log(err));
});
