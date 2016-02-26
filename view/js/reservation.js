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

    // if the box is checked
    if($(this).is(":checked")) {

    // reserve for current user
    $.ajax({url: "/?reserve=1&pid="+pid+"&user="+username, success: function(result) {

            if(result!="success") {
                $('[data-res='+pid+']').prop("checked",false);
                alert(result);
            } else {
                $('[data-res='+pid+'] + span').html(username);
            }
        }
    });

    } else {

        var suspected_current_owner = $(this).next().html()

        var r = true;

        if(suspected_current_owner!=username) {

            r = confirm("Dieses Produkt ist von einem anderen Benutzer reserviert. Reservierung trotzdem aufheben?")

        }

        if(r) {
            $.ajax({url: "/?reserve=0&pid="+pid+"&user="+username+"&prev_user="+suspected_current_owner, success: function(result) {

                console.log(result);

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