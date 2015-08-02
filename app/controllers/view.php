<?php

	class View{

		function render($sViewName = "index", $maParams = []){

			$loader = new Twig_Loader_Filesystem(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR .'templates');
			$twig = new Twig_Environment($loader);

			$body = "unset";

			echo $twig->render($sViewName.'.html',
				array(
					'flot_admin_base_url' => $GLOBALS['admin.flot_admin_base_url'],
					'section' => $maParams['section'],
					'body' => $body
				)
			);
		}
	}