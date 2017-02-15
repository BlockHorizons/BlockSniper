<?php

namespace Sandertv\BlockSniper\commands\cloning;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use Sandertv\BlockSniper\cloning\Copy;
use Sandertv\BlockSniper\cloning\Template;
use Sandertv\BlockSniper\commands\BaseCommand;
use Sandertv\BlockSniper\Loader;

class CloneCommand extends BaseCommand {
	
	public function __construct(Loader $owner) {
		parent::__construct($owner, "clone", "Clone the area you're watching", "<type> <radiusXheight> [name]", []);
		$this->setPermission("blocksniper.command.clone");
	}
	
	/**
	 * @param CommandSender $sender
	 * @param type          $commandLabel
	 * @param array         $args
	 *
	 * @return boolean
	 */
	public function execute(CommandSender $sender, $commandLabel, array $args) {
		if(!$this->testPermission($sender) || !$sender->hasPermission("blocksniper.cloning." . $args[0])) {
			$this->sendNoPermission($sender);
			return true;
		}
		
		if(!$sender instanceof Player) {
			$this->sendConsoleError($sender);
			return true;
		}
		
		if(count($args) < 2 || count($args) > 3) {
			$sender->sendMessage(TF::RED . "[Usage] /clone <type> <radiusXheight> [name]");
			return true;
		}
		
		$sizes = explode("x", strtolower($args[1]));
		
		if((int)$sizes[0] > 70 || (int)$sizes[1] > 70) {
			$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.radius-too-big"));
			return true;
		}
		
		$center = $sender->getTargetBlock(100);
		if(!$center) {
			$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.no-target-found"));
			return true;
		}
		
		switch(strtolower($args[0])) {
			case "copy":
				$clone = new Copy($this->getPlugin(), $sender->getLevel(), $center, $sizes[0], $sizes[1]);
				break;
			
			case "template":
				if(!isset($args[2])) {
					$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.name-not-set"));
					return true;
				}
				$clone = new Template($this->getPlugin(), $sender->getLevel(), $args[2], $center, $sizes[0], $sizes[1]);
				break;
			
			default:
				$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.clone-not-found"));
				return true;
		}
		$clone->saveClone();
		$sender->sendMessage(TF::GREEN . $this->getPlugin()->getTranslation("commands.succeed.clone"));
	}
}
