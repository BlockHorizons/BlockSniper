<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\cloning\types;

use BlockHorizons\BlockSniper\cloning\BaseClone;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\Player;

class TemplateType extends BaseClone{

	public function __construct(Player $player, bool $saveAir, Position $center, array $blocks, string $name){
		parent::__construct($player, $saveAir, $center, $blocks, $name);
	}

	public function getName() : string{
		return "Template";
	}

	public function saveClone() : void{
		$templateBlocks = [];
		foreach($this->blocks as $block){
			if($this->saveAir === false && $block->getId() === Item::AIR){
				continue;
			}
			$templateBlocks[] = $block;
		}
		SessionManager::getPlayerSession($this->player)->getCloneStore()->saveTemplate($this->name, $templateBlocks, $this->center);
	}
}