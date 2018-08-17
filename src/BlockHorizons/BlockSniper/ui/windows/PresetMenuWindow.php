<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\ui\forms\MenuForm;
use BlockHorizons\BlockSniper\ui\WindowHandler;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\Player;

class PresetMenuWindow extends MenuForm {

	private const CREATE_ICON = "http://www.clker.com/cliparts/H/D/e/R/O/P/green-plus-sign-hi.png";
	private const DELETE_ICON = "http://www.pngmart.com/files/3/Red-Cross-Transparent-PNG.png";
	private const SELECT_ICON = "http://www.clker.com/cliparts/k/T/w/u/G/S/transparent-yellow-checkmark-md.png";
	private const LIST_ICON = "http://www.iconsdb.com/icons/preview/guacamole-green/list-xxl.png";
	private const EXIT_ICON = self::DELETE_ICON;

	public function __construct(Loader $loader, Player $requester) {
		parent::__construct($this->t(Translation::UI_PRESET_MENU_TITLE), $this->t(Translation::UI_PRESET_MENU_SUBTITLE));
		$this->setResponseForm(new MainMenuWindow($loader, $requester));

		$this->addOption($this->t(Translation::UI_PRESET_MENU_CREATE), self::CREATE_ICON, "url", function(Player $player) {

		});
		$this->addOption($this->t(Translation::UI_PRESET_MENU_DELETE), self::DELETE_ICON, "url", function(Player $player) {

		});
		$this->addOption($this->t(Translation::UI_PRESET_MENU_SELECT), self::SELECT_ICON, "url", function(Player $player) {

		});
		$this->addOption($this->t(Translation::UI_PRESET_MENU_LIST), self::LIST_ICON, "url", function(Player $player) {

		});
		$this->addOption($this->t(Translation::UI_PRESET_MENU_EXIT), self::EXIT_ICON, "url", function(Player $player) {
			$this->setResponseForm(null);
		});
	}

	/*
	public function process(): void {
		$this->data = [
			"type" => "form",
			"title" => Translation::get(Translation::UI_PRESET_MENU_TITLE),
			"content" => Translation::get(Translation::UI_PRESET_MENU_SUBTITLE),
			"buttons" => [
				[
					"text" => Translation::get(Translation::UI_PRESET_MENU_CREATE),
					"image" => [
						"type" => "url",
						"data" =>
					]
				],
				[
					"text" => Translation::get(Translation::UI_PRESET_MENU_DELETE),
					"image" => [
						"type" => "url",
						"data" =>
					]
				],
				[
					"text" => Translation::get(Translation::UI_PRESET_MENU_SELECT),
					"image" => [
						"type" => "url",
						"data" =>
					]
				],
				[
					"text" => Translation::get(Translation::UI_PRESET_MENU_LIST),
					"image" => [
						"type" => "url",
						"data" =>
					]
				],
				[
					"text" => Translation::get(Translation::UI_PRESET_MENU_EXIT),
					"image" => [
						"type" => "url",
						"data" => "http://www.pngmart.com/files/3/Red-Cross-Transparent-PNG.png"
					]
				]
			]
		];
	}*/

	public function handle(ModalFormResponsePacket $packet): bool {
		$index = (int) $packet->formData + 5;
		$windowHandler = new WindowHandler();
		if($index === 9) {
			$this->navigate(WindowHandler::WINDOW_MAIN_MENU, $this->player, $windowHandler);
			return false;
		}

		$this->navigate($index, $this->player, new WindowHandler());
		return true;
	}
}