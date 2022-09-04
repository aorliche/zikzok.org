/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function convertToElementCoords(e) {
    var rect = e.target.getBoundingClientRect();
    return {x: e.clientX-rect.left, y: e.clientY-rect.top};
}

//https://stackoverflow.com/questions/6234773/can-i-escape-html-special-chars-in-javascript
function escapeHtml(unsafe) {
    return unsafe
         .replace(/&/g, "&amp;")
         .replace(/</g, "&lt;")
         .replace(/>/g, "&gt;")
         .replace(/"/g, "&quot;")
         .replace(/'/g, "&#039;");
 }

/**
 * Fisher-Yates shuffle algorithm
 * https://stackoverflow.com/questions/6274339/how-can-i-shuffle-an-array
 * Shuffles array in place.
 * @param {Array} a items An array containing the items.
 */
/*function shuffle(a) {
    var j, x, i;
    for (i = a.length - 1; i > 0; i--) {
        j = Math.floor(Math.random() * (i + 1));
        x = a[i];
        a[i] = a[j];
        a[j] = x;
    }
    return a;
}*/
function shuffle(a) {
    for (let i=0; i<a.length; i++) {
         let j = Math.floor(Math.random() * a.length);
         let temp = a[i];
         a[i] = a[j];
         a[j] = temp;
    }
}

function assert(exp, message) {
    if (!exp) {
        throw "Assertion failed: " + message;
    }
}

function approxEqual(n1, n2) {
    if (isNaN(n1) || isNaN(n2)) {
        return false;
    }
    if (n1 === Number.POSITIVE_INFINITY && n2 === Number.POSITIVE_INFINITY) {
        return true;
    }
    if (n1 === Number.NEGATIVE_INFINITY && n2 === Number.NEGATIVE_INFINITY) {
        return true;
    }
    return Math.abs(n1-n2) < 1e-2;
}

function deg2rad(deg) {
    return Math.PI*deg/180;
}

function rad2deg(rad) {
    return 180*rad/Math.PI;
}

// get the angle between v1 and v2
// note dy is v1-v2 but dx is v2-v1
// this flips unit circle about x axis
function getAngle(v1, v2) {
    var angle = rad2deg(Math.atan2(v1.y-v2.y, v2.x-v1.x));
    if (angle < 0) {
        angle += 360;
    }
    return angle;
}

// get the angle between v1 and v2 in radians
function getAngle2(v1, v2) {
    var angle = Math.atan2(v1.y-v2.y, v1.x-v2.x);
    if (angle < 0) {
        angle += 2*Math.PI;
    }
    return angle;
}

function arrayIntersection(a1, a2) {
    var common = [];
    var commonUnique = [];
    for (var i=0; i<a1.length; i++) {
        for (var j=0; j<a2.length; j++) {
            if (a1[i] === a2[j]) {
                common.push(a1[i]);
            }
        }
    }
    for (var i=0; i<common.length; i++) {
        var found = false;
        for (var j=0; j<commonUnique.length; j++) {
            if (commonUnique[j] === common[i]) {
                found = true;
                break;
            }
        }
        if (!found) {
            commonUnique.push(common[i]);
        }
    }
    return commonUnique;
}
