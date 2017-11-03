<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\data\Translation;

class GlobalBrushListWindow extends Window {

	public function process(): void {
		$this->data = [
			"type" => "form",
			"title" => Translation::get(Translation::UI_GLOBAL_BRUSH_LIST_TITLE),
			"content" => Translation::get(Translation::UI_GLOBAL_BRUSH_LIST_SUBTITLE),
			"buttons" => []
		];
		foreach($this->getLoader()->getSessionManager()->getServerSessions() as $session) {
			$this->data["buttons"][] = [
				"text" => $session->getName(),
				"image" => [
					"type" => "url",
					"data" => "http://www.iconsdb.com/icons/preview/guacamole-green/list-xxl.png"
				]
			];
		}
	}
}