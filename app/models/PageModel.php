<?php

	class PageModel extends BaseModel
	{
		public function __construct() {
        	parent::__construct();

        	$this->amProperties = [
				'title' => [
					'type' => 'string',
					'editor' => 'text-input',
					'value' => ''
				]
			];

			$this->sType = "page";			
        }
		
	}