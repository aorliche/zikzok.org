/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var deltaAngle = 1;

function doPlaceAndExpandRandomChain(doExpand) {
    assert(selectedMSP, "doPlaceAndExpandRanodmChain: selectedMSP is null");
    
    placeAndExpandRandomChain(selectedMSP, doExpand);
}

function placeAndExpandRandomChain(msp, doExpand) {
    if (!selectedEdge) {
        return;
    }
    if (!isVertexOnEdge(selectedEdge[0], msp) 
            || !isVertexOnEdge(selectedEdge[1], msp)) {
        return;
    }
    
    var sideLength = msp.sideLength;
    
    var v1 = selectedEdge[0];
    var v2 = selectedEdge[1];
    var a1 = getAngle(v1, v2);
    var a2 = getAngle(v2, v1);
    
    // find which vertex to build from (rhombi are constructed counterclockwise)
    var t1a = deg2rad(a1+deltaAngle);
    var t2a = deg2rad(a2+deltaAngle);
    
    var vt1 = new Vertex(
            v1.x + sideLength*Math.cos(t1a), 
            v1.y - sideLength*Math.sin(t1a));
    var vt2 = new Vertex(
            v2.x + sideLength*Math.cos(t2a), 
            v2.y - sideLength*Math.sin(t2a));
            
    var v; // the vertex to build first rhombus from
    var startAngle;
    if (getRhombusAtCoords(msp.rhombi, vt1) === null) {
        v = v1;
        startAngle = a1;
    }
    if (getRhombusAtCoords(msp.rhombi, vt2) === null) {
        assert(!v, "placeAndExpandRandomChain 1");
        v = v2;
        startAngle = a2;
    }
    assert(v, "placeAndExpandRandomChain 2");
    
    var flowerM = parseInt(flowerMField.value);
    
    assert(flowerM > 0, "placeAndExpandRandomChain: flowerM <= 0");
    
    var angles = buildInitialAngles(flowerM);
    var chain = buildInitialChainGeneralized(v, startAngle, angles, sideLength);
    
    colorRhombi(chain);
    msp.rhombi = msp.rhombi.concat(chain);
    msp.polys = msp.rhombi;
    
    if (doExpand) {
        var holes = findFlowerHole(chain[0], msp, selectedEdge, msp.rhombi);
        expandFromAddedChain(holes, chain, msp);
    }
    
    msp.chains = buildChains(msp.rhombi);
    msp.needDiag = true;
    
    updateBoundingRect(msp);
    
    repaint();
}

function buildInitialChainGeneralized(v, startAngle, angles, sideLength) {
    var chain = [];
    for (var i=0; i<angles.length; i++) {
        var r = buildFlowerSkinny(v, angles[i], startAngle, sideLength);
        chain.push(r);
        v = r.vs[3];
    }
    return chain;
}


function expandFromAddedChain(holes, chain, msp) {
    var nIter = 0;
    
    // holes within the newly added chain
    for (var i=0; i<chain.length-1; i++) {
        var hole = new Hole(chain[i], chain[i+1], chain);
        assert(hole.vs !== null, "expandFromAddedChain 1");
        holes.push(hole);
    }
    
    while (holes.length > 0) {
        var hole = holes.shift();
        fillHole(hole, msp.rhombi);
        
        var newestRhombus = msp.rhombi[msp.rhombi.length-1];
        
        // need to color here since we are adding to an existing MSP
        colorRhombi([newestRhombus]);
        
        // cull any holes partially filled by newly placed rhombus
        holes = cullHoles(holes, newestRhombus);
        
        // add any new holes created by newly placed rhombus
        addHoles(holes, newestRhombus, msp.rhombi);
        
        assert((nIter++) < maxExpandIter, "expandFromAddedChain 2");
    }
}
