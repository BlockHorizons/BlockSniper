<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\data\Translation;

class GlobalBrushSelectionMenu extends Window {

	public function process(): void {
		$this->data = [
			"type" => "form",
			"title" => (new Translation(Translation::UI_GLOBAL_BRUSH_SELECTION_TITLE))->getMessage(),
			"content" => (new Translation(Translation::UI_GLOBAL_BRUSH_SELECTION_SUBTITLE))->getMessage(),
			"buttons" => []
		];
		foreach($this->getLoader()->getSessionManager()->getServerSessions() as $session) {
			$this->data["buttons"][] = [
				"text" => $session->getName(),
				"image" => [
					"type" => "url",
					"data" => "http://www.clker.com/cliparts/k/T/w/u/G/S/transparent-yellow-checkmark-md.png"
				]
			];
		}
	}
}