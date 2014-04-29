CKEDITOR.plugins.add( 'flot_pictures',
{
	init: function( editor )
	{
		//Plugin logic goes here.
		editor.addCommand( 'insert_picture',
		{
			exec : function( editor )
			{    
				var timestamp = new Date();
				editor.insertHtml( 'The current date and time is: <em>' + timestamp.toString() + '</em>' );
			}
		});
		editor.ui.addButton( 'Pictures',
		{
			label: 'Insert Timestamp',
			command: 'insert_picture'/*,
			icon: this.path + 'images/timestamp.png'*/
		} );
	}
} );