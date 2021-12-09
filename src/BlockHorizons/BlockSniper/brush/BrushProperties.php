<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush;

use BlockHorizons\BlockSniper\brush\shape\SphereShape;
use BlockHorizons\BlockSniper\brush\type\FillType;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\parser\Parser;
use JsonSerializable;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\BiomeIds;
use pocketmine\Server;
use pocketmine\world\biome\Biome;

/**
 * Class BrushProperties holds all properties used by BrushTypes and BrushShapes. They may be applied using the Brush
 * instance, which extends it, in order to directly brush. Alternatively, a BrushProperties instance may be directly
 * passed into BrushTypes and BrushShapes to use their functionality.
 */
class BrushProperties implements JsonSerializable{

	/**
	 * type is the brush type. It must be a '::class' string of a class that extends Type.
	 *
	 * @var string
	 * @phpstan-var class-string<Type>
	 */
	public $type = FillType::class;
	/**
	 * shape is the brush shape. It must be a '::class' string of a class that extends Shape.
	 *
	 * @var string
	 * @phpstan-var class-string<Shape>
	 */
	public $shape = SphereShape::class;
	/**
	 * mode is the mode of the brush. It must be either Brush::MODE_BRUSH or Brush::MODE_SELECTION.
	 *
	 * @var int
	 */
	public $mode = Brush::MODE_BRUSH;
	/**
	 * size is the size of the brush, which is only applied if the mode is Brush::MODE_BRUSH. Some shapes don't use the
	 * size property, but instead use one of the fields below.
	 *
	 * @var int
	 */
	public $size = 5;
	/**
	 * height, width and length are alternative fields to the size field. The Cuboid-, Cylinder- and EllipsoidShape use
	 * these fields.
	 *
	 * @var int
	 */
	public $height = 5, $width = 5, $length = 5;
	/**
	 * hollow specifies if the shapes created by the brush are hollow.
	 *
	 * @var bool
	 */
	public $hollow = false;
	/**
	 * decrementing specifies if the brush properties' size is decreased by one each time the brush is used.
	 *
	 * @var bool
	 */
	public $decrementing = false;
	/**
	 * resetSize specifies the brush's reset size if the size reaches 0 while decrementing is set to true.
	 *
	 * @var int
	 */
	public $resetSize = 0;
	/**
	 * brushBlocks is a string of brush blocks. These blocks are separated using commas and may contain tags. (Even though
	 * they don't have functionality yet)
	 * An example: stone,birch_log[axis=x]
	 *
	 * @var string
	 */
	public $brushBlocks = "stone";
	/**
	 * replacedBlocks is a string of blocks. These blocks are replaced if the replace brush type is used. The format
	 * of the string is the same as that of the blocks field.
	 *
	 * @var string
	 */
	public $replacedBlocks = "air";
	/**
	 * biomeId is the biome ID used to brush biomes with. The biome ID must be one of the constantsvfound in the BiomeIds
	 * class.
	 *
	 * @var int
	 */
	public $biomeId = BiomeIds::PLAINS;
	/**
	 * tree holds properties specifically used by the tree type brush.
	 *
	 * @var TreeProperties
	 */
	public $tree;
	/**
	 * layerWidth is the width used by the TopLayerType. It specifies the width of the layer brushed at the top. In
	 * other words, the amount of blocks n+1 blocks it should extend down.
	 *
	 * @var int
	 */
	public $layerWidth = 0;
	/**
	 * soilBlocks is a string of soil blocks. These blocks are the only blocks that brush blocks are placed on top of if
	 * the PlantType is used.
	 *
	 * @var string
	 */
	public $soilBlocks = "grass";

	public function __construct(){
		// Initialise the tree properties object as we can't have default property object values.
		$this->tree = new TreeProperties();
	}

	/**
	 * getBrushBlocks returns the parsed brush blocks set in the brush properties.
	 *
	 * @return Block[]
	 */
	public function getBrushBlocks() : array{
		return $this->parseBlocks($this->brushBlocks);
	}

	/**
	 * getReplacedBlocks returns the parsed blocks that are replaced when using the ReplaceType.
	 *
	 * @return Block[]
	 */
	public function getReplacedBlocks() : array{
		return $this->parseBlocks($this->replacedBlocks);
	}

	/**
	 * getSoilBlocks returns the parsed blocks that brush blocks are only placed upon when using the PlantType.
	 *
	 * @return Block[]
	 */
	public function getSoilBlocks() : array{
		return $this->parseBlocks($this->soilBlocks);
	}

	/**
	 * parseBlocks parses a block string as seen in one of the fields above. It throws an exception if the block string
	 * could not be parsed successfully.
	 *
	 * @param string $data
	 *
	 * @return Block[]
	 */
	public function parseBlocks(string $data) : array{
		$items = Parser::parse($data);
		if(empty($items)){
			return [VanillaBlocks::AIR()];
		}
		$blocks = [];
		foreach($items as $key => $item){
			$blocks[$key] = $item->getBlock();
		}

		return $blocks;
	}

	/**
	 * decrement decrements the BrushProperties's size field if the brush's decrementing property is set to true. If the
	 * size reaches 0, and resetDecrementBrush is set to true in the configuration, the size is reset.
	 */
	public function decrement() : void{
		if(!$this->decrementing){
			return;
		}
		if($this->size > 1){
			$this->size = $this->size - 1;

			return;
		}
		/** @var Loader|null $loader */
		$loader = Server::getInstance()->getPluginManager()->getPlugin("BlockSniper");
		if($loader === null){
			return;
		}
		if($loader->config->resetDecrementBrush){
			$this->size = $this->resetSize;
		}
	}

	/**
	 * @return mixed[]
	 */
	public function jsonSerialize() : array{
		return (array) $this;
	}
}