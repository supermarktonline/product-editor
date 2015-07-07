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


$(document).ready(function() {
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
    }});

    $('#ingredients_collector').html('');
    
    // show ingredients
    $.ajax({url: "/?ingredient_connection=get&fdata_id="+$(this).attr('data-open_edit_id'), success: function(result){
            var icons = JSON.parse(result);
            
            for(var i = 0; i < icons.length; i++) {
                appendIngredient(getIngredientBy("id",icons[i]["ingredient_id"]));
            }
        }
    });
    
});


// saving a product after editing
$(document).on('click','#save_now',function() {
    var save_id = $(this).attr('data-save_id');
    
    var product = {};
    product["id"] = save_id;
    product["edited"] = true;
    product["productName___de_AT"] = $('#name').val();
    product["productDescription___de_AT"] = $('#description').val();
    
    $.each(product_simple_properties,function(key,value) {
        if($("#" + value).length > 0) {
          product[value] = $('#'+value).val();
        }
    });
    
    $.ajax({ type:"POST", url: "/?updateproduct", data:product, success: function(result){
       if(result==="success") {
           $('#message_container').html('<div class="umsg success">Article updated successfully.</div>');
           $('tr[data-open_edit_id="'+save_id+'"] td:nth-child(2)').html('<span class="eds eds-edited"></span>');
       } else {
           $('#message_container').html('<div class="umsg error">'+result+'</div>');
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
 
 
 
 /**
  * Ingredients
  */
 
 // set the current ingredient
 function setCurrentIngredient(ingredient,by_property) {
     
     if(by_property) {
         ingredient = getIngredientBy(by_property,ingredient);
     }
     
     $('#current_ingredient').html(ingredient["name"]);
     $('#current_ingredient').attr('data-id',ingredient["id"]);
     
     for(var i = 0; i<allergene.length; i++) {
         if(ingredient[allergene[i]] === true) {
             $('#cur_ingr_'+allergene[i]).prop("checked",true);
         } else {
             $('#cur_ingr_'+allergene[i]).prop("checked",false);
         }
     } 
 }
 
// connect ingredient to the currently edited article
function addCurrentArticleIngredient(ingredient) {
    
    $('#ingredients_selector').val('');
    
    var exists = false;
    $('#ingredients_collector > .ic_ing').each(function(key,val) {
        if($(this).attr('data-id') == ingredient["id"]) {
            exists = true;
        }
    });
    
    if(!exists) {
        $.ajax({url: "/?ingredient_connection=create&fdata_id="+$('#save_now').attr('data-save_id')+"&ingredient_id="+ingredient["id"], success: function(result){
                if(result==="success") {
                    appendIngredient(ingredient);
                } else {
                    $('#message_container').html('<div class="umsg error">'+result+'</div>');
                }
            }
        });
    }
}

// find ingredient in cache
function getIngredientBy(property,value) {
    for(var i = 0; i < ingredients.length; i++) {
        if(ingredients[i][property]==value) {
            return ingredients[i];
        }
    }
    return null;
}

// add ingredient visually
function appendIngredient(ingredient) {
    
    var exists = false;
    $('#ingredients_collector > .ic_ing').each(function() {
        if($(this).attr("data-id")==ingredient["id"]) {
            exists = true;
        }
    });
    
    if(!exists) {
        $('#ingredients_collector').append('<span class="ic_ing" data-id="'+ingredient["id"]+'">'+ingredient["name"]+' <span class="ic_ing_remove">X</span></span>');
    }
}

// delete ingredient
$(document).on('click','.ic_ing_remove',function() {
    var remove = $(this);
    
    $.ajax({url: "/?ingredient_connection=delete&fdata_id="+$('#save_now').attr('data-save_id')+"&ingredient_id="+$(this).parent().attr('data-id'), success: function(result){
            if(result==="success") {
                $(remove).parent().remove();
            } else {
                $('#message_container').html('<div class="umsg error">'+result+'</div>');
            }
        }
    });
});


// set current ingredient
$(document).on('click','.ic_ing',function(e) {
    
    if($(e.target).hasClass('ic_ing')) {
        setCurrentIngredient($(this).attr('data-id'),'id');
    }
});


// create a new ingredient
$(document).on('keypress','#ingredients_selector',function(e){
    
  if(e.keyCode==13) {
      
    // check if ingredient already exists
    var ingrName = $(this).val();

    if(ingrName != "") {
         var ingredient = getIngredientBy('name',ingrName);

         if(null!==ingredient) {
             addCurrentArticleIngredient(ingredient)
         } else {

             var ingredient = {
                 name: ingrName
             };
             
             // create ingredient
              $.ajax({ type:"POST", url: "/?ingredient=create", data:ingredient, success: function(result){
                    
                      var crIngr = JSON.parse(result);

                      if(!crIngr["id"]) {
                          $('#message_container').html('<div class="umsg error">'+crIngr["error"]+'</div>');
                      } else {
                          ingredients.push(crIngr);
                          ingredient_names.push(crIngr["name"]);
                          addCurrentArticleIngredient(crIngr);
                      }
                  }
              });
         }
     }
  }
});


// update the current ingredient
$(document).on('change','*[data-cur_ingr]',function() {
    
    var cur_ingr = parseInt($('#current_ingredient').attr('data-id')) || 0;
    
    if(cur_ingr>0) {
    
        var ingredient = {};

        ingredient[$(this).attr("data-cur_ingr")] = $(this).is(":checked");

        $.ajax({ type:"POST", url: "/?ingredient=update&ingredient_id="+cur_ingr, data:ingredient, success: function(result){
                
                var crIngr = JSON.parse(result);
                
                if(!crIngr["id"]) {
                    $('#message_container').html('<div class="umsg error">'+crIngr["error"]+'</div>');
                } else {
                    
                    for(var i = 0; i < ingredients.length; i++) {
                        if(ingredients[i]["id"]==crIngr["id"]) {
                            ingredients[i] = crIngr;
                        }
                    }
                    
                }
            }
        });
    }
});

// delete a whole ingredient (only possible if connection is only to current article)
$(document).on('click','ingredient_deleter',function() {
    
        var cur_ingr = parseInt($('#current_ingredient').attr('data-id')) || 0;
        var cur_product = $('#save_now').attr('data-save_id');

        if(cur_ingr>0) {

            var ingredient = {};

            ingredient[$(this).attr("data-cur_ingr")] = $(this).is(":checked");

            $.ajax({ type:"POST", url: "/?ingredient=delete&ingredient_id="+cur_ingr+"&fdata_id="+cur_product, data:ingredient, success: function(result){
                    if(result!=="success") {
                        $('#message_container').html('<div class="umsg error">'+result+'</div>');
                    } else {
                        // remove the ingredient also from the interface
                        $('#ingredients_collector').remove($('.ic_ing[data-id="'+cur_ingr+'"]'));
                        
                        // todo: update Array
                    }
                }
            });
        }
    }
);