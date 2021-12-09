<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\BrushProperties;
use BlockHorizons\BlockSniper\exception\InvalidItemException;
use Generator;
use pocketmine\block\Block;
use pocketmine\block\BlockLegacyIds;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use pocketmine\world\Position;
use pocketmine\world\World;

class Tree{

	/** @var Block[] */
	public $trunkBlocks;
	/** @var Block[] */
	public $leavesBlocks;

	/** @var int */
	public $trunkHeight = 50;
	/** @var int */
	public $trunkWidth = 2;
	/** @var int */
	public $leavesClusterSize = 12;
	/** @var int */
	public $maxBranchLength = 15;


	/** @var Position */
	private $position;
	/** @var float */
	private $startY = 0;
	/** @var Random */
	private $random;

	/** @var int */
	private $lastAddX = 0;
	/** @var int */
	private $lastAddZ = 0;
	/** @var Vector3 */
	private $tempVec;
	/** @var TreeType */
	private $type;

	/** @var Vector3[] */
	private $leavesCentres = [];

	/** @phpstan-var array<int, null> */
	private array $set = [];

	public function __construct(Position $position, BrushProperties $brush, TreeType $type){
		$this->random = new Random(random_int(0, 1000000));
		$this->tempVec = new Vector3(0, 0, 0);

		try{
			$this->trunkBlocks = $brush->parseBlocks($brush->tree->trunkBlocks);
			$this->leavesBlocks = $brush->parseBlocks($brush->tree->leavesBlocks);
		}catch(InvalidItemException $exception){
			return;
		}

		$this->trunkHeight = $brush->tree->trunkHeight;
		$this->trunkWidth = $brush->tree->trunkWidth;
		$this->leavesClusterSize = $brush->tree->leavesClusterSize;
		$this->maxBranchLength = $brush->tree->maxBranchLength;

		$this->position = $position;
		$this->type = $type;
		$this->startY = $position->y;
	}

	/**
	 * @phpstan-return \Generator<int, Block, void, void>
	 */
	public function build() : Generator{
		for($i = 0; $i < $this->trunkHeight; $i++){
			foreach($this->buildTrunkDisk() as $block){
				yield $block;
			}
		}
		$this->position->y--;
		foreach($this->buildBranch() as $block){
			yield $block;
		}
		foreach($this->leavesCentres as $centre){
			foreach($this->buildLeaves($centre) as $block){
				yield $block;
			}
		}
	}

	/**
	 * @phpstan-return \Generator<int, Block, void, void>
	 */
	private function buildTrunkDisk() : Generator{
		if(mt_rand(0, 1) === 0){
			if($this->lastAddX !== 0 && $this->lastAddZ !== 0){
				$this->lastAddX = $this->lastAddZ = 0;
			}else{
				while(($x = -$this->random->nextRange(-1, 1)) === -$this->lastAddX){
				}
				while(($z = -$this->random->nextRange(-1, 1)) === -$this->lastAddZ){
				}
				$this->lastAddX = $x;
				$this->position->x += $this->lastAddX;
				$this->lastAddZ = $z;
				$this->position->z += $this->lastAddZ;
			}
		}
		$this->trunkWidth -= $this->trunkWidth / $this->trunkHeight;

		if($this->position->y - $this->startY > 10){
			if(mt_rand(0, (int) (($this->trunkHeight / ($this->position->y - $this->startY)) ** 2 / 2)) === 0){
				foreach($this->buildBranch() as $block){
					yield $block;
				}
			}
		}

		$radiusSquared = $this->trunkWidth ** 2 + 0.5;

		$minX = $this->position->x - $this->trunkWidth;
		$minZ = $this->position->z - $this->trunkWidth;
		$maxX = $this->position->x + $this->trunkWidth;
		$maxZ = $this->position->z + $this->trunkWidth;

		for($x = $minX; $x <= $maxX; $x++){
			$xs = ($this->position->x - $x) ** 2;
			for($z = $minZ; $z <= $maxZ; $z++){
				if($xs + ($this->position->z - $z) ** 2 > $radiusSquared){
					continue;
				}
				[$xInt, $yInt, $zInt] = [(int) floor($x), (int) floor($this->position->y), (int) floor($z)];
				$bHash = World::blockHash($xInt, $yInt, $zInt);
				if(array_key_exists($bHash, $this->set)){
					continue;
				}
				$this->set[$bHash] = null;

				[$this->tempVec->x, $this->tempVec->y, $this->tempVec->z] = [$x, $this->position->y, $z];
				yield $this->position->world->getBlock($this->tempVec);
				$bl = clone $this->trunkBlocks[array_rand($this->trunkBlocks)];
				$this->type->putBlock($this->tempVec->floor(), $bl);
			}
		}
		$this->position->y++;
	}

