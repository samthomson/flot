
$(document).ready(function() {
	new Editor(document.getElementById("item_content_edit"), document.getElementById("item_content_preview"));
});  
function Editor(input, preview) {
    this.update = function () {
      preview.innerHTML = markdown.toHTML(input.value);
    };
    input.editor = this;
    this.update();
  }