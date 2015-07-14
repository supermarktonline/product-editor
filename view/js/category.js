var categories = [];

$(document).ready(function() {
   
   categories = JSON.parse($('#categories').text());
   $('#categories').html('');
    
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
 * Sets the global current cat AND the seal relationships.
 */
function setGlobalCurrentCat(catid) {
    
    // set id to hidden element
    $('#active_category').val(catid);
    
    // remove seal highlighting
    $('.gs label').each(function() {
       $(this).attr('class','');
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
        
        
        $.ajax({url: "/?category_sealetc_connection=get&category_id="+catid+"&parent_ids="+JSON.stringify(parents), success: function(result){

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
                    $('.gs[data-id="'+parentcon[i]["sealetc_id"]+'"] label').each(function() {
                       $(this).addClass('rec-parent'); 
                    });
                }
                
                var directcon = decoded["direct"];
                
                for(var i = 0; i < directcon.length; i++) {
                    $('.gs[data-id="'+directcon[i]["sealetc_id"]+'"] label').each(function() {
                       $(this).addClass('rec-direct'); 
                    });
                }
                

            }
        });
        
        
    }
}


function getParentIds(cat) {
    
    var parents = [];
    
    for(var i = 0; i < categories.length; i++) {
        for(var a = 1; a < 7; a++) {
            if((categories[i]["lvl_"+a] === cat["lvl_"+a]) && (cat["lvl_"+a] !== "") && (categories[i]["lvl_"+(a+1)] === "") && (categories[i]["gid"] !== cat["gid"])) {
                parents.push(categories[i]["gid"]);
            }
        }
    }
    
    return parents;
}


$(document).on('click','#cat_adder',function() {
    createCatRow(0);
});


function populateCategories(category_ids) {
    
    if(category_ids.length>0) {
        
        for(var i = 0; i < category_ids.length;i++) {
            createCatRow(category_ids[i]["category_id"]);
        }
        
    }
    
    createCatRow(0);
}

function eraseLevelsForRow(row_id,level) {
    
    $('.csw-row[data-row_number="'+row_id+'"]').children().each(function() {
        
        if(parseInt($(this).attr("data-level"))>level) {
            
            $('#'+$(this).attr('id')).remove();
        }
    });
}

// cat_id = 0 for new row
function createCatRow(cat_id) {
    
    var numrows = parseInt($("#category_select_wrapper .csw-row:last-child").attr('data-row_number')) || 0;
    numrows++;
    
    var rowHTML = '<div id="csw_row_'+numrows+'" class="csw-row" data-row_number="'+numrows+'" data-current_cat="'+cat_id+'">';
    
    if(cat_id==0) {
        rowHTML += getCategorySelector(1,"","sel-l1-"+numrows,"");
    } else {
        rowHTML += getRecursiveSelectors(cat_id,numrows);
    }
    
    rowHTML += '</div>';
    
    $('#category_select_wrapper').append(rowHTML);
}

function getRecursiveSelectors(cat_id,numrows) {
    
    var html="";
    
    // 1. Find the row within the categories
    var category = null;
    
    for(var i = 0; i < categories.length; i++) {
        if(categories[i]["gid"]===cat_id) {
            category = categories[i];
            break;
        }
    }
    
    // 2. For each non empty level build a pre-selected selector, also build a selector for the level after
    
    for(var i = 1; i <= 7; i++) {
        if(category["lvl_"+i] !== "") {
            html += getCategorySelector(i,category["lvl_"+(i-1)],"sel-l"+i+"-"+numrows,category["lvl_"+i]);
        } else {
            html += getCategorySelector(i,category["lvl_"+(i-1)],"sel-l"+i+"-"+numrows,"");
            break;
        }
    }
    
    return html;
    
}

function getCategorySelector(level,parent,id,preselect) {
    
    return buildCategorySelector(getCategoriesForLevel(level,parent),level,id,preselect);
}


function buildCategorySelector(cats,level,id,preselect) {
    
    if(cats.length>0) {

        var html = '<select data-level="'+level+'" class="cat-selector cat-selector-l'+level+'" name="'+id+'" id="'+id+'">';

        html += '<option value="">-- not selected --</option>';

        for(var i=0;i<cats.length;i++) {
            
            var selected = "";
            
            if(cats[i]["lvl_"+level] === preselect) {
                selected = 'selected="selected"';
            }
            
            html += '<option '+selected+' value="'+cats[i]["gid"]+'">'+cats[i]["lvl_"+level]+'</option>';
        }

        html += '</select>';
    }
    return html;
}

function getCategoriesForLevel(level,parent) {
    
    var cats = [];
    
    if(level===1) {
        for(var i=0;i<categories.length;i++) {
            if(categories[i]["lvl_2"] == "") {
                cats.push(categories[i]);
            }
        }
    } else {
    
        for(var i=0;i<categories.length;i++) {
            if((categories[i]["lvl_"+(level-1)] === parent) 
                    && (categories[i]["lvl_"+level] != "")
                    && (categories[i]["lvl_"+(level+1)] == "")) {
                cats.push(categories[i]);
            }
        }
    }
    
    return cats;
}

function getCategoryById(catid) {
    for(var i = 0; i < categories.length; i++) {
        if(categories[i]["gid"]==catid) {
            return categories[i];
        }
    }
}