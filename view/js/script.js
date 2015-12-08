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
    "nutrient_snd_additional_de",

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

var product_simple_properties_nofloat = [
  "notice","nutrient_unit","nutrient_snd_amount","nutrient_snd_additional","nutrient_snd_additional_de" 
];


var allergene = [
    "a","b","c","d","e","f","g","h","l","m","n","o","p","r"
];

var ingredients;

var ingredient_names = [];

var seals;

var media_path = "";


$(document).ready(function() {
    // initialize the ingredients
   ingredients = JSON.parse($('#ingredients').html());
   
   for(var i = 0; i < ingredients.length; i++) {
       ingredient_names.push(ingredients[i]["name"]);
   }

    // autocomplete ingredients for ingredients selecotrs
   $('#ingredients_selector,#enthalt_spuren,#enthalt_gering').autocomplete({
       source: ingredient_names,
       select: function(event,ui) {
           
           event.preventDefault();
           
           // 1. get full ingredient data
           var datIngr = getIngredientBy("name",ui["item"]["value"]);
           var type = $(this).attr('data-type');

           if(type=="standard") {
                setCurrentIngredient(datIngr);
           }

           var collector_id = "ingredients_collector";
           if(type=="enthalt") {
               collector_id = "enthalt_spuren_collector";
           } else if(type=="gering") {
               collector_id = "enthalt_gering_collector";
           }

           addIngredientToCollection(datIngr,collector_id,type);
       }
   });

   
   media_path = $('#media_path').text();
   
   // initialize the seals
   seals = JSON.parse($('#seals').html());
   
   initializeSeals(seals);
   
});


toggleList = function(){
  $("#table-container").toggle(300);
}

// clicking a product within the list --> get the product via ajax and display the edit fields
$(document).on('click','*[data-open_edit_id]',function() {

    $('[data-open_edit_id]').removeClass('active');

    $(this).addClass('active');


   
    $.ajax({url: "/?productjson="+$(this).attr('data-open_edit_id'), success: function(result){
            
        $('#main-container').show();
            
        var product = JSON.parse(result);
        
        $('#message_container').html('');
        
        $('#save_now').attr('data-save_id',product["id"]);
        $('#finish_now').attr('data-save_id',product["id"]);
        $('#custom_state').val('');
        
        $('#name').val(product["productName de_AT"]);
        $('#description').val(product["productDescription de_AT"]);
        $('#notice').val(product["notice"]);
        
        var images = product["productImages"];
        var imagesAr = images.split(",");
        
        // populate the simple fields
        $.each(product_simple_properties,function(key,value) {

            if($("#" + value).length > 0) {

                if(!product[value] && value=="nutrient_snd_amount") {
                    $('#'+value).val("0");
                } else {
                    $('#'+value).val(product[value]);
                }
            }
        });
        
        // nutrient unit
        $('#nutrient_unit_copy').html(product["nutrient_unit"]);
        
        
        // allergene / ingredients
        $('#ingredients_collector,#enthalt_spuren_collector,#enthalt_gering_collector').html('');

        // show ingredients
        var types = ["standard","enthalt","gering"];

        for(key in types) {
            var type=types[key];

            (function(type) {
                $.ajax({url: "/?ingredient_connection=get&type="+type+"&fdata_id="+product["id"], success: function(result){
                    var icons = JSON.parse(result);

                    var collector_id = "ingredients_collector";
                    if(type=="enthalt") {
                        collector_id = "enthalt_spuren_collector";
                    } else if(type=="gering") {
                        collector_id = "enthalt_gering_collector";
                    }

                    for(var i = 0; i < icons.length; i++) {
                        appendIngredientToCollection(getIngredientBy("id",icons[i]["ingredient_id"]),collector_id,type);
                    }

                    if(type=="standard") {
                        clearCurrentAllergen();

                        for(var i = 0;i<allergene.length; i++) {
                            if(product["allergen_"+allergene[i]]) {
                                $('[data-art_ingr="'+allergene[i]+'"]').prop("checked",true);
                            } else {
                                $('[data-art_ingr="'+allergene[i]+'"]').prop("checked",false);
                            }
                        }
                    }
                }
                });
            })(type);
        }


        
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

        // the images
        
        $('#thumb-container').html('');
        $('#current_image_wrapper').html('');
        
        var images_product = product["productImages"].split(",").map(function(e){return e.trim();});
        var images_article = product["articleImages"].split(",").map(function(e){return e.trim();});

        var allImages = images_product.concat(images_article);

        var allImagesUnique = [];
        
        $.each(allImages, function(i, el){
            if(($.inArray(el, allImagesUnique) === -1) && (el !== "")) allImagesUnique.push(el);
        });
        
        var first = true;
        
        $.each(allImagesUnique,function(i,img_src) {
            if(first===true) {
                
               setActiveImage(media_path+img_src);
               
                first = false;
            }
            
            $('#thumb-container').append('<div data-src="'+media_path+img_src+'"><img src="'+media_path+img_src+'" alt="" /></div>');
            
        });
        
        
        
    }});

});


// saving a product after editing
$(document).on('click','#save_now,#finish_now',function() {
    var save_id = $(this).attr('data-save_id');
    
    var clicked_id = $(this).attr("id");

    $('[data-nfieldu="'+save_id+'"]').text($('#name').val());

    
    var status = 5;
    
    if(clicked_id==="save_now") {
        if(isNormalInteger($('#custom_state').val())) {
            status = parseInt($('#custom_state').val());
        }
    }
    
    if(clicked_id==="finish_now") {
        status = 10;
    }
    
    if(status>20) {
        status = 20;
    }
    
    var product = {};
    product["id"] = save_id;
    product["status"] = status;
    product["productName___de_AT"] = $('#name').val();
    product["productDescription___de_AT"] = $('#description').val();
    product["notice"] = $('#notice').val();
    
    $.each(product_simple_properties,function(key,value) {
        
        if(product_simple_properties_nofloat.indexOf(value) > -1) {
            product[value] = $('#'+value).val();
        } else if($("#" + value).length > 0) {
            var prval =  parseFloat( ($('#'+value).val()).replace(",","."));
            
            if(isNaN(prval)) {
                product[value] = undefined;
            } else {
                product[value] = prval;
            }
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
           $('tr[data-open_edit_id="'+save_id+'"] td:nth-child(2)').html('<span class="eds eds-state-'+status+'">'+status+'</span>');
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
     multiply = multiply / 100.0;
     
     var nutnames = ["energy","fat_total","fat_saturated","protein","fibers","calcium","carb","sugar","salt","lactose","natrium","bread_unit"];
     
     for(var i = 0;i<nutnames.length;i++) {
         
         var origval = $('#nutrient_100_'+nutnames[i]).val().replace(",",".");
         
         if(isNaN(origval) || origval==="") {
             $('#nutrient_snd_'+nutnames[i]).val("");
         } else {
             $('#nutrient_snd_'+nutnames[i]).val( ( parseFloat((  origval * multiply).toFixed(3))).toString().replace(".",",") );
         }
     }
     
 });
 
 
 function isNormalInteger(str) {
    var n = ~~Number(str);
    return String(n) === str && n >= 0;
}