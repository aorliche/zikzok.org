/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function Hole(r1, r2, rhombi) {
    // find the one or two common vertices of the two rhombi
    var cv1 = null, cv2 = null;
    var cv1i = null, cv1j = null, cv2i = null, cv2j;
    this.idx = [r1.idx, r2.idx];
    this.rs = [r1, r2];
    for (var i=0; i<4; i++) {
        for (var j=0; j<4; j++) {
            if (equivVertices(r1.vs[i], r2.vs[j])) {
                if (cv1 === null) {
                    cv1 = r1.vs[i];
                    cv1i = i;
                    cv1j = j;
                } else {
                    cv2 = r1.vs[i];
                    cv2i = i;
                    cv2j = j;
                }
            }
        }
    }
    if (cv1 === null) {
        this.type = 0;
    } else if (cv2 === null) {
        this.type = 1;
        this.vs = findVsTypeOneHole(r1, r2, cv1, cv1i, cv1j, rhombi);
    } else {
        this.type = 2;
        this.vs = findVsTypeOneHole(r1, r2, cv1, cv1i, cv1j, rhombi);
        if (this.vs === null) {
            this.vs = findVsTypeOneHole(r1, r2, cv2, cv2i, cv2j, rhombi);
        }
    }
    if (this.vs) {
        var sl = distance(this.vs[0], this.vs[1]);
        var c = distance(this.vs[0], this.vs[2]);
        this.angle = Math.acos(1-((c*c)/(2*sl*sl)));
        assert(this.angle < Math.PI, "Hole: bad angle");
    }
}

var holeCheckDistance = 1e-1;

function checkCandidateVsOverlapRhombi(cv1, cv2, rhombi, r1, r2) {
    var Dx = cv1.x - cv2.x;
    var Dy = cv1.y - cv2.y;
    var hypot = Math.sqrt(Dx*Dx + Dy*Dy);
    var dx = Dx/hypot*holeCheckDistance;
    var dy = Dy/hypot*holeCheckDistance;
    var center1 = new Vertex(cv1.x-dx, cv1.y-dy);
    var center2 = new Vertex(cv2.x+dx, cv2.y+dy);
    if (contains(r1.vs, center1) || contains(r1.vs, center2)) {
        return false;
    }
    if (contains(r2.vs, center1) || contains(r2.vs, center2)) {
        return false;
    }
    for (var i=0; i<rhombi.length; i++) {
        var r = rhombi[i];
        if (contains(r.vs, center1) || contains(r.vs, center2)) {
            return false;
        }
    }
    return true;
}

function adjacentVertices(i,j) {
    var idxDist = Math.abs(i-j);
    return idxDist === 1 || idxDist === 3;
}

function findVsTypeOneHole(r1, r2, cv, cv1i, cv1j, rhombi) {
    var candvs1 = Array(), candvs2 = Array();
    // get the two candidate v's in rhombus 1, and the two candidate v's in 
    // rhombus 2
    for (var i=0; i<4; i++) {
        if (adjacentVertices(i, cv1i)) {
            candvs1.push(r1.vs[i]);
        }
    }
    for (var i=0; i<4; i++) {
        if (adjacentVertices(i, cv1j)) {
            candvs2.push(r2.vs[i]);
        }
    }
    if (!equivVertices(candvs1[0], candvs2[0]) 
            && checkCandidateVsOverlapRhombi(candvs1[0], candvs2[0], rhombi, r1, r2)
            && !colinear(candvs1[0], cv, candvs2[0])) {
        return [candvs1[0], cv, candvs2[0]];
    }
    if (!equivVertices(candvs1[1], candvs2[0]) 
            && checkCandidateVsOverlapRhombi(candvs1[1], candvs2[0], rhombi, r1, r2)
            && !colinear(candvs1[1], cv, candvs2[0])) {
        return [candvs1[1], cv, candvs2[0]];
    }
    if (!equivVertices(candvs1[0], candvs2[1]) 
            && checkCandidateVsOverlapRhombi(candvs1[0], candvs2[1], rhombi, r1, r2)
            && !colinear(candvs1[0], cv, candvs2[1])) {
        return [candvs1[0], cv, candvs2[1]];
    }
    if (!equivVertices(candvs1[1], candvs2[1]) 
            && checkCandidateVsOverlapRhombi(candvs1[1], candvs2[1], rhombi, r1, r2)
            && !colinear(candvs1[1], cv, candvs2[1])) {
        return [candvs1[1], cv, candvs2[1]];
    }
    return null;
}