	/**
	 * @phpstan-return \Generator<int, Block, void, void>
	 */
	private function buildBranch() : Generator{
		$addX = $this->random->nextRange(-$this->maxBranchLength, $this->maxBranchLength);
		$addY = $this->random->nextRange((int) (-$this->maxBranchLength), (int) (-$this->maxBranchLength * 0.2));
		$addZ = $this->random->nextRange(-$this->maxBranchLength, $this->maxBranchLength);
		$branchEnd = $this->position->add($addX, $addY, $addZ);
		$branchPos = clone $this->position;

		$branchWidth = $this->trunkWidth;

		$direction = $branchPos->subtractVector($branchEnd)->normalize();
		for($i = 0; $i < $this->maxBranchLength; $i++){
			$branchPos = $branchPos->addVector($direction);

			$minX = $branchPos->x - $branchWidth / 2;
			$minZ = $branchPos->z - $branchWidth / 2;
			$maxX = $branchPos->x + $branchWidth / 2;
			$maxZ = $branchPos->z + $branchWidth / 2;
			$minY = $branchPos->y - $branchWidth / 2;
			$maxY = $branchPos->y + $branchWidth / 2;

			$branchWidth -= 0.05;

			$radiusSquared = ($branchWidth / 2) ** 2 + 0.5;
			for($x = $maxX; $x >= $minX; $x--){
				$xs = ($branchPos->x - $x) ** 2;
				for($y = $maxY; $y >= $minY; $y--){
					$ys = ($branchPos->y - $y) ** 2;
					for($z = $maxZ; $z >= $minZ; $z--){
						$zs = ($branchPos->z - $z) ** 2;
						if($xs + $ys + $zs - 0.5 < $radiusSquared){
							if($branchWidth < 0.1){
								break;
							}
							[$xInt, $yInt, $zInt] = [(int) floor($x), (int) floor($y), (int) floor($z)];
							$bHash = World::blockHash($xInt, $yInt, $zInt);
							if(array_key_exists($bHash, $this->set)){
								continue;
							}
							$this->set[$bHash] = null;

							[$this->tempVec->x, $this->tempVec->y, $this->tempVec->z] = [$x, $y, $z];
							yield $this->position->world->getBlock($this->tempVec);

							$bl = clone $this->trunkBlocks[array_rand($this->trunkBlocks)];
							$this->type->putBlock($this->tempVec->floor(), $bl);
						}
					}
				}
			}
		}
		$this->leavesCentres[] = $branchPos;
	}

	/**
	 * @phpstan-return Generator<int, Block, void, void>
	 */
	private function buildLeaves(Vector3 $branchEnd) : Generator{
		$minX = $branchEnd->x - $this->leavesClusterSize / 2;
		$minZ = $branchEnd->z - $this->leavesClusterSize / 2;
		$maxX = $branchEnd->x + $this->leavesClusterSize / 2;
		$maxZ = $branchEnd->z + $this->leavesClusterSize / 2;
		$minY = $branchEnd->y - $this->leavesClusterSize / 2;
		$maxY = $branchEnd->y + $this->leavesClusterSize / 2;

		$radiusSquared = ($this->leavesClusterSize / 2) ** 2 + 0.5;
		for($x = $maxX; $x >= $minX; $x--){
			$xs = ($branchEnd->x - $x) ** 2;
			for($y = $maxY; $y >= $minY; $y--){
				$ys = ($branchEnd->y - $y) ** 2;
				for($z = $maxZ; $z >= $minZ; $z--){
					$zs = ($branchEnd->z - $z) ** 2;
					if($xs + $ys + $zs - 0.5 < $radiusSquared){
						[$this->tempVec->x, $this->tempVec->y, $this->tempVec->z] = [$x, $y, $z];
						$block = $this->position->world->getBlock($this->tempVec);
						if($block->getId() !== BlockLegacyIds::AIR){
							continue;
						}
						if(mt_rand(0, 4) === 0){
							yield $block;
							$bl = clone $this->leavesBlocks[array_rand($this->leavesBlocks)];
							$this->type->putBlock($this->tempVec->floor(), $bl);
						}
					}
				}
			}
		}
	}
}