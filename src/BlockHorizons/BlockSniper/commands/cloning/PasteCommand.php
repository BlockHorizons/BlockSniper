<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\commands\cloning;

use BlockHorizons\BlockSniper\commands\BaseCommand;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\undo\Undo;
use pocketmine\block\Block;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use libschematic\Schematic;

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
				$schematic->fixBlockIds();

				$undoBlocks = [];

				/** @var Block $block */
				foreach($schematic->getBlocks() as $block) {
					if($block->getId() !== Item::AIR) {
						$undoBlocks[] = $center->getLevel()->getBlock($target = $center->add($block->x - floor($schematic->getWidth() / 2), $block->y, $block->z - floor($schematic->getLength() / 2)));
						$center->getLevel()->setBlock($target, $block, false, false);
					}
				}
				$this->getLoader()->getRevertStorer()->saveRevert(new Undo($undoBlocks), $sender);
				break;
		}
		$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("commands.succeed.paste"));
		return true;
	}
}
