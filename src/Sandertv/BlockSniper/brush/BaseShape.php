<?php

namespace Sandertv\BlockSniper\brush;

abstract class BaseShape {

    const MAX_WORLD_HEIGHT = 256;
    const MIN_WORLD_HEIGHT = 0;
    
    const SHAPE_CUBOID = 0, SHAPE_CUBE = 0;
    const SHAPE_SPHERE = 1, SHAPE_BALL = 1;
    const SHAPE_CYLINDER = 2, SHAPE_CYLINDER_STANDING = 2, SHAPE_STANDING_CYLINDER = 2;
    
    const TYPE_OVERLAY = 3;
    const TYPE_LAYER = 4, TYPE_FLAT_LAYER = 4;
    const TYPE_REPLACE = 5;
    
    public abstract function getName(): string;
    
    public abstract function getPermission(): string;
    
    public abstract function fillShape(): bool;
}
