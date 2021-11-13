<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\ui\window;

use BlockHorizons\BlockSniper\brush\Brush;
use BlockHorizons\BlockSniper\brush\registration\ShapeRegistration;
use BlockHorizons\BlockSniper\brush\registration\TypeRegistration;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use pocketmine\player\Player;
use pocketmine\utils\AssumptionFailedError;
use pocketmine\utils\TextFormat as TF;
use function array_map;
use function array_search;
use function ucwords;

class BrushMenuWindow extends CustomWindow{

	public function __construct(Loader $loader, Player $requester, Brush $b, bool $withName = false){
		parent::__construct($this->t(Translation::UI_BRUSH_MENU_TITLE));

		if($withName){
			$this->addInput($this->t(Translation::UI_BRUSH_MENU_NAME), "Brush", "Brush", function(Player $player, string $value){
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
		$shownBrushShapes = $this->getPermittedShapes($requester);
		$shortName = array_search($b->shape, ShapeRegistration::$shapes);
		$key = array_search($shortName, $shownBrushShapes);
		if($key === false){
			//this should never happen; it's only written here
			throw new AssumptionFailedError("Brush has unregistered shape set");
		}
		$this->addDropdown($this->t(Translation::UI_BRUSH_MENU_SHAPE), array_map(fn(string $shape) => ucwords($shape), $shownBrushShapes), $key, function(Player $player, int $value) use ($b, $shownBrushShapes){
			if(!isset($shownBrushShapes[$value])){
				return;
			}
			$b->shape = ShapeRegistration::getShape($shownBrushShapes[$value]) ?? throw new AssumptionFailedError("Shape must exist");
		}
		);
		$shownBrushTypes = $this->getPermittedTypes($requester);
		$shortName = array_search($b->type, TypeRegistration::$types);
		$key = array_search($shortName, $shownBrushTypes);
		if($key === false){
			//player has a brush type that they were previously permitted to use, but no longer are
			$key = 0;
		}
		$this->addDropdown($this->t(Translation::UI_BRUSH_MENU_TYPE), array_map(fn(string $type) => ucwords($type), $shownBrushTypes), $key, function(Player $player, int $value) use ($loader, $b, $shownBrushTypes){
			if(!isset($shownBrushTypes[$value])){
				return;
			}
			$b->type = TypeRegistration::getType($shownBrushTypes[$value]) ?? throw new AssumptionFailedError("Type must exist");

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