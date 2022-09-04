/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function buildInitialFlower(v, m, sideLength) {
    var rs = [];
    var rAngle = 180/m;
    var totalAngle = 0;
    
    for (var i=0; i<m-1; i++) {
        var r = buildFlowerSkinny(v, rAngle, totalAngle, sideLength);
        totalAngle += rAngle;
        rs.push(r);
    }
    return rs;
}

function buildFlowerMSP(m, sideLength) {
    
    if (!selectedVertex) {
        selectedVertex = new Vertex(canvas.width/2, canvas.height/2);
    }

     // center the MSP
    var v = findStartingVertex(m, sideLength, selectedVertex);
    
    // build the initial flower pattern
    var rhombi = buildInitialFlower(v, m, sideLength);
    
    // expand the initial flower pattern
    expandFromInitialFlower(rhombi);
    
    // build chains
    var chains = buildChains(rhombi);
    
    // color the rhombi
    colorRhombi(rhombi);
    
    // combine into msp object and add to MSP array
    return new MSP(rhombi, chains, selectedVertex, m, sideLength);
    
    repaint();
}

function expandFromInitialFlower(msp) {
    var holes = [];
    var nIter = 0;
    for (var i=0; i<msp.length-1; i++) {
        holes.push(new Hole(msp[i], msp[i+1], msp));
    }
    
    while (holes.length > 0) {
        var hole = holes.shift();
        fillHole(hole, msp);
        
        var newestRhombus = msp[msp.length-1];
        
        // cull any holes partially filled by newly placed rhombus
        holes = cullHoles(holes, newestRhombus);
        
        // add any new holes created by newly placed rhombus
        addHoles(holes, newestRhombus, msp);
        
        assert((nIter++) < maxExpandIter, "expandFromIntialFlower");
    }
}
