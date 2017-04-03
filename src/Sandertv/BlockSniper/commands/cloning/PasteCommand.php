<?php

namespace Sandertv\BlockSniper\commands\cloning;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use Sandertv\BlockSniper\commands\BaseCommand;
use Sandertv\BlockSniper\Loader;

class PasteCommand extends BaseCommand {
	
	public function __construct(Loader $owner) {
		parent::__construct($owner, "paste", "Paste the selected clone or template", "<type> [name]", []);
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
			$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.no-target-found"));
			return true;
		}
		
		switch(strtolower($args[0])) {
			case "copy":
				if($this->getPlugin()->getCloneStore()->copyStoreExists()) {
					$this->getPlugin()->getCloneStore()->setTargetBlock($center);
					$this->getPlugin()->getCloneStore()->pasteCopy($sender->getLevel(), $sender);
				}
				break;
			
			case "template":
				if(!$this->getPlugin()->getCloneStore()->templateExists($args[1])) {
					$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.template-not-existing"));
					return true;
				}
				$this->getPlugin()->getCloneStore()->pasteTemplate($args[1], $center, $sender);
				break;
			
			default:
				$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.paste-not-found"));
				return true;
		}
		$sender->sendMessage(TF::GREEN . $this->getPlugin()->getTranslation("commands.succeed.paste"));
	}
}