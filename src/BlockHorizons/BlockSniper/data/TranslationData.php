<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\data;

use BlockHorizons\BlockSniper\Loader;

class TranslationData {

	/** @var array */
	private $messages = [];
	/** @var Loader */
	private $loader = null;

	public function __construct(Loader $loader) {
		$this->loader = $loader;

		$this->collectTranslations();
		new Translation($this);
	}

	/**
	 * @return bool
	 */
	public function collectTranslations(): bool {
		$languageSelected = false;
		$language = "";
		foreach(Loader::getAvailableLanguages() as $availableLanguage) {
			if($this->getLoader()->config->MessageLanguage === $availableLanguage) {
				$this->getLoader()->saveResource("languages/" . $availableLanguage . ".json");
				$language = file_get_contents($this->getLoader()->getDataFolder() . "languages/" . $availableLanguage . ".json");
				$languageSelected = true;
				break;
			}
		}
		if(!$languageSelected) {
			$this->getLoader()->saveResource("languages/en.json");
			$language = file_get_contents($this->getLoader()->getDataFolder() . "languages/en.json");
		}
		$this->messages = json_decode($language, true);
		return $languageSelected;
	}

	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}

	/**
	 * @return array
	 */
	public function getMessages(): array {
		return $this->messages;
	}
}