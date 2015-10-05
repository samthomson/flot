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

        public function render()
        {
        	// open theme template, parse in relevant values, write output to disk?
        	$loader = new Twig_Loader_Filesystem(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR .'storage/themes/default');
			$twig = new Twig_Environment($loader);

			$maParams = [];

			foreach($this->amProperties as $sKey => $aValue)
			{
				$maParams[$sKey] = $aValue['value'];
			}


			$template = $twig->loadTemplate('page.html');

			$sGenerated = $template->render($maParams);
			$sGenerated = $template->render(['title' => 'test']);


			//'fd: '.$sGenerated);

			$sWritePath = $GLOBALS['files.www_path'].'index.html';

			#die($sWritePath);
			FileController::bTempSaveFile($sWritePath, $sGenerated);			
        }
		
	}