// list items document ready
$(document).ready(function() {
    $(".item_delete_start").click(function(){
        _enable_delete();
    });
    $(".item_delete_done").click(function(){
        _disable_delete();
    });
});



//
// item list JS
//

function _enable_delete(){
    $(".item_delete").show();
    $(".item_delete_start").hide();
    $(".item_delete_done").show();
    setTimeout(_disable_delete, 5000);
}

function _disable_delete(){
    $(".item_delete").hide();
    $(".item_delete_start").show();
    $(".item_delete_done").hide();
}