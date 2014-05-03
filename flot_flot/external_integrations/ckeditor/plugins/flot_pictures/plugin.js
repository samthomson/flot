CKEDITOR.plugins.add( 'flot_pictures',
{
	init: function( editor )
	{
		//Plugin logic goes here.
		editor.addCommand( 'insert_picture',
		{
			exec : function( editor )
			{    
				$('#file_browser_modal').modal('show');				
			}
		});
		editor.ui.addButton( 'Pictures',
		{
			label: 'Insert Picture',
			command: 'insert_picture',
			icon: this.path + 'image.PNG'
		} );
	}
} );