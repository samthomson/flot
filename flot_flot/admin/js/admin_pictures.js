
$(function () {
	$('#fileupload').fileupload({
		dataType: 'json',
		done: function (e, data) {
			$.each(data.result.files, function (index, file) {
				$('<p/>').text(file.name).appendTo($("#upload_output"));
			});
		},
		fail: function (e, data) {
			console.log("upload failed: "+e+", "+data);
		}
	});
});