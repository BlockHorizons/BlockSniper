<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\cloning\types;

use BlockHorizons\BlockSniper\brush\Shape;
use BlockHorizons\BlockSniper\cloning\BaseClone;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\level\Position;
use pocketmine\Player;

class CopyType extends BaseClone{

	public function __construct(Player $player, bool $saveAir, Shape $shape){
		$offset = ($shape->maxY - $shape->minY) / 2;
		parent::__construct($player, $saveAir, Position::fromObject($shape->getCentre()->subtract(0, $offset), $player->getLevel()), $shape);
	}

	public function getName() : string{
		return "Copy";
	}

	public function saveClone() : void{
		SessionManager::getPlayerSession($this->player)->getCloneStore()->saveCopy($this->shape, $this->center);
	}
}
