<?php

	class CollectionModel
	{
		/*
		contains all individual items, acts as an overview to them.
		one item contains everything to do with it, but to list items would be inefficient to load all individual items. so we have a collection which will contain a list of all individual items.
		*/
		protected $amPropertiesToExposeOfItems = [];
		protected $sTypeOfItems = "";
		public $sName = "";

		private $amItems = [];

		public function __construct() {
			//$this->sUId = uniqid(16);
		}


		public static function create()
		{
			$o = new static();
			// initiate items from disk
			$o->amItems = $o->createFromFile();

			return $o;
		}


		public function createFromFile()
		{
			$sReadPath = $GLOBALS['files.models_path'];

			$sFilePath = $sReadPath."collection_".$this->sName.".flotcms";

			$fModel = fopen($sFilePath, "r") or die("can't read model file");

			$oParsed = self::createFromJson(fread($fModel, filesize($sFilePath)));

			fclose($fModel);

			return $oParsed;
		}
		public static function createFromJson($sString)
		{
			return json_decode($sString);
		}

		private function save()
		{
			$aItemsToPersist = [];

			foreach ($this->amItems as $key => $mItem) {
				$oItem = [];

				$oItem['sUId'] = $mItem->sUId;

				foreach ($this->amPropertiesToExposeOfItems as $sPropertyToPersist) {
					$oItem[$sPropertyToPersist] = $mItem->mGetProperty($sPropertyToPersist);
				}
				array_push($aItemsToPersist, $oItem);
			}

			$sFileContents = json_encode($aItemsToPersist);

			if(FileController::bSaveCollection($this->sName, $sFileContents))
				return true;

			return false;
		}

		public static function getAllItems()
		{
			$mModel = self::create();

			return $mModel->amItems;
		}

		public static function saveItem($mItem)
		{
			// save that specific item, but also update this collection too
			// save that individual item to disk, overwriting any previou
			$mItem->save();

			$mModel = self::create();
			// update the item in our collection
			$mModel->amItems[$mItem->sUId] = $mItem;
			// now save our whole collection
			$mModel->save();
		}
	}