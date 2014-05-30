
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
    //CKEDITOR.config.extraAllowedContent = 'img[src,alt,width,height],h1,h2,h3,h4,h5,h6,h7,span(*)';
    CKEDITOR.config.extraPlugins = 'autogrow';
    CKEDITOR.config.autoGrow_onStartup = true;
    // ALLOW <i></i>
    CKEDITOR.config.protectedSource.push(/<i[^>]*><\/i>/g);
    CKEDITOR.config.allowedContent = true;

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
    $("#item_parent").change(function(){
        _set_url_from_title();
    });
});  

function _set_url_from_title(){
    //var s_slug = encodeURIComponent('/' + $("#item_edit_title").val() + '/');
    var s_parent = $("#item_parent option:selected").text();
    if(s_parent !== ""){
        s_parent = s_make_slug(s_parent) + '/';
    }
    var s_slug = '/' + s_parent + s_make_slug($("#item_edit_title").val()) + '/';
    $(".item_edit_url").val(s_slug);
}
function s_make_slug(slugcontent)
{
    // found here: http://www.netvivs.com/build-permalinks-slug-with-javascript-jquery/
    // convert to lowercase (important: since on next step special chars are defined in lowercase only)
    slugcontent = slugcontent.toLowerCase();
    // convert special chars
    var   accents={a:/\u00e1/g,e:/u00e9/g,i:/\u00ed/g,o:/\u00f3/g,u:/\u00fa/g,n:/\u00f1/g}
    for (var i in accents) slugcontent = slugcontent.replace(accents[i],i);

    var slugcontent_hyphens = slugcontent.replace(/\s/g,'-');
    var finishedslug = slugcontent_hyphens.replace(/[^a-zA-Z0-9\-]/g,'');
    finishedslug = finishedslug.toLowerCase();
    console.log("finished slug: "+finishedslug);
    return finishedslug;
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