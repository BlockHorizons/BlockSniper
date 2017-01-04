<?php

namespace Sandertv\BlockSniper\brush\types;

use Sandertv\BlockSniper\brush\BaseShape;
use pocketmine\math\Vector3;
use pocketmine\math\Math;
use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\item\Item;

class ReplaceType extends BaseShape {
    
    public function __construct(Level $level, float $radius = null, Vector3 $center = null, $block = null, array $replacements = []) {
        $this->level = $level;
        $this->radius = $radius;
        $this->center = $center;
        $this->block = $block;
        $this->replacements = $replacements;
        
        if(!isset($center)) {
            $this->center = new Vector3(0, 0, 0);
        }
        if(!isset($replacements)) {
            $this->replacements = ["Air"];
        }
    }
    
    /**
     * @return bool
     */
    public function fillShape(): bool {
        $targetX = $this->center->x;
        $targetY = $this->center->y;
        $targetZ = $this->center->z;
        
        $minX = $targetX - $this->radius;
        $minY = $targetY - $this->radius;
        $minZ = $targetZ - $this->radius;
        $maxX = $targetX + $this->radius;
        $maxY = $targetY + $this->radius;
        $maxZ = $targetZ + $this->radius;
        
        for($x = $minX; $x <= $maxX; $x++) {
            for($y = $minY; $y <= $maxY; $y++) {
                for($z = $minZ; $z <= $maxZ; $z++) {
                    $toBeReplaced = is_numeric($this->block) ? Item::get($this->block)->getBlock() : Item::fromString($this->block)->getBlock();
                    $randomName = $this->replacements[array_rand($this->replacements)];
                    $randomBlock = is_numeric($randomName) ? Item::get($randomName)->getBlock() : Item::fromString($randomName)->getBlock();
                    if($randomBlock->getId() !== 0 || strtolower($randomName) === "air" && $this->level->getBlock(new Vector3($x, $y, $z))->getId() === $toBeReplaced->getId()) {
                        $this->level->setBlock(new Vector3($x, $y, $z), $randomBlock, false, false);
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
        return "Replace";
    }
    
    public function getPermission(): string {
        return "blocksniper.type.replace";
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
        return $this->replacements;
    }
    
    public function setBlocks(array $blocks) {
        $this->replacements = $blocks;
    }

    public function getLevel(): Level {
        return $this->level;
    }
}

