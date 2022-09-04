/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function MSP(rhombi, chains, center, m, sideLength) {
    this.rhombi = rhombi;
    this.chains = chains;
    this.center = copyVertex(center);
    this.m = m;
    this.sideLength = sideLength;
    this.polys = rhombi;
}

// find the radius of an MSP
function findRadius(m, sideLength) {
    var diam = 0;
    for (var a=0; a<m; a++){
        diam += sideLength*Math.sin(a*Math.PI/m);
    }
    return diam/2;
}

function copyMSP(msp) {
    return loadMSP(JSON.stringify(msp));
}

function restoreIntegrity(msp) {
    var rhombi = msp.rhombi;
    var chains = msp.chains;

    // keep integrity between rhombi and chains
    for (var i=0; i<rhombi.length; i++) {
        replaceInChains(rhombi[i], chains);
    }

    // replace polys (keep integrity) simple fix
    msp.polys = msp.rhombi;
}

function loadMSP(json) {
    var msp = JSON.parse(json);
    restoreIntegrity(msp);
    return msp;
}

function replaceInChains(r, chains) {
    for (var i=0; i<chains.length; i++) {
        for (var j=0; j<chains[i].length; j++) {
            if (chains[i][j].idx === r.idx) {
                chains[i][j] = r;
            }
        }
    }
}

function createSaveToFileFunction(mspCopy) {
    return function() {
        var text = JSON.stringify(mspCopy);
        var file = new Blob([text], {type: "application/octet-stream"});
        var a = document.createElement("a");
        var url = URL.createObjectURL(file);
        a.href = url;
        a.download = "msp.msp";
        document.body.appendChild(a);
        a.click();
        setTimeout(function() {
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }, 0);
    };
}
