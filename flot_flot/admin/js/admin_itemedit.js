
$(document).ready(function() {
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
    CKEDITOR.config.extraAllowedContent = 'img[src,alt,width,height]';
    CKEDITOR.config.extraPlugins = 'autogrow';
    CKEDITOR.config.autoGrow_onStartup = true;
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