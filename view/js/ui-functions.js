/**
 * Created by david on 11/27/15.
 */
$(document).ready(function() {
    $('#table-container-wrapper').resizable({
        handles: 'n,s'
    });
});


// a + d + m to toggle admin area
var map = []; // Or you could call it "key"
onkeyup = onkeydown = function(e){
    e = e || event; // to deal with IE
    map[e.keyCode] = e.type == 'keydown';

    if(map[65]==true && map[68]==true && map[77] ==true) {
        $('.admin-area').toggle();
    }

}