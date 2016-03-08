var numerical_tags_map = {
    numeric: "",
    percent: "%",
    kilogram: "kg",
    gram: "g",
    milligram: "mg",
    liter: "l",
    milliliter: "ml",
    seconds: "s",
    minutes: "m",
    hours: "h",
    days: "d",
    permill: "‰",
    squaremeters: "m²",
    cubicmeters: "m³"
};

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
    "nutrient_snd_bread_unit",

    "origin",
    "store",
    "container"
];

var product_simple_properties_nofloat = [
  "notice","nutrient_unit","nutrient_snd_amount","nutrient_snd_additional","nutrient_snd_additional_de","origin","store","container"
];


var allergene = [
    "a","b","c","d","e","f","g","h","l","m","n","o","p","r","honig","fleisch"
];

var ingredients;

var ingredient_names = [];

var tags;

var tag_labels = [];

var media_path = "";

var taggroups;

var taggroup_labels = [];


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

    // tag groupings
    taggroups = JSON.parse($('#taggroups').html());

    for(var i = 0; i < taggroups.length; i++) {
        taggroup_labels.push({label: taggroups[i]["name"]+" ("+taggroups[i]["muid"]+")",value: taggroups[i]["id"]});
    }

    $('#tag_group_delete_selector').autocomplete({
        source: taggroup_labels,
        select: function(event,ui) {
            event.preventDefault();
            $('#tag_group_delete_selector').val(ui.item.label);
            $('#tag_group_delete_selected_id').val(ui.item.value);
        }
    });

    $('#tag_group_selector').autocomplete({
        source: taggroup_labels,
        select: function(event,ui) {
            event.preventDefault();
            $('#tag_group_selector').val(ui.item.label);
            $('#tag_group_selected_id').val(ui.item.value);
        }
    });

   
   media_path = $('#media_path').text();
   
   // initialize the tags
   tags = JSON.parse($('#tags').html());

    for(var i = 0; i < tags.length; i++) {
        tag_labels.push({label: tags[i]["name_de"]+" ("+tags[i]["muid"]+")",value: tags[i]["id"]});
    }

    $('#tag_delete_selector').autocomplete({
        source: tag_labels,
        select: function(event,ui) {
            event.preventDefault();
            $('#tag_delete_selector').val(ui.item.label);
            $('#tag_delete_selected_id').val(ui.item.value);
        }
    });

    // for the interface, seperate numerical tags and standard tags

    var standard_tags = [];
    var numerical_tags = [];

    for(var i = 0; i < tags.length; i++) {

        if(tags[i]["type"]) {
            numerical_tags.push(tags[i]);
        } else {
            standard_tags.push(tags[i]);
        }
    }

    initializeStandardTags(standard_tags);

    initializeNumericalTags(numerical_tags);
   
});


toggleList = function(){
  $("#table-container").toggle(300);
}

