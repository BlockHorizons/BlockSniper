<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\commands\cloning;

use BlockHorizons\BlockSniper\commands\BaseCommand;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\exceptions\InvalidBlockException;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use BlockHorizons\libschematic\Schematic;
use pocketmine\command\CommandSender;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class PasteCommand extends BaseCommand{

	public function __construct(Loader $loader){
		parent::__construct($loader, "paste", Translation::COMMANDS_PASTE_DESCRIPTION, "/paste <copy|template|schematic> [name]");
	}

	public function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		/** @var Player $sender */
		if(!isset($args[0])){
			$sender->sendMessage($this->getUsage());

			return;
		}

		$center = $sender->getTargetBlock(100);
		if($center === null){
			throw new InvalidBlockException("No valid block could be found when attempting to paste.");
		}

		switch(strtolower($args[0])){
			default:
			case "copy":
				if(!SessionManager::getPlayerSession($sender)->getCloneStore()->copyStoreExists()){
					$sender->sendMessage($this->getWarning() . Translation::get(Translation::COMMANDS_PASTE_COPY_NO_COPIES));

					return;
				}
				SessionManager::getPlayerSession($sender)->getCloneStore()->pasteCopy($sender->getTargetBlock(100));
				$sender->sendMessage(TF::GREEN . Translation::get(Translation::COMMANDS_PASTE_COPY_SUCCESS));

				return;

			case "template":
				if(!isset($args[1])){
					$sender->sendMessage($this->getWarning() . Translation::get(Translation::COMMANDS_CLONE_TEMPLATE_MISSING_NAME));

					return;
				}
				if(!SessionManager::getPlayerSession($sender)->getCloneStore()->templateExists($args[1])){
					$sender->sendMessage($this->getWarning() . Translation::get(Translation::COMMANDS_PASTE_TEMPLATE_NONEXISTENT, [$args[1]]));

					return;
				}
				SessionManager::getPlayerSession($sender)->getCloneStore()->pasteTemplate($args[1], $center);
				$sender->sendMessage(TF::GREEN . Translation::get(Translation::COMMANDS_PASTE_TEMPLATE_SUCCESS, [$args[1]]));

				return;

			case "schematic":
				if(!isset($args[1])){
					$sender->sendMessage($this->getWarning() . Translation::get(Translation::COMMANDS_CLONE_SCHEMATIC_MISSING_NAME));

					return;
				}
				if(!is_file($file = $this->loader->getDataFolder() . "schematics/" . $args[1] . ".schematic")){
					$sender->sendMessage($this->getWarning() . Translation::get(Translation::COMMANDS_PASTE_SCHEMATIC_NONEXISTENT, [$args[1]]));

					return;
				}
				$schematic = new Schematic($file);
				$schematic->decodeSizes();

				$width = $schematic->getWidth();
				$length = $schematic->getLength();
				$touchedChunks = [];
				for($x = $center->x - $width / 2; $x <= $center->x + $width / 2 + 16; $x += 16){
					for($z = $center->z - $length / 2; $z <= $center->z + $length / 2 + 16; $z += 16){
						$chunk = $sender->getLevel()->getChunk($x >> 4, $z >> 4, true);
						if($chunk === null){
							continue;
						}
						$touchedChunks[Level::chunkHash($x >> 4, $z >> 4)] = $chunk->fastSerialize();
					}
				}
				SessionManager::getPlayerSession($sender)->getCloneStore()->pasteSchematic($file, $sender->getTargetBlock(100)->asVector3(), $touchedChunks);
				$sender->sendMessage(TF::GREEN . Translation::get(Translation::COMMANDS_PASTE_SCHEMATIC_SUCCESS, [$args[1]]));
		}
	}
}