// check that a newly placed rhombus does not completely or partially fill
// any existing holes
function cullHoles(holes, r) {
    var nholes = Array();
    for (var i=0; i<holes.length; i++) {
        var hole = holes[i];
        var Dx = hole.vs[0].x - hole.vs[2].x;
        var Dy = hole.vs[0].y - hole.vs[2].y;
        var hypot = Math.sqrt(Dx*Dx + Dy*Dy);
        var dx = Dx/hypot*holeCheckDistance;
        var dy = Dy/hypot*holeCheckDistance;
        var test0 = new Vertex(hole.vs[0].x-dx, hole.vs[0].y-dy);
        var test2 = new Vertex(hole.vs[2].x+dx, hole.vs[2].y+dy);
        if (contains(r.vs, test0) || contains(r.vs, test2)) {
            continue;
        }
        nholes.push(hole);
    }
    return nholes;
}

// add any new holes created by placing a rhombus
function addHoles(holes, r, rhombi) {
    for (var i=0; i<rhombi.length; i++) {
        if (r === rhombi[i]) {
            continue;
        }
        var candHole = new Hole(r, rhombi[i], rhombi);
        if (candHole.vs) {
            holes.push(candHole);
        }
    }
}

function fillHole(hole, msp) {
    var r = buildRhombusFromThreeVs(hole.vs);
    msp.push(r);
}

// msp plane - fill one hole with two rhombi
function fillHoleMulti(hole, msp) {
    // find point one side length away from central hole vertex
    // within the hole space
    
    // find side length
    var sl = distance(hole.vs[0], hole.vs[1]);
    
    // find angles of the two outer vertices of hole
    var t1 = getAngle2(hole.vs[0], hole.vs[1]);
    var t2 = getAngle2(hole.vs[2], hole.vs[1]);
    
    if (t1 > t2) {
        var temp = t1;
        t1 = t2;
        t2 = temp;
    }
    
    var startAngle;
    var dAngle;
    
    if ((t2 - t1) > Math.PI) {
        startAngle = t2;
        dAngle = 2*Math.PI+t1-t2;
    } else {
        startAngle = t1;
        dAngle = t2-t1;
    }
    
    // determine random point between these two angles
    // that is one side length from central point
    
    var depth = 0;
    var r1, r2;
    
    while (true) {
        var b = startAngle + Math.random()*dAngle;

        var x = hole.vs[1].x + sl*Math.cos(b);
        var y = hole.vs[1].y + sl*Math.sin(b);

        var v = new Vertex(x,y);

        // build the two rhombi where we should have had one
        var h1 = [hole.vs[0], hole.vs[1], v];
        var h2 = [v, hole.vs[1], hole.vs[2]];

        r1 = buildRhombusFromThreeVs(h1);
        r2 = buildRhombusFromThreeVs(h2);
        
        var again = false;

        // check whether we have overlapped an existing rhombus
        for (var i=0; i<msp.length; i++) {
            if (contains(msp[i].vs, r1.vs[3]) 
                    || contains(msp[i].vs, r2.vs[3])
                    || contains(msp[i].vs, v)) {
                again = true;
            }
        }
        
        if (!again) {
            break;
        }
        
        if (depth++ === 2) {
            console.log("depth exceeded");
            console.log(r1.vs[3]);
            console.log(r2.vs[3]);
            console.log(v);
            console.log(t1);
            console.log(t2);
            console.log(containsZero);
            //fillHole(hole, msp);
            for (var i=0; i<msp.length; i++) {
                colorRhombi([msp[i]], 'red', false, true, false);
                repaintRhombus(msp[i]);
            }
            
            colorRhombi([r1], 'green', 0.5, true, true);
            colorRhombi([r2], 'green', 0.5, true, true);
            repaintRhombus(r1);
            repaintRhombus(r2);
            throw 'bad';
            return 1;
        }
    }
    
    msp.push(r1);
    msp.push(r2);
    

    
    
//    throw 'bad';
    return 2;
}
