
var editor;

$(document).ready(function() {
	//new Editor(document.getElementById("item_content_edit"), document.getElementById("item_content_preview"));

	editor = new MediumEditor('.editable');

    $('.editable').mediumInsert({
        editor: editor,
        addons: {
          images: {}
        }
      });
});  

function editor_update(){
	//$("#content").val($("#medium_editor").val());
	//var s_newhtml = $(this).find('.editable').html();
	var s_newhtml = $("#medium_editor").html();
    //console.log("content now: " + s_newhtml);
    $("#content_html").val(s_newhtml);
}
$('.editable').on('input', function() {
  // Do some work
  editor_update();
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