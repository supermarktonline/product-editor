
// add a new tag
$(document).on('click','#tag_adder',function() {
    var new_tag = $('#tag_new').val();

    var tag = {
        name: new_tag
    };

    // create ingredient
     $.ajax({ type:"POST", url: "/?tag=create", data:tag, success: function(result){

             var nSeal = JSON.parse(result);

             if(!nSeal["id"]) {
                 $('#message_container').html('<div class="umsg error">'+nSeal["error"]+'</div>');
             } else {
                 tags.push(nSeal);
                 appendSeal(nSeal,true);
                 $('#message_container').html('<div class="umsg success">Gütesiegel (etc.) '+nSeal["name"]+' erfolgreich gespeichert.</div>');
                 $('#tag_new').val('');
             }
         }
     });
    
});


// remove a tag (only possible if not used)
$(document).on('click','#tag_remover',function() {
    var old_tag = $('#tag_remove').val();

    var tag = {
        name: old_tag
    };

    // create ingredient
     $.ajax({ type:"POST", url: "/?tag=delete", data:tag, success: function(result){
             
             if(result!=="success") {
                 var error = JSON.parse(result);
                $('#message_container').html('<div class="umsg error">'+error["error"]+'</div>');
                $('#tag_remove').val('');
             } else {
                 $('#message_container').html('<div class="umsg success">Gütesiegel (etc.) erfolgreich gelöscht.</div>');
                 removeSealByName(old_tag);
             }
         }
     });
    
});

function removeSealByName(name) {
    
    // remove from list
    var id = -1;
    
    for(var i = 0; i < tags.length; i++) {
        if(tags[i]["name"]==name) {
            id=tags[i]["id"];
            tags.splice(i,1);
        }
    }
    
    // remove from UI
    $('.gs[data-id="'+id+'"]').each(function() {
       $(this).remove(); 
    });
    
}


function initializeTags(tags) {
    
    for(var i = 0; i < tags.length; i++) {
        appendTag(tags[i]);
    }
}

function appendTag(tag,checked) {
    checked  = checked || false;
    
    var dochk = "";
    if(checked) {
        dochk = 'checked="checked"';
    }
    
    var html = '<div class="gs" data-id="'+tag["id"]+'">';
    html += '<label><input type="checkbox" '+dochk+' class="tag" value="'+tag["id"]+'" />'+tag["name_de"]+'</label>';
    html += '</div>';

    $('#guetesiegel').append(html);
}



/* Seal category highlighting */
$(document).on('click','#active_category_tag_update',function() {
   
    var ids = {};
    
    var tpids = [];
    
    $('.tag').each(function() {
        if($(this).is(":checked")) {
            tpids.push($(this).val());
        }
    });
    
    ids["ids"] = tpids;
    
    $.ajax({ type:"POST", url: "/?category_tag_connection=update&category_id="+$('#active_category').val(), data:ids, success: function(result){
       if(result==="success") {
           $('#message_container').html('<div class="umsg success">Category / tag configuration updated successfully.</div>');
           setGlobalCurrentCat($('#active_category').val());
       } else {
           $('#message_container').html('<div class="umsg error">'+result+'</div>');
       }
    }});
    
});