<?php

	class PageModel extends BaseModel
	{
		public function __construct() {
        	parent::__construct();

        	$this->amProperties = [
				'title' => [
					'type' => 'string',
					'editor' => 'text-input',
					'value' => '',
					'exposed_to_collection' => true
				]
			];

			$this->sType = "page";			
        }
		
	}