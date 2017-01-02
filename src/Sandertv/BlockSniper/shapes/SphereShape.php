<?php

namespace Sandertv\BlockSniper\shapes;

use Sandertv\BlockSniper\shapes\BaseShape;
use pocketmine\math\Vector3;
use pocketmine\math\Math;
use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\item\Item;

class SphereShape extends BaseShape {
    
    public function __construct(BaseShape $owner, Level $level, float $radius = null, Vector3 $center = null, array $blocks = []) {
        parent::__construct($owner);
        $this->owner = $owner;
        $this->level = $level;
        if(!isset($radius)) {
            $this->radius = 0;
        }
        if(!isset($center)) {
            $this->center = new Vector3(0, 0, 0);
        }
        if(!isset($block)) {
            $this->blocks = ["Air"];
        }
    }
    
    /**
     * @return bool
     */
    public function fillShape(): bool {;
        $radiusSquared = pow($this->radius, 2);
        
        $targetX = $this->center->x;
        $targetY = $this->center->y;
        $targetZ = $this->center->z;
        
        $minX = Math::floorFloat($targetX - $this->radius);
        $maxX = Math::floorFloat($targetX + $this->radius) + 1;
        $minY = max(Math::floorFloat($targetY - $this->radius), 0);
        $maxY = min(Math::floorFloat($targetY + $this->radius) + 1, BaseShape::MAX_WORLD_HEIGHT);
        $minZ = Math::floorFloat($targetZ - $this->radius);
        $maxZ = Math::floorFloat($targetZ + $this->radius) + 1;

        for($x = $maxX; $x >= $minX; $x--) {
            $xs = ($targetX - $x) * ($targetX - $x);
            for($y = $maxY; $y >= $minY; $y--) {
                $ys = ($targetY - $y) * ($targetY - $y);
                for($z = $maxZ; $z >= $minZ; $z--) {
                    $zs = ($targetZ - $z) * ($targetZ - $z);
                    if($xs + $ys + $zs < $radiusSquared) {
                        $randomBlock = Item::fromString($this->blocks[array_rand($this->blocks)])->getBlock();
                        if($randomBlock->getId() !== null) {
                            $this->level->setBlock(new Vector3($x, $y, $z), $randomBlock, true, false);
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    public function getName(): string {
        return "Sphere";
    }
    
    public function getPermission(): string {
        return "blocksniper.shape.sphere";
    }
    
    public function getApproximateBlocks(): int {
        // TODO
    }
    
    public function getRadius(): float {
        return $this->radius;
    }
    
    public function setRadius(float $radius) {
        $this->radius = $radius;
    }
    
    public function getCenter(): Vector3 {
        return $this->center;
    }
    
    public function setCenter(Vector3 $center) {
        $this->center = $center;
    }
    
    public function getBlock(): Block {
        return $this->block;
    }
    
    public function setBlock(Block $block) {
        $this->block = $block;
    }

    public function getLevel(): Level {
        return $this->level;
    }
}
