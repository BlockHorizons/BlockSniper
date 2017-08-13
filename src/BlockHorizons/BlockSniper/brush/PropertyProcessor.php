<?php

namespace BlockHorizons\BlockSniper\brush;

use BlockHorizons\BlockSniper\events\ChangeBrushPropertiesEvent as Change;
use BlockHorizons\BlockSniper\Loader;
use pocketmine\Player;

class PropertyProcessor {

	const VALUE_SIZE = 0;
	const VALUE_SHAPE = 1;
	const VALUE_TYPE = 2;
	const VALUE_HOLLOW = 3;
	const VALUE_DECREMENT = 4;
	const VALUE_HEIGHT = 5;
	const VALUE_PERFECT = 6;
	const VALUE_BLOCKS = 7;
	const VALUE_OBSOLETE = 8;
	const VALUE_BIOME = 9;
	const VALUE_TREE = 10;
	
	/** @var mixed */
	private $value = null;
	/** @var int */
	private $valueType = 0;
	/** @var Player */
	private $player = null;
	/** @var Loader */
	private $loader = null;

	public function __construct(int $valueType, $value, Player $player, Loader $loader) {
		$this->value = $value;
		$this->valueType = $valueType;
		$this->player = $player;
		$this->loader = $loader;
	}

	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}

	public function process() {
		$brush = BrushManager::get($this->player);
		$action = 0;
		switch($this->valueType) {
			case 0:
				$brush->setSize($this->value);
				$action = Change::ACTION_CHANGE_SIZE;
				break;

			case 1:
				$baseShape = new \ReflectionClass(BaseShape::class);
				$name = "";
				foreach($baseShape->getConstants() as $constant => $value) {
					if($value === $this->value) {
						$name = str_replace("shape_", "", strtolower($constant));
					}
				}
				$brush->setShape($name);
				$action = Change::ACTION_CHANGE_SHAPE;
				break;

			case 2:
				$baseType = new \ReflectionClass(BaseType::class);
				$name = "";
				foreach($baseType->getConstants() as $constant => $value) {
					if($value === $this->value) {
						$name = str_replace("type_", "", strtolower($constant));
					}
				}
				$brush->setType($name);
				$action = Change::ACTION_CHANGE_TYPE;
				break;

			case 3:
				$brush->setHollow((bool) $this->value);
				$action = Change::ACTION_CHANGE_HOLLOW;
				break;

			case 4:
				$brush->setDecrementing((bool) $this->value);
				$brush->resetSize = $brush->getSize();
				$action = Change::ACTION_CHANGE_DECREMENT;
				break;

			case 5:
				$brush->setHeight((int) $this->value);
				$action = Change::ACTION_CHANGE_HEIGHT;
				break;

			case 6:
				$brush->setPerfect((bool) $this->value);
				$action = Change::ACTION_CHANGE_PERFECT;
				break;

			case 7:
				$blocks = explode(",", $this->value);
				$brush->setBlocks($blocks);
				$action = Change::ACTION_CHANGE_BLOCKS;
				break;

			case 8:
				$blocks = explode(",", $this->value);
				$brush->setObsolete($blocks);
				$action = Change::ACTION_CHANGE_OBSOLETE;
				break;

			case 9:
				$brush->setBiome($this->value);
				$action = Change::ACTION_CHANGE_BIOME;
				break;

			case 10:
				$brush->setTree($this->value);
				$action = Change::ACTION_CHANGE_TREE;
				break;
		}
		$this->getLoader()->getServer()->getPluginManager()->callEvent(new Change($this->getLoader(), $this->player, $action, $this->value));
	}
}