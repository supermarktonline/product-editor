/**
 * Created by david on 3/15/16.
 */


// clicking a product within the list --> get the product via ajax and display the edit fields
$(document).on('click','*[data-open_edit_id]',function(e) {

    stopAutosave();

    $('#isreserved-container').hide();

    if($(e.target).hasClass("reserve") || $(e.target).parent().hasClass("reserve")) {
        return;
    }


    $('[data-open_edit_id]').removeClass('active');

    $(this).addClass('active');

    $.ajax({url: "/?productjson="+$(this).attr('data-open_edit_id'), success: function(result){

        $('#main-container').show();

        var product = JSON.parse(result);

        var username = $('#claim_name').val();
        $('#form-readonly').show();
        $('#edit-form').prop('disabled', true);
        if(product["reserved_by"]!="") {

            if(username !== product["reserved_by"]) {
                $('#isreserved-container').show();
                if(!is_admin) {
                    $('#main-container').hide();
                    return;
                }
            }
            
            $('#form-readonly').hide();
            $('#edit-form').prop('disabled', false);
        }
        if (is_admin) {
            $('#form-readonly').hide();
            $('#edit-form').prop('disabled', false);
        }
        
        if (username == "") {
            $('#name-required-container').show();
            $('#main-container').hide();
            return;
        }  else {
            $('#name-required-container').hide();
        }

        $('#last_state').attr('data-last_state',product["status"]);

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
        var imagesAr = images.split(/[;,]/);

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

        // container
        var container = product["container"];

        if(container!="") {
            if($("#container option[value='"+container+"']").length > 0) {
                $('#container').val(container);
                $('#container_custom').val('');
            } else {
                $('#container').val('');
                $('#container_custom').val(container);
            }
        } else {
            $('#container').val('');
            $('#container_custom').val('');
        }



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

        var images_product = product["productImages"].split(/[;,]/).map(function(e){return e.trim();});

        var images_article = product["articleImages"].split(/[;,]/).map(function(e){return e.trim();});

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

        startAutosave();

    }});

});