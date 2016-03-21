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
    "store"
];

var product_simple_properties_nofloat = [
  "notice","nutrient_unit","nutrient_snd_additional","nutrient_snd_additional_de","origin","store"
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
       source: function(request, response) {
        var filteredArray = $.map(ingredient_names, function(item) {
            if((item.toLowerCase()).indexOf((request.term).toLowerCase()) == 0){
                return item;
            } else {
                return null;
            }
        });
        response(filteredArray);
        },
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
        source: function(request, response) {
            var filteredArray = $.map(taggroup_labels, function(item) {
                if((item.toLowerCase()).indexOf((request.term).toLowerCase()) == 0){
                    return item;
                } else {
                    return null;
                }
            });
            response(filteredArray);
        },
        select: function(event,ui) {
            event.preventDefault();
            $('#tag_group_delete_selector').val(ui.item.label);
            $('#tag_group_delete_selected_id').val(ui.item.value);
        }
    });

    $('#tag_group_selector').autocomplete({
        source: function(request, response) {
            var filteredArray = $.map(taggroup_labels, function(item) {
                if((item.toLowerCase()).indexOf((request.term).toLowerCase()) == 0){
                    return item;
                } else {
                    return null;
                }
            });
            response(filteredArray);
        },
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
        source: function(request, response) {
            var filteredArray = $.map(tag_labels, function(item) {
                if((item.toLowerCase()).indexOf((request.term).toLowerCase()) == 0){
                    return item;
                } else {
                    return null;
                }
            });
            response(filteredArray);
        },
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