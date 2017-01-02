<?php

namespace Sandertv\BlockSniper\shapes;

use pocketmine\math\Vector3;

abstract class BaseShape {

    const MAX_WORLD_HEIGHT = 256;
    const MIN_WORLD_HEIGHT = 0;
    
    const TYPE_CUBOID = 1, TYPE_CUBE = 1;
    const TYPE_SPHERE = 2, TYPE_BALL = 2;
    
    public abstract function getName(): string;
    
    public abstract function getPermission(): string;
    
    public abstract function fillShape(): bool;
}
