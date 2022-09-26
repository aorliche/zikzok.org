var $ = e => document.querySelector(e);
var $$ = e => [...document.querySelectorAll(e)];

window.addEventListener('load', e => {
    const UPDINT = 30;
    function getHeight(elt) {
        return elt.getBoundingClientRect().height + 'px';
    }
    function updateHeisenberg(left, duration) {
        if (left == duration) {
            $$('.hoverlay').forEach(elt => elt.style.height = getHeight(elt.parentNode.querySelector('img')));
            $$('.hoverlay-back').forEach(elt => elt.style.height = getHeight(elt.parentNode.querySelector('img')));
        }
        if (left < 0) {
            $$('.hoverlay').forEach(elt => elt.style.visibility = 'hidden');
            $$('.hoverlay-back').forEach(elt => elt.style.visibility = 'hidden');
        } else {
            $$('.hoverlay-back').forEach(elt => elt.style.backgroundColor = `rgba(255,255,255,${left/duration})`);
            requestAnimationFrame(e => updateHeisenberg(left-1, duration));
        }
    }
    updateHeisenberg(360,360);
});
