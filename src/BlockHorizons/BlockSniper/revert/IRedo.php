<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\revert;

interface IRedo{

	public function getDetached();

	public function restore() : void;
}