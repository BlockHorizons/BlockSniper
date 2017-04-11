<?php

namespace BlockHorizons\BlockSniper\commands\cloning;

use BlockHorizons\BlockSniper\commands\BaseCommand;
use BlockHorizons\BlockSniper\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class PasteCommand extends BaseCommand {
	
	public function __construct(Loader $loader) {
		parent::__construct($loader, "paste", "Paste the selected clone or template", "<type> [name]", []);
		$this->setPermission("blocksniper.command.paste");
		$this->setUsage(TF::RED . "[Usage] /paste <type> [name]");
	}
	
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
			$sender->sendMessage($this->getUsage());
			return true;
		}
		
		$center = $sender->getTargetBlock(100);
		if(!$center) {
			$sender->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.no-target-found"));
			return true;
		}
		
		switch(strtolower($args[0])) {
			case "copy":
				if($this->getLoader()->getCloneStore()->copyStoreExists()) {
					$this->getLoader()->getCloneStore()->setTargetBlock($center);
					$this->getLoader()->getCloneStore()->pasteCopy($sender->getLevel(), $sender);
				}
				break;
			
			case "template":
				if(!$this->getLoader()->getCloneStore()->templateExists($args[1])) {
					$sender->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.template-not-existing"));
					return true;
				}
				$this->getLoader()->getCloneStore()->pasteTemplate($args[1], $center, $sender);
				break;
			
			default:
				$sender->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.paste-not-found"));
				return true;
		}
		$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("commands.succeed.paste"));
	}
}