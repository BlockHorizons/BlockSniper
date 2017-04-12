<?php

namespace BlockHorizons\BlockSniper\cloning;


use BlockHorizons\BlockSniper\Loader;
use pocketmine\block\Block;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ByteArrayTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;

class SchematicProcessor {

	private $loader;

	private $blocks = "";
	private $data = "";
	private $length = 1;
	private $width = 1;
	private $height = 1;

	public function __construct(Loader $loader) {
		$this->loader = $loader;
	}

	/**
	 * @param string $schematicName
	 *
	 * @return bool
	 */
	public function save(string $schematicName): bool {
		if(is_file($this->getSchematicFile($schematicName))) {
			return false;
		}
		$nbt = new NBT();
		$nbtCompound = new CompoundTag("Schematic", [
			new ByteArrayTag("Blocks", $this->blocks),
			new ByteArrayTag("Data", $this->data),
			new ShortTag("Length", $this->length),
			new ShortTag("Width", $this->width),
			new ShortTag("Height", $this->height),
			new StringTag("Materials", "Alpha")
		]);
		$nbt->setData($nbtCompound);

		file_put_contents($this->getSchematicFile($schematicName), $nbt->writeCompressed());
		foreach($this as $key => $value) {
			$key = null;
		}
		return true;
	}

	/**
	 * @param string $schematicName
	 *
	 * @return array|bool
	 */
	public function load(string $schematicName) {
		if(!is_file($this->getSchematicFile($schematicName))) {
			return false;
		}
		$nbt = new NBT();
		$nbt->readCompressed(file_get_contents($this->getSchematicFile($schematicName)));
		$values = $nbt->getData();

		var_dump($values);
		$this->blocks = $values->Blocks->getValue();
		$this->data = $values->Data->getValue();
		$this->height = (int) $values->Height->getValue();
		$this->width = (int) $values->Width->getValue();
		$this->length = (int) $values->Length->getValue();
		$blockInfo = [];

		for($x = 0; $x < $this->width; $x++) {
			for($y = 0; $y < $this->height; $y++) {
				for($z = 0; $z < $this->length; $z++) {
					$index = $y * $this->width * $this->length + $z * $this->width + $x;
					$blockInfo[] = [
						"pos" => new Vector3($x, $y, $z),
						"id" => ord($this->blocks[$index]),
						"damage" => ord($this->data[$index])
					];
				}
			}
		}
		return $blockInfo;
	}

	public function paste(string $schematicName, Player $player): bool {
		if(($blockInfo = $this->load($schematicName)) === false) {
			return false;
		}
		$undoBlocks = [];
		foreach($blockInfo as $key => $info) {
			$block = Block::get($info["id"], $info["damage"], new Position($info["pos"]->x, $info["pos"]->y, $info["pos"]->z, $player->getLevel()));
			$undoBlocks[] = $player->getLevel()->getBlock($block);
			$player->getLevel()->setBlock($block, $block, false, false);
		}
		$this->getLoader()->getUndoStorer()->saveUndo($undoBlocks, $player);
		return true;
	}

	/**
	 * @param Block[] $blocks
	 * @param int     $length
	 * @param int     $width
	 * @param int     $height
	 */
	public function submitValues(array $blocks, int $length, int $width, int $height) {
		$blockString = "";
		$dataString = "";

		foreach($blocks as $block) {
			$blockString .= chr($block->getId());
			$dataString .= chr($block->getDamage());
		}

		$this->blocks = $blockString;
		$this->data = $dataString;
		$this->length = $length;
		$this->width = $width;
		$this->height = $height;
	}

	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}

	/**
	 * @return string
	 */
	private function getSchematicFile(string $schematicName): string {
		return $this->getLoader()->getDataFolder() . "schematics/" . $schematicName . ".schematic";
	}

	/**
	 * @param string $schematicName
	 *
	 * @return bool
	 */
	public function schematicExists(string $schematicName): bool {
		return is_file($this->getSchematicFile($schematicName));
	}
}