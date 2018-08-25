<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\revert;

interface IUndo{

	public function getDetached();

	public function restore() : void;
}