<?php
    require_once('../../../flot-admin/core/base.php');
    // spit out correct mime type
    header('Content-type: text/javascript');

?>
// start a periodic post to keep users session alive
(function worker() {
  $.ajax({
    url: '<?php echo S_BASE_EXTENSION; ?>flot-admin/admin/', 
    type: "POST",
    success: function(data) {
    },
    complete: function() {
      // Schedule the next request when the current one's complete
      setTimeout(worker, 30000);
    }
  });
})();


/*
#_reusable_modal_stuff
*/
function _modal_set(html_title, html_body, html_buttons){
    _modal_set_title(html_title);
    _modal_set_body(html_body);
    if(html_buttons === undefined)
        _modal_set_footer_buttons();
    else
        _modal_set_footer_buttons(html_buttons);
}
function _modal_set_title(html_title){
    $("#flot_modal_label").html(html_title);
}
function _modal_set_body(html_body){
    $("#flot_modal_body").html(html_body);
}
function _modal_set_footer_buttons(html_buttons){
    var html_footer_buttons = '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>';
    if(html_buttons !== undefined){
        // cancel button which closes modal as a default
        html_footer_buttons = html_buttons;
    }
    $("#flot_modal_footer").html(html_footer_buttons);
}
function _modal_show(){
    $('#flot_modal').modal('show');
}
function _modal_hide(){
    $('#flot_modal').modal('hide');
}
function _modal_clear(){
    _modal_set_title('');
    _modal_set_body('');
    _modal_set_footer_buttons('');
}