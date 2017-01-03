<?php

namespace Sandertv\BlockSniper\shapes;

use Sandertv\BlockSniper\shapes\BaseShape;
use pocketmine\math\Vector3;
use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\item\Item;

class LayerShape extends BaseShape {
    
    public function __construct(Level $level, float $radius = null, Vector3 $center = null, array $blocks = []) {
        $this->level = $level;
        $this->radius = $radius;
        $this->center = $center;
        $this->blocks = $blocks;
        if(!isset($radius)) {
            $this->radius = 0;
        }
        if(!isset($center)) {
            $this->center = new Vector3(0, 0, 0);
        }
        if(!isset($blocks)) {
            $this->blocks = ["Air"];
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
        $maxX = $targetX + $this->radius;
        $maxZ = $targetZ + $this->radius;
        
        for($x = $minX; $x <= $maxX; $x++) {
            for($z = $minZ; $z <= $maxZ; $z++) {
                if(($x * $x) + ($z * $z) <= $radiusSquared) {
                    $randomName = $this->blocks[array_rand($this->blocks)];
                    $randomBlock = is_numeric($randomName) ? Item::get($randomName)->getBlock() : Item::fromString($randomName)->getBlock();
                    if($randomBlock->getId() !== 0 || strtolower($randomName) === "air") {
                        $this->level->setBlock(new Vector3($x, $targetY + 1, $z), $randomBlock, false, false);
                    }
                }
            }
        }
        if($randomBlock === Block::AIR && strtolower($randomName) !== "air") {
            return false;
        }
        return true;
    }

    public function getName(): string {
        return "Layer";
    }
    
    public function getPermission(): string {
        return "blocksniper.shape.layer";
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
    
    public function getBlocks(): array {
        return $this->blocks;
    }
    
    public function setBlocks(array $blocks) {
        $this->blocks = $blocks;
    }

    public function getLevel(): Level {
        return $this->level;
    }
}

