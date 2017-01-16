<?php

namespace Sandertv\BlockSniper\commands\cloning;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use Sandertv\BlockSniper\commands\BaseCommand;
use Sandertv\BlockSniper\Loader;
use Sandertv\BlockSniper\cloning\Copy;
use Sandertv\BlockSniper\cloning\Template;

class PasteCommand extends BaseCommand {
	
	public function __construct(Loader $owner) {
		parent::__construct($owner, "clone", "Clone the area you're watching", "<type> <radiusXheight>", []);
		$this->setPermission("blocksniper.command.paste");
	}
	
	/**
	 * @param CommandSender $sender
	 * @param type          $commandLabel
	 * @param array         $args
	 *
	 * @return boolean
	 */
	public function execute(CommandSender $sender, $commandLabel, array $args) {
		if(!$this->testPermission($sender)) {
			$this->sendNoPermission($sender);
			return true;
		}
		
		if(!$sender instanceof Player) {
			$this->sendConsoleError($sender);
			return true;
		}
		
		if(count($args) < 1 || count($args) > 3) {
			$sender->sendMessage(TF::RED . "[Usage] /paste <type> [name]");
			return true;
		}
		
		$center = $sender->getTargetBlock(100);
		if(!$center) {
			$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.no-target-found"));
			return true;
		}
		
		switch(strtolower($args[0])) {
			case "copy":
				if($this->getPlugin()->getCopyStore()->copyStoreExists()) {
					$this->getPlugin()->getCopyStore()->setTargetBlock($center);
					$this->getPlugin()->getCopyStore()->pasteCopy();
				}
				break;
			
			case "template":
				$clone = new Template($this); // TODO
				break;
			
			default:
				$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.paste-not-found"));
				return true;
		}
		$sender->sendMessage(TF::GREEN . $this->getPlugin()->getTranslation("commands.succeed.paste"));
	}
}