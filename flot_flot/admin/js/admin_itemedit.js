
var editor;

$(document).ready(function() {
	//new Editor(document.getElementById("item_content_edit"), document.getElementById("item_content_preview"));

	editor = new MediumEditor('.editable');
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
/*
function Editor(input, preview) {
    this.update = function () {
		var html_from_markdown = markdown.toHTML(input.value);
		preview.innerHTML = html_from_markdown;
		$("#content_html").val(html_from_markdown);
    };
    input.editor = this;
    this.update();
  }



$('.editable').each(function() {
    var $this = $(this);
    var styles = $this.attr('style');
    if (typeof styles != 'undefined') {
        styles = ' style="' + styles + '"';
    }

    $this.wrap('<div class="editable-wrapper"/>');
    var $w = $(this).parent();
    $w.prepend('<div class="editable" ' + styles + ' data-placeholder="'+$this.attr('placeholder')+'">' + $this.val()+'</div>');
    $this.hide();
    var editor = new MediumEditor('.editable-wrapper');
});
$('form').submit(function(){
    $('.editable-wrapper').each(function(){
    	var s_newhtml = $(this).find('.editable').html();
        $(this).find('textarea').val(s_newhtml);
        console.log("content now: " + s_newhtml)
    });
});*/