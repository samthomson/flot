
$(document).ready(function() {
    /*
    set up wysiwyg editor
    */
    CKEDITOR.config.removePlugins = 'forms, flash,iframe';
    CKEDITOR.replace('wysiwyg_editor',
        {
            filebrowserBrowseUrl: '/flot_flot/admin/?section=pictures&action=select',
            extraPlugins : 'flot_pictures',
            toolbar :
            [
                [ 'Bold', 'Underline', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink', '-', 'Blockquote', 'CreateDiv', 'Table', 'HorizontalRule', 'TextColor' ],
                [ 'Pictures' ],
                [ 'Source' ],
            ]
        });
    CKEDITOR.config.extraAllowedContent = 'img[src,alt,width,height],h1,h2,h3,h4,h5,h6,h7';
    CKEDITOR.config.extraPlugins = 'autogrow';
    CKEDITOR.config.autoGrow_onStartup = true;
    // ALLOW <i></i>
    CKEDITOR.config.protectedSource.push(/<i[^>]*><\/i>/g);
    CKEDITOR.config.extraAllowedContent = 'span(*)';

    /*
    url stuff, slug title into url if auto is checked
    */
    $("#item_edit_title").keyup(function() {
        if($("#item_edit_auto_url").is(':checked')){
            _set_url_from_title();
        }
    });
    $("#item_edit_url").keyup(function() {
        if(!$("#item_edit_auto_url").is(':checked')){
            $("input[type=hidden].item_edit_url").val($("#item_edit_url").val());
        }
    });
    $("#item_edit_auto_url").change(function() {
        if($("#item_edit_auto_url").is(':checked')){
            // disable url input
            $('#item_edit_url').prop('disabled', true);
            // make sure url is based on title
            _set_url_from_title();
        }else{
            // enable url input
            $('#item_edit_url').prop('disabled', false);
        }
    });
});  

function _set_url_from_title(){
    //var s_slug = encodeURIComponent('/' + $("#item_edit_title").val() + '/');
    var s_slug = '/' + $("#item_edit_title").val().toLowerCase() + '/';
    $(".item_edit_url").val(s_slug);
}


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