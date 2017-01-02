<?php

namespace Sandertv\BlockSniper\shapes;

use Sandertv\BlockSniper\shapes\BaseShape;
use pocketmine\math\Vector3;
use pocketmine\math\Math;
use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\item\Item;

class CuboidShape extends BaseShape {
    
    public function __construct(Level $level, float $radius = null, Vector3 $center = null, array $blocks = []) {
        parent::__construct($level);
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
    public function fillShape(): bool {
        $targetX = $this->center->x;
        $targetY = $this->center->y;
        $targetZ = $this->center->z;
        return false;
    }

    public function getName(): string {
        return "Cuboid";
    }
    
    public function getPermission(): string {
        return "blocksniper.shape.cuboid";
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
