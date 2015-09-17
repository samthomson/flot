<?php

	
	#namespace FlotCMS;

	class View{

		public static function render($sViewName = "index", $maParams = []){

			$loader = new Twig_Loader_Filesystem(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR .'views');
			$twig = new Twig_Environment($loader);

			$body = "unset";

			$aPassOn = [
				'flot_admin_base_url' => $GLOBALS['admin.flot_admin_base_url']
			];
			$saPassOnIfSet = ['section', 'body', 'items'];

			foreach($saPassOnIfSet as $sVarKey)
			{
				if(isset($maParams[$sVarKey]))
					$aPassOn[$sVarKey] = $maParams[$sVarKey];
			}
			return $twig->render($sViewName.'.html', $maParams);
		}
	}