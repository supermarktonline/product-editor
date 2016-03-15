/**
 * Created by david on 11/27/15.
 */

var is_admin = false;

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

        if(!is_admin) {
            $('.admin-area').show();
            $('.no-admin-area').hide();
            is_admin = true;
        } else {
            $('.admin-area').hide();
            $('.no-admin-area').show();
            is_admin = false;
        }
    }
}



$(document).on('change','#nutrient_unit',function() {
    $('#nutrient_unit_copy').html($('#nutrient_unit').val());
});