/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


// are two rhombi neighbors?
function areNeighbors(r1, r2, chains) {
//    var clen = chains[0].length;
    var r1idx = Array();
    var r2idx = Array();
    for (var i=0; i<chains.length; i++) {
        for (var j=0; j<chains[i].length; j++) {
            var r = chains[i][j];
            if (r === r1) {
                r1idx.push([i,j]);
            }
            if (r === r2) {
                r2idx.push([i,j]);
            }
        }
    }
    if (r1idx.length !== 2 || r2idx.length !== 2) {
        console.log("r1.idx: " + r1.idx + " r2.idx: " + r2.idx);
        logChains(chains);
    }
    assert(r1idx.length === 2, "areNeighbors 1");
    assert(r2idx.length === 2, "areNeighbors 2");
    for (var i=0; i<2; i++) {
        for (var j=0; j<2; j++) {
            if (r1idx[i][0] === r2idx[j][0]) {
                if (r1idx[i][1] === r2idx[j][1]-1 
                        || r1idx[i][1] === r2idx[j][1]+1) {
                    return true;
                }
            }
        }
    }
    return false;
}

// find triplets from a candidate rhombus and the chains of the MSP
function findPossibleFlips(r, chains) {
    // find the indices of the candidate rhombus in its two chains
    var idx1 = null;
    var idx2 = null;
    for (var i=0; i<chains.length; i++) {
        for (var j=0; j<chains[i].length; j++) {
            if (r === chains[i][j]) {
                if (idx1 === null) {
                    idx1 = [i,j];
                } else {
                    assert(idx2 === null, "findPossibleFlips 1");
                    idx2 = [i,j];
                }
            }
        }
    }
    assert(idx2 !== null, "findPossibleFlips 2");
    // find neighbors of candidate rhombus
    var ns = Array();
    if (idx1[1] !== 0) {
        ns.push(chains[idx1[0]][idx1[1]-1]);
    }
    if (idx1[1] !== chains[idx1[0]].length-1) {
        ns.push(chains[idx1[0]][idx1[1]+1]);
    }
    if (idx2[1] !== 0) {
        ns.push(chains[idx2[0]][idx2[1]-1]);
    }
    if (idx2[1] !== chains[idx2[0]].length-1) {
        ns.push(chains[idx2[0]][idx2[1]+1]);
    }
    // find which neighbors of candidate rhombus are also neighbors with each other
    // this determines a possible flip
    var flips = Array();
    for (var i=0; i<ns.length; i++) {
        for (var j=i+1; j<ns.length; j++) {
            if (areNeighbors(ns[i], ns[j], chains)) {
                flips.push([r, ns[i], ns[j]]);
            }
        }
    }
    assert(flips.length <= 2, "findPossibleFlips 3");
    return flips;
}

// get all possible flips in an msp, each flip triplet occuring once
function findAllPossibleFlips(msp, chains) {
    var flips = Array();
    for (var i=0; i<msp.length; i++) {
        var rhombusFlips = findPossibleFlips(msp[i], chains);
        flips = flips.concat(rhombusFlips);
    }
    sortFlips(flips);
    flips = cullRedundantFlips(flips);
    return flips;
}

function sortFlips(flips) {
    for (var i=0; i<flips.length; i++) {
        assert(flips[i].length === 3, "sortFlips 1");
        flips[i].sort(function(a,b) {return a.idx - b.idx;});
    }
}

// checks whether two flips are equivalent (assumes flips sorted by rhombus idx)
function equivFlips(f1, f2) {
    assert(f1.length === 3);
    assert(f2.length === 3);
    return f1[0].idx === f2[0].idx 
            && f1[1].idx === f2[1].idx 
            && f1[2].idx === f2[2].idx;
}

// checks whether the flip exists in the array of flips
// all flips are assumed to be sorted by rhombus idx
function flipInArray(flip, arr) {
    for (var i=0; i<arr.length; i++) {
        if (equivFlips(flip, arr[i])) {
            return true;
        }
    }
    return false;
}

