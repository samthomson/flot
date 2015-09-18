<?php

	class FileController{

		private static function bSaveFile($sFile, $sFolder, $sContents)
		{
			$sWritePath = $GLOBALS['files.models_path'].$sFile;

			$myfile = fopen($sWritePath, "w") or die("file fail");
			fwrite($myfile, $sContents);
			fclose($myfile);
			return true;
		}

		public static function bSaveModel($sFile, $sContents)
		{
			return self::bSaveFile($sFile.".flotcms", "datastore", $sContents);
		}

		public static function getModels()
		{
			$sReadPath = $GLOBALS['files.models_path'];

			$sScanPath = $sReadPath."*.flotcms";

			$aObjects = [];

			foreach (glob($sScanPath) as $sModelPath)
			{
				$fModel = fopen($sModelPath, "r") or die("can't read model file");

				$oParsed = PageModel::createFromJson(fread($fModel, filesize($sModelPath)));

				//print_r($oParsed);
				//echo $oParsed->mGetProperty('title'), "<br/>";
				//echo $oParsed->amProperties['title']->value, "<br/>";

				array_push($aObjects, $oParsed);
				fclose($fModel);
			}

			return $aObjects;
		}

		public static function modelFromFile($sName)
		{
			$sReadPath = $GLOBALS['files.models_path'];

			$sSeekPath = $sReadPath."$sName.flotcms";

			$fModel = fopen($sSeekPath, "r") or die("can't read model file");

			$oParsed = PageModel::createFromJson(fread($fModel, filesize($sSeekPath)));

			//print_r($oParsed);
			//echo $oParsed->mGetProperty('title'), "<br/>";
			//echo $oParsed->amProperties['title']->value, "<br/>";

			fclose($fModel);

			return $oParsed;
		}
	}