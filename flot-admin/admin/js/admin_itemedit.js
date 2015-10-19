
// item edit document ready
$(document).ready(function() {
    
    
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
            $("#published").val(true);
            break;
        case "unpublished":
            // set as unpublished
            $("#published").val(false);
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