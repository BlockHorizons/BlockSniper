<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\brush\registration\ShapeRegistration;
use BlockHorizons\BlockSniper\brush\registration\TypeRegistration;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\Player;

class BrushMenuWindow extends CustomWindow{

	public function __construct(Loader $loader, Player $requester){
		parent::__construct($this->t(Translation::UI_BRUSH_MENU_TITLE));
		$b = SessionManager::getPlayerSession($requester)->getBrush();

		$this->addDropdown($this->t(Translation::UI_BRUSH_MENU_MODE_DESCRIPTION), $this->processModes(), $b->mode, function(Player $player, int $value) use ($b){
			$b->mode = $value;
		});
		$this->addDropdown($this->t(Translation::UI_BRUSH_MENU_SHAPE), $this->processShapes($requester), $b->getShape(null)::ID, function(Player $player, int $value) use ($b){
			$b->shape = ShapeRegistration::getShapeById($value);
		});
		$this->addDropdown($this->t(Translation::UI_BRUSH_MENU_TYPE), $this->processTypes($requester), $b->getType($b->getShape(null)->getBlocksInside())::ID, function(Player $player, int $value) use ($loader, $b){
			$b->type = TypeRegistration::getTypeById($value);

			// Note to future readers: This response form is explicitly set after all brush properties have been changed
			// so that the BrushPropertiesWindow is updated property.
			$form = new BrushPropertiesWindow($loader, $player);
			if($form->elementCount() !== 0){
				$this->setResponseForm($form);
			}
		});
	}
}