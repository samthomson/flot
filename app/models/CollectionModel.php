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
			$o->createFromFile();

			return $o;
		}


		public function createFromFile()
		{
			$sReadPath = $GLOBALS['files.models_path'];

			$sFilePath = $sReadPath."collection_".$this->sName.".flotcms";

			$fModel = fopen($sFilePath, "r") or die("can't read model file");

			self::createFromJson(fread($fModel, filesize($sFilePath)));

			fclose($fModel);
		}
		public function createFromJson($sString)
		{
			foreach(json_decode($sString) as $sKey => $oPartialItem)
			{
				// no we'll iterate through partially items, they are partial because only some attributes of a model are stored in the collection file

				$sUId = $sKey;

				$this->amItems[$sUId] = $oPartialItem;
			}
		}

		public function save()
		{
			$aItemsToPersist = [];

			/*

			foreach ($this->amItems as $key => $mItem) {
				$aExposedProperties = [];

				foreach ($this->amPropertiesToExposeOfItems as $sPropertyToPersist) {
					$aExposedProperties[$sPropertyToPersist] = $mItem->mGetProperty($sPropertyToPersist);
				}
				array_push($aItemsToPersist, [$mItem->sUId => $oItem]);
			}

			$sFileContents = json_encode($aItemsToPersist);
			*/
			$sFileContents = json_encode($this->amItems);

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
			$mModel->amItems[$mItem->sUId] = $mItem->aGetPropertiesForCollection();
			// now save our whole collection
			$mModel->save();
		}
	}