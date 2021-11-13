<?php

namespace BlockHorizons\BlockSniper\brush\async;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\math\Facing;
use pocketmine\world\SimpleChunkManager;

class BlockSniperChunkManager extends SimpleChunkManager{

	public function getBlockAt(int $x, int $y, int $z) : Block{
		$block = parent::getBlockAt($x, $y, $z);
		[$block->getPosition()->x, $block->getPosition()->y, $block->getPosition()->z] = [$x, $y, $z];

		return $block;
	}

	/**
	 * @param int $x
	 * @param int $z
	 * @param int $id
	 */
	public function setBiomeId(int $x, int $z, int $id) : void{
		if($chunk = $this->getChunk($x >> 4, $z >> 4)){
			$chunk->setBiomeId($x & 0x0f, $z & 0x0f, $id);
		}
	}

	/**
	 * @param int $x
	 * @param int $y
	 * @param int $z
	 * @param int $side
	 *
	 * @return Block
	 */
	public function getSide(int $x, int $y, int $z, int $side) : Block{
		if($chunk = $this->getChunk($x >> 4, $z >> 4)){
			switch($side){
				default:
				case Facing::DOWN:
					[$x, $y, $z] = [$x, $y - 1, $z];
					break;
				case Facing::UP:
					[$x, $y, $z] = [$x, $y + 1, $z];
					break;
				case Facing::NORTH:
					[$x, $y, $z] = [$x, $y, $z - 1];
					break;
				case Facing::SOUTH:
					[$x, $y, $z] = [$x, $y, $z + 1];
					break;
				case Facing::WEST:
					[$x, $y, $z] = [$x - 1, $y, $z];
					break;
				case Facing::EAST:
					[$x, $y, $z] = [$x + 1, $y, $z];
			}
			$block = $this->getBlockAt($x, $y, $z);
			[$block->getPosition()->x, $block->getPosition()->y, $block->getPosition()->z] = [$x, $y, $z];

			return $block;
		}

		return VanillaBlocks::AIR();
	}
}