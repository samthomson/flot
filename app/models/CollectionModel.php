<?php

	class CollectionModel
	{
		/*
		contains all individual items, acts as an overview to them.
		one item contains everything to do with it, but to list items would be inefficient to load all individual items. so we have a collection which will contain a list of all individual items.
		*/
		protected $amPropertiesToExposeOfItems = [];
		protected $sTypeOfItems = "";
		public $sName = "item";

		private $amItems = [];

		public function __construct() {
			//$this->sUId = uniqid(16);
		}


		public static function create()
		{
			return new static();
		}

		public static function getAllItems($sJson)
		{
			return [];
		}

		public static function createFromFile()
		{
			$sReadPath = $GLOBALS['files.models_path'];

			$sFilePath = $sReadPath."collection_items.flotcms";

			$fModel = fopen($sFilePath, "r") or die("can't read model file");

			$oParsed = self::createFromJson(fread($fModel, filesize($sFilePath)));

			fclose($fModel);

			return $oParsed;
		}
	}