/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var vIdx = 0;

function Vertex(x, y) {
    this.x = x;
    this.y = y;
    this.idx = vIdx++;
}

function copyVertex(v) {
    return new Vertex(v.x, v.y);
}

function distance(v1, v2) {
    return Math.sqrt(Math.pow(v1.x-v2.x,2) + Math.pow(v1.y-v2.y,2));
}

function equivVertices(v1, v2) {
    if (distance(v1, v2) < 1e-4) {
        return true;
    }
    return false;
}

function oneSideLengthApart(v1, v2, msp) {
    return Math.abs(distance(v1, v2)-msp.sideLength) < 1e-4;
}

function getSlope(v1, v2) {
    assert(!equivVertices(v1, v2), "getSlope: vs are equivVertices");
    var m = (v1.y-v2.y)/(v1.x-v2.x);
    if (m > 100 || m < -100) {
        m = Number.POSITIVE_INFINITY;
    }
    return m;
}

function equivSlopes(m1, m2) {
    if (!Number.isFinite(m1) && !Number.isFinite(m2)) {
        return true;
    }
    return Math.abs(m1-m2) < 1e-2;
}

function colinear(v1, v2, v3) {
    var m1 = getSlope(v1, v2);
    var m2 = getSlope(v1, v3);
    return equivSlopes(m1, m2);
}

function vertexInArray(v, vs) {
    for (var i=0; i<vs.length; i++) {
        if (equivVertices(v, vs[i])) {
            return true;
        }
    }
    return false;
}

function clockwise(p, q, r) {
    var val =   (q.y - p.y) * (r.x - q.x) - 
                (q.x - p.x) * (r.y - q.y);
    return val < 0;
}