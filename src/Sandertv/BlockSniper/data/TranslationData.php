<?php

namespace Sandertv\BlockSniper\data;

use Sandertv\BlockSniper\Loader;

class TranslationData {
	
	public $messages = [];
	public $plugin;
	
	public function __construct(Loader $plugin) {
		$this->plugin = $plugin;
		
		$this->collectTranslations();
	}
	
	/**
	 * @return bool
	 */
	public function collectTranslations(): bool {
		$languageSelected = false;
		foreach($this->getOwner()->availableLanguages as $availableLanguage) {
			if($this->getOwner()->getSettings()->get("Message-Language") === $availableLanguage) {
				$this->getOwner()->saveResource("languages/" . $availableLanguage . ".yml");
				$language = yaml_parse_file($this->getOwner()->getDataFolder() . "languages/" . $availableLanguage . ".yml");
				$languageSelected = true;
				break;
			}
		}
		if(!$languageSelected) {
			$this->getOwner()->saveResource("languages/en.yml");
			$language = yaml_parse_file($this->getOwner()->getDataFolder() . "languages/en.yml");
		}
		
		// This is going to burn your eyes. Don't look at it for too long.
		$this->messages = [
			"commands.errors.no-permission" => $language["commands"]["errors"]["no-permission"],
			"commands.errors.console-use" => $language["commands"]["errors"]["console-use"],
			"commands.errors.radius-not-numeric" => $language["commands"]["errors"]["radius-not-numeric"],
			"commands.errors.radius-too-big" => $language["commands"]["errors"]["radius-too-big"],
			"commands.errors.no-target-found" => $language["commands"]["errors"]["no-target-found"],
			"commands.errors.no-valid-block" => $language["commands"]["errors"]["no-valid-block"],
			"commands.errors.shape-not-found" => $language["commands"]["errors"]["shape-not-found"],
			"commands.errors.no-modifications" => $language["commands"]["errors"]["no-modifications"],
			"commands.errors.paste-not-found" => $language["commands"]["errors"]["paste-not-found"],
			"commands.errors.clone-not-found" => $language["commands"]["errors"]["clone-not-found"],
			"commands.errors.name-not-set" => $language["commands"]["errors"]["name-not-set"],
			"commands.errors.template-not-existing" => $language["commands"]["errors"]["template-not-existing"],
			
			"commands.succeed.default" => $language["commands"]["succeed"]["default"],
			"commands.succeed.undo" => $language["commands"]["succeed"]["undo"],
			"commands.succeed.language" => $language["commands"]["succeed"]["language"],
			"commands.succeed.paste" => $language["commands"]["succeed"]["paste"],
			"commands.succeed.clone" => $language["commands"]["succeed"]["clone"],
		];
		return ($languageSelected ? true : false);
	}
	
	/**
	 * @return Loader
	 */
	public function getOwner(): Loader {
		return $this->plugin;
	}
	
	/**
	 * @param string $key
	 *
	 * @return mixed|null
	 */
	public function get(string $key) {
		if(isset($this->messages[$key])) {
			return $this->messages[$key];
		}
		return null;
	}
}