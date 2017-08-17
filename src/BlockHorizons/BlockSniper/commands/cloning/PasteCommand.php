<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\commands\cloning;

use BlockHorizons\BlockSniper\commands\BaseCommand;
use BlockHorizons\BlockSniper\Loader;
use libschematic\Schematic;
use pocketmine\command\CommandSender;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class PasteCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "paste", "Paste the selected clone, template or schematic", "/paste <type> [name]", []);
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

		$center = $sender->getTargetBlock(100);
		switch(strtolower($args[0])) {
			default:
			case "copy":
				if($this->getLoader()->getCloneStorer()->copyStoreExists($sender)) {
					$this->getLoader()->getCloneStorer()->pasteCopy($sender);
				}
				break;

			case "template":
				if(!$this->getLoader()->getCloneStorer()->templateExists($args[1])) {
					$sender->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.template-not-existing"));
					return true;
				}
				$this->getLoader()->getCloneStorer()->pasteTemplate($args[1], $center, $sender);
				break;

			case "schematic":
				if(!is_file($file = $this->getLoader()->getDataFolder() . "schematics/" . $args[1] . ".schematic")) {
					$sender->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.template-not-existing"));
					return true;
				}
				$schematic = new Schematic($file);
				$schematic->decode();

				$width = $schematic->getWidth();
				$length = $schematic->getLength();
				$touchedChunks = [];
				for($x = $center->x - $width / 2; $x <= $center->x + $width / 2 + 16; $x += 16) {
					for($z = $center->z - $length / 2; $z <= $center->z + $length / 2 + 16; $z += 16) {
						$chunk = $sender->getLevel()->getChunk($x >> 4, $z >> 4, true);
						$touchedChunks[Level::chunkHash($x >> 4, $z >> 4)] = $chunk->fastSerialize();
					}
				}
				$this->getLoader()->getCloneStorer()->pasteSchematic($file, $sender->getTargetBlock(100)->asVector3(), $touchedChunks, $sender);
				break;
		}
		$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("commands.succeed.paste"));
		return true;
	}
}
