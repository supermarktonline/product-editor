var product_simple_properties = [
    "notice",
    "nutrient_unit",
    
    "nutrient_100_energy",
    "nutrient_100_fat_total",
    "nutrient_100_fat_saturated",
    "nutrient_100_protein",
    "nutrient_100_fibers",
    "nutrient_100_calcium",
    "nutrient_100_carb",
    "nutrient_100_sugar",
    "nutrient_100_salt",
    "nutrient_100_lactose",
    "nutrient_100_natrium",
    "nutrient_100_bread_unit",
    
    "nutrient_snd_amount",
    "nutrient_snd_additional",

    "nutrient_snd_energy",
    "nutrient_snd_fat_total",
    "nutrient_snd_fat_saturated",
    "nutrient_snd_protein",
    "nutrient_snd_fibers",
    "nutrient_snd_calcium",
    "nutrient_snd_carb",
    "nutrient_snd_sugar",
    "nutrient_snd_salt",
    "nutrient_snd_lactose",
    "nutrient_snd_natrium",
    "nutrient_snd_bread_unit"
];

var allergene = [
    "a","b","c","d","e","f","g","h","l","m","n","o","p","r"
];

var ingredients;

var ingredient_names = [];

var seals;


$(document).ready(function() {
    // initialize the ingredients
   ingredients = JSON.parse($('#ingredients').html());
   
   for(var i = 0; i < ingredients.length; i++) {
       ingredient_names.push(ingredients[i]["name"]);
   }
   
   $('#ingredients_selector').autocomplete({
       source: ingredient_names,
       select: function(event,ui) {
           
           event.preventDefault();
           
           // 1. get full ingredient data
           var datIngr = getIngredientBy("name",ui["item"]["value"]);
           
           setCurrentIngredient(datIngr);
           addCurrentArticleIngredient(datIngr);
           
       }
   });
   
   
   // initialize the seals
   seals = JSON.parse($('#seals').html());
   
   initializeSeals(seals);
   
   
});


toggleList = function(){
  $("#table-container").toggle(300);
}

// clicking a product within the list --> get the product via ajax and display the edit fields
$(document).on('click','*[data-open_edit_id]',function() {
   
    $.ajax({url: "/?productjson="+$(this).attr('data-open_edit_id'), success: function(result){
            
        $('#main-container').show();
            
        var product = JSON.parse(result);
        
        $('#message_container').html('');
        
        $('#save_now').attr('data-save_id',product["id"]);
        
        $('#name').val(product["productName de_AT"]);
        $('#description').val(product["productDescription de_AT"]);
        $('#notice').val(product["notice"]);
        
        var images = product["productimages"];
        var imagesAr = images.split(",");
        
        if(imagesAr) {
            $.each(imagesAr, function( index, value ) {
              if(index===0) {
                  $('#current_image').html('<img src="'+value+'" alt="" />');
              }
            });
        }
        
        // populate the simple fields
        $.each(product_simple_properties,function(key,value) {
            if($("#" + value).length > 0) {
              $('#'+value).val(product[value]);
            }
        });
        
        // nutrient unit
        $('#nutrient_unit_copy').html(product["nutrient_unit"]);
        
        
        // allergene / ingredients
        $('#ingredients_collector').html('');

        // show ingredients
        $.ajax({url: "/?ingredient_connection=get&fdata_id="+product["id"], success: function(result){
                var icons = JSON.parse(result);

                for(var i = 0; i < icons.length; i++) {
                    appendIngredient(getIngredientBy("id",icons[i]["ingredient_id"]));
                }
                
                clearCurrentAllergen();
                
                for(var i = 0;i<allergene.length; i++) {
                    if(product["allergen_"+allergene[i]]) {
                        $('[data-art_ingr="'+allergene[i]+'"]').prop("checked",true);
                    } else {
                        $('[data-art_ingr="'+allergene[i]+'"]').prop("checked",false);
                    }
                }
            }
        });
        
        // show categories
        $('#category_select_wrapper').html('');
        
        $.ajax({url: "/?category_connection=get&fdata_id="+product["id"], success: function(result){
                
                var ids;
                
                try {
                    ids = JSON.parse(result);
                } catch(e) {
                    $('#message_container').html('<div class="umsg error">'+result+'</div>');
                    return;
                }
                
                populateCategories(ids);
                
            }
        });
        
        // Show Gütesiegel etc.
        $('.seal').each(function() {
           $(this).prop('checked', false);  
        });
        
        $.ajax({url: "/?sealetc_connection=get&fdata_id="+product["id"], success: function(result){
                
                var ids;
                
                try {
                    ids = JSON.parse(result);
                } catch(e) {
                    $('#message_container').html('<div class="umsg error">'+result+'</div>');
                    return;
                }
                
                for(var i = 0; i < ids.length; i++) {
                   
                    var sid = ids[i]["sealetc_id"];
                    
                    $('.seal[value="'+sid+'"]').each(function() {
                       $(this).prop("checked",true); 
                    });
                }
                
            }
        });
        
        
    }});
    
});


