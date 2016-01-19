var categories = [];

var gs_tree = {};

$(document).ready(function() {
   
   categories = JSON.parse($('#categories').text());
   $('#categories').html('');

    // build a tree, which is much nicer to work with
    for(i=0;i<categories.length;i++) {

        var segment_code = categories[i]["segment_code"];
        var family_code = categories[i]["family_code"];
        var class_code = categories[i]["class_code"];
        var brick_code = categories[i]["brick_code"];
        var category_id = categories[i]["gid"];

        if(!(segment_code in gs_tree)) {
            gs_tree[segment_code] = {};
            gs_tree[segment_code]["label"] = categories[i]["segment_description_en"];
            gs_tree[segment_code]["family_codes"] = {};
        }

        if(!(family_code in gs_tree[segment_code]["family_codes"] )) {
            gs_tree[segment_code]["family_codes"][family_code] = {};
            gs_tree[segment_code]["family_codes"][family_code]["label"] = categories[i]["family_description_en"];
            gs_tree[segment_code]["family_codes"][family_code]["class_codes"] = {};
        }

        if(!(class_code in gs_tree[segment_code]["family_codes"][family_code]["class_codes"] )) {
            gs_tree[segment_code]["family_codes"][family_code]["class_codes"][class_code] = {};
            gs_tree[segment_code]["family_codes"][family_code]["class_codes"][class_code]["label"] = categories[i]["class_description_en"];
            gs_tree[segment_code]["family_codes"][family_code]["class_codes"][class_code]["brick_codes"] = {};
        }

        // we assume that the brick codes are also unique identifiers for now
        if(!(brick_code in gs_tree[segment_code]["family_codes"][family_code]["class_codes"][class_code]["brick_codes"] )) {
            gs_tree[segment_code]["family_codes"][family_code]["class_codes"][class_code]["brick_codes"][brick_code] = {};
            gs_tree[segment_code]["family_codes"][family_code]["class_codes"][class_code]["brick_codes"][brick_code]["label"] = categories[i]["brick_description_en"];
            gs_tree[segment_code]["family_codes"][family_code]["class_codes"][class_code]["brick_codes"][brick_code]["category_id"] = category_id;
        }

    }
    
});


// category in category selector was selected
$(document).on('change','.cat-selector',function() {
    
    var rowId = $(this).parent().attr("data-row_number");
    var currentLevel = parseInt($(this).attr('data-level'));
    var selected = $(this).val();
    var selectedName = $(this).children('option[value="'+selected+'"]').text();
    var nextLevel = parseInt($(this).attr('data-level'))+1;
    
    // selector was unselected
    if(selected=="") {
        
        // erase levels below
        eraseLevelsForRow(rowId,currentLevel);
        
        // the row number is set to the value of the parent selector, if it doesnt exist, it is set to zero
        if(currentLevel === 1) {
            $(this).parent().attr('data-current_cat',0);
            $(this).parent().remove();
            setGlobalCurrentCat(0);
        } else {
            var parselid = $('#sel-l'+(currentLevel-1)+'-'+rowId).val();
            $(this).parent().attr('data-current_cat',parselid);
            setGlobalCurrentCat(parselid);
        }
        
    } else {
    
        // erase levels below
        eraseLevelsForRow(rowId,currentLevel);
        
        // the value is set to the selected category
        $(this).parent().attr('data-current_cat',selected);
        
        setGlobalCurrentCat(selected);
        
        // a selector of the next level has to be created
        $(this).parent().append(getCategorySelector(nextLevel,selectedName,"sel-l"+nextLevel+"-"+rowId,""));
    }
    
});


/*
 * Sets the global current cat AND the tag relationships.
 */
function setGlobalCurrentCat(catid) {
    
    // set id to hidden element
    $('#active_category').val(catid);
    
    // remove tag highlighting
    $('.gs label').each(function() {
       $(this).parent().attr('class','gs');
    });
    
    // set the label
    if(catid==0) {
        $('#active_category_display').text("");
    } else {

        // set the label
        var cat = getCategoryById(catid);

        for(var i = 7; i > 0; i--) {
            if(cat["lvl_"+i]!="") {
                $('#active_category_display').text(cat["lvl_"+i]);
                break;
            }
        }
        
        // lets calculate this client side, less stress for the poor server
        var parents = getParentIds(cat);
        
        
        $.ajax({url: "/?category_tag_connection=get&category_id="+catid+"&parent_ids="+JSON.stringify(parents), success: function(result){

                var decoded;

                try {
                    decoded = JSON.parse(result);
                    
                } catch(e) {
                    $('#message_container').html('<div class="umsg error">'+result+'</div>');
                    return;
                }
                
                // we have two cases here
                var parentcon = decoded["parent"];

                for(var i = 0; i < parentcon.length; i++) {
                    $('.gs[data-id="'+parentcon[i]["tag_id"]+'"] label').each(function() {
                       $(this).parent().addClass('rec-parent');
                    });
                }
                
                var directcon = decoded["direct"];
                
                for(var i = 0; i < directcon.length; i++) {
                    $('.gs[data-id="'+directcon[i]["tag_id"]+'"] label').each(function() {
                       $(this).parent().addClass('rec-direct');
                    });
                }
                

            }
        });
        
        
    }
}


function setCategorySelector(category_id) {

    var id = parseInt(category_id) || 0;

    $('#cs_segment').html(buildSelector(1));

}

$(document).on('change','.catsel',function() {
    var level = $(this).attr('data-level');
    var segment = $("#cs_segment .catsel").first().val();
    var family = $('#cs_family .catsel').first().val();
    var _class = $('#cs_class .catsel').first().val();
    var brick = $('#cs_brick .catsel').first().val();

    if(level==1) {
        $('#cs_family,#cs_class,#cs_brick').html('');

        if(segment!="") {
            $('#cs_family').html(buildSelector(2,segment));
        }
    } else if(level==2) {
        $('#cs_class,#cs_brick').html('');

        if(family!="") {
            $('#cs_class').html(buildSelector(3,segment,family));
        }
    } else if(level==3) {
        $('#cs_brick').html('');

        if(_class!="") {
            $('#cs_brick').html(buildSelector(4, segment,family,_class));
        }
    } else if(level==4) {

        //  Populate GS1 Tags for this category (including their taggroups)
        populateGS1TagsForCategory(brick);

        // TODO other tags

    }


});


function buildSelector(level,segment,family,_class,brick) {

    var html = '<select class="catsel" data-level="'+level+'">';
    html += '<option value=""> - </option>';

    var base = gs_tree;

    if(level==2) {
        base = gs_tree[segment]["family_codes"];
    } else if(level==3) {
        base = gs_tree[segment]["family_codes"][family]["class_codes"];
    } else if(level==4) {
        base = gs_tree[segment]["family_codes"][family]["class_codes"][_class]["brick_codes"];
    }

    Object.keys(base).forEach(function(key, index) {
        html += '<option value="'+key+'">'+this[key]["label"]+'</option>';
    }, base);

    html += '</select>';

    return html;
}


function getCategoryById(catid) {
    for(var i = 0; i < categories.length; i++) {
        if(categories[i]["gid"]==catid) {
            return categories[i];
        }
    }
}