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


toggleList = function(){
  $("#table-container").toggle(300);
}

// clicking a product within the list --> get the product via ajax and display the edit fields
$(document).on('click','*[data-open_edit_id]',function() {
   
    $.ajax({url: "/?productjson="+$(this).attr('data-open_edit_id'), success: function(result){
        var product = JSON.parse(result);
        
        console.log(product);
        
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