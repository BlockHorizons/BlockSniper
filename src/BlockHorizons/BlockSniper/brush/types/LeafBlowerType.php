<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\Loader;
use pocketmine\block\Flowable;
use pocketmine\item\Item;
use pocketmine\Server;

/*
 * Blows away all plants and flowers within the brush radius.
 */

class LeafBlowerType extends BaseType{

	const ID = self::TYPE_LEAF_BLOWER;

	/**
	 * @return \Generator
	 */
	public function fillSynchronously() : \Generator{
		/** @var Loader $loader */
		$loader = Server::getInstance()->getPluginManager()->getPlugin("BlockSniper");
		if($loader === null){
			return;
		}
		$dropPlants = $loader->config->dropLeafBlowerPlants;
		foreach($this->blocks as $block){
			if($block instanceof Flowable){
				yield $block;
				if($dropPlants){
					$this->getLevel()->dropItem($block, Item::get($block->getId()));
				}
				$this->putBlock($block, 0);
			}
		}
	}

	public function fillAsynchronously() : void{
		foreach($this->blocks as $block){
			if($block instanceof Flowable){
				$this->putBlock($block, 0);
			}
		}
	}

	public function getName() : string{
		return "Leaf Blower";
	}
}
