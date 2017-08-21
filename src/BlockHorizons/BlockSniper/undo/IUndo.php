<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\undo;

interface IUndo {

	public function getDetached();

	public function restore();
}