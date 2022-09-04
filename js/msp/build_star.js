/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function buildStarMSP(m, sideLength) {
    
    if (!selectedVertex) {
        selectedVertex = new Vertex(canvas.width/2, canvas.height/2);
    }
    
    // construct the initial star pattern
    var rhombi = buildInitialStar(selectedVertex, m, sideLength);
    
    // expand the star pattern
    expandFromInitialStarPattern(rhombi);
    
    // build chains
    var chains = buildChains(rhombi);
    
    // color the rhombi
    colorRhombi(rhombi);
    
    // combine into msp object and add to MSP array
    return new MSP(rhombi, chains, selectedVertex, m, sideLength);

    repaint();
}

function buildInitialStar(v, m, sideLength) {
    var msp = Array();
    var rAngle = 360/m;
    var totalAngle = 0;
    for (var i=0; i<m; i++) {
        var r = buildRhombusFromTwoAngles(v, rAngle, totalAngle, sideLength);
        totalAngle += rAngle;
        msp.push(r);
    }
    return msp;
}

function expandFromInitialStarPattern(rs) {
    var holes = Array();
    var nIter = 0;
    // holes in the initial star pattern
    for (var i=0; i<rs.length-1; i++) {
        var hole = new Hole(rs[i], rs[i+1], rs);
        assert(hole.vs !== null, "expandFromInitialStarPattern 1");
        holes.push(hole);
    }
    
    // hole between initial and final
    var hole = new Hole(rs[rs.length-1], rs[0], rs);
    holes.push(hole);
    
    while (holes.length > 0) {
        var hole = holes.shift();
        fillHole(hole, rs);
        
        var newestRhombus = rs[rs.length-1];
        
        // cull any holes partially filled by newly placed rhombus
        holes = cullHoles(holes, newestRhombus);
        
        // add any new holes created by newly placed rhombus
        addHoles(holes, newestRhombus, rs);
        
        assert((nIter++) < maxExpandIter, "expandFromInitialStarPattern 2");
    }
}
