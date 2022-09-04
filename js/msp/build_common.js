/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// find the initial vertex of the first rhombus 
// if we are building from the bottom (as in random and flower MSPs)
function findStartingVertex(m, sideLength, center) {
    // radius of the inscribed circle,
    // not circumscribed
    var rad = findRadius(m, sideLength);
    var dx = 0.5*sideLength;
//    var dy = Math.sqrt((rad*rad)-(0.25*sideLength*sideLength));
    var dy = rad;
    return new Vertex(center.x-dx, center.y+dy);
}
