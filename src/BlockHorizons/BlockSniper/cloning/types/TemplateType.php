<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\cloning\types;

use BlockHorizons\BlockSniper\brush\BaseShape;
use BlockHorizons\BlockSniper\cloning\BaseClone;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\level\Position;
use pocketmine\Player;

class TemplateType extends BaseClone{

	public function __construct(Player $player, bool $saveAir, Position $center, BaseShape $shape, string $name){
		parent::__construct($player, $saveAir, $center, $shape, $name);
	}

	public function getName() : string{
		return "Template";
	}

	public function saveClone() : void{
		SessionManager::getPlayerSession($this->player)->getCloneStore()->saveTemplate($this->name, $this->shape->getBlocksInside(), $this->center);
	}
}