/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function testAngle(msp, v, sl, angle) {
    var ta = deg2rad(angle) + Math.PI/2;
    var tx = v.x + sl*Math.cos(ta);
    var ty = v.y - sl*Math.sin(ta);
    var tv = new Vertex(tx, ty);
    return getRhombusAtCoords(msp.rhombi, tv);
}

function placeFlowerAtVertex(v, msp, flowerInfo) {
    var flowerM = Number.parseInt(flowerMField.value);
    assert(flowerM >= 3, "placeFlowerAtVertex 2");
    
    var candvs = getVerticesOneSideLengthFromVertex(v, msp);
    var vs = [];
    for (var i=0; i<candvs.length; i++) {
        if (isVertexOnEdge(candvs[i], msp)) {
            vs.push(candvs[i]);
        }
    }
    assert(vs.length === 2, "placeFlowerAtVertex 1");
    var a0 = getAngle(v, vs[0]);
    var a1 = getAngle(v, vs[1]);
    if (a0 > a1) {
        var temp = a0;
        a0 = a1;
        a1 = temp;
    }
    var startAngle, endAngle;
    // we are on a colinear triplet
    // need to decide on which side to place flower
    if (approxEqual(Math.abs(a1-a0), 180)) {
        var sl = distance(v, vs[0]);
        var r = testAngle(msp, v, sl, a0);
        if (r === null) {
            r = testAngle(msp, v, sl, a1);
            assert(r, "placeFlowerAtVertex: no rhombi on either side of line");
            startAngle = a0;
            endAngle = a1;
        } else {
            startAngle = a1;
            endAngle = a0+360;
        }
    } else if (a1-a0 > 180) {
        startAngle = a0;
        endAngle = a1;
    } else {
        startAngle = a1;
        endAngle = a0+360;
    }
    var rAngle = 180/flowerM;
    var rs = [];
    while ((startAngle+rAngle) < endAngle) {
        var r = buildFlowerSkinny(v, rAngle, startAngle, msp.sideLength);
        startAngle += rAngle;
        rs.push(r);
    }
    flowerInfo.startAngle = startAngle - rAngle;
    flowerInfo.remainAngle = endAngle - startAngle;
    flowerInfo.vs = vs;
    return rs;
}

function placeAndExpandFlower(msp) {
    if (!msp) {
        return;
    }
    if (msp.polys !== msp.rhombi) {
        return;
    }
    if (!selectedVertex) {
        return;
    }
    if (!isVertexOnEdge(selectedVertex, msp)) {
        return;
    }
    
    var flowerInfo = {};
    var rs = placeFlowerAtVertex(selectedVertex, msp, flowerInfo);
    
    colorRhombi(rs);
    var origRhombi = msp.rhombi.concat([]);
    msp.rhombi = msp.rhombi.concat(rs);
    msp.polys = msp.rhombi;
    
    // find rhombus that makes hole with first flower rhombus
    var firstHole = 
            findFlowerHole(rs[0], msp, flowerInfo.vs, origRhombi)[0];
    
    assert(firstHole !== null, "placeAndExpandFlower 1");
    
    // find rhombus that makes hole with last flower rhombus
    var lastHole = 
            findFlowerHole(rs[rs.length-1], msp, 
                    [selectedVertex, flowerInfo.vs[0], flowerInfo.vs[1]],
                    origRhombi)[0];
                    
    assert(lastHole !== null, "placeAndExpandFlower 2");
    
    expandFromAddedFlower(msp, rs, firstHole, lastHole);
    
    msp.chains = buildChains(msp.rhombi);
    msp.needDiag = true;
    
    updateBoundingRect(msp);
    
    selectedVertex = null;
    
    repaint();
}


function expandFromAddedFlower(msp, rs, firstHole, lastHole) {
    var holes = [firstHole, lastHole];
    var nIter = 0;
    
    // holes in initial flower pattern
    for (var i=0; i<rs.length-1; i++) {
        var hole = new Hole(rs[i], rs[i+1], rs);
        assert(hole.vs !== null, "expandFromAddedFlower 1");
        holes.push(hole);
    }
    
    while (holes.length > 0) {
        var hole = holes.shift();
//        if (!hole.vs) {
//            hole.rs[0].color = 'green';
//            hole.rs[1].color = 'green';
//            repaintRhombus(hole.rs[0]);
//            repaintRhombus(hole.rs[1]);
//            throw "Error";
//        }
        fillHole(hole, msp.rhombi);
        
        var newestRhombus = msp.rhombi[msp.rhombi.length-1];
        
        // need to color here since we are adding to an existing MSP
        colorRhombi([newestRhombus]);
        
        // cull any holes partially filled by newly placed rhombus
        holes = cullHoles(holes, newestRhombus);
        
        // add any new holes created by newly placed rhombus
        addHoles(holes, newestRhombus, msp.rhombi);
        
        assert((nIter++) < maxExpandIter, "expandFromAddedFlower 2");
    }
}