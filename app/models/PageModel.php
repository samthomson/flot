<?php

	class PageModel extends BaseModel
	{
		public function __construct() {
        	parent::__construct();

        	$this->amProperties = [
				'title' => [
					'type' => 'string',
					'editor' => 'text',
					'value' => '',
					'exposed_to_collection' => true
				],				
				'content' => [
					'type' => 'string',
					'editor' => 'textarea',
					'value' => '',
					'exposed_to_collection' => false
				]
			];

			$this->sType = "page";	
			$this->sCollection = "PageCollectionModel";			
        }
		
	}