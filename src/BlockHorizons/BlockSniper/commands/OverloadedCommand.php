<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\commands;

use pocketmine\Player;

interface OverloadedCommand {

	/**
	 * @param Player $player
	 *
	 * @return array
	 */
	public function generateCustomCommandData(Player $player): array;

}