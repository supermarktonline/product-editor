/**
 * Created by david on 12/22/15.
 */

$(document).on('click','#tag_group_new_create',function(e) {
    e.preventDefault();

    var taggroup = {
        muid:   $('#tag_group_new_muid').val(),
        name:   $('#tag_group_new_name').val()
    };

    $.ajax({ type:"POST", url: "/?taggroup=create", data:taggroup, success: function(result){

        var dec = JSON.parse(result);

        // success
        if(dec["id"]>0) {
            $('#message_container').html('<div class="umsg success">New Tag group successfully created.</div>');

            // TODO: Add tag group to the tag groups array
            $('#tag_group_new_muid,#tag_group_new_name').val('');
            taggroups.push(dec);
            taggroup_labels.push({label: dec["name"]+" ("+dec["muid"]+")",value: dec["id"]});

        } else {
            $('#message_container').html('<div class="umsg error">'+dec["error"]+'</div>');
        }
    }});


});