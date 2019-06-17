<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\BrushProperties;
use BlockHorizons\BlockSniper\brush\Target;
use BlockHorizons\BlockSniper\brush\Type;
use BlockHorizons\BlockSniper\Loader;
use Generator;
use pocketmine\block\Flowable;
use pocketmine\item\Item;
use pocketmine\Server;

/*
 * Blows away all plants and flowers within the brush radius.
 */

class LeafBlowerType extends Type{

	public const ID = self::TYPE_LEAF_BLOWER;

	/** @var bool */
	private $dropPlants = false;

	public function __construct(BrushProperties $properties, Target $target, Generator $blocks = null){
		parent::__construct($properties, $target, $blocks);
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
	 * @return Generator
	 */
	public function fill() : Generator{
		foreach($this->blocks as $block){
			if($block instanceof Flowable){
				yield $block;
				if($this->dropPlants){
					$this->chunkManager->dropItem($block, Item::get($block->getId()));
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
	public function usesBrushBlocks() : bool{
		return false;
	}
}
