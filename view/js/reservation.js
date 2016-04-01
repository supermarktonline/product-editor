/**
 * Created by david on 2/26/16.
 */

$(document).on('click','[data-res]',function(e) {

    var username = $('#claim_name').val();

    if(username=="") {
        e.preventDefault();
        alert("Bitte einen Benutzernamen eintragen.");
        return;
    }

    var pid = $(this).attr('data-res');
    $('#form-readonly').show();
    $('#edit-form').prop('disabled', true);

    // if the box is checked
    if($(this).is(":checked")) {

    // reserve for current user
    $.ajax({url: "/?reserve=1&pid="+pid+"&user="+username, success: function(result) {

            if(result!="success") {
                $('[data-res='+pid+']').prop("checked",false);
                alert(result);
            } else {
                $('[data-res='+pid+'] + span').html(username);
                $('#form-readonly').hide();
                $('#edit-form').prop('disabled', false);
            }
        }
    });

    } else {

        var suspected_current_owner = $(this).next().html()

        var r = true;

        if(suspected_current_owner!=username) {

            if(is_admin) {
                r = confirm("Dieses Produkt ist von einem anderen Benutzer reserviert. Reservierung trotzdem aufheben?")
            } else {
                alert("Dieses Produkt ist von einem anderen Benutzer reserviert. Sie können es nur bearbeiten, wenn diese Reservierung nicht mehr besteht.");
                r = false;
            }

        }

        if(r) {
            $.ajax({url: "/?reserve=0&pid="+pid+"&user="+username, success: function(result) {

                if(result!="success") {
                    $('[data-res='+pid+']').prop("checked",true);
                    alert(result);
                } else {
                    $('[data-res='+pid+'] + span').html("");
                }
            }
            });
        } else {
            e.preventDefault();
            return;
        }

    }




});