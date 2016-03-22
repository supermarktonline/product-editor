/**
 * Created by david on 3/15/16.
 */


$(document).on('click','.save_current_product',function() {
    var status = $(this).attr("data-state");
    saveCurrentProduct(status);
});


function saveCurrentProduct(status) {

    var status = parseInt(status);

    if(status<0 || status===undefined || isNaN(status)) {
        $('#message_container').html('<div class="umsg error">Nicht gespeichert: Status-Problem.</div>');
        return false;
    }

    var save_id = $('#save_id').attr('data-save_id');

    $('[data-nfieldu="' + save_id + '"]').text($('#name').val());
    $('[data-nfieldb="' + save_id + '"]').text($('#brand').val());


    // for status allowance we have to check the state of the gs1 tags first
    var ntags = [];
    var gs1tag_unselected = false;

    $('.gs1tag select').each(function () {
        var ar = {};
        var val = $(this).val();

        if (parseInt(val) == "undefined" || isNaN(parseInt(val)) || parseInt(val) < 1) {
            gs1tag_unselected = true;
        } else {
            ar["tag_id"] = val;
            ar["numerical_value"] = null;
            ntags.push(ar);
        }
    });

    if (isNormalInteger($('#custom_state').val())) {
        status = parseInt($('#custom_state').val());
    }

    if (status == 5 && ($('#notice').val()).trim().length > 0) {
        status = 6;
    } else if(status==6) {
        status = 5;
    }

    if (status == 10 && ($('#notice').val()).trim().length > 0) {
        status = 11;
    }

    if (status > 20) {
        status = 20;
    }

    if (status > 9) {
        var honig_in = $('#art_ingr_honig').is(":checked");
        var honig_out = $('#check_no_honey').is(":checked");

        var meat_in = $('#art_ingr_fleisch').is(":checked");
        var meat_out = $('#check_no_meat').is(":checked");

        if (!(honig_in ^ honig_out)) {
            $('#message_container').html('<div class="umsg error">Nicht gespeichert: Ist Honig enthalten oder nicht?</div>');
            return false;
        }

        if (!(meat_in ^ meat_out)) {
            $('#message_container').html('<div class="umsg error">Nicht gespeichert: Ist Fleisch enthalten oder nicht?</div>');
            return false;
        }

        if (gs1tag_unselected) {
            $('#message_container').html('<div class="umsg error">Nicht gespeichert: Alle GS1 Tags müssen korrekt zugeordnet sein, Zuordnung fehlt.</div>');
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


    product["articleWeight"] = "";
    product["articleVolume"] = "";
    product["articleArea"] = "";
    product["articleLength"] = "";
    product["articleUses"] = "";

    // weight amount and weight amount unit calculations
    var weight_amount = parseFloat($('#weight_amount').val());
    var weight_amount_unit = $('#weight_amount_unit').val();

    // add toFixed(10) to prevent double weirdness. Ex: 0.009 would otherwise become 0.009000000000000001
    if (!isNaN(weight_amount)) {
        switch (weight_amount_unit) {
            case "g":
            {
                // the changes the rounded string-number back to a number trimming the trailing 0s
                product["articleWeight"] = +((weight_amount * 0.001).toFixed(10)) + " kg";
                break;
            }
            case "kg":
            {
                product["articleWeight"] = weight_amount + " kg";
                break;
            }
            case "l":
            {
                product["articleVolume"] = weight_amount + " l";
            }
            case "ml":
            {
                product["articleVolume"] = +((weight_amount * 0.001).toFixed(10)) + " l";
            }
            case "m³":
            {
                product["articleVolume"] = weight_amount + " m³";
            }
            case "m²":
            {
                product["articleArea"] = weight_amount + " m²";
            }
            case "m":
            {
                product["articleLength"] = weight_amount + " m";
            }
            case "uses":
            {
                product["articleUses"] = parseInt(weight_amount);
            }
        }
    }


    // category
    if ($('#cs_brick select').length > 0) {

        var id = parseInt($('#cs_brick select:first-child option:selected').attr("data-categoryid"));

        if (!isNaN(id) && id > 0) {
            product["category"] = id;
        }
    }


    $.each(product_simple_properties, function (key, value) {

        if (product_simple_properties_nofloat.indexOf(value) > -1) {
            product[value] = $('#' + value).val();
        } else if ($("#" + value).length > 0) {
            var prval = parseFloat(($('#' + value).val()).replace(",", "."));

            if (isNaN(prval)) {
                product[value] = undefined;
            } else {
                product[value] = prval;
            }
        }
    });

    // containers
    product["container"] = $('#container').val();

    if (product["container"] == "") {
        product["container"] = $("#container_custom").val();
    }

    if ($('#nutrient_snd_prepared').is(":checked")) {
        product["nutrient_snd_prepared"] = true;
    } else {
        product["nutrient_snd_prepared"] = false;
    }

    for (var i = 0; i < allergene.length; i++) {
        if ($('#art_ingr_' + allergene[i]).is(":checked")) {
            product["allergen_" + allergene[i]] = true;
        } else {
            product["allergen_" + allergene[i]] = false;
        }
    }

    $.ajax({
        type: "POST", url: "/?updateproduct", data: product, success: function (result) {
            if (result === "success") {
                $('#message_container').html('<div class="umsg success">Article updated successfully.</div>');
                $('tr[data-open_edit_id="' + save_id + '"] td:nth-child(2)').html('<span class="eds eds-state-' + status + '">' + status + '</span>');
                $('#last_state').attr('data-last_state', status);
            } else {
                $('#message_container').html('<div class="umsg error">' + result + '</div>');
            }
        }
    });


    // update all tags


    // standard tags
    $('.tag').each(function () {
        if ($(this).is(":checked")) {
            var ar = {};
            ar["tag_id"] = $(this).val();
            ar["numerical_value"] = null;
            ntags.push(ar);
        }
    });

    // numerical tags
    $('.numerical-tag').each(function () {
        var tv = ($(this).val()).trim();
        if (tv != "") {
            var ar = {};
            ar["tag_id"] = $(this).attr('data-tagid');
            ar["numerical_value"] = tv;
            ntags.push(ar);
        }
    });

    $.ajax({
        type: "POST",
        url: "/?tag_connection=update&fdata_id=" + save_id,
        data: {cons: ntags},
        success: function (result) {
            if (result === "success") {
                $('#message_container').append('<div class="umsg success">Article tags updated successfully.</div>');
            } else {
                $('#message_container').append('<div class="umsg error">' + result + '</div>');
            }
        }
    });

}