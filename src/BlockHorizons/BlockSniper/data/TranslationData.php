<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\data;

use BlockHorizons\BlockSniper\Loader;
use function file_get_contents;
use function json_decode;

class TranslationData{

	/** @var mixed[] */
	private $messages = [];
	/** @var Loader */
	private $loader = null;

	public function __construct(Loader $loader){
		$this->loader = $loader;

		$this->collectTranslations();
	}

	/**
	 * @return bool
	 */
	public function collectTranslations() : bool{
		$languageSelected = false;
		$language = "";
		foreach(Loader::getAvailableLanguages() as $availableLanguage){
			if($this->loader->config->messageLanguage === $availableLanguage){
				$this->loader->saveResource("languages/" . $availableLanguage . ".json");
				$language = file_get_contents($this->loader->getDataFolder() . "languages/" . $availableLanguage . ".json");
				$languageSelected = true;
				break;
			}
		}
		if(!$languageSelected){
			$this->loader->saveResource("languages/en.json");
			$language = file_get_contents($this->loader->getDataFolder() . "languages/en.json");
		}
		$this->messages = json_decode($language, true);

		new Translation($this);

		return $languageSelected;
	}

	public function regenerateLanguageFile() : void{
		@unlink($this->loader->getDataFolder() . "languages/" . $this->loader->config->messageLanguage . ".json");
		// Always unlink the default translations.
		@unlink($this->loader->getDataFolder() . "languages/en.json");
		$this->collectTranslations();
	}

	/**
	 * @return mixed[]
	 */
	public function getMessages() : array{
		return $this->messages;
	}
}