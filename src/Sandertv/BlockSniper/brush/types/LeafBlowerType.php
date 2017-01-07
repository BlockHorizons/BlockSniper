<?php

namespace Sandertv\BlockSniper\brush\types;

use Sandertv\BlockSniper\brush\BaseType;
use pocketmine\math\Vector3;
use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\item\Item;
use pocketmine\block\Flowable;

class LeafBlowerType extends BaseType {
    
    public function __construct(Level $level, float $radius = null, Vector3 $center = null) {
        $this->level = $level;
        $this->radius = $radius;
        $this->center = $center;

        if(!isset($center)) {
            $this->center = new Vector3(0, 0, 0);
        }
    }
    
    /**
     * @return bool
     */
    public function fillShape(): bool {
        $radiusSquared = pow($this->radius, 2);
        $targetX = $this->center->x;
        $targetY = $this->center->y;
        $targetZ = $this->center->z;
        
        $minX = $targetX - $this->radius;
        $minZ = $targetZ - $this->radius;
        $minY = $targetY - 2;
        $maxX = $targetX + $this->radius;
        $maxZ = $targetZ + $this->radius;
        $maxY = $targetY + 2;
        
        $valid = false;
        for($x = $minX; $x <= $maxX; $x++) {
            for($z = $minZ; $z <= $maxZ; $z++) {
                for($y = $minY; $y <= $maxY; $y++) {
                    if(pow($targetX - $x, 2) + pow($targetZ - $z, 2) <= $radiusSquared) {
                        if($this->level->getBlock(new Vector3($x, $targetY + 1, $z)) instanceof Flowable) {
                            $this->level->dropItem(new Vector3($x, $targetY + 1, $z), Item::get($this->level->getBlock(new Vector3($x, $targetY + 1, $z))->getId()));
                            $this->level->setBlock(new Vector3($x, $targetY + 1, $z), Block::get(Block::AIR), false, false);
                            $valid = true;
                        }
                    }
                }
            }
        }
        if($randomBlock === Block::AIR && strtolower($randomName) !== "air") {
            return false;
        }
        if($valid) {
            return true;
        }
        return false;
    }

    public function getName(): string {
        return "Leaf blower";
    }
    
    public function getPermission(): string {
        return "blocksniper.type.leafblower";
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

    public function getLevel(): Level {
        return $this->level;
    }
}