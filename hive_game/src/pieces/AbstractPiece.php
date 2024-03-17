<?php

namespace Joyce0398\HiveGame\pieces;

use Joyce0398\HiveGame\Player;

abstract class AbstractPiece
{
    protected Player $player;
    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    abstract function validateMove(string $from, string $to): bool;

}