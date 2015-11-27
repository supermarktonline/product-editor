
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
    
        for(var i = 0;i<allergene.length; i++) {
            if(ingredient[allergene[i]]) {
                $('[data-art_ingr="'+allergene[i]+'"]').prop('checked', true);
            }
        }
    
    }
}

// delete ingredient
$(document).on('click','.ic_ing_remove',function() {
    var remove = $(this);
    
    $.ajax({url: "/?ingredient_connection=delete&fdata_id="+$('#save_now').attr('data-save_id')+"&ingredient_id="+$(this).parent().attr('data-id'), success: function(result){
            if(result==="success") {
                $(remove).parent().remove();
                
                refreshArticleAllergeneAuto();
                clearCurrentAllergen();
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
                    
                    refreshArticleAllergeneAuto();
                    
                    
                    $('#message_container').html('<div class="umsg success">Inhaltsstoff erfolgreich aktualisiert. Do not forget to save the article, too.</div>');
                }
            }
        });
    }
});

// delete a whole ingredient (only possible if connection is only to current article)
$(document).on('click','#ingredient_deleter',function() {
    
        var cur_ingr = parseInt($('#current_ingredient').attr('data-id')) || 0;
        var cur_product = $('#save_now').attr('data-save_id');

        if(cur_ingr>0) {

            var ingredient = {};

            ingredient[$(this).attr("data-cur_ingr")] = $(this).is(":checked");

            $.ajax({ type:"POST", url: "/?ingredient=delete&ingredient_id="+cur_ingr+"&fdata_id="+cur_product, data:ingredient, success: function(result){
                    if(result!=="success") {
                        
                        var res = JSON.parse(result);
                        
                        $('#message_container').html('<div class="umsg error">'+res["error"]+'</div>');
                    } else {
                        
                        // remove the ingredient also from the interface
                        $('#ingredients_collector .ic_ing[data-id="'+cur_ingr+'"]').remove();
                        
                        for(var i=0;i<ingredients.length; i++) {
                            if(ingredients[i]["id"]==cur_ingr) {
                                
                                var name = ingredients[i]["name"];
                                ingredients.splice(i,1);
                                
                                for(var a = 0; a<ingredient_names.length; a++) {
                                    if(ingredient_names[a]==name) {
                                        
                                        ingredient_names.splice(a,1);
                                        
                                        $('#message_container').html('<div class="umsg success">Ingredient successfully deleted.</div>');
                                        
                                        refreshArticleAllergeneAuto();
                                        clearCurrentAllergen();
                                        return;
                                    }
                                }
                            }
                        }
                        
                        
                        
                    }
                }
            });
        }
    }
);


function refreshArticleAllergeneAuto() {
    // add/remove allergene
    var collector = [];

    // collect all allergene iterating ingredients
    $('#ingredients_collector').children(".ic_ing").each(function() {
       var iid = $(this).attr("data-id");
       var ing = getIngredientBy("id",iid);

       for(var i=0;i<allergene.length;i++) {
           if(ing[allergene[i]]) {
               collector.push(allergene[i]);
           }
       }
    });

    for(var i = 0;i<allergene.length;i++) {
        if(collector.indexOf(allergene[i])>-1) {
            $('[data-art_ingr="'+allergene[i]+'"]').prop("checked",true);
        } else {
            $('[data-art_ingr="'+allergene[i]+'"]').prop("checked",false);
        }
    }
}

function clearCurrentAllergen() {
    $('#current_ingredient').attr('data-id','').html("...");
    $('[data-cur_ingr').each(function() {
       $(this).prop("checked",false); 
    });
}