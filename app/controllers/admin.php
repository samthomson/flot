<?php

	#namespace FlotCMS;

	class Admin{

		public static function makeUI($maParams)
		{

			$htmlBody = "";

			switch($maParams['request']->section){
				case 'items':
					switch($maParams['request']->action){
						case 'edit':
							$htmlBody .= "edit buttons";
							$htmlBody .= "<hr/>";
							$htmlBody .= "edit ui";
							break;
						default:
							return View::render("pages\\admin\\contents-overview");
							
							$htmlBody .= View::render("partials\\admin\\contents-overview-buttons");
							$htmlBody .= "<hr/>";
							$htmlBody .= "list??";
							break;
					}
					break;
				default:
					$htmlBody = $maParams['request']->section;
					break;
			}


			return View::render("templates\admin", ["section" => $maParams['request']->section, "body" => $htmlBody]);
		}

		function makeContentsPage($sAction = "list"){

			// depending on the action construct a different content menu

    		

		}

		function logout(){}

		function start()
		{
			// validate user credentials then register the user and do any first run stuff
		}
	}