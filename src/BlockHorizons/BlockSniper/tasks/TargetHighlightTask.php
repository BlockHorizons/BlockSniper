<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\tasks;

use BlockHorizons\BlockSniper\iterator\BlockEdgeIterator;
use pocketmine\level\particle\FlameParticle;
use pocketmine\math\Vector3;
use pocketmine\Player;

class TargetHighlightTask extends BlockSniperTask{

	public function onRun(int $currentTick) : void{
		$brushItem = $this->loader->config->brushItem->parse();
		foreach($this->loader->getServer()->getOnlinePlayers() as $player){
			if(!$player->hasPermission("blocksniper.command.brush")){
				// The player does not have permission to brush, so we don't need to highlight the target block of the
				// player.
				continue;
			}
			if(!$player->getInventory()->getItemInHand()->equals($brushItem)){
				// The player isn't holding the brush item, so no need to highlight either.
				continue;
			}
			$this->highlightTarget($player);
		}
	}

	/**
	 * @param Player $player
	 */
	public function highlightTarget(Player $player) : void{
		$level = $player->getLevel();
		$iterator = new BlockEdgeIterator($player->getTargetBlock(16 * $player->getViewDistance()));

		foreach($iterator->getEdges() as $edge){
			/** @var Vector3 $position */
			foreach($edge->walk(0.2) as $position){
				if(mt_rand(0, 5) === 0){
					$level->addParticle($position, new FlameParticle());
				}
			}
		}
	}
}