// clicking a product within the list --> get the product via ajax and display the edit fields
$(document).on('click','*[data-open_edit_id]',function(e) {

    $('#isreserved-container').hide();

    if($(e.target).hasClass("reserve") || $(e.target).parent().hasClass("reserve")) {
        return;
    }


    $('[data-open_edit_id]').removeClass('active');

    $(this).addClass('active');
   
    $.ajax({url: "/?productjson="+$(this).attr('data-open_edit_id'), success: function(result){
            
        $('#main-container').show();

            
        var product = JSON.parse(result);

        if(product["reserved_by"]!="") {
            var username = $('#claim_name').val();

            if(username !== product["reserved_by"]) {
                $('#isreserved-container').show();
                if(!is_admin) {
                    $('#main-container').hide();
                    return;
                }
            }

        }

        $('#message_container').html('');
        
        $('#save_id').attr('data-save_id',product["id"]);
        $('#custom_state').val('');
        
        $('#name').val(product["productName de_AT"]);
        $('#description').val(product["productDescription de_AT"]);
        $('#brand').val(product["productBrand de_AT"]);
        $('#notice').val(product["notice"]);
        $('#company').val(product["productCorporation de_AT"]);


        // weight amount and weight amount unit calculations
        var pcSets = ["articleWeight","articleVolume","articleArea","articleLength","articleUses"];

        var did = false;

        for(st in pcSets) {
            var val = product[pcSets[st]];
            if(val!="") {
                var parts = val.split(" ");
                $('#weight_amount').val(parts[0]);

                did=true;

                if(typeof parts[1] !== 'undefined') {
                    $('#weight_amount_unit').val(parts[1]);
                } else {
                    $('#weight_amount_unit').val("uses");
                }
            }
        }

        if(!did) {
            $('#weight_amount').val('');
        }


        
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

        // nutrients for prepared meal?
        if(product["nutrient_snd_prepared"]===true) {
            $('#nutrient_snd_prepared').prop("checked",true);
        } else {
            $('#nutrient_snd_prepared').prop("checked",false);
        }
        
        // nutrient unit
        $('#nutrient_unit_copy').html(product["nutrient_unit"]);
        
        
        // allergene / ingredients
        $('#ingredients_collector,#enthalt_spuren_collector,#enthalt_gering_collector').html('');

        // taggroups
        // clear tag area
        $('#tag_group_new_muid,#tag_group_new_name,#tag_uid_new,#tag_name_new,#tag_name_at_new,#tag_numerical_new,#tag_delete_selector,#tag_group_delete_selector').val('');
        $('#tag_group_selected_id,#tag_group_delete_selected_id,#tag_delete_selected_id').val(0);
        $('#tag_group_new_numerical_required').attr('checked',false);

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

                        $('#check_no_honey,#check_no_meat').prop("checked",false);

                        for(var i = 0;i<allergene.length; i++) {

                            var aval = product["allergen_"+allergene[i]];
                            if(aval===true) {
                                $('[data-art_ingr="'+allergene[i]+'"]').prop("checked",true);
                            } else {
                                $('[data-art_ingr="'+allergene[i]+'"]').prop("checked",false);

                                if(aval === false && allergene[i] == "honig") {
                                    $('#check_no_honey').prop("checked",true);
                                }

                                if(aval === false && allergene[i] == "fleisch") {
                                    $('#check_no_meat').prop("checked",true);
                                }
                            }
                        }
                    }
                }
                });
            })(type);
        }

        // Show Categories and Tags
        $('.tag').each(function() {
           $(this).prop('checked', false);  
        });


        // Show numerical Tags
        $('.numerical-tag').each(function() {
            $(this).val("");
        });
        
        $.ajax({url: "/?tag_connection=get&fdata_id="+product["id"], success: function(result){
                
                var cons;
                
                try {
                    cons = JSON.parse(result);
                } catch(e) {
                    $('#message_container').html('<div class="umsg error">'+result+'</div>');
                    return;
                }

                if(product["status"]>0) {
                    // set the categories and populate the GS1 Tags
                    $('#cs_segment,#cs_family,#cs_class,#cs_brick').html('');

                    setCategorySelectorAndGS1Tags(product["category"],cons);
                }


                // populate the custom and the numerical tags
                for(var i = 0; i < cons.length; i++) {
                   
                    var sid = cons[i]["tag_id"];
                    
                    $('.tag[value="'+sid+'"]').each(function() {
                       $(this).prop("checked",true); 
                    });

                    $('.numerical-tag[data-tagid="'+sid+'"]').each(function() {
                       $(this).val(cons[i]["numerical_value"]);
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
$(document).on('click','.save_current_product',function() {
    var save_id = $('#save_id').attr('data-save_id');
    
    var clicked_id = $(this).attr("id");

    $('[data-nfieldu="'+save_id+'"]').text($('#name').val());
    $('[data-nfieldb="'+save_id+'"]').text($('#brand').val());

    
    var status = $(this).attr("data-state");

    if(isNormalInteger($('#custom_state').val())) {
        status = parseInt($('#custom_state').val());
    }

    if(status==5 && ($('#notice').val()).trim().length > 0) {
        status = 6;
    }

    if(status==10 && ($('#notice').val()).trim().length > 0) {
        status = 11;
    }
    
    if(status>20) {
        status = 20;
    }

    if(status>9) {
        var honig_in = $('#art_ingr_honig').is(":checked");
        var honig_out = $('#check_no_honey').is(":checked");

        var meat_in = $('#art_ingr_fleisch').is(":checked");
        var meat_out = $('#check_no_meat').is(":checked");

        if(!(honig_in ^ honig_out)) {
            $('#message_container').html('<div class="umsg error">Nicht gespeichert: Ist Honig enthalten oder nicht?</div>');
            return false;
        }

        if(!(meat_in ^ meat_out)) {
            $('#message_container').html('<div class="umsg error">Nicht gespeichert: Ist Fleisch enthalten oder nicht?</div>');
            return false;
        }

    }

    
    var product = {};
    product["id"] = save_id;
    product["status"] = status;
    product["productName___de_AT"] = $('#name').val();
    product["productDescription___de_AT"] = $('#description').val();
    product["productBrand___de_AT"] = $('#brand').val();
    product["notice"] = $('#notice').val();
    product["productCorporation___de_AT"] = $('#company').val();



    product["articleWeight"]="";
    product["articleVolume"]="";
    product["articleArea"]="";
    product["articleLength"]="";
    product["articleUses"]="";

    // weight amount and weight amount unit calculations
    var weight_amount = parseFloat($('#weight_amount').val());
    var weight_amount_unit = $('#weight_amount_unit').val();

    if(!isNaN(weight_amount)) {
        switch(weight_amount_unit) {
            case "g": {
                product["articleWeight"] = (weight_amount * 0.001)+" kg";
                break;
            }
            case "kg": {
                product["articleWeight"] = weight_amount + " kg";
                break;
            }
            case "l": {
                product["articleVolume"] = weight_amount + " l";
            }
            case "ml": {
                product["articleVolume"] = (weight_amount * 0.001)+" l";
            }
            case "m³": {
                product["articleVolume"] = weight_amount+" m³";
            }
            case "m²": {
                product["articleArea"] = weight_amount+" m²";
            }
            case "m": {
                product["articleLength"] = weight_amount+" m";
            }
            case "uses": {
                product["articleUses"] = parseInt(weight_amount);
            }
        }
    }


    // category
    if($('#cs_brick select').length > 0 ) {

        var id = parseInt($('#cs_brick select:first-child option:selected').attr("data-categoryid"));

        if(!isNaN(id) && id > 0) {
            product["category"] = id;
        }
    }

    
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

    if($('#nutrient_snd_prepared').is(":checked")) {
        product["nutrient_snd_prepared"] = true;
    } else {
        product["nutrient_snd_prepared"] = false;
    }

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
    
    
    // update all tags

    // gs1 tags
    var ntags = [];

    $('.gs1tag select').each(function() {
       var ar = {};
        ar["tag_id"] = $(this).val();
        ar["numerical_value"] = null;
        ntags.push(ar);
    });


    // standard tags
    $('.tag').each(function() {
        if($(this).is(":checked")) {
            var ar = {};
            ar["tag_id"] = $(this).val();
            ar["numerical_value"] = null;
            ntags.push(ar);
        }
    });

    // numerical tags
    $('.numerical-tag').each(function() {
       var tv = ($(this).val()).trim();
        if(tv!="") {
            var ar = {};
            ar["tag_id"] = $(this).attr('data-tagid');
            ar["numerical_value"] = tv;
            ntags.push(ar);
        }
    });

    $.ajax({ type:"POST", url: "/?tag_connection=update&fdata_id="+save_id, data: {cons: ntags}, success: function(result){
       if(result==="success") {
           $('#message_container').append('<div class="umsg success">Article tags updated successfully.</div>');
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

$(document).on('click','#show_status_info',function(e) {
    e.preventDefault();
   alert('Der Bearbeitungsstatus eines Produkts wird kurz durch eine Zahl dargestellt.\n\n0 = neu\n5 = bereits bearbeitet\n6 = bereits bearbeitet mit Anmerkungen\n7 = wird später bearbeitet\n8 = Bearbeitung problematisch\n10 = Bearbeitung abgeschlossen\n11=Bearbeitung abgeschlossen mit Anmerkungen\n15 = Produkt bereits exportiert\nAndere Zahl = Sonderstatus, vom Administrator festgelegt');
});