<?php

namespace BlockHorizons\BlockSniper\data;

use BlockHorizons\BlockSniper\Loader;

class TranslationData {
	
	private $messages = [];
	private $loader;
	
	public function __construct(Loader $loader) {
		$this->loader = $loader;
		
		$this->collectTranslations();
	}
	
	/**
	 * @return bool
	 */
	public function collectTranslations(): bool {
		$languageSelected = false;
		$language = [];
		foreach($this->getLoader()->availableLanguages as $availableLanguage) {
			if($this->getLoader()->getSettings()->get("Message-Language") === $availableLanguage) {
				$this->getLoader()->saveResource("languages/" . $availableLanguage . ".yml");
				$language = yaml_parse_file($this->getLoader()->getDataFolder() . "languages/" . $availableLanguage . ".yml");
				$languageSelected = true;
				break;
			}
		}
		if(!$languageSelected) {
			$this->getLoader()->saveResource("languages/en.yml");
			$language = yaml_parse_file($this->getLoader()->getDataFolder() . "languages/en.yml");
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
			"commands.errors.preset-already-exists" => $language["commands"]["errors"]["preset-already-exists"],
			"commands.errors.preset-doesnt-exist" => $language["commands"]["errors"]["preset-doesnt-exist"],
			
			"commands.succeed.default" => $language["commands"]["succeed"]["default"],
			"commands.succeed.undo" => $language["commands"]["succeed"]["undo"],
			"commands.succeed.language" => $language["commands"]["succeed"]["language"],
			"commands.succeed.paste" => $language["commands"]["succeed"]["paste"],
			"commands.succeed.clone" => $language["commands"]["succeed"]["clone"],
			"commands.succeed.brush.reset" => $language["commands"]["succeed"]["brush"]["reset"],
			"commands.succeed.preset.name" => $language["commands"]["succeed"]["preset"]["name"],
			"commands.succeed.preset.cancel" => $language["commands"]["succeed"]["preset"]["cancel"],
			"commands.succeed.preset.canceled" => $language["commands"]["succeed"]["preset"]["canceled"],
			
			"brush.shape" => $language["brush"]["shape"],
			"brush.type" => $language["brush"]["type"],
			"brush.blocks" => $language["brush"]["blocks"],
			"brush.size" => $language["brush"]["size"],
			"brush.perfect" => $language["brush"]["perfect"],
			"brush.obsolete" => $language["brush"]["obsolete"],
			"brush.height" => $language["brush"]["height"],
			"brush.gravity" => $language["brush"]["gravity"],
			"brush.decrement" => $language["brush"]["decrement"],
			"brush.biome" => $language["brush"]["biome"],
			"brush.hollow" => $language["brush"]["hollow"],
			"brush.preset" => $language["brush"]["preset"],
			"brush.tree" => $language["brush"]["tree"]
		];
		return $languageSelected;
	}
	
	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
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