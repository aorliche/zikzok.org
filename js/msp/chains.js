/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function copyChains(chains) {
    var copy = Array();
    for (var i=0; i<chains.length; i++) {
        copy.push(chains[i].slice());
    }
    return copy;
}

function NeighboringRhombi(r, v1, v2) {
    this.r = r;
    this.m = getSlope(v1, v2);
}

function getTwoCommonVertices(r1, r2) {
    var vs = Array();
    for (var i=0; i<4; i++) {
        for (var j=0; j<4; j++) {
            if (equivVertices(r1.vs[i], r2.vs[j])) {
                vs.push(r1.vs[i]);;
            }
        }
    }
    if (vs.length === 2) {
        return vs;
    }
    assert(vs.length <= 2, "getTwoCommonVertices");
    return null;
}

function findNeighborsWithCommonVertices(r, rhombi) {
    var ns = Array();
    for (var i=0; i<rhombi.length; i++) {
        if (r === rhombi[i]) {
            continue;
        }
        var vs = getTwoCommonVertices(r, rhombi[i]);
        if (vs !== null) {
            ns.push(new NeighboringRhombi(rhombi[i], vs[0], vs[1]));
        }
    }
    assert(ns.length <= 4, "findNeighborsWithCommonVertices");
    return ns;
}

function buildChainUp(nsUp, chain, rhombi) {
    var prevRhombus = chain[chain.length-1];
    var ns = findNeighborsWithCommonVertices(nsUp.r, rhombi);
    
    // safety for multi msp
    if (chain.length > 100) {
        return;
    }
    
    chain.push(nsUp.r);
    
    for (var i=0; i<ns.length; i++) {
        if (ns[i].r === prevRhombus) {
            continue;
        }
        if (equivSlopes(ns[i].m, nsUp.m)) {
            buildChainUp(ns[i], chain, rhombi);
        }
    }
}

function buildChainDown(nsDown, chain, rhombi) {
    var prevRhombus = chain[0];
    var ns = findNeighborsWithCommonVertices(nsDown.r, rhombi);
    
     // safety for multi msp
    if (chain.length > 100) {
        return;
    }
    
    chain.unshift(nsDown.r);
    
    for (var i=0; i<ns.length; i++) {
        if (ns[i].r === prevRhombus) {
            continue;
        }
        if (equivSlopes(ns[i].m, nsDown.m)) {
            buildChainDown(ns[i], chain, rhombi);
        }
    }
}

// check whether the rhombi in ns (neighbors array)
// are already part of a chain
function neighborsExistInChains(chains, ns) {
    outer:
    for (var i=0; i<chains.length; i++) {
        var c = chains[i];
        for (var j=0; j<c.length; j++) {
            // check forwards
            if (c[j] === ns[0]) {
                if (j+ns.length > c.length) {
                    continue outer;
                }
                for (var k=1; k<ns.length; k++) {
                    if (c[j+k] !== ns[k]) {
                        continue outer;
                    }
                }
                return true;
            } 
            // check backwards
            if (c[j] === ns[ns.length-1]) {
                if (j+ns.length > c.length) {
                    continue outer;
                }
                for (var k=1; k<ns.length; k++) {
                    if (c[j+k] !== ns[ns.length-k-1]) {
                        continue outer;
                    }
                }
                return true;
            } 
        }
    }
    return false;
}

function rhombusExistsInTwoChains(chains, r) {
    var n = 0;
    for (var i=0; i<chains.length; i++) {
        var c = chains[i];
        for (var j=0; j<c.length; j++) {
            if (c[j] === r) {
                n++;
            }
        }
    }
    assert(n <= 2, "rhombusExistsInTwoChains: n > 2");
    return n === 2;
}

// generalized chain building for any msp
// including msps in which some of the rhombi, but not all, have 
// been split into smaller rhombi
function buildChains(rhombi) {
    assert(rhombi.length > 0, "buildChains: rhombi.length <= 0");
    var chains = [];
    buildChainsWorker(rhombi[0], rhombi, chains);
    return chains;
}

// build the two chains that r is part of
function buildChainsWorker(r, rhombi, chains) {
    assert(!rhombusExistsInTwoChains(chains, r), 
            "buildChainsWorker: r already in two chains");
    var ns = findNeighborsWithCommonVertices(r, rhombi);
    // single rhombus msp
    if (ns.length === 0) {
        chains.push([r]);
        chains.push([r]);
        return;
    }
    var nsA = [ns[0]];
    var nsB = [];
    assert(ns.length >= 1, "buildChainsWorker: ns.length < 2");
    var m = ns[0].m;
    for (var i=1; i<ns.length; i++) {
        if (equivSlopes(ns[i].m, m)) {
            nsA.push(ns[i]);
        } else {
            nsB.push(ns[i]);
        }
    }
    assert(nsA.length <= 2, "buildChainsWorker: nsA.length > 2");
    assert(nsB.length <= 2, "buildChainsWorker: nsB.length > 2"); 
    if (nsA.length > 0) {
        var seqA = (nsA.length === 2) ? [nsA[0].r, r, nsA[1].r] : [nsA[0].r, r]; 
        if (!neighborsExistInChains(chains, seqA)) {
            var c = [r];
            chains.push(c);
            buildChainDown(nsA[0], c, rhombi);
            if (nsA.length === 2) {
                buildChainUp(nsA[1], c, rhombi);
            }
        }
    } else {
        chains.push([r]);
    }
    if (nsB.length > 0) {
        var seqB = (nsB.length === 2) ? [nsB[0].r, r, nsB[1].r] : [nsB[0].r, r]; 
        if (!neighborsExistInChains(chains, seqB)) {
            var c = [r];
            chains.push(c);
            buildChainDown(nsB[0], c, rhombi);
            if (nsB.length === 2) {
                buildChainUp(nsB[1], c, rhombi);
            }
        }
    } else {
        chains.push([r]);
    }
    buildChainsFiller(rhombi, chains);
}

// select one rhombus not in two chains and build chains from it
function buildChainsFiller(msp, chains) {
    for (var i=0; i<msp.length; i++) {
        var r = msp[i];
        if (!rhombusExistsInTwoChains(chains, r)) {
            buildChainsWorker(r, msp, chains);
            return;
        }
    }
}

function logChains(chains) {
    assert(chains.length > 2, "logChains 1");
    var str = "";
    for (var i=0; i<chains.length; i++) {
        str += "chain " + i + ": " + chains[i][0].idx;
        for (var j=1; j<chains[i].length; j++) {
            str += "," + chains[i][j].idx;
        }
        str += "\n";
    }
    console.log(str);
}

function chainsToBasicString(chains) {
    var str = "";
    for (var i=0; i<chains.length; i++) {
        for (var j=0; j<chains[i].length; j++) {
            if (j === 0) {
                str += chains[i][j];
            } else {
                str += "," + chains[i][j];
            }
        }
        str += "\n";
    }
    return str;
}

function logBasicChains(chains) {
    console.log(chainsToBasicString(chains));
}
