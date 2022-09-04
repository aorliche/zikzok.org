/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function buildRandomMSP(m, sideLength, doMulti) {
    if (!m) {
        m = parseInt(mField.value);
    }
    if (!sideLength) {
        sideLength = parseInt(sideLengthField.value);
    }
    
    if (!selectedVertex) {
        selectedVertex = new Vertex(canvas.width/2, canvas.height/2);
    }
     // center the MSP
    var v = findStartingVertex(m, sideLength, selectedVertex);
    
    // construct the initial chain
    var initChain = buildInitialChain(v, m, sideLength);
    
    // copy into msp
    var rhombi = initChain.slice(0);
    
    // expand the msp
    if (doMulti) {
        expandFromInitialChainMulti(initChain, rhombi);
    } else {
        expandFromInitialChain(initChain, rhombi);
    }
    
    // build the chains
    var chains = buildChains(rhombi);
//    chains = null;
    
    // color the rhombi
    colorRhombi(rhombi);
    
    // combine into msp object and add to MSP array
    return new MSP(rhombi, chains, selectedVertex, m, sideLength);
    
    // draw onto canvas
    repaint();
}

function buildInitialChain(v, m, sideLength) {
    var angles = buildInitialAngles(m);
    var chain = Array();
    for (var i=0; i<angles.length; i++) {
        var r = buildRhombusFromAngle(v, angles[i], sideLength);
        chain.push(r);
        v = r.vs[3];
    }
    return chain;
}



function buildInitialAngles(m) {
    var angles = Array();
    for (var i=1; i<m; i++) {
        angles.push(i*180/m);
    }
    shuffle(angles);
    return angles;
}

function expandFromInitialChain(initChain, msp) {
    var holes = Array();
    var nIter = 0;
    for (var i=0; i<initChain.length-1; i++) {
        holes.push(new Hole(initChain[i], initChain[i+1], msp));
    }
    while (holes.length > 0) {
        // randomize hole order
        shuffle(holes);
        
        // fill a random hole in the msp
        var hole = holes.shift();
        fillHole(hole, msp);
        
        var newestRhombus = msp[msp.length-1];
        
        // cull any holes partially filled by newly placed rhombus
        holes = cullHoles(holes, newestRhombus);
        
        // add any new holes created by newly placed rhombus
        addHoles(holes, newestRhombus, msp);

        assert((nIter++) < maxExpandIter, "expandFromInitialChain");
    }
}

function expandFromInitialChainMulti(initChain, msp) {
    var holes = Array();
    var nIter = 0;
    var maxIter = 1000;
    for (var i=0; i<initChain.length-1; i++) {
        holes.push(new Hole(initChain[i], initChain[i+1], msp));
    }
    while (holes.length > 0) {
        // randomize hole order
        shuffle(holes);
        
        // fill a random hole in the msp
        var hole = holes.shift();
        if (holes.length === 0) {
            var n = fillHoleMulti(hole, msp);
            if (n === 2) {
                addHoles(holes, msp[msp.length-2], msp);
            }
        } else {
            fillHole(hole, msp);
        }
        
        var newestRhombus = msp[msp.length-1];
        
        // cull any holes partially filled by newly placed rhombus
        holes = cullHoles(holes, newestRhombus);
        
        // add any new holes created by newly placed rhombus
        addHoles(holes, newestRhombus, msp);
        
        if (nIter > maxIter) {
            return;
        }

        assert((nIter++) < maxExpandIter, "expandFromInitialChain");
    }
}
