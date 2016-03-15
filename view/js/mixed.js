/**
 * Created by david on 3/15/16.
 */

/**
 * Generate Button
 */


toggleList = function(){
    $("#table-container").toggle(300);
}

$(document).on('click','#generate_nw',function() {

    var multiply = parseFloat( ($('#nutrient_snd_amount').val()).replace(",",".")) || 0;
    multiply = multiply / 100.0;

    var nutnames = ["energy","fat_total","fat_saturated","protein","fibers","calcium","carb","sugar","salt","lactose","natrium","bread_unit"];

    for(var i = 0;i<nutnames.length;i++) {

        var origval = $('#nutrient_100_'+nutnames[i]).val().replace(",",".");

        if(isNaN(origval) || origval==="") {
            $('#nutrient_snd_'+nutnames[i]).val("");
        } else {
            $('#nutrient_snd_'+nutnames[i]).val( ( parseFloat((  origval * multiply).toFixed(3))).toString().replace(".",",") );
        }
    }

});


function isNormalInteger(str) {
    var n = ~~Number(str);
    return String(n) === str && n >= 0;
}

$(document).on('click','#show_status_info',function(e) {
    e.preventDefault();
    alert('Der Bearbeitungsstatus eines Produkts wird kurz durch eine Zahl dargestellt.\n\n0 = neu\n5 = bereits bearbeitet\n6 = bereits bearbeitet mit Anmerkungen\n7 = wird später bearbeitet\n8 = Bearbeitung problematisch\n10 = Bearbeitung abgeschlossen\n11=Bearbeitung abgeschlossen mit Anmerkungen\n15 = Produkt bereits exportiert\nAndere Zahl = Sonderstatus, vom Administrator festgelegt');
});