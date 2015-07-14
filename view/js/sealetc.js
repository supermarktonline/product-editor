
// add a new sealetc
$(document).on('click','#seal_adder',function() {
    var new_seal = $('#seal_new').val();

    var seal = {
        name: new_seal
    };

    // create ingredient
     $.ajax({ type:"POST", url: "/?sealetc=create", data:seal, success: function(result){

             var nSeal = JSON.parse(result);

             if(!nSeal["id"]) {
                 $('#message_container').html('<div class="umsg error">'+nSeal["error"]+'</div>');
             } else {
                 seals.push(nSeal);
                 appendSeal(nSeal,true);
                 $('#message_container').html('<div class="umsg success">Gütesiegel (etc.) '+nSeal["name"]+' erfolgreich gespeichert.</div>');
                 $('#seal_new').val('');
             }
         }
     });
    
});


// remove a seal (only possible if not used)
$(document).on('click','#seal_remover',function() {
    var old_seal = $('#seal_remove').val();

    var seal = {
        name: old_seal
    };

    // create ingredient
     $.ajax({ type:"POST", url: "/?sealetc=delete", data:seal, success: function(result){
             
             if(result!=="success") {
                 var error = JSON.parse(result);
                $('#message_container').html('<div class="umsg error">'+error["error"]+'</div>');
                $('#seal_remove').val('');
             } else {
                 $('#message_container').html('<div class="umsg success">Gütesiegel (etc.) erfolgreich gelöscht.</div>');
                 removeSealByName(old_seal);
             }
         }
     });
    
});

function removeSealByName(name) {
    
    // remove from list
    var id = -1;
    
    for(var i = 0; i < seals.length; i++) {
        if(seals[i]["name"]==name) {
            id=seals[i]["id"];
            seals.splice(i,1);
        }
    }
    
    // remove from UI
    $('.gs[data-id="'+id+'"]').each(function() {
       $(this).remove(); 
    });
    
}


function initializeSeals(seals) {
    
    for(var i = 0; i < seals.length; i++) {
        appendSeal(seals[i]);
    }
}

function appendSeal(seal,checked) {
    checked  = checked || false;
    
    var dochk = "";
    if(checked) {
        dochk = 'checked="checked"';
    }
    
    var html = '<div class="gs" data-id="'+seal["id"]+'">';
    html += '<label><input type="checkbox" '+dochk+' class="seal" value="'+seal["id"]+'" />'+seal["name"]+'</label>';
    html += '</div>';

    $('#guetesiegel').append(html);
}