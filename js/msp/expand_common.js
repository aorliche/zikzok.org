/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function findFlowerHole(r, msp, vs, origRhombi) {
    var holes = [];
    var addedRhombi = [];
    for (var i=0; i<vs.length; i++) {
        var origMSP = {rhombi: origRhombi};
        var rs = findRhombiContainingVertex(vs[i], origMSP);
        for (var j=0; j<rs.length; j++) {
            if (rs[j] === r) {
                continue;
            }
            if (addedRhombi.includes(rs[j])) {
                continue;
            }
            if (!rhombiHaveVertexInCommon(r, rs[j], vs[i])) {
                continue;
            }
            var hole = new Hole(r, rs[j], msp.rhombi);
            if (hole.vs !== null) {
                holes.push(hole);
                addedRhombi.push(rs[j]);
            }
        }
    }
//    if (holes.length === 2) {
//        console.log(holes);
//        for (var i=0; i<vs.length; i++) {
//            var rs = findRhombiContainingVertex(vs[i], msp);
//            for (var j=0; j<rs.length; j++) {
//                if (rs[j] === r) {
//                    continue;
//                }
//                if (!rhombiHaveVertexInCommon(r, rs[j], vs[i])) {
//                    continue;
//                }
//                rs[j].color = 'green';
//                repaintRhombus(rs[j]);
//            }
//        }
//        r.color = 'red';
//        repaintRhombus(r);
//        throw 'bad';
//    }
//    assert(holes.length === 1, "findFlowerHoles: holes.length != 1");
    return holes;
}