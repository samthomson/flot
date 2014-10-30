var o_active_ckeditor = null;
// item edit document ready
$(document).ready(function() {
    /*
    set up wysiwyg editor
    */
    CKEDITOR.timestamp = "7";
    CKEDITOR.config.removePlugins = 'forms, flash,iframe,image,link';
    $('.ckeditor').each(function(){
        CKEDITOR.replace($(this).attr('id'),
        {
            filebrowserBrowseUrl: '/flot-admin/admin/?section=pictures&action=select',
            extraPlugins : 'flot_pictures,flot_links,autogrow',
            toolbar :
            [
                [ 'Bold', 'Underline', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Links', 'Unlink', '-', 'Blockquote', 'CreateDiv', 'Table', 'HorizontalRule', 'TextColor' ],
                [ 'Pictures' ],
                ['Format'],
                ['Source' ],
                ['Maximize']
            ]
        });
    });
    //CKEDITOR.config.extraAllowedContent = 'img[src,alt,width,height],h1,h2,h3,h4,h5,h6,h7,span(*)';
    //CKEDITOR.config.extraPlugins = 'autogrow';
    // ALLOW <i></i>
    //CKEDITOR.config.autoGrow_minHeight = 100;
    CKEDITOR.config.protectedSource.push(/<i[^>]*><\/i>/g);
    CKEDITOR.config.allowedContent = true;

    /*
    url stuff, slug title into url if auto is checked
    */
    // title text input
    $('#item_edit_title').each(function() {
        var elem = $(this);

        // Save current value of element
        elem.data('oldVal', elem.val());

        // Look for changes in the value
        elem.bind("propertychange keyup input paste", function(event){
            // If value has changed...
            if (elem.data('oldVal') != elem.val()) {
                // Updated stored value
                elem.data('oldVal', elem.val());

                // Do action
                if($("#item_edit_auto_url").is(':checked')){
                    _set_url_from_title();
                }
            }
        });
    });
    CKEDITOR.config.skin = 'flot';

    // url text input
    $('#item_edit_url').each(function() {
        var elem = $(this);

        // Save current value of element
        elem.data('oldVal', elem.val());

        // Look for changes in the value
        elem.bind("propertychange keyup input paste", function(event){
            // If value has changed...
            if (elem.data('oldVal') != elem.val()) {
                // Updated stored value
                elem.data('oldVal', elem.val());

                // Do action
                if(!$("#item_edit_auto_url").is(':checked')){
                    $("input[type=hidden].item_edit_url").val($("#item_edit_url").val());
                }
            }
        });
     });
    $("#item_edit_url").keyup(function() {
        if(!$("#item_edit_auto_url").is(':checked')){
            $("input[type=hidden].item_edit_url").val($("#item_edit_url").val());
        }
    });

    // checkbox
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
    // drop down parent select
    $("#item_parent").change(function(){
        _set_url_from_title();
    });

    /* preview page changes */
    $("#preview_edits").click(function(){
        
        _modal_set_title("Preview");
        _modal_set_body('<i class="glyphicon glyphicon-refresh spinning"></i> Loading');
        _modal_show();
        

        var s_item_id = '';
        s_item_id = $("[name=item_id]").val();

        var data = $('#item_edit_form').serializeArray();
        data.push({name: 'section', value: "items"});
        data.push({name: 'action', value: "edit"});
        data.push({name: 'item_id', value: s_item_id});
        data.push({name: 'preview', value: true});

        $("textarea.ckeditor").each(function(){
            data.push({name: $(this).attr("name"), value: CKEDITOR.instances[$(this).attr("id")].getData()});
        });

        $.post('/flot-admin/admin/', data, function(data){
            var html_preview = 'There was a problem generating the preview.';

            if(data.length){
                html_preview = data;
            }

            _modal_set_body(html_preview);
        }); 
    });
});  



//
// item edit JS
//

function _set_url_from_title(){
    //var s_slug = encodeURIComponent('/' + $("#item_edit_title").val() + '/');
    var s_parent = $("#item_parent option:selected").text();
    if(s_parent !== ""){
        s_parent = s_make_slug(s_parent) + '/';
    }
    var s_slug = '/' + s_parent + s_make_slug($("#item_edit_title").val()) + '/';

    if(sa_page_urls.indexOf(s_slug) > -1){
        s_slug = s_slug.slice(0, -1);
        s_slug += "-2/";
        $("#url_input").addClass("has-error");
    }else{
        $("#url_input").removeClass("has-error");
    }

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
    return finishedslug;
}

function publish(s_publish_status){
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
function _make_home_page(){
    // set form fields according to home page and submit form

    // uncheck auto url    
    $('#item_edit_auto_url').prop('checked', false);

    // set url index
    $('#item_edit_url').val('index.html');
    $("input[type=hidden].item_edit_url").val($("#item_edit_url").val());

    $("#item_edit_form").submit();
}