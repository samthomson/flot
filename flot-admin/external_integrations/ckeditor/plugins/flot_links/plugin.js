CKEDITOR.plugins.add( 'flot_links',
{
	init: function( editor )
	{
		//Plugin logic goes here.
		editor.addCommand( 'insert_link',
		{
			exec : function( editor )
			{
				// store the editor which the user clicked on, for when we have multiple editors
				o_active_ckeditor = editor;

				_modal_set_title("Enter or choose a link");
				_modal_set_body('<i class="glyphicon glyphicon-refresh spinning"></i> Loading');
				_modal_show();

				$.get('/flot-admin/admin/?section=flot&action=list_pages', function(data){
						var html_link_choice = '<h3>Insert a web url</h3><div class="input-group">      <input type="text" class="form-control" id="insert_external_link">      <span class="input-group-btn">        <button class="btn btn-default" type="button" onclick="insert_external_link()">Insert</button></span></div>';

						if(data.length){
							html_link_choice += '<h3>or Choose a page from this site</h3><table class="table table-hover"><thead><tr><th>Page</th><th>URL</th></tr></thead>';

							data.forEach(function(page){
								html_link_choice += '<tr onclick="return_link(\''+page.url+'\',\''+page.title+'\')"><td>'+page.title+'</td><td>'+page.url+'</td></tr>';
							});
							

							html_link_choice += '</table>';
						}

						_modal_set_body(html_link_choice);
					});	
			}
		});
		editor.ui.addButton( 'Links',
		{
			label: 'Insert link',
			command: 'insert_link',
			icon: this.path + '../icons.png'
		} );
	}
} );

function return_link(s_link, s_title){

	s_link = '<a href="'+s_link+'">'+s_title+'</a>';


	if(o_active_ckeditor !== null){
		o_active_ckeditor.insertHtml(s_link);
	}
	_modal_clear();
	_modal_hide();
}

function insert_external_link(){
	var s_link = $("#insert_external_link").val();
	return_link(s_link, s_link);
}