
$(document).ready(function() {
	new Editor(document.getElementById("item_content_edit"), document.getElementById("item_content_preview"));
});  
function Editor(input, preview) {
    this.update = function () {
		var html_from_markdown = markdown.toHTML(input.value);
		preview.innerHTML = html_from_markdown;
		$("#content_html").val(html_from_markdown);
    };
    input.editor = this;
    this.update();
  }