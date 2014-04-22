/*(function() {
  // When using more than one `textarea` on your page, change the following line to match the one you’re after
  var textarea = document.getElementById('textarea'),
      preview = document.createElement('div'),
      convert = new Markdown.getSanitizingConverter().makeHtml;

  console.log(textarea);
  //console.log(preview);
  //console.log(convert);
  // Continue only if the `textarea` is found
  if (textarea) {
    preview.id = 'preview';
    // Insert the preview `div` after the `textarea`
    textarea.parentNode.insertBefore(preview, textarea.nextSibling);
    // instead of `onkeyup`, consider using `oninput` where available: http://mathiasbynens.be/notes/oninput
    textarea.onkeyup = function() {
      preview.innerHTML = convert(eTextarea.value);
    };
    // Trigger the `onkeyup` event
    textarea.onkeyup.call(eTextarea);
  }
}());
*/
$(function() {
  // When using more than one `textarea` on your page, change the following line to match the one you’re after
  var $textarea = $('#edit_item_markdown');     
  var $convert = new Markdown.getSanitizingConverter().makeHtml;

  //console.log(convert);
  // instead of `keyup`, consider using `input` using this plugin: http://mathiasbynens.be/notes/oninput#comment-1
  $textarea.keyup(function() {
  	console.log($convert($textarea.val()));
    $("#content_html_preview").html($convert($textarea.val()));
  }).trigger('keyup');
});
