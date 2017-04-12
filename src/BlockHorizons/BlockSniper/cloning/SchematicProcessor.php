<?php

namespace BlockHorizons\BlockSniper\cloning;


use BlockHorizons\BlockSniper\Loader;
use pocketmine\block\Block;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ByteArrayTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;

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
	 * @param string $name
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
		return true;
	}

	public function load(string $schematicName): bool {
		if(!is_file($this->getSchematicFile($schematicName))) {
			return false;
		}
		return true;
	}

	public function paste(string $schematicName): bool {
		if(!is_file($this->getSchematicFile($schematicName))) {
			return false;
		}
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
}