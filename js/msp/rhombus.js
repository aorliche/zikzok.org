/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var rIdx = 0;

function Rhombus(vs, idx) {
    this.vs = vs;
    this.idx = idx;
    
    this.center = new Vertex(
            (this.vs[0].x+this.vs[2].x)/2, 
            (this.vs[0].y+this.vs[2].y)/2);
}

// for building rhombi in a flower pattern at the initial vertex
function buildFlowerSkinny(v, angle, startAngle, sideLength) {
    var vs = [];
    
    angle = deg2rad(angle);
    startAngle = deg2rad(startAngle);
    
    vs[0] = copyVertex(v);
    
    vs[1] = new Vertex(
            v.x + sideLength*Math.cos(startAngle), 
            v.y - sideLength*Math.sin(startAngle));

    var hyp = 2*Math.cos(angle/2)*sideLength;
    var theta = angle/2 + startAngle;
    
    vs[2] = new Vertex(
            v.x + hyp*Math.cos(theta), 
            v.y - hyp*Math.sin(theta));

    vs[3] = new Vertex(
            v.x + sideLength*Math.cos(startAngle + angle), 
            v.y - sideLength*Math.sin(startAngle + angle));
            
    return new Rhombus(vs, rIdx++);
}

// for building rhombi in a random chain
function buildRhombusFromAngle(v, angle, sideLength) {
    var vs = Array();
    
    vs[0] = copyVertex(v);
    vs[1] = new Vertex(v.x+sideLength, v.y);
    
    var x = v.x + sideLength*Math.cos(angle*Math.PI/180);
    var y = v.y - sideLength*Math.sin(angle*Math.PI/180);
    
    vs[3] = new Vertex(x, y);
    vs[2] = new Vertex(x+sideLength, y);
    
    return new Rhombus(vs, rIdx++);
}

// for building rhombi in a star pattern
function buildRhombusFromTwoAngles(v, rAngle, startAngle, sideLength) {
    var vs = Array();
    
    // be careful because adjacent vertices define sides of rhombus
    // so not just any order works
    vs[1] = copyVertex(v);
    vs[0] = new Vertex(
            v.x + sideLength*Math.cos(Math.PI*startAngle/180),
            v.y - sideLength*Math.sin(Math.PI*startAngle/180));
            
    startAngle += rAngle;
    
    vs[2] = new Vertex(
            v.x + sideLength*Math.cos(Math.PI*startAngle/180),
            v.y - sideLength*Math.sin(Math.PI*startAngle/180));
    
    return buildRhombusFromThreeVs(vs);
}

function buildRhombusFromThreeVs(cvs) {
    var vs = [copyVertex(cvs[0]), copyVertex(cvs[1]), copyVertex(cvs[2])];
    
    var cx = (cvs[0].x + cvs[2].x)/2;
    var cy = (cvs[0].y + cvs[2].y)/2;
    
    var x = (2*cx - cvs[1].x);
    var y = (2*cy - cvs[1].y);
    
    vs.push(new Vertex(x, y));
    
    return new Rhombus(vs, rIdx++);
}

function contains(vs, point) {
    // ray-casting algorithm based on
    // http://www.ecse.rpi.edu/Homepages/wrf/Research/Short_Notes/pnpoly.html

    var x = point.x, y = point.y;

    var inside = false;
    for (var i = 0, j = vs.length - 1; i < vs.length; j = i++) {
        var xi = vs[i].x, yi = vs[i].y;
        var xj = vs[j].x, yj = vs[j].y;

        var intersect = ((yi > y) !== (yj > y))
            && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
        if (intersect) inside = !inside;
    }

    return inside;
}

function moveRhombus(r, dx, dy) {
    for (var i=0; i<r.vs.length; i++) {
        r.vs[i].x += dx;
        r.vs[i].y += dy;
    }
    r.center.x += dx;
    r.center.y += dy;
};