<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\command\cloning;

use BlockHorizons\BlockSniper\command\BaseCommand;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\session\SessionManager;
use BlockHorizons\libschematic\Schematic;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use pocketmine\world\format\io\FastChunkSerializer;
use pocketmine\world\World;
use function strtolower;

class PasteCommand extends BaseCommand{

	public function __construct(Loader $loader){
		parent::__construct($loader, "paste", Translation::COMMANDS_PASTE_DESCRIPTION, "/paste <copy|schematic> [name]");
	}

	public function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		/** @var Player $sender */
		if(!isset($args[0])){
			$sender->sendMessage($this->getUsage());
			return;
		}

		$session = SessionManager::getPlayerSession($sender);
		$center = $session->getTargetBlock();

		switch(strtolower($args[0])){
			default:
			case "copy":
				if(!$sender->hasPermission("blocksniper.paste.copy")){
					return;
				}
				if(!$session->getCloneStore()->copyStoreExists()){
					$sender->sendMessage($this->getWarning() . Translation::get(Translation::COMMANDS_PASTE_COPY_NO_COPIES));

					return;
				}
				$session->getCloneStore()->pasteCopy($center);
				$sender->sendMessage(TF::GREEN . Translation::get(Translation::COMMANDS_PASTE_COPY_SUCCESS));

				return;
			case "schematic":
				if(!$sender->hasPermission("blocksniper.paste.schematic")){
					return;
				}
				if(!isset($args[1])){
					$sender->sendMessage($this->getWarning() . Translation::get(Translation::COMMANDS_CLONE_SCHEMATIC_MISSING_NAME));

					return;
				}
				if(!is_file($file = $this->loader->getDataFolder() . "schematics/" . $args[1] . ".schematic")){
					$sender->sendMessage($this->getWarning() . Translation::get(Translation::COMMANDS_PASTE_SCHEMATIC_NONEXISTENT, $args[1]));

					return;
				}
				$schematic = new Schematic();
				$schematic->parse($file);

				$width = $schematic->getWidth();
				$length = $schematic->getLength();
				$touchedChunks = [];
				for($x = $center->x - $width / 2; $x <= $center->x + $width / 2 + 16; $x += 16){
					for($z = $center->z - $length / 2; $z <= $center->z + $length / 2 + 16; $z += 16){
						$chunk = $sender->getWorld()->loadChunk($x >> 4, $z >> 4);
						if($chunk === null){
							continue;
						}
						$touchedChunks[World::chunkHash($x >> 4, $z >> 4)] = FastChunkSerializer::serializeTerrain($chunk);
					}
				}
				$session->getCloneStore()->pasteSchematic($file, $center->asVector3(), $touchedChunks);
				$sender->sendMessage(TF::GREEN . Translation::get(Translation::COMMANDS_PASTE_SCHEMATIC_SUCCESS, $args[1]));
		}
	}
}
