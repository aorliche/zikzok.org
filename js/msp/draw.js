function getRandomHexDigit() {
    //var hex = "0123456789abcdef";
    var hex = "6789abcdef";
    return hex.charAt(Math.floor(hex.length*Math.random()));
}

function colorRhombi(rhombi, color, alpha, applyColor, applyAlpha) {
    var useColor = false;
    if (color && color !== "") {
        useColor = true;
    }
    for (var i=0; i<rhombi.length; i++) {
        if (!useColor) {
            var r = getRandomHexDigit();
            var g = getRandomHexDigit();
            var b = getRandomHexDigit();
            color = '#' + r + g + b;
            rhombi[i].color = color;
        } 
        if (applyColor) {
            rhombi[i].color = color;
        }
        if (applyAlpha) {
            rhombi[i].alpha = alpha;
        }
    }
}

function drawRhombus(ctx, r) {
//    ctx.fillStyle = 'orange';
    if (r.selectedForColoring) {
        ctx.fillStyle = 'green';
    } else if (r.selectedForFlip) {
        ctx.fillStyle = 'blue';
    } else {
        ctx.fillStyle = r.color;
    }
    if (r.highlight) {
        ctx.fillStyle = 'gold';
    }
    ctx.strokeStyle = '#000';
    ctx.beginPath();
    ctx.moveTo(r.vs[0].x, r.vs[0].y);
    for (var i=1; i<r.vs.length; i++) {
        ctx.lineTo(r.vs[i].x, r.vs[i].y);
    }
    ctx.closePath();
    ctx.fill();
    ctx.stroke();
}

function drawMSP(ctx, msp) {
    for (var i=0; i<msp.polys.length; i++) {
        drawRhombus(ctx, msp.polys[i]);
    }
}

function repaint(canvas) {
    var ctx = canvas.getContext('2d');
    
    ctx.fillStyle = '#fff';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    
    for (var i=0; i<msps.length; i++) {
        drawMSP(ctx, msps[i]);
    }
}

function canvasColorToTransparent(canvas, r, g, b) {
    const ctx = canvas.getContext('2d');
    const data = ctx.getImageData(0,0,canvas.width,canvas.height);
    for (let i=0; i<data.data.length; i+=4) {
        if (data.data[i] == r && data.data[i+1] == g && data.data[i+2] == b) {
            data.data[i+3] = 0;
        }
    }
    ctx.putImageData(data, 0, 0);
}

var selectedVertex = null;
var canvas = null;
var maxExpandIter = 1000;
var msps = [];
