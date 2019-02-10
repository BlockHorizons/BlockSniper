<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\brush\Brush;
use BlockHorizons\BlockSniper\Loader;
use pocketmine\block\Flowable;
use pocketmine\item\Item;
use pocketmine\level\ChunkManager;
use pocketmine\Server;

/*
 * Blows away all plants and flowers within the brush radius.
 */

class LeafBlowerType extends BaseType{

	public const ID = self::TYPE_LEAF_BLOWER;

	/** @var bool */
	private $dropPlants = false;

	public function __construct(Brush $brush, ChunkManager $level, \Generator $blocks = null){
		parent::__construct($brush, $level, $blocks);
		if(!$this->isAsynchronous()){
			/** @var Loader $loader */
			$loader = Server::getInstance()->getPluginManager()->getPlugin("BlockSniper");
			if($loader === null){
				return;
			}
			$this->dropPlants = $loader->config->dropLeafBlowerPlants;
		}
	}

	/**
	 * @return \Generator
	 */
	public function fill() : \Generator{
		foreach($this->blocks as $block){
			if($block instanceof Flowable){
				yield $block;
				if($this->dropPlants){
					$this->getLevel()->dropItem($block, Item::get($block->getId()));
				}
				$this->delete($block);
			}
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Leaf Blower";
	}

	/**
	 * @return bool
	 */
	public function usesBlocks() : bool{
		return false;
	}
}
