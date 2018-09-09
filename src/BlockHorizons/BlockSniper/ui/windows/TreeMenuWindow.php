<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\brush\types\TreeType;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\Player;

class TreeMenuWindow extends CustomWindow{

	public function __construct(Loader $loader, Player $requester){
		parent::__construct($this->t(Translation::UI_TREE_MENU_TITLE));
		$b = SessionManager::getPlayerSession($requester)->getBrush();

		$this->addInput($this->t(Translation::UI_TREE_MENU_TRUNK_BLOCKS), $b->tree->trunkBlocks, "log:12,log:13", function(Player $player, string $value) use ($b){
			$b->tree->trunkBlocks = $value;
		});
		$this->addInput($this->t(Translation::UI_TREE_MENU_LEAVES_BLOCKS), $b->tree->leavesBlocks, "leaves:12,leaves:13", function(Player $player, string $value) use ($b){
			$b->tree->leavesBlocks = $value;
		});
		$this->addSlider($this->t(Translation::UI_TREE_MENU_TRUNK_HEIGHT), 0, $loader->config->maxSize, 1, $b->tree->trunkHeight, function(Player $player, float $value) use ($b){
			$b->tree->trunkHeight = (int) $value;
		});
		$this->addSlider($this->t(Translation::UI_TREE_MENU_TRUNK_WIDTH), 0, (int) ($loader->config->maxSize / 3), 1, $b->tree->trunkWidth, function(Player $player, float $value) use ($b){
			$b->tree->trunkWidth = (int) $value;
		});
		$this->addSlider($this->t(Translation::UI_TREE_MENU_MAX_BRANCH_LENGTH), 0, $loader->config->maxSize, 1, $b->tree->maxBranchLength, function(Player $player, float $value) use ($b){
			$b->tree->maxBranchLength = (int) $value;
		});
		$this->addSlider($this->t(Translation::UI_TREE_MENU_LEAVES_CLUSTER_SIZE), 0, $loader->config->maxSize / 2, 1, $b->tree->leavesClusterSize, function(Player $player, float $value) use ($b){
			$b->tree->leavesClusterSize = (int) $value;
		});

		// We set the type of the brush to TreeType when this window is opened, as that was probably intended by the user.
		$b->type = TreeType::class;
	}
}