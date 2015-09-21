<?php

	class FileController{

		private static function bSaveFile($sFile, $sFolder, $sContents)
		{
			$sWritePath = $GLOBALS['files.models_path'].$sFile;

			$myfile = fopen($sWritePath, "w") or die("file fail");

			if (flock($myfile,LOCK_EX))
			{
				fwrite($myfile, $sContents);
  				flock($myfile,LOCK_UN);
				fclose($myfile);
				return true;
			}
			else{
				return false;
			}
		}

		public static function bSaveModel($sFile, $sContents)
		{
			return self::bSaveFile("item_".$sFile.".php", "datastore", $sContents);
		}

		public static function bSaveCollection($sCollectionName, $sContents)
		{
			return self::bSaveFile("collection_".$sCollectionName.".php", "datastore", $sContents);
		}

		public static function getModels()
		{
			$sReadPath = $GLOBALS['files.models_path'];

			$sScanPath = $sReadPath."*.php";

			$aObjects = [];

			foreach (glob($sScanPath) as $sModelPath)
			{
				$fModel = fopen($sModelPath, "r") or die("can't read model file");

				$oParsed = PageModel::createFromJson(fread($fModel, filesize($sModelPath)));

				array_push($aObjects, $oParsed);
				fclose($fModel);
			}

			return $aObjects;
		}

		public static function sReadTextFromFile($sFilePath)
		{
			
			$sText = '';

			$fModel = fopen($sFilePath, "r");

			clearstatcache(true, $sFilePath);
			$sText = fread($fModel, filesize($sFilePath));

			fclose($fModel);

			return $sText;
		}
	}