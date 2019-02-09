<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\commands;

use BlockHorizons\BlockSniper\brush\Brush;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\listeners\BrushListener;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\ui\windows\BrushMenuWindow;
use BlockHorizons\BlockSniper\ui\windows\MainMenuWindow;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\nbt\tag\IntTag;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

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
				if($item->getId() === Item::AIR){
					$sender->sendMessage($this->getWarning() . Translation::get(Translation::COMMANDS_BRUSH_NEED_ITEM));
					return;
				}
				$b = new Brush($sender->getName());

				$brushIndex = BrushListener::$brushIndex++;
				BrushListener::$brushItems[$brushIndex] = $b;
				$sender->sendForm(new BrushMenuWindow($this->loader, $sender, $b));

				$item->getNamedTag()->setInt(BrushListener::KEY_BRUSH_ID, $brushIndex);
				$sender->getInventory()->setItemInHand($item);
				break;
			case $args[0] === "unbind":
				if(!$item->getNamedTag()->hasTag(BrushListener::KEY_BRUSH_ID, IntTag::class)){
					$sender->sendMessage($this->getWarning() . Translation::get(Translation::COMMANDS_BRUSH_NOT_BOUND));
					return;
				}
				$index = $item->getNamedTag()->getInt(BrushListener::KEY_BRUSH_ID);
				$item->getNamedTag()->removeTag(BrushListener::KEY_BRUSH_ID);
				unset(BrushListener::$brushItems[$index]);
				$sender->sendMessage(TF::GREEN . Translation::get(Translation::COMMANDS_BRUSH_CLEAR_SUCCESS));
		}
	}
}
