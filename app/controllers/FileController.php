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
			return self::bSaveFile("item_".$sFile.".flotcms", "datastore", $sContents);
		}

		public static function bSaveCollection($sCollectionName, $sContents)
		{
			return self::bSaveFile("collection_".$sCollectionName.".flotcms", "datastore", $sContents);
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

				array_push($aObjects, $oParsed);
				fclose($fModel);
			}

			return $aObjects;
		}

		public static function sReadTextFromFile($sFilePath)
		{
			
			$sText = '';

			$fModel = fopen($sFilePath, "r");

			$sText = fread($fModel, filesize($sFilePath));

			fclose($fModel);

			return $sText;

			/*

			$line = '';

$f = fopen($sFilePath, 'r');
$cursor = -1;

fseek($f, $cursor, SEEK_END);
$char = fgetc($f);

while ($char === "\n" || $char === "\r") {
    fseek($f, $cursor--, SEEK_END);
    $char = fgetc($f);
}

while ($char !== false && $char !== "\n" && $char !== "\r") {

    $line = $char . $line;
    fseek($f, $cursor--, SEEK_END);
    $char = fgetc($f);
}

return $line;
*/
		}
	}