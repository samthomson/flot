
$(function () {
	$('#fileupload').fileupload({
		dataType: 'json',
		done: function (e, data) {
			$.each(data.result.files, function (index, file) {
				$('<p/>').text(file.name).appendTo($("#upload_output"));
			});
	        $("#upload_output").html("");
	        _pic_search();
		},
		fail: function (e, data) {
			console.log("upload failed: "+e+", "+data);
		},
		progressall: function (e, data) {
	        var progress = parseInt(data.loaded / data.total * 100, 10);
	        $('#progress .bar').css(
	            'width',
	            progress + '%'
	        );
	        $("#upload_output").html("uploading.. "+progress+"%");
	    }
	});
});

function _pic_search(s_term, s_mode, i_page){
	if(i_page === null)
		i_page = 1;
	if(s_term === null)
		s_term = "";


	$.get('/flot_flot/admin/search_pics.php',{"term": s_term, "mode": s_mode}, function(data){
		$("#picture_browser_results").html(data);
	});
}

var s_file_selected = "";
var s_file_size = "medium";

function selected_picture(s_filename){
	s_file_selected = "/" + s_upload_dir + "/" + s_file_size + "/" + s_filename;
	console.log(s_filename);
	chooseFile();
}

var sa_selected = [];

function select_picture(s_filename){
	if($.inArray(s_filename, sa_selected) > -1){
		// file already there, deselect it
		sa_selected.splice(sa_selected.indexOf(s_filename),1);
	}else{
		// add to selected
		sa_selected.push(s_filename);
	}
	show_selected_pics();
}
function show_selected_pics(){
	var html_selected_images = "";

	for(var cSelected = 0; cSelected < sa_selected.length; cSelected++){
		html_selected_images += '<img src="/flot_flot/uploads/tiny/'+sa_selected[cSelected]+'" />';
	}

	$("#file_browser_selected").html(html_selected_images);

	if(sa_selected.length > 0){
		// enable insert button
		$("#file_browser_insert_selected").removeClass('disabled');
	}else{
		// disable insert button
		$("#file_browser_insert_selected").addClass('disabled');
	}
}
function insert_selected_pictures(s_upload_dir, s_size){
	var html_selected_images = "";
	for(var cSelected = 0; cSelected < sa_selected.length; cSelected++){
		html_selected_images += '<img src="/'+s_upload_dir+'/'+s_size+'/'+sa_selected[cSelected]+'" />';
	}
	CKEDITOR.instances.wysiwyg_editor.insertHtml(html_selected_images);
	$('#file_browser_modal').modal('hide');
	// reset selected
	sa_selected = [];
}
