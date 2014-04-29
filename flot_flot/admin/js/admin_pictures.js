
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