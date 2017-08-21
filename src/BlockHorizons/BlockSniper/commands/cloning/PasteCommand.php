<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\commands\cloning;

use BlockHorizons\BlockSniper\commands\BaseCommand;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use libschematic\Schematic;
use pocketmine\command\CommandSender;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class PasteCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "paste", (new Translation(Translation::COMMANDS_PASTE_DESCRIPTION))->getMessage(), "/paste <type> [name]", []);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
		if(!$this->testPermission($sender)) {
			$this->sendNoPermission($sender);
			return false;
		}

		if(!$sender instanceof Player) {
			$this->sendConsoleError($sender);
			return false;
		}

		$center = $sender->getTargetBlock(100);
		switch(strtolower($args[0])) {
			default:
			case "copy":
				if(!SessionManager::getPlayerSession($sender)->getCloneStorer()->copyStoreExists()) {
					$sender->sendMessage($this->getWarning() . (new Translation(Translation::COMMANDS_PASTE_COPY_NO_COPIES))->getMessage());
					return false;
				}
				SessionManager::getPlayerSession($sender)->getCloneStorer()->pasteCopy($sender->getTargetBlock(100));
				$sender->sendMessage(TF::GREEN . (new Translation(Translation::COMMANDS_PASTE_COPY_SUCCESS))->getMessage());
				break;

			case "template":
				if(!SessionManager::getPlayerSession($sender)->getCloneStorer()->templateExists($args[1])) {
					$sender->sendMessage($this->getWarning() . (new Translation(Translation::COMMANDS_PASTE_TEMPLATE_NONEXISTENT, [$args[1]]))->getMessage());
					return false;
				}
				SessionManager::getPlayerSession($sender)->getCloneStorer()->pasteTemplate($args[1], $center);
				$sender->sendMessage(TF::GREEN . (new Translation(Translation::COMMANDS_PASTE_TEMPLATE_SUCCESS, [$args[1]]))->getMessage());
				break;

			case "schematic":
				if(!is_file($file = $this->getLoader()->getDataFolder() . "schematics/" . $args[1] . ".schematic")) {
					$sender->sendMessage($this->getWarning() . (new Translation(Translation::COMMANDS_PASTE_SCHEMATIC_NONEXISTENT, [$args[1]]))->getMessage());
					return true;
				}
				$schematic = new Schematic($file);
				$schematic->decodeSizes();

				$width = $schematic->getWidth();
				$length = $schematic->getLength();
				$touchedChunks = [];
				for($x = $center->x - $width / 2; $x <= $center->x + $width / 2 + 16; $x += 16) {
					for($z = $center->z - $length / 2; $z <= $center->z + $length / 2 + 16; $z += 16) {
						$chunk = $sender->getLevel()->getChunk($x >> 4, $z >> 4, true);
						$touchedChunks[Level::chunkHash($x >> 4, $z >> 4)] = $chunk->fastSerialize();
					}
				}
				SessionManager::getPlayerSession($sender)->getCloneStorer()->pasteSchematic($file, $sender->getTargetBlock(100)->asVector3(), $touchedChunks);
				$sender->sendMessage(TF::GREEN . (new Translation(Translation::COMMANDS_PASTE_SCHEMATIC_SUCCESS, [$args[1]]))->getMessage());
				break;
		}
		return true;
	}
}
