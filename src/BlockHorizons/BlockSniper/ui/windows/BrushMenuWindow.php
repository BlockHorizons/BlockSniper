<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\brush\Brush;
use BlockHorizons\BlockSniper\brush\registration\ShapeRegistration;
use BlockHorizons\BlockSniper\brush\registration\TypeRegistration;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;

class BrushMenuWindow extends CustomWindow{

	public function __construct(Loader $loader, Player $requester, Brush $b, bool $withName = false){
		parent::__construct($this->t(Translation::UI_BRUSH_MENU_TITLE));

		if($withName){
			$this->addInput($this->t(Translation::UI_BRUSH_MENU_NAME), "Brush", "Brush", function(Player $player, string $value) use ($b){
				$item = $player->getInventory()->getItemInHand();
				$item->setCustomName($value);
				$player->getInventory()->setItemInHand($item);
			}
			);
		}
		$this->addDropdown($this->t(Translation::UI_BRUSH_MENU_MODE_DESCRIPTION), $this->processModes(), $b->mode, function(Player $player, int $value) use ($b){
			$b->mode = $value;
		}
		);
		$this->addDropdown($this->t(Translation::UI_BRUSH_MENU_SHAPE), $this->processShapes($requester), $b->getShape()::ID, function(Player $player, int $value) use ($b){
			$b->shape = ShapeRegistration::getShapeById($value);
		}
		);
		$this->addDropdown($this->t(Translation::UI_BRUSH_MENU_TYPE), $this->processTypes($requester), $b->getType()::ID, function(Player $player, int $value) use ($loader, $b){
			$b->type = TypeRegistration::getTypeById($value);

			$item = $player->getInventory()->getItemInHand();
			if($item->getName() !== $item->getVanillaName()){
				$item->setCustomName(sprintf("%s\n%s %s Brush", TF::RESET . TF::WHITE . $item->getName(), TF::AQUA . $b->getShape()->getName(), $b->getType()->getName()));
				$player->getInventory()->setItemInHand($item);
			}

			// Note to future readers: This response form is explicitly set after all brush properties have been changed
			// so that the BrushPropertiesWindow is updated property.
			$form = new BrushPropertiesWindow($loader, $b);
			if($form->elementCount() !== 0){
				$this->setResponseForm($form);
			}
		}
		);
	}
}