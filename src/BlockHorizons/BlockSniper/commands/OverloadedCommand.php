<?php

namespace BlockHorizons\BlockSniper\commands;

use pocketmine\Player;

interface OverloadedCommand {

	public function generateCustomCommandData(Player $player);

}