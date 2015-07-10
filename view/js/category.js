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
            
        } else {
            $(this).parent().attr('data-current_cat',$('#sel-l'+(currentLevel-1)+'-'+rowId).val());
        }
        
    } else {
    
        // erase levels below
        eraseLevelsForRow(rowId,currentLevel);
        
        // the value is set to the selected category
        $(this).parent().attr('data-current_cat',selected);
        
        // a selector of the next level has to be created
        $(this).parent().append(getCategorySelector(nextLevel,selectedName,"sel-l"+nextLevel+"-"+rowId,""));
    }
    
});

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


function buildCategorySelector(categories,level,id,preselect) {
    
    if(categories.length>0) {

        var html = '<select data-level="'+level+'" class="cat-selector cat-selector-l'+level+'" name="'+id+'" id="'+id+'">';

        html += '<option value="">-- not selected --</option>';

        for(var i=0;i<categories.length;i++) {
            
            var selected = "";
            
            if(categories[i]["lvl_"+level] === preselect) {
                selected = 'selected="selected"';
            }
            
            html += '<option '+selected+' value="'+categories[i]["gid"]+'">'+categories[i]["lvl_"+level]+'</option>';
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
    }
    
    for(var i=0;i<categories.length;i++) {
        if((categories[i]["lvl_"+(level-1)] === parent) 
                && (categories[i]["lvl_"+level] != "")
                && (categories[i]["lvl_"+(level+1)] == "")) {
            cats.push(categories[i]);
        }
    }
    
    return cats;
}