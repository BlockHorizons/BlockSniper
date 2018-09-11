# BlockSniper API Documentation

<br>

### Getting an instance of BlockHorizons\BlockSniper\Loader
To get an instance of the main class of BlockSniper, the Loader class, add this code on enable.
```php
public $blockSniperPlugin;
  
public function onEnable() {
    $this->blockSniperPlugin = $this->getServer()->getPluginManager()->getPlugin("BlockSniper");
}
```
From here on inside functions in the plugin can be called.

<br>

### Hooking into BlockSniper events
Hooking into BlockSniper events is very easy, and follows the same usage as a normal PocketMine event would. Hooking in the events would require registering events, implementing Listener and adding a function. Some events are cancellable, some are not.
```php
<?php

namespace Me\MyPlugin;
    
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use BlockHorizons\BlockSniper\events\BrushUseEvent;
    
class MyClass extends PluginBase implements Listener {
    
    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    
    public function onBrushUse(BrushUseEvent $event) {
        if(strtolower($event->getPlayer()->getName()) === "steve") {
            $event->setCancelled();
        }
    }
}
```
|Event|Cancellable|
|-----|-----------|
|BrushRecoverEvent|True|
|BrushUseEvent|True|
|ChangeBrushPropertiesEvent|False|
|PresetCreationEvent|True|

<br>

### Registering new Shapes
BlockSniper adds very easy to use API for adding new Shapes and Types. A new Shape or Type class requires at LEAST the following:
```php
<?php
    
namespace MyPlugin\MyShapes;
    
use BlockHorizons\BlockSniper\brush\BaseShape;
use pocketmine\Player;
use pocketmine\level\Level;
use pocketmine\level\Position;
    
class MyShape extends BaseShape {
	
	/**
     * ID is a unique ID for this shape. Unless it is intended to replace an existing shape,
     * you should check for existing shapes to find which IDs are free to use.
     */
	const ID = 5;
    
    /**
     * getBlocksInside should return a generator containing the blocks collected in the shape.
     * If $vectorOnly is true, the generator should yield only Vector3s.
     */
    public function getBlocksInside(bool $vectorOnly = false): \Generator {
        return [];
    }
    
    /**
     * getName should return the name of the shape as seen in the brush UI.
     */
    public function getName(): string {
        return "";
    }
    
    public function getApproximateProcessedBlocks(): int {
        return 0;
    }
    
    public function getTouchedChunks(): array {
    	return [];
    }
}
```

In order to make this new shape able to be used, the following code should be executed anywhere in your plugin:
```php
\BlockHorizons\BlockSniper\brush\registration\ShapeRegistration::registerShape(MyShape::class, MyShape::ID);
```

<br>

### Registering new Types
Registering new types is very similar to the registering of types. Only a couple small things are different. No further explanation should be needed, see the example below.
```php
<?php
    
namespace MyPlugin\MyTypes;
    
use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\level\ChunkManager;
use pocketmine\Player;
    
class MyType extends BaseType {
	
    public function __construct(Player $player, ChunkManager $level, \Generator $blocks) {
        parent::__construct($player, $level, $blocks);
    }
    
    /**
     * fillSynchronously should return an generator yielding the undo blocks.
     */
    public function fillSynchronously(): \Generator {
    	foreach($this->blocks as $block){
    		// Yield the block before it is changed.
    		yield $block;
    		// Set the block to dirt.
    		$this->putBlock($block, 3, 0);
    	}
        return [];
    }
    
    /**
     * fillAsynchronously is optional, and could be left if the function below is added.
     * $this->blocks will have a list of Vector3s, and $this->level is a BlockSniperChunkManager
     * instead of a level.
     */
    public function fillAsynchronously(): void {
        
    }
    
    /**
     * canBeExecutedAsynchronously specifies whether this type can be executed asynchronously or not.
     */
    public function canBeExecutedAsynchronously(): bool {
        return false;
    }
    
    /**
     * getName returns the name to show up in the brush UI. Can have spaces or other
     * otherwise weird characters.
     */
    public function getName(): string {
        return "";
    }
}
```
To register a new type, use:
```php
\BlockHorizons\BlockSniper\brush\registration\TypeRegistration::registerType(MyType::class, MyType::ID);
```

<br>

### Getting the Brush of a player
Getting the brush of a player is very easy. A brush object of a player can be obtained by using:
```php
<?php
/** var pocketmine\Player $player */
if(($session = \BlockHorizons\BlockSniper\sessions\SessionManager::getPlayerSession($player)) === null) {
    return false;
}
$brush = $session->getBrush();
$brush->size = 10;
$brush->hollow = true;
?>
```

<br><br>

Hopefully this API introduction was helpful. If there's anything you think should be added, make sure to say so in the [issues](https://github.com/BlockHorizons/BlockSniper/issues) or contact us on our [Gitter chat](https://gitter.im/BlockHorizons/BlockSniper)

