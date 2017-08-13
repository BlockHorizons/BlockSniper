<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\commands;

use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\user_interface\WindowHandler;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class BrushCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "brush", "Change the properties of the brush", "/brush <parameter> <args>", ["b"]);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
		if(!$this->testPermission($sender)) {
			$this->sendNoPermission($sender);
			return true;
		}

		if(!$sender instanceof Player) {
			$this->sendConsoleError($sender);
			return true;
		}

		$this->getLoader()->getBrushManager()->createBrush($sender);
		if(!isset($args[0])) {
			$args[0] = "menu";
		}

		switch(strtolower($args[0])) {
			default:
			case "window":
			case "menu":
				$windowHandler = new WindowHandler();
				$packet = new ModalFormRequestPacket();
				$packet->formId = $windowHandler->getWindowIdFor(WindowHandler::WINDOW_MAIN_MENU);
				$packet->formData = $windowHandler->getWindowJson(WindowHandler::WINDOW_MAIN_MENU);
				$sender->dataPacket($packet);
				return true;

			case "preset":
			case "pr":
				switch($args[1]) {
					case "new":
					case "create":
						if($this->getLoader()->getPresetManager()->isPreset($args[1])) {
							$sender->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.preset-already-exists"));
							return true;
						}
						$this->getLoader()->getPresetManager()->presetCreation[$sender->getId()] = [];
						$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("commands.succeed.preset.name"));
						$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("commands.succeed.preset.cancel"));
						return true;

					case "list":
						$presetList = implode(", ", $this->getLoader()->getPresetManager()->getAllPresets());
						$sender->sendMessage(TF::GREEN . "--- " . TF::YELLOW . "Preset List" . TF::GREEN . " ---");
						$sender->sendMessage(TF::AQUA . $presetList);
						return true;

					case "delete":
						if(!$this->getLoader()->getPresetManager()->isPreset($args[2])) {
							$sender->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.preset-doesnt-exist"));
							return true;
						}
						$this->getLoader()->getPresetManager()->deletePreset($args[2]);
						$sender->sendMessage(TF::YELLOW . "Preset " . TF::RED . $args[2] . TF::YELLOW . " has been deleted successfully.");
						return true;

					default:
						if(!$this->getLoader()->getPresetManager()->isPreset($args[1])) {
							$sender->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.preset-doesnt-exist"));
							return true;
						}
						$preset = $this->getLoader()->getPresetManager()->getPreset($args[1]);
						$preset->apply($sender);
						$sender->sendMessage(TF::YELLOW . $this->getLoader()->getTranslation("brush.preset") . TF::BLUE . $preset->name);
						foreach($preset->getParsedData() as $key => $value) {
							if($value !== null && $key !== "name") {
								if(is_array($value)) {
									$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("brush" . $key) . TF::AQUA . implode(", ", $value));
								} else {
									$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("brush." . $key) . TF::AQUA . $value);
								}
							}
						}
						return true;
				}
		}
	}
}
