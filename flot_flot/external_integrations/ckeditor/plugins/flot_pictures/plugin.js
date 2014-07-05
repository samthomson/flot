CKEDITOR.plugins.add( 'flot_pictures',
{
	init: function( editor )
	{
		//Plugin logic goes here.
		editor.addCommand( 'insert_picture',
		{
			exec : function( editor )
			{
				// store the editor which the user clicked on, for when we have multiple editors
				o_active_ckeditor = editor;
				$('#file_browser_modal').modal('show');				
			}
		});
		editor.ui.addButton( 'Pictures',
		{
			label: 'Insert Picture',
			command: 'insert_picture',
			icon: this.path + '../icons.png'
		} );
	}
} );