<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\cloning\types;

use BlockHorizons\BlockSniper\cloning\BaseClone;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\Player;

class CopyType extends BaseClone {

	public function __construct(Player $player, bool $saveAir, Position $center, array $blocks) {
		parent::__construct($player, $saveAir, $center, $blocks);
	}

	public function getName(): string {
		return "Copy";
	}

	public function saveClone(): bool {
		$copyBlocks = [];
		foreach($this->blocks as $block) {
			if($this->saveAir === false && $block->getId() === Item::AIR) {
				continue;
			}
			$copyBlocks[] = $block;
		}
		SessionManager::getPlayerSession($this->player)->getCloneStorer()->setOriginalCenter($this->center);
		SessionManager::getPlayerSession($this->player)->getCloneStorer()->saveCopy($copyBlocks);
		return true;
	}
}
