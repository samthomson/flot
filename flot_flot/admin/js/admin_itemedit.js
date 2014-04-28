
$(document).ready(function() {
    CKEDITOR.replace('wysiwyg_editor',
        {
            filebrowserBrowseUrl: '/flot_flot/admin/?section=pictures&action=select'
        });
});  


function publish(s_publish_status){
    console.log("set status to: " + s_publish_status);
    switch(s_publish_status){
        case "published":
            // set as published
            $("#published").val("true");
            break;
        case "unpublished":
            // set as unpublished
            $("#published").val("false");
            break;
    }
    $("#item_edit_form").submit();
}