// gets rid of redundant flips (assumes flips are sorted by rhombus idx)
function cullRedundantFlips(flips) {
    var newFlips = Array();
    for (var i=0; i<flips.length; i++) {
        var redundant = false;
        for (var j=0; j<newFlips.length; j++) {
            if (equivFlips(flips[i], newFlips[j])) {
                redundant = true;
                break;
            }
        }
        if (!redundant) {
            newFlips.push(flips[i]);
        }
    }
    return newFlips;
} 

function logFlips(flips) {
    var str = "Flips:\n";
    for (var i=0; i<flips.length; i++) {
        str += "" + flips[i][0].idx + "," 
                + flips[i][1].idx + "," + flips[i][2].idx + "\n";
    }
    console.log(str);
}

// carry out a flip
// a flip is represented by an array of three rhombi
function doFlip(tri, chains) {
    assert(tri.length === 3, "doFlip 1");
    var center = null;
    var move1 = null;
    var move2 = null;
    var move3 = null;
    var vs1 = tri[0].vs;
    var vs2 = tri[1].vs;
    var vs3 = tri[2].vs;
    findCenterLoop:
    for (var i=0; i<4; i++) {
        for (var j=0; j<4; j++) {
            for (var k=0; k<4; k++) {
                if (equivVertices(vs1[i], vs2[j])
                        && equivVertices(vs1[i], vs3[k])) {
                    center = copyVertex(vs1[i]);
                    break findCenterLoop;
                } 
            }
        }
    }
    assert(center !== null, "doFlip 2");
    findMove3Loop:
    for (var i=0; i<4; i++) {
        for (var j=0; j<4; j++) {
            if (equivVertices(vs1[i], vs2[j]) 
                    && !equivVertices(vs1[i], center)) {
                move3 = copyVertex(vs1[i]);
                break findMove3Loop;
            }
        }
    }
    assert(move3 !== null, "doFlip 3");
    findMove2Loop:
    for (var i=0; i<4; i++) {
        for (var j=0; j<4; j++) {
            if (equivVertices(vs1[i], vs3[j]) 
                    && !equivVertices(vs1[i], center)) {
                move2 = copyVertex(vs1[i]);
                break findMove2Loop;
            }
        }
    }
    assert(move2 !== null, "doFlip 4");
    findMove1Loop:
    for (var i=0; i<4; i++) {
        for (var j=0; j<4; j++) {
            if (equivVertices(vs2[i], vs3[j]) 
                    && !equivVertices(vs2[i], center)) {
                move1 = copyVertex(vs2[i]);
                break findMove1Loop;
            }
        }
    }
    assert(move1 !== null, "doFlip 5");
    // perform the move
    moveRhombus(tri[0], move1.x-center.x, move1.y-center.y);
    moveRhombus(tri[1], move2.x-center.x, move2.y-center.y);
    moveRhombus(tri[2], move3.x-center.x, move3.y-center.y);
    // update the chains
    for (var i=0; i<chains.length; i++) {
        var chain = chains[i];
        var i0 = chain.indexOf(tri[0]);
        var i1 = chain.indexOf(tri[1]);
        var i2 = chain.indexOf(tri[2]);
        var r1, r2;
        if (i0 !== -1 && i1 !== -1) {
            r1 = chain[i0];
            r2 = chain[i1];
            chain[i0] = r2;
            chain[i1] = r1;
        }else if (i1 !== -1 && i2 !== -1) {
            r1 = chain[i1];
            r2 = chain[i2];
            chain[i1] = r2;
            chain[i2] = r1;
        }else if (i0 !== -1 && i2 !== -1) {
            r1 = chain[i0];
            r2 = chain[i2];
            chain[i0] = r2;
            chain[i2] = r1;
        }
    }

}
