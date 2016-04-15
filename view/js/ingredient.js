
 /**
  * Ingredients
  */
 
 // set the current ingredient
 function setCurrentIngredient(ingredient,by_property) {

     if(by_property) {
         ingredient = getIngredientBy(by_property,ingredient);
     }

     $('#ingredient_upnew_id').val(ingredient.id);
     $('#ingredient_updater').val(ingredient.name);

     $('#ingredient_upnew').val(ingredient.name);
     
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

    $(document).on('click','#ingredient_updater',function() {
        updateIngredient(getIngredientBy('id',$('#ingredient_upnew_id').val()));
    });

 function updateIngredient(ingredient) {

     if(!ingredient) {
         $('#message_container').html('<div class="umsg error">Keine Zutat ausgewählt.</div>');
         return;
     }


     var old = ingredient.name;
     ingredient.name = $('#ingredient_upnew').val();

     // create ingredient
     $.ajax({ type:"POST", url: "/?ingredient=update_name", data:ingredient, success: function(result){

         var crIngr = JSON.parse(result);

         if(!crIngr["id"]) {
             $('#message_container').html('<div class="umsg error">'+crIngr["error"]+'</div>');
         } else {

             for(ingredient in ingredients) {
                 if(ingredient["id"] == crIngr["id"]) {
                     ingredient["name"] = crIngr["name"];
                 }
             }

             var index = ingredient_names.indexOf(old);
             ingredient_names.splice(index,1);
             ingredient_names.push(crIngr["name"]);

             $(".ic_ing[data-id="+crIngr["id"]+"]").each(function() {
                 var text_to_change = this.childNodes[0];
                 text_to_change.nodeValue = crIngr["name"]+" ";
             });

             $('#ingredients_selector,#enthalt_spuren,#enthalt_gering').autocomplete("close");

             $('#message_container').html('<div class="umsg success">Zutat erfolgreich aktualisiert.</div>');
         }
     }
     });
 }

 /**
  *
  *
  * @param ingredient
  * @param collector_id
  * @param connection_type: One of standard,enthalt,gering
  */
 function addIngredientToCollection(ingredient,collector_id,connection_type) {
     $('#'+collector_id).val('');

     var exists = false;
     $('#'+collector_id+' > .ic_ing').each(function(key,val) {
         if($(this).attr('data-id') == ingredient["id"]) {
             exists = true;
         }
     });

     if(!exists) {
         $.ajax({url: "/?ingredient_connection=create&type="+connection_type+"&fdata_id="+$('#save_id').attr('data-save_id')+"&ingredient_id="+ingredient["id"], success: function(result){
             if(result==="success") {
                 appendIngredientToCollection(ingredient,collector_id,connection_type);
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

function appendIngredientToCollection(ingredient,collector_id,connection_type, sort_nb) {

    var exists = false;

    $('#'+collector_id+' > .ic_ing').each(function() {
        if($(this).attr("data-id")==ingredient["id"]) {
            exists = true;
        }
    });

    var selector = "ingredients_selector";
    if(connection_type=="enthalt") {
        selector = "enthalt_spuren";
    } else if(connection_type=="gering") {
        selector = "enthalt_gering";
    }

    $('#'+selector).val('');

    if(!exists) {
        $('#'+collector_id).append('<span class="ic_ing" sort-nb="' + sort_nb + '" data-id="'+ingredient["id"]+'">'+ingredient["name"]+' <span class="ic_ing_remove" data-type="'+connection_type+'">X</span><span class="admin-area">ing-id: ' + ingredient["id"] + ", sort-nb: " + sort_nb + '</span></span></span>');

        if(connection_type=="standard") {
            for(var i = 0;i<allergene.length; i++) {
                if(ingredient[allergene[i]]) {
                    $('[data-art_ingr="'+allergene[i]+'"]').prop('checked', true);
                }
            }
        }
    }
}

// delete ingredient from a collection
$(document).on('click','.ic_ing_remove',function() {
    var remove = $(this);
    var type = $(this).attr('data-type');

    if($(this).parent())

    $.ajax({url: "/?ingredient_connection=delete&type="+type+"&fdata_id="+$('#save_id').attr('data-save_id')+"&ingredient_id="+$(this).parent().attr('data-id'), success: function(result){
            if(result==="success") {
                $(remove).parent().remove();

                if(type=="standard") {
                    refreshArticleAllergeneAuto();
                    clearCurrentAllergen();
                }
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
$(document).on('keypress','#ingredients_selector,#enthalt_spuren,#enthalt_gering',function(e){
    
  if(e.keyCode==13) {
      
    // check if ingredient already exists
    var ingrName = $(this).val();

    if(ingrName != "") {

         var ingredient = getIngredientBy('name',ingrName);

         var type = $(this).attr("data-type");

         var collector_id = "ingredients_collector";
         if(type=="enthalt") {
            collector_id = "enthalt_spuren_collector";
         } else if(type=="gering") {
            collector_id = "enthalt_gering_collector";
         }


         if(null!==ingredient) {
             addIngredientToCollection(ingredient,collector_id,type);

             if(type=="standard") {
                 setCurrentIngredient(ingredient);
             }
             $('#ingredients_selector,#enthalt_spuren,#enthalt_gering').autocomplete("close");
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

                          addIngredientToCollection(crIngr,collector_id,type);

                          if(type=="standard") {
                              setCurrentIngredient(crIngr);
                          }
                          $('#ingredients_selector,#enthalt_spuren,#enthalt_gering').autocomplete("close");
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
        var cur_product = $('#save_id').attr('data-save_id');

        if(cur_ingr>0) {

            var ingredient = {};

            ingredient[$(this).attr("data-cur_ingr")] = $(this).is(":checked");

            $.ajax({ type:"POST", url: "/?ingredient=delete&ingredient_id="+cur_ingr+"&fdata_id="+cur_product, data:ingredient, success: function(result){
                    if(result!=="success") {
                        
                        var res = JSON.parse(result);
                        
                        $('#message_container').html('<div class="umsg error">'+res["error"]+'</div>');
                    } else {

                        var remover = ' .ic_ing[data-id="'+cur_ingr+'"]';

                        $('#ingredients_collector'+remover+',#enthalt_spuren_collector'+remover+' ,#enthalt_gering_collector'+remover).remove();
                        
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
    $('[data-cur_ingr]').each(function() {
       $(this).prop("checked",false); 
    });
}

$(document).on('click','#ingredient-change-sort-nb-button',function(e) {
    e.preventDefault();

    var fdataId = $('#save_id').attr('data-save_id');
    var ingredientId = $('#change-sort-ingredient-id').val();
    var newSortNb = $('#change-sort-sort-nb').val();

    $.ajax({ type:"GET", url: "/?ingredient_connection=update-sort-nb&fdata_id="+fdataId+"&ingredient_id=" + ingredientId + "&sort_nb=" + newSortNb, success: function(result){
        if(result=="success") {
            $('#message_container').html('<div class="umsg success">Sort number changed.</div>');
        } else {
            var dec = JSON.parse(result);
            $('#message_container').html('<div class="umsg error">'+dec["error"]+'</div>');
        }
    }});
});
