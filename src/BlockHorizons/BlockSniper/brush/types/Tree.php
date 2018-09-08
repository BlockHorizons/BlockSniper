<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\Brush;
use pocketmine\block\Solid;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

class Tree{

	/** @var []Block */
	public $trunkBlocks;
	/** @var []Block */
	public $leavesBlocks;

	public $trunkHeight = 50;
	public $trunkWidth = 2;
	public $leavesClusterSize = 12;
	public $maxBranchLength = 15;


	/** @var Position */
	private $position;
	private $startY = 0;
	/** @var Random */
	private $random;

	private $lastAddX = 0;
	private $lastAddZ = 0;
	/** @var Vector3 */
	private $tempVec;

	public function __construct(Position $position, Brush $brush){
		$this->position = $position;
		$this->startY = $position->y;

		$this->trunkBlocks = $brush->parseBlocks($brush->tree->trunkBlocks);
		$this->leavesBlocks = $brush->parseBlocks($brush->tree->leavesBlocks);
		$this->trunkHeight = $brush->tree->trunkHeight;
		$this->trunkWidth = $brush->tree->trunkWidth;
		$this->leavesClusterSize = $brush->tree->leavesClusterSize;
		$this->maxBranchLength = $brush->tree->maxBranchLength;

		try{
			$this->random = new Random(random_int(0, 1000000));
		}catch(\Exception $exception){

		}

		$this->tempVec = new Vector3();
	}

	public function build() : \Generator{
		for($i = 0; $i < $this->trunkHeight; $i++){
			foreach($this->buildTrunkDisk() as $block){
				yield $block;
			}
		}
		$this->position->y--;
		foreach($this->buildBranch() as $block){
			yield $block;
		}
	}

	private function buildTrunkDisk() : \Generator{
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
				[$this->tempVec->x, $this->tempVec->y, $this->tempVec->z] = [$x, $this->position->y, $z];
				yield $this->position->level->getBlock($this->tempVec);
				$this->position->level->setBlock($this->tempVec, clone $this->trunkBlocks[array_rand($this->trunkBlocks)], false, false);
			}
		}
		$this->position->y++;
	}

	private function buildBranch() : \Generator{
		$addX = $this->random->nextRange(-$this->maxBranchLength, $this->maxBranchLength);
		$addY = $this->random->nextRange((int) (-$this->maxBranchLength), (int) (-$this->maxBranchLength * 0.2));
		$addZ = $this->random->nextRange(-$this->maxBranchLength, $this->maxBranchLength);
		$branchEnd = $this->position->add($addX, $addY, $addZ);
		$branchPos = clone $this->position;

		$branchWidth = $this->trunkWidth;

		$direction = $branchPos->subtract($branchEnd)->normalize();
		for($i = 0; $i < $this->maxBranchLength; $i++){
			$branchPos = $branchPos->add($direction);

			$minX = $branchPos->x - $branchWidth / 2;
			$minZ = $branchPos->z - $branchWidth / 2;
			$maxX = $branchPos->x + $branchWidth / 2;
			$maxZ = $branchPos->z + $branchWidth / 2;
			$minY = $branchPos->y - $branchWidth / 2;
			$maxY = $branchPos->y + $branchWidth / 2;

			$branchWidth -= 0.05;

			$radiusSquared = ($branchWidth / 2) ** 2 + 0.5;
			$j = 0;
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
							[$this->tempVec->x, $this->tempVec->y, $this->tempVec->z] = [$x, $y, $z];
							yield $this->position->level->getBlock($this->tempVec);
							$this->position->level->setBlock($this->tempVec, clone $this->trunkBlocks[array_rand($this->trunkBlocks)], false, false);
							$j++;
						}
					}
				}
			}
			if($j === 0){
				break;
			}
		}
		foreach($this->buildLeaves($branchPos) as $block){
			yield $block;
		}
	}

	private function buildLeaves(Vector3 $branchEnd) : \Generator{
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
						if(($block = $this->position->level->getBlock($this->tempVec)) instanceof Solid){
							continue;
						}
						if(mt_rand(0, 4) === 0){
							yield($block);
							$this->position->level->setBlock($this->tempVec, clone $this->leavesBlocks[array_rand($this->leavesBlocks)], false, false);
						}
					}
				}
			}
		}
	}
}