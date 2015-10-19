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
					'exposed_to_collection' => true,
					'editable' => true
				],	
				'url' => [
					'type' => 'string',
					'editor' => 'text',
					'value' => '',
					'exposed_to_collection' => true,
					'editable' => true
				],				
				'content' => [
					'type' => 'string',
					'editor' => 'textarea',
					'value' => '',
					'exposed_to_collection' => false,
					'editable' => true
				],				
				'published' => [
					'type' => 'boolean',
					'value' => false,
					'exposed_to_collection' => true,
					'editable' => false
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

			# vars to pass to the template
			$maParams = $this->aGetKeyValueProperties();

			$template = $twig->loadTemplate('page.html');

			$sGenerated = $template->render($maParams);

			$sUrl = $this->mGetProperty('url');

			// webpage requires a url
			if($sUrl !== '')
			{
				$sWritePath = $GLOBALS['files.www_path'].$sUrl;

				#die($sWritePath);
				FileController::bTempSaveFile($sWritePath, $sGenerated);	

			}else{
				// failed as no url/filepath to write to
				return false;
			}		
        }
		
	}