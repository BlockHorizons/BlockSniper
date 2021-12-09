<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\command;

use BlockHorizons\BlockSniper\brush\Brush;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\listener\BrushListener;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\ui\window\BrushMenuWindow;
use BlockHorizons\BlockSniper\ui\window\MainMenuWindow;
use pocketmine\command\CommandSender;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\StringTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use pocketmine\utils\UUID;

class BrushCommand extends BaseCommand{

	public function __construct(Loader $loader){
		parent::__construct($loader, "brush", Translation::COMMANDS_BRUSH_DESCRIPTION, "/brush <bind|unbind>", ["b"]);
	}

	public function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		/** @var Player $sender */
		$item = $sender->getInventory()->getItemInHand();
		switch(true){
			default:
			case count($args) === 0:
				$sender->sendForm(new MainMenuWindow($this->loader, $sender));
				break;
			case $args[0] === "bind":
				if($item->getId() === ItemIds::AIR){
					$sender->sendMessage($this->getWarning() . Translation::get(Translation::COMMANDS_BRUSH_NEED_ITEM));

					return;
				}
				$brush = $this->loader->config->brushItem->parse();
				if($item->getId() === $brush->getId() && $item->getMeta() === $brush->getMeta()){
					$sender->sendMessage($this->getWarning() . Translation::get(Translation::COMMANDS_BRUSH_BIND_BRUSH_ITEM));

					return;
				}
				$b = new Brush();

				$uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
				BrushListener::$brushItems[$uuid] = $b;
				$sender->sendForm(new BrushMenuWindow($this->loader, $sender, $b, true));

				$item->getNamedTag()->setString(BrushListener::KEY_BRUSH_UUID, $uuid);
				$sender->getInventory()->setItemInHand($item);
				break;
			case $args[0] === "unbind":
				$brushUuidTag = $item->getNamedTag()->getTag(BrushListener::KEY_BRUSH_UUID);
				if(!$brushUuidTag instanceof StringTag){
					$sender->sendMessage($this->getWarning() . Translation::get(Translation::COMMANDS_BRUSH_NOT_BOUND));

					return;
				}
				$uuid = $brushUuidTag->getValue();
				$item->getNamedTag()->removeTag(BrushListener::KEY_BRUSH_UUID);
				unset(BrushListener::$brushItems[$uuid]);

				$sender->getInventory()->setItemInHand($item);

				$sender->sendMessage(TF::GREEN . Translation::get(Translation::COMMANDS_BRUSH_CLEAR_SUCCESS));
		}
	}
}
