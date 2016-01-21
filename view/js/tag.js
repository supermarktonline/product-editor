
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



function initializeStandardTags(tags) {
    
    for(var i = 0; i < tags.length; i++) {
        appendTag(tags[i]);
    }
}

function initializeNumericalTags(tags) {
    for(var i = 0; i < tags.length; i++) {
        appendNumericalTag(tags[i]);
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

function appendNumericalTag(tag,value) {
    value = value || "";

    var html = '<div class="gs" data-id="'+tag["id"]+'">';

    var input = '<input size="4" type="text" class="numerical-tag" data-tagid="'+tag["id"]+'" value="'+value+'" />';

    var name = tag["name_de"];

    var prep = (name.replace("$",input)).replace("~",numerical_tags_map[tag["type"]]);


    html += '<label>'+prep+'</label>';
    html += '</div>';

    $('#tags_numerical').append(html);
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

    $('.numerical-tag').each(function() {
        var tv = ($(this).val()).trim();
        if(tv!="") {
            tpids.push($(this).attr('data-tagid'));
        }
    });

    ids["ids"] = tpids;
    
    $.ajax({ type:"POST", url: "/?category_tag_connection=update&category_id="+$('#active_category').val(), data:ids, success: function(result){
       if(result==="success") {
           $('#message_container').html('<div class="umsg success">Category / tag configuration updated successfully.</div>');
       } else {
           $('#message_container').html('<div class="umsg error">'+result+'</div>');
       }
    }});
    
});


$(document).on('click','#switch_show_recommended',function() {
    if($(this).attr('data-ishidden')=="0") {
        $('.gs:not(.rec-parent):not(.rec-direct)').hide();
        $(this).attr('data-ishidden',1);
    } else {
        $('.gs').show();
        $(this).attr('data-ishidden',0);
    }
});


function populateGS1TagsForCategory(brick,tag_connections) {

    $('#tags_gs1').html('');

    // 1. Query all tags for brick including their taggroup
    $.ajax({ type:"GET", url: "/?action=bricktree&brick_code="+brick, success: function(result){
        var parsed = JSON.parse(result);

        if(typeof parsed == 'object' ) {


            for(var tagsel in parsed) {
                appendGS1Selector(parsed[tagsel],tag_connections);
            }

        } else {
            $('#tags_gs1').html('Error: GS1 Tags could not be queried.');
        }

    }});
}

function appendGS1Selector(tagsel,tag_connections) {

    var html = '<div class="gs1tag" data-id="'+tagsel["id"]+'" data-group_code="'+tagsel["code"]+'" >';
    html += '<label>'+tagsel["muid"]+'</label> ';
    html += '<select data-id="'+tagsel["id"]+'" data-group_code="'+tagsel["code"]+'">';

    for(var tag in tagsel["tags"]) {
        selected ="";

        for(var con in tag_connections) {
            if(tag_connections[con]["tag_id"] == tagsel["tags"][tag]["tag_id"]) {
                selected="selected";
                break;
            }
        }

      html += '<option '+selected+' value="'+tagsel["tags"][tag]["tag_id"]+'">'+tagsel["tags"][tag]["tag_muid"]+'</option>';
    }
    html += '</select></div>';
    $('#tags_gs1').append(html);
}