// saving a product after editing
$(document).on('click','#save_now',function() {
    var save_id = $(this).attr('data-save_id');
    
    var product = {};
    product["id"] = save_id;
    product["edited"] = true;
    product["productName___de_AT"] = $('#name').val();
    product["productDescription___de_AT"] = $('#description').val();
    product["notice"] = $('#notice').val();
    
    $.each(product_simple_properties,function(key,value) {
        if($("#" + value).length > 0) {
          product[value] = $('#'+value).val();
        }
    });
    
    for(var i=0; i<allergene.length;i++) {
        if($('#art_ingr_'+allergene[i]).is(":checked")) {
            product["allergen_"+allergene[i]] = true;
        } else {
            product["allergen_"+allergene[i]] = false;
        }
    }
    
    $.ajax({ type:"POST", url: "/?updateproduct", data:product, success: function(result){
       if(result==="success") {
           $('#message_container').html('<div class="umsg success">Article updated successfully.</div>');
           $('tr[data-open_edit_id="'+save_id+'"] td:nth-child(2)').html('<span class="eds eds-edited"></span>');
       } else {
           $('#message_container').html('<div class="umsg error">'+result+'</div>');
       }
    }});

    // update all categories
    var categories = {};
    categories["categories"]=[];
    
    $('#category_select_wrapper').children('.csw-row').each(function() {
        var catid = parseInt($(this).attr('data-current_cat')) || 0;
        
        if(catid>0) {
            categories["categories"].push(catid);
        }
    });
    
    $.ajax({ type:"POST", url: "/?category_connection=update&fdata_id="+save_id, data:categories, success: function(result){
       if(result==="success") {
           $('#message_container').append('<div class="umsg success">Article categories updated successfully.</div>');
       } else {
           $('#message_container').append('<div class="umsg error">'+result+'</div>');
       }
    }});
    
    
    // update all seals
    var ids = {};
    
    var tpids = [];
    
    $('.seal').each(function() {
        if($(this).is(":checked")) {
            tpids.push($(this).val());
        }
    });
    
    ids["ids"] = tpids;
    
    $.ajax({ type:"POST", url: "/?sealetc_connection=update&fdata_id="+save_id, data:ids, success: function(result){
       if(result==="success") {
           $('#message_container').append('<div class="umsg success">Article seals updated successfully.</div>');
       } else {
           $('#message_container').append('<div class="umsg error">'+result+'</div>');
       }
    }});
    
    
});


/**
 * Simple UI Improvements
 */
 $(document).on('change','#nutrient_unit',function() {
    $('#nutrient_unit_copy').html($('#nutrient_unit').val()); 
 });
 
 
 /**
  * Generate Button
  */
 
 $(document).on('click','#generate_nw',function() {
     
     var multiply = parseInt($('#nutrient_snd_amount').val()) || 0;
     multiply = multiply / 100;
     
     var nutnames = ["energy","fat_total","fat_saturated","protein","fibers","calcium","carb","sugar","salt","lactose","natrium","bread_unit"];
     
     for(var i = 0;i<nutnames.length;i++) {
         $('#nutrient_snd_'+nutnames[i]).val( Math.ceil($('#nutrient_100_'+nutnames[i]).val() * multiply));
     }
     
 });
 
 
 