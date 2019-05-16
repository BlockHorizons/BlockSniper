<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\tasks;

use BlockHorizons\BlockSniper\brush\TargetHighlight;
use pocketmine\Player;
use pocketmine\world\Location;

class TargetHighlightTask extends BlockSniperTask{

	/** @var TargetHighlight[] */
	private $entities = [];

	public function onRun(int $currentTick) : void{
		$brushItem = $this->loader->config->brushItem->parse();
		foreach($this->loader->getServer()->getOnlinePlayers() as $player){
			if(!$player->hasPermission("blocksniper.command.brush")){
				// The player does not have permission to brush, so we don't need to highlight the target block of the
				// player.
				continue;
			}
			$name = $player->getName();
			if(!$player->getInventory()->getItemInHand()->equals($brushItem)){
				if(isset($this->entities[$name])){
					// The player still had a target highlight entity active, so we need to remove that as the player
					// is no longer holding the brush item.
					$entity = $this->entities[$name];
					$entity->close();
					unset($this->entities[$name]);
				}

				// The player isn't holding the brush item, so no need to highlight either.
				continue;
			}
			if(!isset($this->entities[$name])){
				$this->entities[$name] = new TargetHighlight($player->getPosition());
				$this->entities[$name]->spawnToAll();
			}
			$this->highlightTarget($player);
		}
		foreach($this->entities as $playerName => $entity){
			if($this->loader->getServer()->getPlayerExact($playerName) === null){
				$entity->close();
				unset($this->entities[$playerName]);
			}
		}
	}

	/**
	 * @param Player $player
	 */
	public function highlightTarget(Player $player) : void{
		$pos = $player->getTargetBlock(16 * $player->getViewDistance())->add(0.0, 0, 1.0)->subtract(0.04, 0.04, -0.04);
		$this->entities[$player->getName()]->teleport(Location::fromObject($pos, $player->getWorld()));
	}
}